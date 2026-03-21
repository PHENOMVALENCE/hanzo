<div class="app-brand demo">
  <a href="{{ route('factory.dashboard') }}" class="app-brand-link">
    <img src="{{ asset('assets/hanzo/logo.png') }}" alt="HANZO" style="height: 28px; width: auto;" class="app-brand-logo">
    <span class="app-brand-text demo menu-text fw-bolder ms-2">hanzo</span>
  </a>
  <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block" title="Toggle sidebar">
    <i class="bx bx-chevron-left bx-sm align-middle"></i>
  </a>
</div>

<div class="menu-inner-shadow"></div>

<ul class="menu-inner py-1">
  <li class="menu-item {{ request()->routeIs('factory.dashboard') ? 'active' : '' }}">
    <a href="{{ route('factory.dashboard') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-home-circle"></i>
      <div data-i18n="Dashboard">Dashboard</div>
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('factory.products.*') ? 'active' : '' }}">
    <a href="{{ route('factory.products.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-package"></i>
      <div data-i18n="Products">Products</div>
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('factory.rfqs.*') ? 'active' : '' }}">
    <a href="{{ route('factory.rfqs.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-task"></i>
      <div data-i18n="RFQ Inbox">RFQ Inbox</div>
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('factory.orders.*') ? 'active' : '' }}">
    <a href="{{ route('factory.orders.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-package"></i>
      <div data-i18n="Orders">Orders</div>
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('factory.profile.*') ? 'active' : '' }}">
    <a href="{{ route('factory.profile.edit') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-buildings"></i>
      <div data-i18n="Factory Profile">Factory Profile</div>
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('factory.analytics.*') ? 'active' : '' }}">
    <a href="{{ route('factory.analytics.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-bar-chart-alt"></i>
      <div data-i18n="Analytics">Analytics</div>
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('notifications.index') ? 'active' : '' }}">
    <a href="{{ route('notifications.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-bell"></i>
      <div data-i18n="Notifications">Notifications</div>
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
    <a href="{{ route('profile.edit') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-cog"></i>
      <div data-i18n="Settings">Settings</div>
    </a>
  </li>
</ul>
