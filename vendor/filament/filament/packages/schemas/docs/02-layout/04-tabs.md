---
title: Tabs
---
import AutoScreenshot from "@components/AutoScreenshot.astro"

## Overview

Some schemas can be long and complex. You may want to use tabs to reduce the number of components that are visible at once:

```php
use Filament\Schemas\Components\Tabs;

Tabs::make('Tabs')
    ->tabs([
        \Filament\Schemas\Components\Tabs\Tab::make('Tab 1')
            ->schema([
                // ...
            ]),
        \Filament\Schemas\Components\Tabs\Tab::make('Tab 2')
            ->schema([
                // ...
            ]),
        \Filament\Schemas\Components\Tabs\Tab::make('Tab 3')
            ->schema([
                // ...
            ]),
    ])
```

<AutoScreenshot name="schemas/layout/tabs/simple" alt="Tabs" version="4.x" />

## Setting the default active tab

The first tab will be open by default. You can change the default open tab using the `activeTab()` method:

```php
use Filament\Schemas\Components\Tabs;

Tabs::make('Tabs')
    ->tabs([
        \Filament\Schemas\Components\Tabs\Tab::make('Tab 1')
            ->schema([
                // ...
            ]),
        \Filament\Schemas\Components\Tabs\Tab::make('Tab 2')
            ->schema([
                // ...
            ]),
        \Filament\Schemas\Components\Tabs\Tab::make('Tab 3')
            ->schema([
                // ...
            ]),
    ])
    ->activeTab(2)
```

## Setting a tab icon

Tabs may have an [icon](../../styling/icons), which you can set using the `icon()` method:

```php
use Filament\Schemas\Components\Tabs;

Tabs::make('Tabs')
    ->tabs([
        \Filament\Schemas\Components\Tabs\Tab::make('Notifications')
            ->icon('heroicon-m-bell')
            ->schema([
                // ...
            ]),
        // ...
    ])
```

<AutoScreenshot name="schemas/layout/tabs/icons" alt="Tabs with icons" version="4.x" />

### Setting the tab icon position

The icon of the tab may be positioned before or after the label using the `iconPosition()` method:

```php
use Filament\Schemas\Components\Tabs;
use Filament\Support\Enums\IconPosition;

Tabs::make('Tabs')
    ->tabs([
        \Filament\Schemas\Components\Tabs\Tab::make('Notifications')
            ->icon('heroicon-m-bell')
            ->iconPosition(IconPosition::After)
            ->schema([
                // ...
            ]),
        // ...
    ])
```

<AutoScreenshot name="schemas/layout/tabs/icons-after" alt="Tabs with icons after their labels" version="4.x" />

## Setting a tab badge

Tabs may have a badge, which you can set using the `badge()` method:

```php
use Filament\Schemas\Components\Tabs;

Tabs::make('Tabs')
    ->tabs([
        \Filament\Schemas\Components\Tabs\Tab::make('Notifications')
            ->badge(5)
            ->schema([
                // ...
            ]),
        // ...
    ])
```

<AutoScreenshot name="schemas/layout/tabs/badges" alt="Tabs with badges" version="4.x" />

If you'd like to change the color for a badge, you can use the `badgeColor()` method:

```php
use Filament\Schemas\Components\Tabs;

Tabs::make('Tabs')
    ->tabs([
        \Filament\Schemas\Components\Tabs\Tab::make('Notifications')
            ->badge(5)
            ->badgeColor('success')
            ->schema([
                // ...
            ]),
        // ...
    ])
```

## Using grid columns within a tab

You may use the `columns()` method to customize the [grid](grid) within the tab:

```php
use Filament\Schemas\Components\Tabs;

Tabs::make('Tabs')
    ->tabs([
        \Filament\Schemas\Components\Tabs\Tab::make('Tab 1')
            ->schema([
                // ...
            ])
            ->columns(3),
        // ...
    ])
```

## Removing the styled container

By default, tabs and their content are wrapped in a container styled as a card. You may remove the styled container using `contained()`:

```php
use Filament\Schemas\Components\Tabs;

Tabs::make('Tabs')
    ->tabs([
        \Filament\Schemas\Components\Tabs\Tab::make('Tab 1')
            ->schema([
                // ...
            ]),
        \Filament\Schemas\Components\Tabs\Tab::make('Tab 2')
            ->schema([
                // ...
            ]),
        \Filament\Schemas\Components\Tabs\Tab::make('Tab 3')
            ->schema([
                // ...
            ]),
    ])
    ->contained(false)
```

## Persisting the current tab

By default, the current tab is not persisted in the browser's local storage. You can change this behavior using the `persistTab()` method. You must also pass in a unique `id()` for the tabs component, to distinguish it from all other sets of tabs in the app. This ID will be used as the key in the local storage to store the current tab:

```php
use Filament\Schemas\Components\Tabs;

Tabs::make('Tabs')
    ->tabs([
        // ...
    ])
    ->persistTab()
    ->id('order-tabs')
```

### Persisting the current tab in the URL's query string

By default, the current tab is not persisted in the URL's query string. You can change this behavior using the `persistTabInQueryString()` method:

```php
use Filament\Schemas\Components\Tabs;

Tabs::make('Tabs')
    ->tabs([
        \Filament\Schemas\Components\Tabs\Tab::make('Tab 1')
            ->schema([
                // ...
            ]),
        \Filament\Schemas\Components\Tabs\Tab::make('Tab 2')
            ->schema([
                // ...
            ]),
        \Filament\Schemas\Components\Tabs\Tab::make('Tab 3')
            ->schema([
                // ...
            ]),
    ])
    ->persistTabInQueryString()
```

By default, the current tab is persisted in the URL's query string using the `tab` key. You can change this key by passing it to the `persistTabInQueryString()` method:

```php
use Filament\Schemas\Components\Tabs;

Tabs::make('Tabs')
    ->tabs([
        \Filament\Schemas\Components\Tabs\Tab::make('Tab 1')
            ->schema([
                // ...
            ]),
        \Filament\Schemas\Components\Tabs\Tab::make('Tab 2')
            ->schema([
                // ...
            ]),
        \Filament\Schemas\Components\Tabs\Tab::make('Tab 3')
            ->schema([
                // ...
            ]),
    ])
    ->persistTabInQueryString('settings-tab')
```

