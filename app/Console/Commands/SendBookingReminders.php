<?php
// app/Console/Commands/SendBookingReminders.php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders';
   protected $description = 'Send booking reminders to users';

   protected $notificationService;

   public function __construct(NotificationService $notificationService)
   {
       parent::__construct();
       $this->notificationService = $notificationService;
   }

   public function handle()
   {
       $this->info('Sending booking reminders...');
       
       $now = Carbon::now();
       $reminderTime = $now->copy()->addMinutes(30);
       
       // Ambil booking yang akan dimulai dalam 30 menit
       $upcomingBookings = Booking::with(['user', 'room'])
                                 ->where('booking_date', $now->format('Y-m-d'))
                                 ->where('start_time', '>=', $now->format('H:i:s'))
                                 ->where('start_time', '<=', $reminderTime->format('H:i:s'))
                                 ->where('status', 'approved')
                                 ->get();

       $remindersSent = 0;
       
       foreach ($upcomingBookings as $booking) {
           // Cek apakah reminder sudah dikirim (bisa ditambahkan field untuk tracking)
           $this->notificationService->sendBookingReminderNotification($booking);
           $remindersSent++;
       }
       
       $this->info("Sent {$remindersSent} booking reminders.");
   }
}