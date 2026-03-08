<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'HANZO') | {{ config('app.name', 'HANZO') }}</title>
  <link rel="icon" type="image/png" href="{{ asset('assets/hanzo/logo.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/fonts/boxicons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/css/core.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/css/theme-default.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/hanzo/hanzo-theme.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/hanzo/hanzo.css') }}">
  @stack('styles')
</head>
<body class="hanzo-public">
  @include('components.navbar-public')
  <main>
    @yield('content')
  </main>
  @include('components.footer-public')
  <script src="{{ asset('assets/sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('assets/sneat/assets/vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ asset('assets/sneat/assets/vendor/js/bootstrap.js') }}"></script>
  @stack('scripts')
</body>
</html>
