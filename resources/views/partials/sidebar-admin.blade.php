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
    <a href="{{ route('admin.dashboard') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-home-circle"></i>
      <div data-i18n="Dashboard">Dashboard</div>
    </a>
  </li>
  <li class="menu-header small text-uppercase"><span class="menu-header-text">Approvals</span></li>
  <li class="menu-item">
    <a href="{{ route('admin.approvals.buyers') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-user-check"></i>
      <div data-i18n="Buyers">Buyers</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="{{ route('admin.approvals.factories') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-building"></i>
      <div data-i18n="Factories">Factories</div>
    </a>
  </li>
  <li class="menu-header small text-uppercase"><span class="menu-header-text">Operations</span></li>
  <li class="menu-item">
    <a href="{{ route('admin.rfqs.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-file"></i>
      <div data-i18n="Product Requests">{{ __('labels.rfqs') }}</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="{{ route('admin.rfqs.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-calculator"></i>
      <div data-i18n="Quote Builder">Quote Builder</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="{{ route('admin.freight-rates.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-truck"></i>
      <div data-i18n="Freight Rates">Freight Rates</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="{{ route('admin.orders.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-package"></i>
      <div data-i18n="Orders">Orders</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="{{ route('admin.payments.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-credit-card"></i>
      <div data-i18n="Payments">Payments</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="{{ route('admin.documents.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-folder"></i>
      <div data-i18n="Documents">Documents</div>
    </a>
  </li>
  <li class="menu-header small text-uppercase"><span class="menu-header-text">System</span></li>
  <li class="menu-item">
    <a href="{{ route('admin.users.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-group"></i>
      <div data-i18n="User Management">User Management</div>
    </a>
  </li>
</ul>
