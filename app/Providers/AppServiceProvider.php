<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share unread notifications count with all views
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $unreadNotifications = auth()->user()->unreadNotifications()->count();
                $view->with('unreadNotifications', $unreadNotifications);
            } else {
                $view->with('unreadNotifications', 0);
            }
        });
    }
}
