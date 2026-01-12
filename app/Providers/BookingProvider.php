<?php

namespace App\Providers;

use App\Services\BookingService;
use Illuminate\Support\ServiceProvider;

class BookingProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(BookingService::class, function ($app) {
            return new BookingService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
