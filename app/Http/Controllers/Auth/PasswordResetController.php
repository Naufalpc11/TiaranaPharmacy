<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    protected SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    /**
     * Handle forgot password request via Supabase
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

        // Send reset email via Supabase Auth
        $result = $this->supabase->sendPasswordResetEmail($user->email);

        if (!$result['success']) {
            \Log::error('Supabase reset password error: ' . $result['message']);
            return back()->withErrors(['email' => 'Gagal mengirim email. Hubungi administrator.']);
        }

        return back()->with('status', 'Link reset password telah dikirim ke email Anda. Cek inbox atau folder spam.');
    }

    /**
     * Handle password reset via Supabase
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Verify token and reset password via Supabase
        $result = $this->supabase->resetPassword(
            $request->email,
            $request->token,
            $request->password
        );

        if (!$result['success']) {
            \Log::error('Reset password failed: ' . $result['message']);
            return back()->withErrors(['token' => $result['message']]);
        }

        // Also update local database password for sync
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect('/admin/login')->with('status', 'Password berhasil direset. Silakan login dengan password baru.');
    }
}

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('filament.admin.auth.login')
            ->with('status', 'Password berhasil direset. Silakan login dengan password baru.');
    }
}
