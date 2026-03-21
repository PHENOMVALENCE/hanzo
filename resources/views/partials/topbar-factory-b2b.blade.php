<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-2 me-xl-3">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)" title="Toggle sidebar" aria-label="Open menu">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <a href="{{ route('factory.dashboard') }}" class="navbar-brand d-none d-xl-flex align-items-center me-4">
    <img src="{{ asset('assets/hanzo/logo.png') }}" alt="HANZO" style="height: 32px; width: auto;" class="me-2">
    <span class="fw-bold text-body">HANZO Factory</span>
  </a>

  <div class="navbar-nav-right d-flex align-items-center flex-grow-1 justify-content-end">
    <div class="nav-item dropdown me-2">
      <a class="nav-link dropdown-toggle d-flex align-items-center gap-1 py-2 px-2" href="#" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('labels.currency') }}">
        <i class="bx bx-dollar-circle" style="font-size: 1.1rem;"></i>
        <span class="d-none d-md-inline">{{ config('currencies.names')[session('currency', 'USD')] ?? config('currencies.names.USD') }}</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><form method="POST" action="{{ route('currency.switch') }}">@csrf<input type="hidden" name="currency" value="USD"><button type="submit" class="dropdown-item {{ (session('currency', 'USD')) === 'USD' ? 'active' : '' }}">{{ config('currencies.names.USD') }}</button></form></li>
        <li><form method="POST" action="{{ route('currency.switch') }}">@csrf<input type="hidden" name="currency" value="CNY"><button type="submit" class="dropdown-item {{ (session('currency', 'USD')) === 'CNY' ? 'active' : '' }}">{{ config('currencies.names.CNY') }}</button></form></li>
        <li><form method="POST" action="{{ route('currency.switch') }}">@csrf<input type="hidden" name="currency" value="TZS"><button type="submit" class="dropdown-item {{ (session('currency', 'USD')) === 'TZS' ? 'active' : '' }}">{{ config('currencies.names.TZS') }}</button></form></li>
      </ul>
    </div>
    <div class="nav-item dropdown me-2">
      <a class="nav-link dropdown-toggle d-flex align-items-center gap-1 py-2 px-2" href="#" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('labels.language') }}">
        <i class="bx bx-globe" style="font-size: 1.1rem;"></i>
        <span class="d-none d-md-inline">{{ app()->getLocale() === 'en' ? 'English' : (app()->getLocale() === 'sw' ? 'Kiswahili' : 'Chinese') }}</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><form method="POST" action="{{ route('locale.switch') }}">@csrf<input type="hidden" name="locale" value="en"><button type="submit" class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}">English</button></form></li>
        <li><form method="POST" action="{{ route('locale.switch') }}">@csrf<input type="hidden" name="locale" value="sw"><button type="submit" class="dropdown-item {{ app()->getLocale() === 'sw' ? 'active' : '' }}">Kiswahili</button></form></li>
        <li><form method="POST" action="{{ route('locale.switch') }}">@csrf<input type="hidden" name="locale" value="zh"><button type="submit" class="dropdown-item {{ app()->getLocale() === 'zh' ? 'active' : '' }}">Chinese</button></form></li>
      </ul>
    </div>
    @php
      $pendingAlerts = app(\App\Services\PendingAlertsService::class)->forUser();
      $notifications = Auth::user()?->notifications->take(10) ?? collect();
      $unread = Auth::user()?->unreadNotifications->count();
      $totalNotif = $unread + $pendingAlerts->count();
    @endphp
    <div class="nav-item dropdown me-2">
      <a class="nav-link position-relative py-2 px-2" href="#" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
        <i class="bx bx-bell" style="font-size: 1.25rem;"></i>
        @if($totalNotif > 0)
        <span id="notification-badge" class="badge rounded-pill bg-danger position-absolute top-0 end-0 translate-middle" style="font-size: 0.6rem;">{{ $totalNotif > 9 ? '9+' : $totalNotif }}</span>
        @else
        <span id="notification-badge" class="badge rounded-pill bg-danger position-absolute top-0 end-0 translate-middle d-none">0</span>
        @endif
      </a>
      <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="min-width: 300px; max-height: 400px; overflow-y: auto;">
        <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
          <span class="fw-semibold">{{ __('factory.dashboard.notifications') }}</span>
          @if($unread > 0)
          <form method="POST" action="{{ route('notifications.readAll') }}" class="d-inline">@csrf<button type="submit" class="btn btn-link btn-sm p-0">{{ __('factory.dashboard.mark_all_read') }}</button></form>
          @endif
        </li>
        @foreach($pendingAlerts->take(5) as $alert)
        <li><a class="dropdown-item py-2" href="{{ $alert['url'] }}"><i class="bx {{ $alert['icon'] }} me-2 text-warning"></i>{{ $alert['title'] }}</a></li>
        @endforeach
        @foreach($notifications as $n)
        @php $url = route('notifications.index'); $type = $n->data['type'] ?? ''; if ($type === 'order' && !empty($n->data['order_id'])) $url = route('factory.orders.show', $n->data['order_id']); elseif ($type === 'quote_rejected' && !empty($n->data['rfq_id'])) $url = route('factory.rfqs.show', $n->data['rfq_id']); $title = $n->data['order_code'] ?? $n->data['message'] ?? 'Notification'; $title = is_array($title) ? 'Notification' : (string)$title; @endphp
        <li><a class="dropdown-item py-2 {{ $n->read_at ? '' : 'bg-light' }}" href="{{ $url }}"><span class="d-block small">{{ $title }}</span><small class="text-muted">{{ $n->created_at->diffForHumans() }}</small></a></li>
        @endforeach
        @if($totalNotif === 0)
        <li class="dropdown-item text-muted py-3 text-center">{{ __('factory.dashboard.no_notifications') }}</li>
        @endif
        <li class="dropdown-footer text-center border-top"><a class="dropdown-item small py-2" href="{{ route('notifications.index') }}">{{ __('factory.dashboard.view_all') }}</a></li>
      </ul>
    </div>

    <div class="nav-item dropdown">
      <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 py-2 px-3" href="javascript:void(0);" data-bs-toggle="dropdown">
        @if(Auth::user()?->avatarUrl())
        <img src="{{ Auth::user()->avatarUrl() }}" alt="" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
        @else
        <span class="rounded-circle d-flex align-items-center justify-content-center fw-600" style="width: 36px; height: 36px; background: #f0fdfa; color: #0d9488;">{{ strtoupper(substr(Auth::user()?->name ?? 'F', 0, 1)) }}</span>
        @endif
        <span class="d-none d-lg-inline text-truncate" style="max-width: 140px;">{{ is_array($n = Auth::user()?->name) ? 'Factory' : ($n ?? 'Factory') }}</span>
        <i class="bx bx-chevron-down"></i>
      </a>
      <ul class="dropdown-menu dropdown-menu-end shadow-lg">
        <li class="dropdown-header px-3 py-2">
          <span class="fw-semibold d-block">{{ is_array($n = Auth::user()?->name) ? 'Factory' : ($n ?? 'Factory') }}</span>
          <small class="text-muted">{{ Auth::user()?->factory?->factory_name ?? '' }}</small>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.edit') }}"><i class="bx bx-user"></i> {{ __('factory.dashboard.profile') }}</a></li>
        <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.edit') }}"><i class="bx bx-cog"></i> {{ __('factory.dashboard.settings') }}</a></li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <form method="POST" action="{{ route('logout') }}" class="d-inline">@csrf
            <button type="submit" class="dropdown-item w-100 text-start border-0 bg-transparent"><i class="bx bx-power-off me-2"></i> {{ __('factory.dashboard.log_out') }}</button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</nav>
