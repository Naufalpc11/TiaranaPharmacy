<?php

namespace App\Http\Controllers\Filament;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AdminLoginController
{
    public function create(): View
    {
        return view('filament.admin.login');
    }

    public function store(Request $request): LoginResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $this->ensureIsNotRateLimited($request);

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        $remember = (bool) ($validated['remember'] ?? false);

        if (! Filament::auth()->attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey($request), $this->decaySeconds());
            $this->throwFailedValidation();
        }

        $user = Filament::auth()->user();

        if (($user instanceof FilamentUser) && (! $user->canAccessPanel(Filament::getCurrentPanel()))) {
            Filament::auth()->logout();
            RateLimiter::hit($this->throttleKey($request), $this->decaySeconds());
            $this->throwFailedValidation();
        }

        RateLimiter::clear($this->throttleKey($request));

        $request->session()->regenerate();

        return app(LoginResponse::class);
    }

    /**
     * @throws ValidationException
     */
    protected function throwFailedValidation(): never
    {
        throw ValidationException::withMessages([
            'email' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

    /**
     * Ensure the admin login is not rate limited.
     *
     * @throws ValidationException
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        $key = $this->throttleKey($request);

        if (! RateLimiter::tooManyAttempts($key, $this->maxAttempts())) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($key);

        throw ValidationException::withMessages([
            'email' => __('Terlalu banyak percobaan login. Coba lagi dalam :seconds detik.', [
                'seconds' => $seconds,
            ]),
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return Str::lower((string) $request->input('email')).'|'.$request->ip();
    }

    protected function maxAttempts(): int
    {
        return 5;
    }

    protected function decaySeconds(): int
    {
        return 5 * 60;
    }
}
