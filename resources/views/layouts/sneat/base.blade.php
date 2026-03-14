<!DOCTYPE html>
<!-- beautify ignore:start -->
<html
  lang="{{ str_replace('zh', 'zh-CN', app()->getLocale()) }}"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('assets/sneat/assets/') }}"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, viewport-fit=cover"
    />

    <title>@yield('title', 'Dashboard') | HANZO</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/hanzo/logo.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@500;600;700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/sneat/assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/hanzo/hanzo-theme.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/hanzo/hanzo.css') }}" />
    @stack('layout-css')

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    @yield('vendor-css')

    <!-- Page CSS -->
    @yield('page-css')
    @stack('page-css')

    <!-- Helpers -->
    <script src="{{ asset('assets/sneat/assets/vendor/js/helpers.js') }}"></script>

    <!-- Config -->
    <script src="{{ asset('assets/sneat/assets/js/config.js') }}"></script>
  </head>

  <body class="@if(auth()->user()) hanzo-role-{{ auth()->user()->getRoleNames()->first() ?? 'buyer' }} @endif">
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container{{ request()->routeIs('buyer.*') ? ' hanzo-buyer-b2b' : '' }}{{ request()->routeIs('factory.*') ? ' hanzo-factory-b2b' : '' }}{{ request()->routeIs('admin.*') ? ' hanzo-admin-mc' : '' }}">
        <!-- Menu -->
        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          @hasSection('sidebar')
            @yield('sidebar')
          @else
            @include('partials.sidebar-admin')
          @endif
        </aside>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          @hasSection('navbar')
            @yield('navbar')
          @else
            @include('partials.topbar')
          @endif
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              @yield('content')
            </div>
            <!-- / Content -->

            <!-- Footer -->
            @include('partials.footer')
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="{{ asset('assets/sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/sneat/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/sneat/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/sneat/assets/vendor/js/menu.js') }}"></script>

    <!-- Vendors JS -->
    @yield('vendor-js')

    <!-- Main JS -->
    <script src="{{ asset('assets/sneat/assets/js/main.js') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
      // On mobile: close sidebar when a menu link is clicked (so overlay closes after navigation)
      function closeMenuOnSmallScreen() {
        try {
          if (typeof window.Helpers !== 'undefined') {
            if (window.Helpers.isSmallScreen && window.Helpers.isSmallScreen()) {
              if (window.Helpers.setCollapsed) {
                window.Helpers.setCollapsed(true);
              } else {
                document.documentElement.classList.remove('layout-menu-expanded');
              }
            }
          } else {
            if (window.innerWidth < 1200) document.documentElement.classList.remove('layout-menu-expanded');
          }
        } catch (e) {}
      }
      document.querySelectorAll('.layout-menu .menu-link').forEach(function(link) {
        link.addEventListener('click', function() {
          setTimeout(closeMenuOnSmallScreen, 150);
        });
      });
    });
    </script>

    <!-- Notification count poll (updates badge) -->
    @auth
    <script>
    (function() {
      var badge = document.getElementById('notification-badge');
      if (!badge) return;
      function fetchCount() {
        fetch('{{ route("notifications.count") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' })
          .then(function(r) { return r.json(); })
          .then(function(d) {
            var total = d.total || 0;
            badge.textContent = total > 9 ? '9+' : String(total);
            badge.classList.toggle('d-none', total === 0);
          })
          .catch(function() {});
      }
      fetchCount();
      setInterval(fetchCount, 60000);
    })();
    </script>
    @endauth

    <!-- Page JS -->
    @stack('page-js')
    @yield('page-js')
  </body>
</html>
