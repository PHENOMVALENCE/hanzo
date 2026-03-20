<!DOCTYPE html>
<html lang="{{ str_replace('zh', 'zh-CN', app()->getLocale()) }}" class="light-style" dir="ltr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <title>@yield('title', 'Dashboard') | HANZO</title>
  <meta name="description" content="HANZO B2B Trade Platform" />

  <link rel="icon" type="image/png" href="{{ asset('assets/hanzo/logo.png') }}" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

  <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/fonts/boxicons.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/css/core.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/css/theme-default.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/hanzo/hanzo-theme.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/hanzo/buyer-modern.css') }}" />
  @yield('vendor-css')
  @stack('page-css')

  <script src="{{ asset('assets/sneat/assets/vendor/js/helpers.js') }}"></script>
  <script src="{{ asset('assets/sneat/assets/js/config.js') }}"></script>
</head>
<body class="buyer-modern">
  <div class="buyer-modern-wrapper">
    @include('partials.buyer-sidebar-modern')

    <div class="buyer-modern-main">
      @include('partials.buyer-header-modern')

      @hasSection('promo-banner')
        @yield('promo-banner')
      @endif

      <main class="buyer-modern-content">
        @yield('content')
      </main>

      <footer class="buyer-modern-footer">
        © <script>document.write(new Date().getFullYear());</script>
        <img src="{{ asset('assets/hanzo/logo.png') }}" alt="HANZO" style="height: 18px; vertical-align: middle; margin: 0 0.25rem;"> HANZO
      </footer>
    </div>
  </div>

  @include('partials.buyer-floating-nav')

  <script src="{{ asset('assets/sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('assets/sneat/assets/vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ asset('assets/sneat/assets/vendor/js/bootstrap.js') }}"></script>
  <script>
    document.getElementById('buyer-scroll-top')?.addEventListener('click', function(e) {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    document.querySelectorAll('.buyer-sidebar-toggle').forEach(function(btn) {
      btn.addEventListener('click', function() { document.body.classList.toggle('buyer-sidebar-open'); });
    });
    document.querySelector('.buyer-sidebar-overlay')?.addEventListener('click', function() {
      document.body.classList.remove('buyer-sidebar-open');
    });
  </script>
  @stack('page-js')
  @yield('page-js')
</body>
</html>
