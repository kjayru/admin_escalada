<?php

namespace App\Providers\Filament;

use Filament\Pages\Dashboard;
use Filament\Widgets\AccountWidget;
use Slimani\MediaManager\MediaManagerPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => Blade::render('
                <link rel="apple-touch-icon" sizes="57x57" href="{{ asset("apple-icon-57x57.png") }}">
                <link rel="apple-touch-icon" sizes="60x60" href="{{ asset("apple-icon-60x60.png") }}">
                <link rel="apple-touch-icon" sizes="72x72" href="{{ asset("apple-icon-72x72.png") }}">
                <link rel="apple-touch-icon" sizes="76x76" href="{{ asset("apple-icon-76x76.png") }}">
                <link rel="apple-touch-icon" sizes="114x114" href="{{ asset("apple-icon-114x114.png") }}">
                <link rel="apple-touch-icon" sizes="120x120" href="{{ asset("apple-icon-120x120.png") }}">
                <link rel="apple-touch-icon" sizes="144x144" href="{{ asset("apple-icon-144x144.png") }}">
                <link rel="apple-touch-icon" sizes="152x152" href="{{ asset("apple-icon-152x152.png") }}">
                <link rel="apple-touch-icon" sizes="180x180" href="{{ asset("apple-icon-180x180.png") }}">
                <link rel="icon" type="image/png" sizes="192x192" href="{{ asset("android-icon-192x192.png") }}">
                <link rel="icon" type="image/png" sizes="32x32" href="{{ asset("favicon-32x32.png") }}">
                <link rel="icon" type="image/png" sizes="96x96" href="{{ asset("favicon-96x96.png") }}">
                <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("favicon-16x16.png") }}">
                <link rel="manifest" href="{{ asset("manifest.json") }}">
                <meta name="msapplication-TileColor" content="#ffffff">
                <meta name="msapplication-TileImage" content="{{ asset("ms-icon-144x144.png") }}">
                <meta name="theme-color" content="#ffffff">
            ')
        );
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->favicon(asset('favicon.ico'))
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->assets([
                Css::make('media-manager-compiled', public_path('css/media-manager-compiled.css'))
                    ->relativePublicPath('css/media-manager-compiled.css'),
                Css::make('media-manager-custom', public_path('css/media-manager-custom.css'))
                    ->relativePublicPath('css/media-manager-custom.css'),
            ])
            ->plugin(
                MediaManagerPlugin::make()
                    ->navigationGroup('Contenido')
                    ->navigationLabel('Biblioteca de Medios')
                    ->navigationSort(5)
            );
    }
}
