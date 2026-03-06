<div class="app-brand demo">
  <a href="{{ url('/') }}" class="app-brand-link">
    <img src="{{ asset('assets/hanzo/logo.png') }}" alt="HANZO" style="height: 32px; width: auto;" class="app-brand-logo">
    <span class="app-brand-text demo menu-text fw-bolder ms-2">HANZO</span>
  </a>
  <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block" title="Toggle sidebar">
    <i class="bx bx-chevron-left bx-sm align-middle"></i>
  </a>
</div>

<div class="menu-inner-shadow"></div>

<ul class="menu-inner py-1">
  <li class="menu-item">
    <a href="{{ route('buyer.dashboard') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-home-circle"></i>
      <div data-i18n="Dashboard">{{ __('buyer.sidebar.dashboard') }}</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="{{ route('buyer.rfqs.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-file"></i>
      <div data-i18n="RFQs">{{ __('buyer.sidebar.rfqs') }}</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="{{ route('buyer.quotes.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-list-check"></i>
      <div data-i18n="Quotes">{{ __('buyer.sidebar.quotes') }}</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="{{ route('buyer.orders.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-package"></i>
      <div data-i18n="Orders">{{ __('buyer.sidebar.orders') }}</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="{{ route('buyer.orders.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-credit-card"></i>
      <div data-i18n="Payments">{{ __('buyer.sidebar.payments') }}</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="{{ route('buyer.orders.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-folder"></i>
      <div data-i18n="Documents">{{ __('buyer.sidebar.documents') }}</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="{{ route('contact') }}" class="menu-link" target="_blank">
      <i class="menu-icon tf-icons bx bx-support"></i>
      <div data-i18n="Support">Support</div>
    </a>
  </li>
</ul>
