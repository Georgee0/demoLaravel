<?php

namespace App\Providers;

use App\Services\DriverService;
use Illuminate\Support\ServiceProvider;

class DriverProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(DriverService::class, function($app): DriverService {
            return new DriverService();
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
