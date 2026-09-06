<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        if (config('app.env') !== 'local' || request()->header('x-forwarded-proto') === 'https' || isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        
        // Unconditionally force HTTPS if URL contains https or production domain
        \Illuminate\Support\Facades\URL::forceScheme('https');
    }
}
