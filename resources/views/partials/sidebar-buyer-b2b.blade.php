<div class="app-brand demo">
  <a href="{{ route('buyer.dashboard') }}" class="app-brand-link">
    <img src="{{ asset('assets/hanzo/logo.png') }}" alt="HANZO" style="height: 28px; width: auto;" class="app-brand-logo">
    <span class="app-brand-text demo menu-text fw-bolder ms-2">HANZO</span>
  </a>
  <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block" title="Toggle sidebar">
    <i class="bx bx-chevron-left bx-sm align-middle"></i>
  </a>
</div>

<div class="menu-inner-shadow"></div>

<ul class="menu-inner py-1">
  <li class="menu-item {{ request()->routeIs('buyer.dashboard') ? 'active' : '' }}">
    <a href="{{ route('buyer.dashboard') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-home-circle"></i>
      <div data-i18n="Dashboard">{{ __('menu.dashboard') }}</div>
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('buyer.catalog.*') ? 'active' : '' }}">
    <a href="{{ route('buyer.catalog.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-grid-alt"></i>
      <div data-i18n="Product Catalog">{{ __('Product Catalog') }}</div>
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('buyer.rfqs.*') ? 'active' : '' }}">
    <a href="{{ route('buyer.rfqs.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-file"></i>
      <div data-i18n="My RFQs">{{ __('My RFQs') }}</div>
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('buyer.quotes.*') ? 'active' : '' }}">
    <a href="{{ route('buyer.quotes.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-inbox"></i>
      <div data-i18n="Quotation Inbox">{{ __('Quotation Inbox') }}</div>
      @php $quoteUnread = \App\Models\Quotation::whereHas('rfq', fn($q) => $q->where('buyer_id', auth()->id()))->where('status', 'sent')->count(); @endphp
      @if($quoteUnread > 0)
      <span class="badge rounded-pill bg-warning ms-auto">{{ $quoteUnread > 9 ? '9+' : $quoteUnread }}</span>
      @endif
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('buyer.suppliers.*') ? 'active' : '' }}">
    <a href="{{ route('buyer.suppliers.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-buildings"></i>
      <div data-i18n="Suppliers">{{ __('Suppliers') }}</div>
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('buyer.orders.*') ? 'active' : '' }}">
    <a href="{{ route('buyer.orders.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-package"></i>
      <div data-i18n="Orders">{{ __('menu.orders') }}</div>
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('buyer.saved.*') ? 'active' : '' }}">
    <a href="{{ route('buyer.saved.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-bookmark"></i>
      <div data-i18n="Saved Items">{{ __('Saved Items') }}</div>
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('buyer.messages.*') ? 'active' : '' }}">
    <a href="{{ route('buyer.messages.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-message-dots"></i>
      <div data-i18n="Messages">{{ __('Messages') }}</div>
    </a>
  </li>
  <li class="menu-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
    <a href="{{ route('notifications.index') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-bell"></i>
      <div data-i18n="Notifications">{{ __('Notifications') }}</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="{{ route('buyer.settings') }}" class="menu-link">
      <i class="menu-icon tf-icons bx bx-cog"></i>
      <div data-i18n="Settings">{{ __('Settings') }}</div>
    </a>
  </li>
</ul>
