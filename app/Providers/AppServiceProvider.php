<?php

namespace App\Providers;

use App\Services\FooterContentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(FooterContentService $footerContentService): void
    {
        Inertia::share([
            'auth' => fn () => [
                'user' => optional(Auth::user(), fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]),
            ],
            'footer' => fn () => $footerContentService->get(),
        ]);
    }
}
