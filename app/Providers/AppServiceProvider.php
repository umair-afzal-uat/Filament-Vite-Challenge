<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // You can leave this empty or add service bindings here if needed.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Initialize ViewErrorBag with an empty MessageBag for the default bag
        $viewErrorBag = new ViewErrorBag;
        $viewErrorBag->put('default', new MessageBag);
        View::share('errors', $viewErrorBag);

        // Filament customizations
        Filament::serving(function () {
            Vite::asset('resources/css/app.css'); // Load CSS asset
            Blade::component('components.grid', 'grid'); // Register a custom Blade component
        });

        // Register a custom render hook for Filament
        Filament::registerRenderHook(
            'head.end',
            function () {
                return Vite::asset('resources/images/logo.svg'); // Load logo asset
            }
        );

        // Register a custom view namespace for Filament
        $this->loadViewsFrom(__DIR__ . '/../resources/views/vendor/filament', 'filament-schemas');
    }
}