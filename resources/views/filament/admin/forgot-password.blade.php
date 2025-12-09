<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password | Admin</title>
    <style>
        body {margin:0;font-family:'Segoe UI',system-ui,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;background:#f5f6fa;color:#111827;}
        .card {width:min(420px,92vw);background:#fff;padding:28px;border-radius:16px;box-shadow:0 18px 40px rgba(0,0,0,0.08);}
        h1 {margin:0 0 10px;font-size:1.6rem;}
        p {margin:0 0 18px;color:#4b5563;}
        button {width:100%;padding:12px 14px;border:none;border-radius:10px;background:linear-gradient(135deg,#f97316,#fb923c);color:#fff;font-weight:600;font-size:1rem;cursor:pointer;}
        .status {background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;border-radius:12px;padding:12px 14px;margin-bottom:16px;font-size:0.9rem;}
        .errors {background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:12px;padding:12px 14px;margin-bottom:16px;font-size:0.9rem;}
        a {color:#f97316;text-decoration:none;font-weight:600;}
    </style>
</head>
<body>
    @php $targetEmail = 'tiaranafarma@gmail.com'; @endphp
    <main class="card">
        <h1>Lupa Password</h1>
        <p>Kami akan kirim tautan reset ke email admin berikut:</p>
        <p style="font-weight:600;color:#111827;">{{ $targetEmail }}</p>

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="errors">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.forgot') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $targetEmail }}">
            <div style="margin-top:4px;">
                <button type="submit">Kirim Email Verifikasi</button>
            </div>
        </form>

        <div style="margin-top:18px; text-align:center;">
            <a href="/admin/login">Kembali ke Login</a>
        </div>
    </main>
</body>
</html>
