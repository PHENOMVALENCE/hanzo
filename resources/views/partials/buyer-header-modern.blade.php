<header class="buyer-modern-header">
  <button type="button" class="buyer-sidebar-toggle d-lg-none" aria-label="Open menu">
    <i class="bx bx-menu"></i>
  </button>
  <form action="{{ route('buyer.products.index') }}" method="GET" class="buyer-modern-search">
    <i class="bx bx-search"></i>
    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('labels.search_products') }}" aria-label="Search">
  </form>

  <div class="buyer-modern-header-actions">
    <div class="dropdown">
      <a class="dropdown-toggle buyer-modern-header-btn" href="#" data-bs-toggle="dropdown">
        <i class="bx bx-dollar-circle"></i>
        <span>{{ config('currencies.names')[session('currency', 'USD')] ?? 'USD' }}</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><form method="POST" action="{{ route('currency.switch') }}">@csrf<input type="hidden" name="currency" value="USD"><button type="submit" class="dropdown-item {{ (session('currency', 'USD')) === 'USD' ? 'active' : '' }}">{{ config('currencies.names.USD') }}</button></form></li>
        <li><form method="POST" action="{{ route('currency.switch') }}">@csrf<input type="hidden" name="currency" value="CNY"><button type="submit" class="dropdown-item {{ (session('currency', 'USD')) === 'CNY' ? 'active' : '' }}">{{ config('currencies.names.CNY') }}</button></form></li>
        <li><form method="POST" action="{{ route('currency.switch') }}">@csrf<input type="hidden" name="currency" value="TZS"><button type="submit" class="dropdown-item {{ (session('currency', 'USD')) === 'TZS' ? 'active' : '' }}">{{ config('currencies.names.TZS') }}</button></form></li>
      </ul>
    </div>
    <div class="dropdown">
      <a class="dropdown-toggle buyer-modern-header-btn" href="#" data-bs-toggle="dropdown">
        <i class="bx bx-globe"></i>
        <span>{{ app()->getLocale() === 'en' ? 'English' : (app()->getLocale() === 'sw' ? 'Kiswahili' : 'Chinese') }}</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><form method="POST" action="{{ route('locale.switch') }}">@csrf<input type="hidden" name="locale" value="en"><button type="submit" class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}">English</button></form></li>
        <li><form method="POST" action="{{ route('locale.switch') }}">@csrf<input type="hidden" name="locale" value="sw"><button type="submit" class="dropdown-item {{ app()->getLocale() === 'sw' ? 'active' : '' }}">Kiswahili</button></form></li>
        <li><form method="POST" action="{{ route('locale.switch') }}">@csrf<input type="hidden" name="locale" value="zh"><button type="submit" class="dropdown-item {{ app()->getLocale() === 'zh' ? 'active' : '' }}">Chinese</button></form></li>
      </ul>
    </div>
    <a href="{{ route('notifications.index') }}" class="buyer-modern-header-btn position-relative" title="{{ __('labels.notifications') }}">
      <i class="bx bx-bell"></i>
      @if(($unread = auth()->user()->unreadNotifications->count() ?? 0) > 0)
        <span class="buyer-modern-badge">{{ $unread > 9 ? '9+' : $unread }}</span>
      @endif
    </a>
    <div class="dropdown">
      <a class="dropdown-toggle buyer-modern-header-user" href="#" data-bs-toggle="dropdown">
        @if(auth()->user()->avatarUrl())
          <img src="{{ auth()->user()->avatarUrl() }}" alt="">
        @else
          <span class="buyer-modern-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
        @endif
        <span class="buyer-modern-user-name">{{ auth()->user()->name ?? 'Account' }}</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end shadow">
        <li class="dropdown-header">
          <span class="fw-semibold">{{ auth()->user()->name ?? 'User' }}</span>
          <small class="d-block text-muted">{{ auth()->user()->email ?? '' }}</small>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bx bx-user me-2"></i>{{ __('profile.dropdown_profile') }}</a></li>
        <li><a class="dropdown-item" href="{{ route('buyer.rfqs.create') }}"><i class="bx bx-plus me-2"></i>{{ __('labels.request_quote') }}</a></li>
        <li>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item border-0 bg-transparent w-100 text-start"><i class="bx bx-power-off me-2"></i>{{ __('profile.log_out') }}</button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</header>
