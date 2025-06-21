<?php
// app/Console/Kernel.php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\UpdateBookingStatus::class,
        Commands\SendBookingReminders::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Update booking status setiap 5 menit
        $schedule->command('bookings:update-status')
                 ->everyFiveMinutes()
                 ->withoutOverlapping();

        // Kirim reminder setiap 15 menit
        $schedule->command('bookings:send-reminders')
                 ->everyFifteenMinutes()
                 ->withoutOverlapping();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}