<?php

namespace App\Providers\Filament;

use App\Http\Controllers\Filament\AdminLoginController;
use App\Support\DatabaseUsage;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Notifications\Notification;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
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
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->bootUsing(function (Panel $panel) {
                Filament::serving(function () {
                    if (app()->runningInConsole()) {
                        return;
                    }

                    $guard = Filament::auth();

                    if (! $guard || ! $guard->check()) {
                        return;
                    }

                    $usage = app(DatabaseUsage::class)->getFormattedUsage();

                    if (! $usage) {
                        return;
                    }

                    $threshold = (float) config('database-usage.threshold_percent', 90);

                    if ($usage['percentage'] < $threshold) {
                        return;
                    }

                    $rounded = (int) floor($usage['percentage']);
                    $sessionKey = 'database_usage.notified_percent';
                    $lastNotified = session()->get($sessionKey);

                    if ($lastNotified === $rounded) {
                        return;
                    }

                    session()->put($sessionKey, $rounded);

                    Notification::make()
                        ->title('Basis data hampir penuh')
                        ->body(
                            sprintf(
                                'Penggunaan basis data telah mencapai %s%% (%s / %s MB). Pertimbangkan untuk menghapus data lama atau menambah kapasitas.',
                                $usage['percentage_formatted'],
                                $usage['used_mb_formatted'],
                                $usage['max_mb_formatted'],
                            )
                        )
                        ->warning()
                        ->persistent()
                        ->send();
                });
            })
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
            ]);
    }
}
