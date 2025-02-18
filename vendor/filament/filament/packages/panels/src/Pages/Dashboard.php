<?php

namespace Filament\Pages;

use BackedEnum;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\NestedSchema;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends Page
{
    protected static string $routePath = '/';

    protected static ?int $navigationSort = -2;

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ??
            static::$title ??
            __('filament-panels::pages/dashboard.title');
    }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return static::$navigationIcon
            ?? FilamentIcon::resolve('panels::pages.dashboard.navigation-item')
            ?? (Filament::hasTopNavigation() ? Heroicon::Home : Heroicon::OutlinedHome);
    }

    public static function getRoutePath(): string
    {
        return static::$routePath;
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return Filament::getWidgets();
    }

    /**
     * @deprecated Use `getWidgetsSchemaComponents($this->getWidgets())` to transform widgets into schema components instead, which also filters their visibility.
     *
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getVisibleWidgets(): array
    {
        return $this->filterVisibleWidgets($this->getWidgets());
    }

    /**
     * @return int | array<string, ?int>
     */
    public function getColumns(): int | array
    {
        return 2;
    }

    public function getTitle(): string | Htmlable
    {
        return static::$title ?? __('filament-panels::pages/dashboard.title');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...(method_exists($this, 'getFiltersForm') ? [NestedSchema::make('filtersForm')] : []),
                Grid::make($this->getColumns())
                    ->schema($this->getWidgetsSchemaComponents($this->getWidgets())),
            ]);
    }
}
