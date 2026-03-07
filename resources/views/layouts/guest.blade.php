<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name')) | HANZO</title>
        <link rel="icon" type="image/png" href="{{ asset('assets/hanzo/logo.png') }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/fonts/boxicons.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/css/core.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/css/theme-default.css') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('assets/hanzo/hanzo-theme.css') }}">
        <style>
            .hanzo-auth { background: linear-gradient(135deg, #0f172a 0%, #134e4a 50%, #0d9488 100%); min-height: 100vh; }
            .hanzo-logo { font-size: 1.75rem; font-weight: 700; color: #fff; letter-spacing: 0.1em; }
            .hanzo-card { background: rgba(255,255,255,0.98); border: 1px solid rgba(13,148,136,0.2); border-radius: 12px; }
            .btn-hanzo { background: #0d9488; color: #fff; border: none; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-radius: 0.5rem; }
            .btn-hanzo:hover { background: #0f766e; color: #fff; }
            .text-hanzo { color: #14b8a6; }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen hanzo-auth flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <a href="{{ url('/') }}" class="hanzo-logo text-decoration-none mb-4 d-flex flex-column align-items-center gap-2">
                <img src="{{ asset('assets/hanzo/logo.png') }}" alt="HANZO" style="height: 52px; width: auto;">
                <span>HANZO</span>
            </a>
            <div class="w-full sm:max-w-md px-6">
                <div class="hanzo-card shadow-lg p-6">
                    {{ $slot }}
                </div>
                <p class="text-center mt-4">
                    <a href="{{ url('/') }}" class="text-white-50 small">← Back to HANZO</a>
                </p>
            </div>
        </div>
    </body>
</html>
