@php
    use Filament\Support\Enums\Width;
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    @props([
        'after' => null,
        'heading' => null,
        'subheading' => null,
    ])

    <div class="fi-simple-layout flex min-h-screen flex-col items-center">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_START, scopes: $livewire->getRenderHookScopes()) }}

        @if (($hasTopbar ?? true) && filament()->auth()->check())
            <div
                class="absolute end-0 top-0 flex h-16 items-center gap-x-4 pe-4 md:pe-6 lg:pe-8"
            >
                @if (filament()->hasDatabaseNotifications())
                    @livewire(Filament\Livewire\DatabaseNotifications::class, [
                        'lazy' => filament()->hasLazyLoadedDatabaseNotifications(),
                    ])
                @endif

                @livewire(Filament\Livewire\SimpleUserMenu::class)
            </div>
        @endif

        <div
            class="fi-simple-main-ctn flex w-full grow items-center justify-center"
        >
            <main
                @class([
                    'fi-simple-main my-16 w-full bg-white px-6 py-12 ring-1 shadow-xs ring-gray-950/5 sm:rounded-xl sm:px-12 dark:bg-gray-900 dark:ring-white/10',
                    match ($maxWidth ??= (filament()->getSimplePageMaxContentWidth() ?? Width::Large)) {
                        Width::ExtraSmall, 'xs' => 'max-w-xs',
                        Width::Small, 'sm' => 'max-w-sm',
                        Width::Medium, 'md' => 'max-w-md',
                        Width::Large, 'lg' => 'max-w-lg',
                        Width::ExtraLarge, 'xl' => 'max-w-xl',
                        Width::TwoExtraLarge, '2xl' => 'max-w-2xl',
                        Width::ThreeExtraLarge, '3xl' => 'max-w-3xl',
                        Width::FourExtraLarge, '4xl' => 'max-w-4xl',
                        Width::FiveExtraLarge, '5xl' => 'max-w-5xl',
                        Width::SixExtraLarge, '6xl' => 'max-w-6xl',
                        Width::SevenExtraLarge, '7xl' => 'max-w-7xl',
                        Width::Full, 'full' => 'max-w-full',
                        Width::MinContent, 'min' => 'max-w-min',
                        Width::MaxContent, 'max' => 'max-w-max',
                        Width::FitContent, 'fit' => 'max-w-fit',
                        Width::Prose, 'prose' => 'max-w-prose',
                        Width::ScreenSmall, 'screen-sm' => 'max-w-(--breakpoint-sm)',
                        Width::ScreenMedium, 'screen-md' => 'max-w-(--breakpoint-md)',
                        Width::ScreenLarge, 'screen-lg' => 'max-w-(--breakpoint-lg)',
                        Width::ScreenExtraLarge, 'screen-xl' => 'max-w-(--breakpoint-xl)',
                        Width::ScreenTwoExtraLarge, 'screen-2xl' => 'max-w-(--breakpoint-2xl)',
                        default => $maxWidth,
                    },
                ])
            >
                {{ $slot }}
            </main>
        </div>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $livewire->getRenderHookScopes()) }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_END, scopes: $livewire->getRenderHookScopes()) }}
    </div>
</x-filament-panels::layout.base>
