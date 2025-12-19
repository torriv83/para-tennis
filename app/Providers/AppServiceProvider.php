<?php

namespace App\Providers;

use App\Services\PinAuthService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PinAuthService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::if('canEditResults', function ($tournament) {
            return app(PinAuthService::class)->canEditResults($tournament);
        });
    }
}
