@php
$user = filament()->auth()->user();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }} - Admin Login</title>
    @filamentStyles
    <style>
        .forgot-link {
            text-align: center;
            margin-top: 18px;
        }
        .forgot-link a {
            color: #f97316;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .forgot-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh;">
        <div style="width: 100%; max-width: 420px;">
            {{ $this->form }}
            
            <div class="forgot-link">
                <a href="{{ route('password.forgot.form') }}">Lupa password?</a>
            </div>
        </div>
    </div>

    @filamentScripts
</body>
</html>
