<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Kirim link reset password via email Laravel (tanpa Supabase)
     */
    public function forgot(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User tidak ditemukan']);
        }

        // Buat URL reset menggunakan route kita sendiri
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return url(route('password.reset.form', ['token' => $token, 'email' => $user->email], false));
        });

        $status = Password::sendResetLink(['email' => $user->email]);

        // Kembalikan generator URL ke default
        ResetPassword::createUrlUsing(null);

        if ($status !== Password::RESET_LINK_SENT) {
            return back()->withErrors(['email' => __($status)]);
        }

        return back()->with('status', 'Link reset password telah dikirim ke email Anda. Cek inbox atau folder spam.');
    }

    /**
     * Reset password menggunakan token Laravel (tanpa Supabase)
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => __($status)]);
        }

        return redirect('/admin/login')->with('status', 'Password berhasil direset. Silakan login dengan password baru.');
    }
}
