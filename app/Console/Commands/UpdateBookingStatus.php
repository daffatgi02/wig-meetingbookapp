<?php
// app/Console/Commands/UpdateBookingStatus.php

namespace App\Console\Commands;

use App\Services\BookingService;
use Illuminate\Console\Command;

class UpdateBookingStatus extends Command
{
    protected $signature = 'bookings:update-status';
    protected $description = 'Update booking status based on current time';

    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        parent::__construct();
        $this->bookingService = $bookingService;
    }

    public function handle()
    {
        $this->info('Updating booking statuses...');
        
        $this->bookingService->updateBookingStatuses();
        
        $this->info('Booking statuses updated successfully.');
    }
}