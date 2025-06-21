<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index()
    {
        // Update status booking otomatis
        $this->bookingService->updateBookingStatuses();

        $user = auth()->user();
        $today = Carbon::today();
        $isAdmin = $user ? $user->isAdmin() : false;

        // Data untuk semua user (termasuk guest)
        $publicData = [
            'rooms' => Room::active()->with(['bookings' => function ($query) use ($today) {
                $query->where('booking_date', '>=', $today)
                      ->whereIn('status', ['approved', 'ongoing'])
                      ->orderBy('booking_date')
                      ->orderBy('start_time');
            }])->get(),
            'todayBookings' => Booking::with(['room', 'user'])
                                    ->where('booking_date', $today)
                                    ->whereIn('status', ['approved', 'ongoing', 'completed'])
                                    ->orderBy('start_time')
                                    ->get(),
            'upcomingBookings' => Booking::with(['room', 'user'])
                                        ->where('booking_date', '>', $today)
                                        ->whereIn('status', ['approved'])
                                        ->orderBy('booking_date')
                                        ->orderBy('start_time')
                                        ->limit(10)
                                        ->get(),
        ];

        // Data untuk user yang login
        if ($user) {
            $userStats = [
                'myBookings' => Booking::where('user_id', $user->id) // Perbaiki method call
                                   ->where('booking_date', '>=', $today)
                                   ->whereIn('status', ['pending', 'approved', 'ongoing'])
                                   ->with(['room'])
                                   ->orderBy('booking_date')
                                   ->orderBy('start_time')
                                   ->limit(5)
                                   ->get(),
                'totalBookings' => Booking::where('user_id', $user->id)->count(),
                'pendingBookings' => Booking::where('user_id', $user->id)->where('status', 'pending')->count(),
                'approvedBookings' => Booking::where('user_id', $user->id)->where('status', 'approved')->count(),
            ];

            // Data tambahan untuk admin
            if ($isAdmin) {
                $adminStats = [
                    'pendingApprovals' => Booking::where('status', 'pending')->count(),
                    'todayBookingsCount' => Booking::where('booking_date', $today)->count(),
                    'totalRooms' => Room::count(),
                    'activeRooms' => Room::where('is_active', true)->count(),
                    'totalUsers' => User::count(),
                    'activeUsers' => User::where('is_active', true)->count(),
                    'recentBookings' => Booking::with(['room', 'user'])
                                              ->orderBy('created_at', 'desc')
                                              ->limit(10)
                                              ->get(),
                ];

                return view('dashboard.admin', array_merge($publicData, $userStats, ['adminStats' => $adminStats]));
            }

            return view('dashboard.user', array_merge($publicData, $userStats));
        }

        // Dashboard untuk guest
        return view('dashboard.guest', $publicData);
    }

    public function getRoomAvailability(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = $request->date;
        $rooms = Room::active()->get();
        $availability = [];

        foreach ($rooms as $room) {
            $bookings = $room->bookings()
                           ->where('booking_date', $date)
                           ->whereIn('status', ['approved', 'ongoing'])
                           ->orderBy('start_time')
                           ->get();

            $availability[] = [
                'room' => $room,
                'bookings' => $bookings,
                'isAvailable' => $bookings->isEmpty(),
            ];
        }

        return response()->json($availability);
    }

    public function getBookingStats()
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stats = [
            'total' => Booking::where('user_id', $user->id)->count(),
            'pending' => Booking::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved' => Booking::where('user_id', $user->id)->where('status', 'approved')->count(),
            'completed' => Booking::where('user_id', $user->id)->where('status', 'completed')->count(),
            'cancelled' => Booking::where('user_id', $user->id)->where('status', 'cancelled')->count(),
        ];

        if ($user->isAdmin()) {
            $stats['systemStats'] = [
                'totalBookings' => Booking::count(),
                'pendingApprovals' => Booking::where('status', 'pending')->count(),
                'todayBookings' => Booking::where('booking_date', Carbon::today())->count(),
                'totalRooms' => Room::count(),
                'totalUsers' => User::count(),
            ];
        }

        return response()->json($stats);
    }
}