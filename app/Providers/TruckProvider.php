<?php

namespace App\Providers;

use App\Services\TruckService;
use Illuminate\Support\ServiceProvider;

class TruckProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(TruckService::class, function ($app) {
            return new TruckService();
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
