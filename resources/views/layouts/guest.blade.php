<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name')) | HANZO</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/fonts/boxicons.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/css/core.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/css/theme-default.css') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            :root { --hanzo-navy: #0a1628; --hanzo-gold: #d4af37; --hanzo-gold-light: #e8c547; }
            .hanzo-auth { background: linear-gradient(135deg, #0a1628 0%, #132942 100%); min-height: 100vh; }
            .hanzo-logo { font-size: 1.75rem; font-weight: 700; color: var(--hanzo-gold); letter-spacing: 0.1em; }
            .hanzo-card { background: rgba(255,255,255,0.98); border: 1px solid rgba(212,175,55,0.3); border-radius: 0.5rem; }
            .btn-hanzo { background: var(--hanzo-gold); color: var(--hanzo-navy); border: none; font-weight: 600; }
            .btn-hanzo:hover { background: var(--hanzo-gold-light); color: var(--hanzo-navy); }
            .text-hanzo { color: var(--hanzo-gold); }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen hanzo-auth flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <a href="{{ url('/') }}" class="hanzo-logo text-decoration-none mb-4">HANZO</a>
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
