<header class="buyer-header">
  <div class="buyer-header-top">
    <a href="{{ route('buyer.dashboard') }}" class="buyer-header-logo">
      <img src="{{ asset('assets/hanzo/logo.png') }}" alt="HANZO">
      <span>HANZO</span>
    </a>

    <form action="{{ route('buyer.products.index') }}" method="GET" class="buyer-header-search">
      <span class="search-camera" title="{{ __('labels.search') }}"><i class="bx bx-camera"></i></span>
      <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('labels.search_products') ?? 'Search products...' }}" aria-label="Search">
      <button type="submit" class="search-btn">
        <i class="bx bx-search"></i> {{ __('labels.search') ?? 'Search' }}
      </button>
    </form>

    <div class="buyer-header-tools">
      <div class="dropdown">
        <a class="dropdown-toggle d-flex align-items-center gap-1 text-decoration-none" href="#" data-bs-toggle="dropdown">
          <i class="bx bx-map-pin"></i>
          <span class="d-none d-md-inline">{{ __('labels.deliver_to') ?? 'Deliver to' }}</span>
          <span class="small text-muted"> {{ config('currencies.names')[session('currency', 'USD')] ?? 'USD' }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><form method="POST" action="{{ route('currency.switch') }}">@csrf<input type="hidden" name="currency" value="USD"><button type="submit" class="dropdown-item {{ (session('currency', 'USD')) === 'USD' ? 'active' : '' }}">{{ config('currencies.names.USD') }}</button></form></li>
          <li><form method="POST" action="{{ route('currency.switch') }}">@csrf<input type="hidden" name="currency" value="CNY"><button type="submit" class="dropdown-item {{ (session('currency', 'USD')) === 'CNY' ? 'active' : '' }}">{{ config('currencies.names.CNY') }}</button></form></li>
          <li><form method="POST" action="{{ route('currency.switch') }}">@csrf<input type="hidden" name="currency" value="TZS"><button type="submit" class="dropdown-item {{ (session('currency', 'USD')) === 'TZS' ? 'active' : '' }}">{{ config('currencies.names.TZS') }}</button></form></li>
        </ul>
      </div>
      <div class="dropdown">
        <a class="dropdown-toggle d-flex align-items-center gap-1 text-decoration-none" href="#" data-bs-toggle="dropdown">
          <i class="bx bx-globe"></i>
          <span class="d-none d-md-inline">{{ app()->getLocale() === 'en' ? 'English' : (app()->getLocale() === 'sw' ? 'Kiswahili' : 'Chinese') }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><form method="POST" action="{{ route('locale.switch') }}">@csrf<input type="hidden" name="locale" value="en"><button type="submit" class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}">English</button></form></li>
          <li><form method="POST" action="{{ route('locale.switch') }}">@csrf<input type="hidden" name="locale" value="sw"><button type="submit" class="dropdown-item {{ app()->getLocale() === 'sw' ? 'active' : '' }}">Kiswahili</button></form></li>
          <li><form method="POST" action="{{ route('locale.switch') }}">@csrf<input type="hidden" name="locale" value="zh"><button type="submit" class="dropdown-item {{ app()->getLocale() === 'zh' ? 'active' : '' }}">Chinese</button></form></li>
        </ul>
      </div>
      <a href="{{ route('notifications.index') }}" class="nav-link position-relative d-flex align-items-center" title="{{ __('labels.notifications') ?? 'Notifications' }}">
        <i class="bx bx-bell" style="font-size: 1.25rem;"></i>
        @if(($unreadCount = auth()->user()->unreadNotifications->count() ?? 0) > 0)
          <span class="badge rounded-pill bg-danger position-absolute top-0 end-0 translate-middle" style="font-size: 0.6rem;">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
        @endif
      </a>
      <div class="dropdown">
        <a class="dropdown-toggle d-flex align-items-center gap-2 text-decoration-none" href="#" data-bs-toggle="dropdown">
          @if(auth()->user()->avatarUrl())
            <img src="{{ auth()->user()->avatarUrl() }}" alt="" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
          @else
            <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 600;">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
          @endif
          <span class="d-none d-lg-inline">{{ auth()->user()->name ?? 'Account' }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow">
          <li class="dropdown-header">
            <span class="fw-semibold">{{ auth()->user()->name ?? 'User' }}</span>
            <small class="d-block text-muted">{{ auth()->user()->email ?? '' }}</small>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="{{ route('buyer.dashboard') }}"><i class="bx bx-home me-2"></i>{{ __('menu.dashboard') }}</a></li>
          <li><a class="dropdown-item" href="{{ route('buyer.products.index') }}"><i class="bx bx-store me-2"></i>{{ __('labels.products') }}</a></li>
          <li><a class="dropdown-item" href="{{ route('buyer.rfqs.index') }}"><i class="bx bx-file me-2"></i>{{ __('menu.product_requests') }}</a></li>
          <li><a class="dropdown-item" href="{{ route('buyer.quotes.index') }}"><i class="bx bx-list-check me-2"></i>{{ __('menu.quotes') }}</a></li>
          <li><a class="dropdown-item" href="{{ route('buyer.orders.index') }}"><i class="bx bx-package me-2"></i>{{ __('menu.orders') }}</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bx bx-user me-2"></i>{{ __('profile.dropdown_profile') }}</a></li>
          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="dropdown-item border-0 bg-transparent w-100 text-start"><i class="bx bx-power-off me-2"></i>{{ __('profile.log_out') }}</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <nav class="buyer-subnav">
    <div class="buyer-subnav-inner">
      <div class="buyer-subnav-left">
        <a href="{{ route('buyer.products.index') }}"><i class="bx bx-menu me-1"></i> {{ __('labels.all_categories') ?? 'All categories' }}</a>
        <a href="{{ route('how-it-works') }}">{{ __('labels.find_factories') ?? 'Find factories' }}</a>
        <a href="{{ route('buyer.orders.index') }}">{{ __('labels.order_protections') ?? 'Order protections' }}</a>
      </div>
      <div class="buyer-subnav-right">
        <a href="{{ route('buyer.dashboard') }}">{{ __('labels.buyer_central') ?? 'Buyer Central' }}</a>
        <a href="{{ route('about') }}#contact">{{ __('labels.help_center') ?? 'Help Center' }}</a>
      </div>
    </div>
  </nav>
</header>
