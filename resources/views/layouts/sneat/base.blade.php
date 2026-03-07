<!DOCTYPE html>
<!-- beautify ignore:start -->
<html
  lang="en"
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
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
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

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    @yield('vendor-css')

    <!-- Page CSS -->
    @yield('page-css')

    <!-- Helpers -->
    <script src="{{ asset('assets/sneat/assets/vendor/js/helpers.js') }}"></script>

    <!-- Config -->
    <script src="{{ asset('assets/sneat/assets/js/config.js') }}"></script>
  </head>

  <body class="@if(auth()->user()) hanzo-role-{{ auth()->user()->getRoleNames()->first() ?? 'buyer' }} @endif">
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
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
          @include('partials.topbar')
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
      function toggleSidebar() {
        var html = document.documentElement;
        var menu = document.getElementById('layout-menu');
        html.classList.toggle('layout-menu-collapsed');
        if (menu) menu.classList.toggle('menu-collapsed');
        window.dispatchEvent(new Event('resize'));
      }
      document.querySelectorAll('.layout-menu-toggle').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          toggleSidebar();
        });
      });
    });
    </script>

    <!-- Page JS -->
    @stack('page-js')
    @yield('page-js')
  </body>
</html>
