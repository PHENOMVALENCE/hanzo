<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-2 me-xl-3">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)" title="Toggle sidebar" aria-label="Open menu">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  {{-- HANZO Logo --}}
  <a href="{{ route('buyer.dashboard') }}" class="navbar-brand d-none d-xl-flex align-items-center me-4">
    <img src="{{ asset('assets/hanzo/logo.png') }}" alt="HANZO" style="height: 32px; width: auto;" class="me-2">
    <span class="fw-bold" style="color: var(--hz-gold); letter-spacing: 0.05em;">HANZO</span>
  </a>

  {{-- Global Search --}}
  <form action="{{ route('buyer.catalog.index') }}" method="GET" class="d-flex flex-grow-1 mx-2 mx-xl-4" style="max-width: 420px;">
    <div class="hanzo-b2b-search d-flex align-items-center w-100">
      <i class="bx bx-search ms-2 text-muted" style="font-size: 1.1rem;"></i>
      <input type="search" name="q" value="{{ is_array($q = request('q')) ? '' : e($q ?? '') }}" class="form-control border-0 shadow-none" placeholder="Search products or suppliers..." aria-label="Search">
    </div>
  </form>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    {{-- Categories Dropdown --}}
    <div class="nav-item dropdown me-2">
      <a class="nav-link dropdown-toggle d-flex align-items-center py-2 px-3" href="#" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bx bx-category me-1"></i>
        <span class="d-none d-lg-inline">{{ __('landing.nav_categories') }}</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-start">
        @foreach(\App\Models\Category::where('active', true)->get() as $cat)
        <li><a class="dropdown-item" href="{{ route('buyer.catalog.index', ['category' => $cat->slug]) }}">{{ trans_category($cat) }}</a></li>
        @endforeach
        @if(\App\Models\Category::where('active', true)->count() === 0)
        <li><a class="dropdown-item text-muted" href="{{ route('buyer.catalog.index') }}">{{ __('All Categories') }}</a></li>
        @endif
      </ul>
    </div>

    {{-- Messages --}}
    <a href="{{ route('buyer.messages.index') }}" class="nav-link position-relative py-2 px-2" title="{{ __('Messages') }}">
      <i class="bx bx-message-dots" style="font-size: 1.25rem;"></i>
      @php $msgCount = 0; @endphp
      @if($msgCount > 0)
      <span class="badge rounded-pill bg-danger position-absolute top-0 end-0 translate-middle" style="font-size: 0.6rem;">{{ $msgCount > 9 ? '9+' : $msgCount }}</span>
      @endif
    </a>

    {{-- Notifications --}}
    @php
      $pendingAlerts = app(\App\Services\PendingAlertsService::class)->forUser();
      $notifications = Auth::user()?->notifications->take(10) ?? collect();
      $unread = Auth::user()?->unreadNotifications->count();
      $totalNotif = $unread + $pendingAlerts->count();
    @endphp
    <div class="nav-item dropdown me-1">
      <a class="nav-link position-relative py-2 px-2" href="#" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Notifications') }}">
        <i class="bx bx-bell" style="font-size: 1.25rem;"></i>
        @if($totalNotif > 0)
        <span id="notification-badge" class="badge rounded-pill bg-danger position-absolute top-0 end-0 translate-middle" style="font-size: 0.6rem;">{{ $totalNotif > 9 ? '9+' : $totalNotif }}</span>
        @else
        <span id="notification-badge" class="badge rounded-pill bg-danger position-absolute top-0 end-0 translate-middle d-none" style="font-size: 0.6rem;">0</span>
        @endif
      </a>
      <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="min-width: 320px; max-height: 400px; overflow-y: auto;">
        <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
          <span class="fw-semibold">{{ __('Notifications') }}</span>
          @if($unread > 0)
          <form method="POST" action="{{ route('notifications.readAll') }}" class="d-inline">@csrf<button type="submit" class="btn btn-link btn-sm p-0">{{ __('Mark all read') }}</button></form>
          @endif
        </li>
        @foreach($pendingAlerts->take(5) as $alert)
        <li><a class="dropdown-item py-2" href="{{ $alert['url'] }}"><div class="d-flex"><i class="bx {{ $alert['icon'] }} me-2 mt-1 text-warning"></i><div><span class="d-block small">{{ $alert['title'] }}</span><small class="text-muted">{{ __('Needs attention') }}</small></div></div></a></li>
        @endforeach
        @foreach($notifications as $n)
        @php
          $type = $n->data['type'] ?? 'order';
          $url = $type === 'quote_sent' && !empty($n->data['quotation_id']) ? route('buyer.quotes.show', $n->data['quotation_id']) : (in_array($type, ['order','order_milestone']) && !empty($n->data['order_id']) ? route('buyer.orders.show', $n->data['order_id']) : route('notifications.index'));
          $title = $n->data['order_code'] ?? $n->data['message'] ?? 'Notification';
          $title = is_array($title) ? 'Notification' : (string) $title;
          $icon = match($type) { 'quote_sent' => 'bx-file', 'order' => 'bx-package', 'order_milestone' => 'bx-package', default => 'bx-bell' };
        @endphp
        <li><a class="dropdown-item py-2 {{ $n->read_at ? '' : 'bg-light' }}" href="{{ $url }}"><div class="d-flex"><i class="bx {{ $icon }} me-2 mt-1 text-primary"></i><div><span class="d-block small">{{ $title }}</span><small class="text-muted">{{ $n->created_at->diffForHumans() }}</small></div></div></a></li>
        @endforeach
        @if($totalNotif === 0)
        <li class="dropdown-item text-muted py-3 text-center">{{ __('No notifications') }}</li>
        @endif
        <li class="dropdown-footer text-center border-top"><a class="dropdown-item small py-2" href="{{ route('notifications.index') }}">{{ __('View all') }}</a></li>
      </ul>
    </div>

    {{-- Saved Items --}}
    <a href="{{ route('buyer.saved.index') }}" class="nav-link py-2 px-2" title="{{ __('Saved Items') }}">
      <i class="bx bx-bookmark" style="font-size: 1.25rem;"></i>
    </a>

    {{-- Profile Dropdown --}}
    <div class="nav-item dropdown ms-1">
      <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 py-2 px-3" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
        @if(Auth::user()?->avatarUrl())
        <img src="{{ Auth::user()->avatarUrl() }}" alt="" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
        @else
        <span class="avatar-initial rounded-circle d-flex align-items-center justify-content-center fw-600" style="width: 36px; height: 36px; background: #eff6ff; color: #2563eb;">{{ strtoupper(substr(Auth::user()?->name ?? 'U', 0, 1)) }}</span>
        @endif
        <span class="d-none d-lg-inline text-truncate" style="max-width: 120px;">{{ Auth::user()?->name ?? 'Profile' }}</span>
        <i class="bx bx-chevron-down opacity-75"></i>
      </a>
      <ul class="dropdown-menu dropdown-menu-end shadow-lg">
        <li class="dropdown-header px-3 py-2">
          @php
            $dn = Auth::user()?->name;
            $ds = Auth::user()?->company_name ?? Auth::user()?->email ?? '';
          @endphp
          <span class="fw-semibold d-block">{{ is_array($dn) ? 'User' : ($dn ?? 'User') }}</span>
          <small class="text-muted">{{ is_array($ds) ? '' : $ds }}</small>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.edit') }}"><i class="bx bx-user"></i> {{ is_array($v = __('profile.dropdown_profile')) ? 'Profile' : $v }}</a></li>
        <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('buyer.settings') }}"><i class="bx bx-cog"></i> {{ __('Settings') }}</a></li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button type="submit" class="dropdown-item w-100 text-start border-0 bg-transparent"><i class="bx bx-power-off me-2"></i> {{ __('Log out') }}</button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</nav>
