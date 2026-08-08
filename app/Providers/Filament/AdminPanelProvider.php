<?php

namespace App\Providers\Filament;

use App\Filament\Pages\ChangePassword;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\Profile;
use App\Http\Middleware\DenyCampaignUsersFromAdminPanel;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
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
            ->authGuard('admin')
            ->authPasswordBroker('admins')
            ->brandName('Good Neighbors')
            ->brandLogo(asset('images/design/logo-paper-plane.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('favicon.png'))
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->profile(Profile::class, isSimple: false)
            ->colors([
                'primary' => Color::hex('#8aa330'),
                'danger' => Color::hex('#f17025'),
            ])
            ->maxContentWidth(Width::Full)
            ->navigationGroups([
                'Content Management',
                'Campaign Management',
                'User Management',
                'Donations and BMS',
                'Security and Administration',
                'Settings',
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('Profile')
                    ->url(fn (): string => Profile::getUrl())
                    ->icon(Heroicon::UserCircle),
                MenuItem::make()
                    ->label('Change Password')
                    ->url(fn (): string => ChangePassword::getUrl())
                    ->icon(Heroicon::Key),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
                ChangePassword::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                DenyCampaignUsersFromAdminPanel::class,
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
