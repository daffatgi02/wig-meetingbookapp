<?php
// app/Console/Commands/SetupMeetingRoomApp.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupMeetingRoomApp extends Command
{
    protected $signature = 'meeting-room:setup';
    protected $description = 'Setup Meeting Room Booking Application';

    public function handle()
    {
        $this->info('Setting up Meeting Room Booking Application...');
        
        // Run migrations
        $this->info('Running migrations...');
        Artisan::call('migrate');
        
        // Run seeders
        $this->info('Seeding database...');
        Artisan::call('db:seed');
        
        // Clear cache
        $this->info('Clearing cache...');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        
        $this->info('Setup completed successfully!');
        $this->line('');
        $this->line('Default Admin Credentials:');
        $this->line('Email: admin@meetingroom.local');
        $this->line('Password: admin123');
        $this->line('');
        $this->line('Default User Credentials:');
        $this->line('Email: user@meetingroom.local');
        $this->line('Password: user123');
        
        return 0;
    }
}