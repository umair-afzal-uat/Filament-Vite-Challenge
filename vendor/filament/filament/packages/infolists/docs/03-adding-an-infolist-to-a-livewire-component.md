---
title: Adding an infolist to a Livewire component
---

## Setting up the Livewire component

First, generate a new Livewire component:

```bash
php artisan make:livewire ViewProduct
```

Then, render your Livewire component on the page:

```blade
@livewire('view-product')
```

Alternatively, you can use a full-page Livewire component:

```php
use App\Livewire\ViewProduct;
use Illuminate\Support\Facades\Route;

Route::get('products/{product}', ViewProduct::class);
```

You must use the `InteractsWithSchemas` trait, and implement the `HasSchemas` interface on your Livewire component class:

```php
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Livewire\Component;

class ViewProduct extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    // ...
}
```

## Adding the infolist

Next, add a method to the Livewire component which accepts an `$infolist` object, modifies it, and returns it:

```php
use Filament\Schemas\Schema;

public function productInfolist(Schema $infolist): Schema
{
    return $infolist
        ->record($this->product)
        ->schema([
            // ...
        ]);
}
```

Finally, render the infolist in the Livewire component's view:

```blade
{{ $this->productInfolist }}
```

## Passing data to the infolist

You can pass data to the infolist in two ways:

Either pass an Eloquent model instance to the `record()` method of the infolist, to automatically map all the model attributes and relationships to the entries in the infolist's schema:

```php
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

public function productInfolist(Schema $infolist): Schema
{
    return $infolist
        ->record($this->product)
        ->schema([
            TextEntry::make('name'),
            TextEntry::make('category.name'),
            // ...
        ]);
}
```

Alternatively, you can pass an array of data to the `state()` method of the infolist, to manually map the data to the entries in the infolist's schema:

```php
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

public function productInfolist(Schema $infolist): Schema
{
    return $infolist
        ->constantState([
            'name' => 'MacBook Pro',
            'category' => [
                'name' => 'Laptops',
            ],
            // ...
        ])
        ->schema([
            TextEntry::make('name'),
            TextEntry::make('category.name'),
            // ...
        ]);
}
```
