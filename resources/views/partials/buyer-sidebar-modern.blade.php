<aside class="buyer-sidebar">
  <div class="buyer-sidebar-header">
    <a href="{{ route('buyer.dashboard') }}" class="buyer-sidebar-logo">
      <img src="{{ asset('assets/hanzo/logo.png') }}" alt="HANZO">
      <span>HANZO</span>
    </a>
    <button type="button" class="buyer-sidebar-toggle d-lg-none" aria-label="Toggle menu">
      <i class="bx bx-x"></i>
    </button>
  </div>

  <nav class="buyer-sidebar-nav">
    <a href="{{ route('buyer.dashboard') }}" class="buyer-sidebar-link {{ request()->routeIs('buyer.dashboard') ? 'active' : '' }}">
      <i class="bx bx-home-alt"></i>
      <span>{{ __('menu.dashboard') }}</span>
    </a>
    <a href="{{ route('buyer.products.index') }}" class="buyer-sidebar-link {{ request()->routeIs('buyer.products.*') ? 'active' : '' }}">
      <i class="bx bx-package"></i>
      <span>{{ __('labels.products') }}</span>
    </a>
    <a href="{{ route('buyer.rfqs.index') }}" class="buyer-sidebar-link {{ request()->routeIs('buyer.rfqs.*') ? 'active' : '' }}">
      <i class="bx bx-file-blank"></i>
      <span>{{ __('menu.product_requests') }}</span>
    </a>
    <a href="{{ route('buyer.quotes.index') }}" class="buyer-sidebar-link {{ request()->routeIs('buyer.quotes.*') ? 'active' : '' }}">
      <i class="bx bx-list-check"></i>
      <span>{{ __('menu.quotes') }}</span>
    </a>
    <a href="{{ route('buyer.orders.index') }}" class="buyer-sidebar-link {{ request()->routeIs('buyer.orders.*') ? 'active' : '' }}">
      <i class="bx bx-cart"></i>
      <span>{{ __('menu.orders') }}</span>
    </a>
    <hr class="buyer-sidebar-divider">
    <a href="{{ route('how-it-works') }}" class="buyer-sidebar-link">
      <i class="bx bx-buildings"></i>
      <span>{{ __('labels.find_factories') }}</span>
    </a>
    <a href="{{ route('about') }}#contact" class="buyer-sidebar-link">
      <i class="bx bx-help-circle"></i>
      <span>{{ __('labels.help_center') }}</span>
    </a>
  </nav>

  <div class="buyer-sidebar-footer">
    <a href="{{ route('profile.edit') }}" class="buyer-sidebar-link small">
      <i class="bx bx-user"></i>
      <span>{{ __('profile.dropdown_profile') }}</span>
    </a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="buyer-sidebar-link small border-0 bg-transparent w-100 text-start">
        <i class="bx bx-log-out"></i>
        <span>{{ __('profile.log_out') }}</span>
      </button>
    </form>
  </div>
</aside>

<div class="buyer-sidebar-overlay" aria-hidden="true"></div>
