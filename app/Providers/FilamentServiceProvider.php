<?php

namespace App\Providers;

use Filament\Panel; // Import the Panel class
use Filament\PanelProvider; // Import the PanelProvider class
use Filament\Pages\Dashboard; // Import Dashboard page if you want to use the default one
use Filament\Widgets\AccountWidget; // Import AccountWidget if you want to use the default one
use Filament\Widgets\FilamentInfoWidget; // Import FilamentInfoWidget if you want to use the default one
use Filament\Navigation\NavigationGroup; // Import NavigationGroup if you want to organize navigation
use App\Filament\Resources\PostResource; // Example: if you have a PostResource

use Illuminate\Support\ServiceProvider; // You likely don't need to extend this directly anymore

class FilamentServiceProvider extends PanelProvider // Extend PanelProvider instead of ServiceProvider
{
    public function panel(Panel $panel): Panel // Add the panel() method that returns a Panel instance
    {
        return $panel
            ->default() // Make this panel the default one
            ->id('admin') // Set a unique ID for this panel (usually 'admin')
            ->path('admin') // Set the URL path for this panel (e.g., /admin)
            ->login() // Enable login page for this panel
            ->colors(['primary' => 'violet']) // Example: customize colors
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources') // Discover resources in this directory
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages') // Discover pages
            ->pages([
                Dashboard::class, // Register the default Dashboard page
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets') // Discover widgets
            ->widgets([
                AccountWidget::class, // Register the AccountWidget
                FilamentInfoWidget::class, // Register the FilamentInfoWidget
            ])
            ->navigationGroups([ // Example of navigation groups (optional)
                NavigationGroup::make('Blog')
                    ->items([
                        PostResource::class, // Example: PostResource under 'Blog' group
                    ]),
            ])
            // ->middleware([ // Add middleware if needed
            //     \App\Http\Middleware\VerifyCsrfToken::class,
            // ])
            // ->authMiddleware([ // Add auth middleware if needed
            //     \App\Http\Middleware\Authenticate::class,
            // ])
            ;
    }
}