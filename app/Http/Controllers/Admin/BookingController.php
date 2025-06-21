<?php
// app/Http/Controllers/Admin/BookingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Booking::with(['room', 'user', 'participants']);

        // Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->where('booking_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('booking_date', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('room', function ($roomQuery) use ($search) {
                      $roomQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->orderBy('booking_date', 'desc')
                         ->orderBy('start_time', 'desc')
                         ->paginate(15);

        $rooms = Room::active()->orderBy('name')->get();
        $users = User::active()->orderBy('name')->get();

        return view('admin.bookings.index', compact('bookings', 'rooms', 'users'));
    }

    public function create()
    {
        $rooms = Room::active()->orderBy('name')->get();
        $users = User::active()->users()->orderBy('name')->get();
        
        return view('admin.bookings.create', compact('rooms', 'users'));
    }

    public function store(StoreBookingRequest $request)
    {
        try {
            $user = User::findOrFail($request->user_id);
            
            $booking = $this->bookingService->createBooking(
                $request->validated(),
                $user,
                true // isAdminCreated
            );

            return redirect()->route('admin.bookings.show', $booking)
                           ->with('success', 'Pemesanan berhasil dibuat untuk ' . $user->name);
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Booking $booking)
    {
        $booking->load(['room', 'user', 'participants', 'approver', 'adminCreator']);
        
        return view('admin.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $rooms = Room::active()->orderBy('name')->get();
        $users = User::active()->users()->orderBy('name')->get();
        $booking->load('participants');
        
        return view('admin.bookings.edit', compact('booking', 'rooms', 'users'));
    }

    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        try {
            $updatedBooking = $this->bookingService->updateBooking(
                $booking,
                $request->validated()
            );

            return redirect()->route('admin.bookings.show', $updatedBooking)
                           ->with('success', 'Pemesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function approve(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return redirect()->back()
                           ->with('error', 'Pemesanan tidak dalam status pending.');
        }

        try {
            $this->bookingService->approveBooking($booking, auth()->user());
            
            return redirect()->back()
                           ->with('success', 'Pemesanan berhasil disetujui.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Booking $booking)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($booking->status !== 'pending') {
            return redirect()->back()
                           ->with('error', 'Pemesanan tidak dalam status pending.');
        }

        try {
            $this->bookingService->rejectBooking(
                $booking,
                auth()->user(),
                $request->rejection_reason
            );
            
            return redirect()->back()
                           ->with('success', 'Pemesanan berhasil ditolak.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cancel(Booking $booking)
    {
        try {
            $this->bookingService->cancelBooking($booking, auth()->user());
            
            return redirect()->back()
                           ->with('success', 'Pemesanan berhasil dibatalkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function move(Request $request, Booking $booking)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Validasi ketersediaan
        $room = Room::find($request->room_id);
        if (!$room->isAvailable($request->booking_date, $request->start_time, $request->end_time, $booking->id)) {
            return redirect()->back()
                           ->with('error', 'Ruangan tidak tersedia pada waktu yang dipilih.');
        }

        try {
            $this->bookingService->moveBooking(
                $booking,
                $request->only(['room_id', 'booking_date', 'start_time', 'end_time']),
                auth()->user()
            );
            
            return redirect()->back()
                           ->with('success', 'Pemesanan berhasil dipindahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Booking $booking)
    {
        try {
            $booking->participants()->delete();
            $booking->delete();
            
            return redirect()->route('admin.bookings.index')
                           ->with('success', 'Pemesanan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function pending()
    {
        $bookings = Booking::with(['room', 'user'])
                          ->pending()
                          ->orderBy('created_at', 'asc')
                          ->paginate(10);

        return view('admin.bookings.pending', compact('bookings'));
    }
}