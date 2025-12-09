<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} &mdash; Admin Login</title>
    <style>
        :root {
            color-scheme: light dark;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f6fa;
            color: #1f2937;
        }

        .card {
            width: min(400px, 92vw);
            background: #ffffff;
            padding: 32px;
            border-radius: 16px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.12);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 1.75rem;
            text-align: center;
            color: #111827;
        }

        p.description {
            margin: 0 0 24px;
            text-align: center;
            font-size: 0.95rem;
            color: #6b7280;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            color: #374151;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-size: 0.95rem;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.15);
        }

        .field {
            margin-bottom: 18px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            font-size: 0.9rem;
            color: #374151;
        }

        .errors {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .status {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        button {
            width: 100%;
            border: none;
            padding: 12px 14px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: #ffffff;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 20px rgba(249, 115, 22, 0.22);
        }

        button:active {
            transform: translateY(0);
            box-shadow: 0 6px 14px rgba(249, 115, 22, 0.18);
        }
    </style>
</head>
<body>
<main class="card">
    <h1>Admin Panel</h1>
    <p class="description">Masuk dengan akun admin untuk mengelola konten.</p>

    @if (session('status'))
        <div class="status">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="errors">
            {{ __('Email atau kata sandi tidak cocok.') }}
            <div style="margin-top:10px;">
                <a href="{{ route('password.forgot.form') }}" style="color:#f97316;font-weight:600;text-decoration:none;">Reset password via email</a>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('filament.admin.auth.login.store') }}">
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email') }}"
                required
                autofocus
            >
        </div>

        <div class="field">
            <label for="password">Kata sandi</label>
            <input
                type="password"
                name="password"
                id="password"
                required
                autocomplete="current-password"
            >
        </div>

        <label class="remember">
            <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
            Ingat saya
        </label>

        <button type="submit">Masuk</button>
    </form>

    <div id="forgot-password-link" style="margin-top:20px; padding-top:20px; border-top:1px solid #e5e7eb; text-align:center;">
        <a href="/auth/forgot-password" style="display:inline-block; color:#f97316; background:#fff7ed; padding:8px 16px; border-radius:8px; font-weight:600; text-decoration:none; font-size:0.9rem; border:1px solid #fed7aa;">
            🔑 Lupa password?
        </a>
    </div>
</main>

<script>
    // Ensure link is visible
    document.addEventListener('DOMContentLoaded', function() {
        const link = document.getElementById('forgot-password-link');
        if (link) {
            link.style.display = 'block';
            link.style.visibility = 'visible';
            link.style.opacity = '1';
            console.log('Forgot password link loaded');
        }
    });
</script>

</body>
</html>
