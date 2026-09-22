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
        // Gunakan env variable atau konfigurasi APP_URL untuk memaksa HTTPS agar konsisten di balik reverse proxy
        if (env('FORCE_HTTPS', true) || str_starts_with(config('app.url'), 'https://') || app()->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
