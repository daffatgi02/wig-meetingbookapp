<?php
// app/Http/Controllers/BookingController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
        $this->middleware('auth');
    }

    public function index()
    {
        $bookings = Booking::with(['room', 'user', 'participants'])
                          ->forUser(auth()->id())
                          ->orderBy('booking_date', 'desc')
                          ->orderBy('start_time', 'desc')
                          ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        $rooms = Room::active()->orderBy('name')->get();
        return view('bookings.create', compact('rooms'));
    }

    public function store(StoreBookingRequest $request)
    {
        try {
            $booking = $this->bookingService->createBooking(
                $request->validated(),
                auth()->user()
            );

            return redirect()->route('bookings.show', $booking)
                           ->with('success', 'Pemesanan berhasil dibuat. Status: ' . $booking->getStatusLabel());
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        
        $booking->load(['room', 'user', 'participants', 'approver']);
        
        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $this->authorize('update', $booking);
        
        if (!$booking->isEditable()) {
            return redirect()->route('bookings.show', $booking)
                           ->with('error', 'Pemesanan tidak dapat diedit.');
        }

        $rooms = Room::active()->orderBy('name')->get();
        $booking->load('participants');
        
        return view('bookings.edit', compact('booking', 'rooms'));
    }

    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        try {
            $updatedBooking = $this->bookingService->updateBooking(
                $booking,
                $request->validated()
            );

            return redirect()->route('bookings.show', $updatedBooking)
                           ->with('success', 'Pemesanan berhasil diperbarui. Status: ' . $updatedBooking->getStatusLabel());
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cancel(Booking $booking)
    {
        $this->authorize('delete', $booking);
        
        if (!$booking->isCancellable()) {
            return redirect()->route('bookings.show', $booking)
                           ->with('error', 'Pemesanan tidak dapat dibatalkan.');
        }

        try {
            $this->bookingService->cancelBooking($booking, auth()->user());
            
            return redirect()->route('bookings.index')
                           ->with('success', 'Pemesanan berhasil dibatalkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'exclude_booking_id' => 'nullable|exists:bookings,id',
        ]);

        $room = Room::find($request->room_id);
        $isAvailable = $room->isAvailable(
            $request->date,
            $request->start_time,
            $request->end_time,
            $request->exclude_booking_id
        );

        $conflicts = [];
        if (!$isAvailable) {
            $conflicts = $room->getConflictingBookings(
                $request->date,
                $request->start_time,
                $request->end_time,
                $request->exclude_booking_id
            )->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'title' => $booking->title,
                    'user' => $booking->user->name,
                    'start_time' => $booking->start_time,
                    'end_time' => $booking->end_time,
                ];
            });
        }

        return response()->json([
            'available' => $isAvailable,
            'conflicts' => $conflicts,
        ]);
    }
}