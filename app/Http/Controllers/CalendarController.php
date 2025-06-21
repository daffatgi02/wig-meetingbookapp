<?php
// app/Http/Controllers/CalendarController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index()
    {
        $rooms = Room::active()->orderBy('name')->get();
        return view('calendar.index', compact('rooms'));
    }

    public function getEvents(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
            'room_id' => 'nullable|exists:rooms,id',
        ]);

        $query = Booking::with(['room', 'user'])
                       ->whereBetween('booking_date', [$request->start, $request->end])
                       ->whereIn('status', ['pending', 'approved', 'ongoing', 'completed']);

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        $bookings = $query->get();

        $events = $bookings->map(function ($booking) {
            $startDateTime = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->start_time);
            $endDateTime = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->end_time);

            $user = auth()->user();
            $canEdit = $user && ($user->role === 'admin' || // Perbaiki method call isAdmin()
                                ($user->id === $booking->user_id && $booking->isEditable()));
            $canCancel = $user && ($user->role === 'admin' || // Perbaiki method call isAdmin()
                                  ($user->id === $booking->user_id && $booking->isCancellable()));

            return [
                'id' => $booking->id,
                'title' => $booking->title . ' - ' . $booking->room->name,
                'start' => $startDateTime->toISOString(),
                'end' => $endDateTime->toISOString(),
                'backgroundColor' => $booking->getStatusColor(),
                'borderColor' => $booking->getStatusColor(),
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'booking_id' => $booking->id,
                    'room_id' => $booking->room_id,
                    'room_name' => $booking->room->name,
                    'user_name' => $booking->user->name,
                    'status' => $booking->status,
                    'status_label' => $booking->getStatusLabel(),
                    'participant_count' => $booking->participant_count,
                    'purpose' => $booking->purpose,
                    'description' => $booking->description,
                    'can_edit' => $canEdit,
                    'can_cancel' => $canCancel,
                ],
            ];
        });

        return response()->json($events);
    }

    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'room_id' => 'required|exists:rooms,id',
            'duration' => 'integer|min:30|max:480', // dalam menit
        ]);

        $date = $request->date;
        $roomId = $request->room_id;
        $duration = $request->duration ?? 60; // default 1 jam

        $room = Room::findOrFail($roomId);
        
        // Ambil jam operasional dari setting
        $operatingStart = \App\Models\Setting::get('operating_hours_start', '08:00');
        $operatingEnd = \App\Models\Setting::get('operating_hours_end', '18:00');

        $startTime = Carbon::parse($date . ' ' . $operatingStart);
        $endTime = Carbon::parse($date . ' ' . $operatingEnd);

        // Ambil booking yang sudah ada
        $existingBookings = $room->bookings()
                                ->where('booking_date', $date)
                                ->whereIn('status', ['approved', 'ongoing', 'pending'])
                                ->orderBy('start_time')
                                ->get();

        $availableSlots = [];
        $currentTime = $startTime->copy();

        while ($currentTime->copy()->addMinutes($duration)->lte($endTime)) {
            $slotEnd = $currentTime->copy()->addMinutes($duration);
            
            $isAvailable = true;
            foreach ($existingBookings as $booking) {
                $bookingStart = Carbon::parse($date . ' ' . $booking->start_time);
                $bookingEnd = Carbon::parse($date . ' ' . $booking->end_time);
                
                // Cek overlap
                if ($currentTime->lt($bookingEnd) && $slotEnd->gt($bookingStart)) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                $availableSlots[] = [
                    'start' => $currentTime->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                    'label' => $currentTime->format('H:i') . ' - ' . $slotEnd->format('H:i'),
                ];
            }

            $currentTime->addMinutes(30); // Increment 30 menit
        }

        return response()->json([
            'room' => $room,
            'date' => $date,
            'available_slots' => $availableSlots,
            'existing_bookings' => $existingBookings,
        ]);
    }

    public function moveEvent(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'new_start' => 'required|date',
            'new_end' => 'required|date|after:new_start',
        ]);

        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $booking = Booking::findOrFail($request->booking_id);
        $user = auth()->user();

        // Cek permission
        if ($user->role !== 'admin' && $user->id !== $booking->user_id) { // Perbaiki method call isAdmin()
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$booking->isEditable()) {
            return response()->json(['error' => 'Booking cannot be edited'], 400);
        }

        try {
            $newStart = Carbon::parse($request->new_start);
            $newEnd = Carbon::parse($request->new_end);

            $data = [
                'room_id' => $booking->room_id,
                'booking_date' => $newStart->format('Y-m-d'),
                'start_time' => $newStart->format('H:i'),
                'end_time' => $newEnd->format('H:i'),
                'title' => $booking->title,
                'description' => $booking->description,
                'purpose' => $booking->purpose,
                'participant_count' => $booking->participant_count,
            ];

            // Validasi ketersediaan
            if (!$booking->room->isAvailable($data['booking_date'], $data['start_time'], $data['end_time'], $booking->id)) {
                return response()->json(['error' => 'Time slot not available'], 400);
            }

            $updatedBooking = $this->bookingService->updateBooking($booking, $data);

            return response()->json([
                'success' => true,
                'booking' => $updatedBooking,
                'message' => 'Booking berhasil dipindahkan'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}