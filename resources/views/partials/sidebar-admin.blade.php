<div class="app-brand demo">
  <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
    <img src="{{ asset('assets/hanzo/logo.png') }}" alt="HANZO" style="height: 30px; width: auto;" class="app-brand-logo">
    <span class="app-brand-text demo menu-text fw-bolder ms-2">HANZO Admin</span>
  </a>
  <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block" title="Toggle sidebar">
    <i class="bx bx-chevron-left bx-sm align-middle"></i>
  </a>
</div>

<div class="menu-inner-shadow"></div>

<ul class="menu-inner py-1">
  <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <a href="{{ route('admin.dashboard') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-home-circle"></i>
      <div data-i18n="Dashboard">{{ __('menu.dashboard') }}</div>
    </a>
  </li>

  <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ __('admin.menu.user_management') }}</span></li>
  <li class="menu-item {{ request()->routeIs('admin.users.*') && request('role') === 'buyer' ? 'active' : '' }}">
    <a href="{{ route('admin.users.index', ['role' => 'buyer']) }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-user"></i>
      <div data-i18n="Buyers">{{ __('menu.buyers') }}</div>
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('admin.users.*') && request('role') === 'factory' ? 'active' : '' }}">
    <a href="{{ route('admin.users.index', ['role' => 'factory']) }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-building"></i>
      <div data-i18n="Factories">{{ __('menu.factories') }}</div>
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('admin.users.index') && request('role') === 'admin' ? 'active' : '' }}">
    <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-shield-alt-2"></i>
      <div data-i18n="Admin">{{ __('admin.menu.admin_subaccounts') }}</div>
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('admin.invite-factory.*') ? 'active' : '' }}">
    <a href="{{ route('admin.invite-factory.create') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-plus-circle"></i>
      <div data-i18n="Invite Factory">{{ __('admin.menu.invite_factory') }}</div>
    </a>
  </li>

  <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ __('admin.menu.verification') }}</span></li>
  <li class="menu-item {{ request()->routeIs('admin.approvals.*') ? 'active' : '' }}">
    <a href="{{ route('admin.approvals.buyers') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-user-check"></i>
      <div data-i18n="Factory Verification">{{ __('admin.menu.factory_verification') }}</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="{{ route('admin.approvals.buyers') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-id-card"></i>
      <div data-i18n="Buyer KYC">{{ __('admin.menu.buyer_kyc') }}</div>
    </a>
  </li>

  <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ __('admin.menu.product_catalog') }}</span></li>
  <li class="menu-item">
    <a href="{{ route('admin.dashboard') }}#products" class="menu-link">
      <i class="menu-icon tf-icons bx bx-check-shield"></i>
      <div data-i18n="Product Review Queue">{{ __('admin.menu.product_review_queue') }}</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="{{ route('admin.dashboard') }}#catalog" class="menu-link">
      <i class="menu-icon tf-icons bx bx-grid-alt"></i>
      <div data-i18n="Full Catalog">{{ __('admin.menu.full_catalog') }}</div>
    </a>
  </li>

  <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ __('admin.menu.rfq_quotes') }}</span></li>
  <li class="menu-item {{ request()->routeIs('admin.rfqs.*') ? 'active' : '' }}">
    <a href="{{ route('admin.rfqs.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-file"></i>
      <div data-i18n="RFQ Overview">{{ __('admin.menu.rfq_overview') }}</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="{{ route('admin.rfqs.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-calculator"></i>
      <div data-i18n="Quote Builder">{{ __('menu.quote_builder') }}</div>
    </a>
  </li>

  <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ __('admin.menu.finance') }}</span></li>
  <li class="menu-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
    <a href="{{ route('admin.orders.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-package"></i>
      <div data-i18n="Orders">{{ __('menu.orders') }}</div>
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
    <a href="{{ route('admin.payments.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-credit-card"></i>
      <div data-i18n="Payments">{{ __('menu.payments') }}</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="{{ route('admin.freight-rates.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-truck"></i>
      <div data-i18n="Freight Rates">{{ __('menu.freight_rates') }}</div>
    </a>
  </li>

  <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ __('admin.menu.system') }}</span></li>
  <li class="menu-item">
    <a href="{{ route('admin.documents.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-folder"></i>
      <div data-i18n="Documents">{{ __('menu.documents') }}</div>
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('admin.users.*') && !request('role') ? 'active' : '' }}">
    <a href="{{ route('admin.users.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-cog"></i>
      <div data-i18n="User Management">{{ __('menu.user_management') }}</div>
    </a>
  </li>
</ul>
