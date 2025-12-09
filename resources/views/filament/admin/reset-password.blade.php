<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | Admin</title>
    <style>
        body {margin:0;font-family:'Segoe UI',system-ui,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;background:#f5f6fa;color:#111827;}
        .card {width:min(420px,92vw);background:#fff;padding:28px;border-radius:16px;box-shadow:0 18px 40px rgba(0,0,0,0.08);} 
        h1 {margin:0 0 10px;font-size:1.6rem;}
        p {margin:0 0 12px;color:#4b5563;}
        label {display:block;margin-bottom:6px;font-weight:600;color:#374151;}
        input {width:100%;padding:12px 14px;border:1px solid #d1d5db;border-radius:10px;font-size:0.95rem;}
        input:focus {outline:none;border-color:#f97316;box-shadow:0 0 0 4px rgba(249,115,22,0.15);}
        button {width:100%;padding:12px 14px;border:none;border-radius:10px;background:linear-gradient(135deg,#f97316,#fb923c);color:#fff;font-weight:600;font-size:1rem;cursor:pointer;}
        .errors {background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:12px;padding:12px 14px;margin-bottom:16px;font-size:0.9rem;}
        .status {background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;border-radius:12px;padding:12px 14px;margin-bottom:16px;font-size:0.9rem;}
        a {color:#f97316;text-decoration:none;font-weight:600;}
    </style>
</head>
<body>
    <main class="card">
        <h1>Reset Password</h1>
        <p>Link berlaku 1 jam. Jika kadaluarsa, minta link baru.</p>

        @if ($errors->any())
            <div class="errors">{{ $errors->first() }}</div>
        @endif

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.reset') }}">
            @csrf
            <input type="hidden" name="token" value="{{ request('token') }}">

            <label for="email">Email admin</label>
            <input type="email" name="email" id="email" value="{{ old('email', 'tiaranafarma@gmail.com') }}" required autocomplete="email">

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

        <div style="margin-top:18px; text-align:center;">
            <a href="/admin/login">← Kembali ke Login</a>
        </div>
    </main>
</body>
</html>
