<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use App\Services\ActivityLogService;
use App\Services\BookingService;
use App\Services\NotificationService;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
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
        config(['app.locale' => 'id']);
        Carbon::setLocale('id');

        Paginator::useBootstrap();

        // Global view composers
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $unreadNotifications = auth()->user()
                    ->notifications()
                    ->unread()
                    ->count();

                $view->with('unreadNotifications', $unreadNotifications);
            }
        });
    }
}
