<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
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
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
        // L'interface utilise Bootstrap 5 (thème Falcon) : aligner la pagination
        // sur ce framework au lieu du gabarit Tailwind par défaut de Laravel.
        Paginator::useBootstrapFive();
    }
}
