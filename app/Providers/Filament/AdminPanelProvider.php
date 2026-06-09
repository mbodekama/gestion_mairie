<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->navigationGroups([
                NavigationGroup::make('Contribuables')
                    ->icon('heroicon-o-users')
                    ->collapsible(false),

                NavigationGroup::make('Établissements')
                    ->icon('heroicon-o-building-office')
                    ->collapsible(false),

                NavigationGroup::make('Émission')
                    ->icon('heroicon-o-document-text')
                    ->collapsible(false),

                NavigationGroup::make('Taxe foncière')
                    ->icon('heroicon-o-home')
                    ->collapsible(false),

                NavigationGroup::make('Recouvrement')
                    ->icon('heroicon-o-banknotes')
                    ->collapsible(false),

                NavigationGroup::make('Dossiers')
                    ->icon('heroicon-o-folder')
                    ->collapsible(false),

                NavigationGroup::make('Convocations')
                    ->icon('heroicon-o-envelope')
                    ->collapsible(false),

                NavigationGroup::make('Contrôle')
                    ->icon('heroicon-o-shield-check')
                    ->collapsible(false),

                NavigationGroup::make('Paramétrage fiscal')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->collapsible(false),

                NavigationGroup::make('Pilotage')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsible()
                    ->collapsed(),

                NavigationGroup::make('Administration')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible()
                    ->collapsed(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
