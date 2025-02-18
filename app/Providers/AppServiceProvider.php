<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Vite;

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

        Filament::serving(function () {
            // Replace default Filament theme with your custom one built via Vite:
            Filament::registerTheme(fn() => Vite::asset('resources/css/app.css'));

            // For example, update the brand logo:
            Filament::registerRenderHook(
                'head.end',
                fn() => Vite::asset('resources/images/logo.svg')
            );
        });
    }
}
