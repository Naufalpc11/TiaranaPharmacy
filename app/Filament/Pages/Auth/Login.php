<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    /**
     * Arahkan tombol "Forgot password?" ke halaman kustom tanpa input email.
     */
    public function getPasswordResetUrl(): ?string
    {
        return route('password.forgot.form');
    }
}
