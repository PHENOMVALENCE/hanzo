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
  <link rel="stylesheet" href="{{ asset('assets/hanzo/buyer-alibaba.css') }}" />
  @yield('vendor-css')
  @stack('page-css')

  <script src="{{ asset('assets/sneat/assets/vendor/js/helpers.js') }}"></script>
  <script src="{{ asset('assets/sneat/assets/js/config.js') }}"></script>
</head>
<body class="buyer-alibaba layout-content-navbar">
  <div class="layout-wrapper">
    <div class="layout-container" style="min-height: 100vh; display: flex; flex-direction: column;">
      @include('partials.buyer-header-alibaba')

      @hasSection('promo-banner')
        @yield('promo-banner')
      @endif

      <main class="buyer-content flex-grow-1">
        @yield('content')
      </main>

      <footer class="content-footer footer" style="background: var(--hanzo-white); border-top: 1px solid var(--hanzo-border); padding: 1rem 1.5rem;">
        <div class="d-flex flex-wrap justify-content-between py-2" style="max-width: 1400px; margin: 0 auto;">
          <div>© <script>document.write(new Date().getFullYear());</script> <img src="{{ asset('assets/hanzo/logo.png') }}" alt="HANZO" style="height: 20px; width: auto; vertical-align: middle;"> HANZO</div>
        </div>
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
  </script>
  @stack('page-js')
  @yield('page-js')
</body>
</html>
