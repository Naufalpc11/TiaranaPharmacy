<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | Admin</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body {margin:0;font-family:'Inter', system-ui, -apple-system, sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;background:#f8fafc;color:#0f172a;padding:16px;}
        .card {width:min(480px,100%);background:#fff;border-radius:18px;box-shadow:0 20px 50px rgba(15,23,42,0.12);padding:28px;}
        h1 {margin:0 0 8px;font-size:1.8rem;font-weight:700;color:#0f172a;}
        p {margin:0 0 16px;color:#475569;}
        label {display:block;margin-bottom:6px;font-weight:600;color:#1e293b;}
        input {width:100%;padding:12px 14px;border:1px solid #e2e8f0;border-radius:12px;font-size:0.95rem;background:#f8fafc;transition:border-color .15s, box-shadow .15s;}
        input:focus {outline:none;border-color:#f97316;box-shadow:0 0 0 4px rgba(249,115,22,0.18);background:#fff;}
        button {width:100%;padding:12px 14px;border:none;border-radius:12px;background:linear-gradient(135deg,#f97316,#fb923c);color:#fff;font-weight:700;font-size:1rem;cursor:pointer;transition:transform .12s, box-shadow .12s;}
        button:hover {transform:translateY(-1px);box-shadow:0 12px 22px rgba(249,115,22,0.22);}
        .errors {background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:12px;padding:12px 14px;margin-bottom:16px;font-size:0.92rem;}
        .status {background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;border-radius:12px;padding:12px 14px;margin-bottom:16px;font-size:0.92rem;}
        .muted {color:#94a3b8;font-size:0.9rem;margin-top:14px;text-align:center;}
        .link {color:#f97316;text-decoration:none;font-weight:600;}
    </style>
</head>
<body>
    <main class="card">
        <h1>Reset Password</h1>
        <p id="reset-subtitle">Link berlaku 1 jam. Jika kadaluarsa, minta link baru.</p>

        <div id="expired-alert" class="errors" style="display:none;">
            Link reset sudah tidak valid atau sudah kedaluwarsa. Silakan minta link baru lewat halaman lupa password.
            <div style="margin-top:12px;">
                <a href="{{ route('password.forgot.form') }}">Minta link reset baru</a>
            </div>
        </div>

        @if ($errors->any())
            <div class="errors">{{ $errors->first() }}</div>
        @endif

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.reset') }}">
            @csrf
            <input type="hidden" name="token" id="token" value="{{ request('token') }}">
            <input type="hidden" name="email" id="email" value="{{ old('email') }}">

            <div style="margin-top:14px;">
                <label for="password">Password baru</label>
                <input type="password" name="password" id="password" required minlength="8" autocomplete="new-password">
            </div>

            <div style="margin-top:14px;">
                <label for="password_confirmation">Konfirmasi password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8" autocomplete="new-password">
            </div>

            <div style="margin-top:18px;">
                <button type="submit">Simpan Password Baru</button>
            </div>
        </form>

        <div id="back-to-login" style="margin-top:18px; text-align:center; display:none;">
            <a href="/admin/login" class="link">Kembali ke Login</a>
        </div>
    </main>

    <script>
        (function() {
            const tokenInput = document.getElementById('token');
            const emailInput = document.getElementById('email');
            const supabaseUrl = @json(config('services.supabase.url'));
            const supabaseAnonKey = @json(config('services.supabase.anon_key'));
            const form = document.querySelector('form');
            const expiredAlert = document.getElementById('expired-alert');
            const subtitle = document.getElementById('reset-subtitle');
            const backToLogin = document.getElementById('back-to-login');

            const hashParams = new URLSearchParams(window.location.hash.replace(/^#/, ''));
            const queryParams = new URLSearchParams(window.location.search);
            const storedToken = sessionStorage.getItem('reset_access_token') || '';
            const storedEmail = sessionStorage.getItem('reset_email') || '';
            const accessToken = hashParams.get('access_token') || queryParams.get('token') || tokenInput.value || storedToken;
            const errorCode = hashParams.get('error') || queryParams.get('error');
            const errorDescription = hashParams.get('error_description') || queryParams.get('error_description');

            // Jika Supabase mengirim error (mis. otp_expired atau link invalid)
            if (errorCode || errorDescription) {
                if (expiredAlert) {
                    expiredAlert.style.display = 'block';
                }
                if (form) {
                    form.style.display = 'none';
                }
                if (subtitle) {
                    subtitle.textContent = 'Link reset tidak valid atau sudah kedaluwarsa.';
                }
                return;
            }

            if (accessToken) {
                tokenInput.value = accessToken;
                sessionStorage.setItem('reset_access_token', accessToken);

                // Ambil email dari Supabase berdasarkan token
                if (supabaseUrl && supabaseAnonKey && emailInput && !emailInput.value) {
                    fetch(`${supabaseUrl}/auth/v1/user`, {
                        headers: {
                            'apikey': supabaseAnonKey,
                            'Authorization': `Bearer ${accessToken}`,
                        },
                    })
                        .then(async (res) => {
                            if (!res.ok) return;
                            const data = await res.json();
                            if (data?.email) {
                                emailInput.value = data.email;
                                sessionStorage.setItem('reset_email', data.email);
                            }
                        })
                        .catch(() => {});
                } else if (emailInput && !emailInput.value && storedEmail) {
                    emailInput.value = storedEmail;
                }
            } else {
                const statusBox = document.createElement('div');
                statusBox.className = 'errors';
                statusBox.textContent = 'Token reset tidak ditemukan. Coba klik ulang link pada email.';
                document.querySelector('main.card').insertBefore(statusBox, document.querySelector('form'));
            }

            // Tampilkan tombol kembali setelah submit berhasil (dari session status)
            @if (session('status'))
                if (backToLogin) backToLogin.style.display = 'block';
            @endif
        })();
    </script>
</body>
</html>
