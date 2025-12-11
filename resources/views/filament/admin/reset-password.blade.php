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
        button {width:100%;padding:12px 14px;border:none;border-radius:12px;background:linear-gradient(135deg,#f97316,#fb923c);color:#fff;font-weight:700;font-size:1rem;cursor:pointer;}
        .errors {background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:12px;padding:12px 14px;margin-bottom:16px;font-size:0.92rem;}
        .status {background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;border-radius:12px;padding:12px 14px;margin-bottom:16px;font-size:0.92rem;}
        .muted {color:#94a3b8;font-size:0.9rem;margin-top:14px;text-align:center;}
        .link {color:#f97316;text-decoration:none;font-weight:600;}
        .toggle-visibility {position:absolute; right:10px; top:70%; transform:translateY(-50%); border:none; background:transparent; cursor:pointer; font-size:1rem; line-height:1; width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center; color:#6b7280; padding:0; border-radius:50%;}
        .toggle-visibility:focus-visible {outline: 2px solid #f97316; outline-offset: 2px; border-radius: 50%;}
        .input-wrapper { position:relative; }
    </style>
</head>
<body>
    <main class="card">
        <h1>Reset Password</h1>
        <p id="reset-subtitle">Link berlaku 1 jam. Jika kadaluarsa, minta link baru.</p>

        @if ($errors->any())
            <div class="errors">{{ $errors->first() }}</div>
        @endif

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.reset') }}">
            @csrf
            <input type="hidden" name="token" id="token" value="{{ $token ?? request('token') }}">
            <input type="hidden" name="email" id="email" value="{{ old('email', $email ?? request('email')) }}">

            <div style="margin-top:8px; font-size:0.95rem; color:#475569;">
                Reset untuk: <strong>{{ $email ?? request('email') }}</strong>
            </div>

            <div class="input-wrapper" style="margin-top:14px;">
                <label for="password">Password baru</label>
                <input type="password" name="password" id="password" required minlength="8" autocomplete="new-password" style="padding-right:46px;">
                <button type="button" class="toggle-visibility" data-target="password" aria-label="Tampilkan/sembunyikan password" data-open="👁" data-closed="👁‍🗨">👁</button>
            </div>

            <div class="input-wrapper" style="margin-top:14px;">
                <label for="password_confirmation">Konfirmasi password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8" autocomplete="new-password" style="padding-right:46px;">
                <button type="button" class="toggle-visibility" data-target="password_confirmation" aria-label="Tampilkan/sembunyikan konfirmasi" data-open="👁" data-closed="👁‍🗨">👁</button>
            </div>

            <div style="margin-top:18px;">
                <button type="submit">Simpan Password Baru</button>
            </div>
        </form>

        <div id="back-to-login" style="margin-top:18px; text-align:center;">
            <a href="/admin/login" class="link">Kembali ke Login</a>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.toggle-visibility').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const targetId = btn.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    if (!input) return;

                    const isPassword = input.getAttribute('type') === 'password';
                    input.setAttribute('type', isPassword ? 'text' : 'password');
                    const openIcon = btn.getAttribute('data-open') || '👁';
                    const closedIcon = btn.getAttribute('data-closed') || '👁‍🗨';
                    btn.textContent = isPassword ? closedIcon : openIcon;
                });
            });
        });
    </script>
</body>
</html>
