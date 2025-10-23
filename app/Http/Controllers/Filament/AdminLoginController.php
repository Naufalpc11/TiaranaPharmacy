<?php

namespace App\Http\Controllers\Filament;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Http\Request;
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

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        $remember = (bool) ($validated['remember'] ?? false);

        if (! Filament::auth()->attempt($credentials, $remember)) {
            $this->throwFailedValidation();
        }

        $user = Filament::auth()->user();

        if (($user instanceof FilamentUser) && (! $user->canAccessPanel(Filament::getCurrentPanel()))) {
            Filament::auth()->logout();
            $this->throwFailedValidation();
        }

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
}
