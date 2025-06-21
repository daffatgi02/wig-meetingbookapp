<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use App\Services\ActivityLogService;
use App\Services\BookingService;
use App\Services\NotificationService;
use App\Models\Notification;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(ActivityLogService::class);
        $this->app->singleton(BookingService::class);
    }

    public function boot()
    {
        Paginator::useBootstrap();
        
        // Global view composers
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $unreadNotifications = Notification::where('user_id', auth()->id()) // Perbaiki method call
                    ->where('is_read', false)
                    ->count();
                    
                $view->with('unreadNotifications', $unreadNotifications);
            }
        });
    }
}