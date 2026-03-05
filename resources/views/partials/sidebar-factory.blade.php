<div class="app-brand demo">
  <a href="{{ url('/') }}" class="app-brand-link">
    <span class="app-brand-text demo menu-text fw-bolder ms-2">HANZO</span>
  </a>
  <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
    <i class="bx bx-chevron-left bx-sm align-middle"></i>
  </a>
</div>

<div class="menu-inner-shadow"></div>

<ul class="menu-inner py-1">
  <li class="menu-item">
    <a href="{{ route('factory.dashboard') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-home-circle"></i>
      <div data-i18n="Dashboard">Dashboard</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="{{ route('factory.rfqs.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-task"></i>
      <div data-i18n="Assigned RFQs">Assigned RFQs</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="{{ route('factory.orders.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-package"></i>
      <div data-i18n="Orders">Orders</div>
    </a>
  </li>
</ul>
