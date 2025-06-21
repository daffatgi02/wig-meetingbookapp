<?php
// routes/web.php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Default Laravel Auth Routes
Auth::routes();

// Public Routes (accessible by guests)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
Route::get('/calendar/events', [CalendarController::class, 'getEvents'])->name('calendar.events');
Route::get('/room-availability', [DashboardController::class, 'getRoomAvailability'])->name('room.availability');

// Protected Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    
    // Booking Routes for Users
    Route::resource('bookings', BookingController::class);
    Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('bookings/check-availability', [BookingController::class, 'checkAvailability'])->name('bookings.check-availability');
    
    // Calendar Routes
    Route::get('/calendar/available-slots', [CalendarController::class, 'getAvailableSlots'])->name('calendar.available-slots');
    Route::post('/calendar/move-event', [CalendarController::class, 'moveEvent'])->name('calendar.move-event');
    
    // Notification Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [NotificationController::class, 'unread'])->name('unread');
        Route::get('/count', [NotificationController::class, 'getUnreadCount'])->name('count');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });
    
    // User Stats
    Route::get('/booking-stats', [DashboardController::class, 'getBookingStats'])->name('booking.stats');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin Booking Management
    Route::resource('bookings', AdminBookingController::class);
    Route::get('bookings-pending', [AdminBookingController::class, 'pending'])->name('bookings.pending');
    Route::post('bookings/{booking}/approve', [AdminBookingController::class, 'approve'])->name('bookings.approve');
    Route::post('bookings/{booking}/reject', [AdminBookingController::class, 'reject'])->name('bookings.reject');
    Route::post('bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('bookings/{booking}/move', [AdminBookingController::class, 'move'])->name('bookings.move');
    
    // Room Management
    Route::resource('rooms', AdminRoomController::class);
    Route::post('rooms/{room}/toggle', [AdminRoomController::class, 'toggle'])->name('rooms.toggle');
    
    // User Management
    Route::resource('users', AdminUserController::class);
    Route::post('users/{user}/toggle', [AdminUserController::class, 'toggle'])->name('users.toggle');
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AdminSettingController::class, 'index'])->name('index');
        Route::put('/', [AdminSettingController::class, 'update'])->name('update');
        Route::post('/test-notification', [AdminSettingController::class, 'testNotification'])->name('test-notification');
    });
});

// API Routes untuk AJAX requests
Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {
    Route::get('/rooms/{room}/availability', function(\App\Models\Room $room, \Illuminate\Http\Request $request) {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'exclude_booking_id' => 'nullable|exists:bookings,id',
        ]);
        
        return response()->json([
            'available' => $room->isAvailable(
                $request->date,
                $request->start_time,
                $request->end_time,
                $request->exclude_booking_id
            )
        ]);
    })->name('rooms.availability');
    
    Route::get('/users/search', function(\Illuminate\Http\Request $request) {
        $request->validate(['q' => 'required|string|min:2']);
        
        $users = \App\Models\User::where('name', 'like', '%' . $request->q . '%')
                                ->orWhere('email', 'like', '%' . $request->q . '%')
                                ->limit(10)
                                ->get(['id', 'name', 'email']);
        
        return response()->json($users);
    })->name('users.search');
});