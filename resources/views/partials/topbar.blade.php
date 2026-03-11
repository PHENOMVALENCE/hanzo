<nav
  class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
  id="layout-navbar"
>
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-2 me-xl-3">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)" title="Toggle sidebar" aria-label="Open menu">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center flex-grow-1 justify-content-end" id="navbar-collapse">
    <ul class="navbar-nav flex-row align-items-center gap-1 gap-sm-2">
      @auth
      @php
        $pendingAlerts = app(\App\Services\PendingAlertsService::class)->forUser();
        $notifications = Auth::user()?->notifications->take(10) ?? collect();
        $totalCount = $notifications->count() + $pendingAlerts->count();
        $unread = Auth::user()?->unreadNotifications->count();
      @endphp
      <li class="nav-item dropdown me-0 me-sm-2">
        <a class="nav-link position-relative d-flex align-items-center justify-content-center py-2 px-2" href="#" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications" style="min-width: 44px; min-height: 44px;">
          <i class="bx bx-bell" style="font-size: 1.25rem;"></i>
          @if($unread > 0 || $pendingAlerts->isNotEmpty())
            <span class="badge rounded-pill bg-danger position-absolute top-0 end-0 translate-middle" style="font-size: 0.6rem; padding: 0.15em 0.4em;">{{ ($unread + $pendingAlerts->count()) > 9 ? '9+' : ($unread + $pendingAlerts->count()) }}</span>
          @endif
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="min-width: 320px; max-height: 400px; overflow-y: auto;">
          <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
            <span class="fw-semibold">Notifications &amp; Alerts</span>
            @if($unread > 0)
              <form method="POST" action="{{ route('notifications.readAll') }}" class="d-inline">@csrf<button type="submit" class="btn btn-link btn-sm p-0">Mark all read</button></form>
            @endif
          </li>
          @if($pendingAlerts->isNotEmpty())
          <li class="dropdown-header px-3 py-1 pt-2 small text-muted">Pending</li>
          @foreach($pendingAlerts->take(5) as $alert)
          <li>
            <a class="dropdown-item py-2" href="{{ $alert['url'] }}">
              <div class="d-flex">
                <i class="bx {{ $alert['icon'] }} me-2 mt-1 text-warning"></i>
                <div>
                  <span class="d-block small">{{ $alert['title'] }}</span>
                  <small class="text-muted">Needs attention</small>
                </div>
              </div>
            </a>
          </li>
          @endforeach
          @endif
          @if($notifications->isNotEmpty())
          @if($pendingAlerts->isNotEmpty())
          <li class="dropdown-divider"></li>
          <li class="dropdown-header px-3 py-1 small text-muted">Recent</li>
          @endif
          @foreach($notifications as $n)
          @php
            $type = $n->data['type'] ?? 'order';
            $url = '#';
            if ($type === 'order' && !empty($n->data['order_id'])) {
              $url = Auth::user()->hasRole('admin') ? route('admin.orders.show', $n->data['order_id']) : (Auth::user()->hasRole('factory') ? route('factory.orders.show', $n->data['order_id']) : route('buyer.orders.show', $n->data['order_id']));
            } elseif ($type === 'quote_sent' && !empty($n->data['quotation_id'])) {
              $url = route('buyer.quotes.show', $n->data['quotation_id']);
            } elseif ($type === 'quote_rejected' && !empty($n->data['rfq_id'])) {
              $url = Auth::user()->hasRole('admin') ? route('admin.rfqs.show', $n->data['rfq_id']) : route('factory.rfqs.show', $n->data['rfq_id']);
            } elseif ($type === 'payment_pending' && !empty($n->data['payment_id']) && Auth::user()->hasRole('admin')) {
              $url = route('admin.payments.show', $n->data['payment_id']);
            }
            $title = match($type) {
              'quote_sent' => 'New quote: ' . ($n->data['quote_code'] ?? ''),
              'quote_rejected' => 'Quote rejected: ' . ($n->data['quote_code'] ?? '') . ' by ' . ($n->data['buyer_name'] ?? ''),
              'payment_pending' => 'Payment pending: $' . number_format($n->data['amount'] ?? 0, 2) . ' – ' . ($n->data['order_code'] ?? ''),
              default => $n->data['order_name'] ?? $n->data['order_code'] ?? 'New order',
            };
            $icon = match($type) {
              'quote_sent' => 'bx-file',
              'quote_rejected' => 'bx-x-circle',
              'payment_pending' => 'bx-dollar',
              default => 'bx-package',
            };
          @endphp
          <li>
            <a class="dropdown-item py-2 {{ $n->read_at ? '' : 'bg-light' }}" href="{{ $url }}">
              <div class="d-flex">
                <i class="bx {{ $icon }} me-2 mt-1 text-primary"></i>
                <div>
                  <span class="d-block small">{{ $title }}</span>
                  <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
                </div>
              </div>
            </a>
          </li>
          @endforeach
          @endif
          @if($totalCount === 0)
          <li class="dropdown-item text-muted py-3 text-center">No notifications or pending items</li>
          @endif
          <li class="dropdown-footer text-center border-top">
            <a class="dropdown-item small py-2" href="{{ route('notifications.index') }}">View all</a>
          </li>
        </ul>
      </li>
      @endauth
      <li class="nav-item dropdown me-0 me-sm-2">
        <a class="nav-link dropdown-toggle d-flex align-items-center gap-1 py-2 px-2" href="#" data-bs-toggle="dropdown" aria-expanded="false" title="Language">
          <i class="bx bx-globe" style="font-size: 1.1rem;"></i>
          <span class="d-none d-md-inline">{{ app()->getLocale() === 'en' ? 'English' : (app()->getLocale() === 'sw' ? 'Kiswahili' : '中文') }}</span>
          <span class="d-inline d-md-none">{{ app()->getLocale() === 'en' ? 'EN' : (app()->getLocale() === 'sw' ? 'SW' : '中文') }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><form method="POST" action="{{ route('locale.switch') }}">@csrf<input type="hidden" name="locale" value="en"><button type="submit" class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}">English</button></form></li>
          <li><form method="POST" action="{{ route('locale.switch') }}">@csrf<input type="hidden" name="locale" value="sw"><button type="submit" class="dropdown-item {{ app()->getLocale() === 'sw' ? 'active' : '' }}">Kiswahili</button></form></li>
          <li><form method="POST" action="{{ route('locale.switch') }}">@csrf<input type="hidden" name="locale" value="zh"><button type="submit" class="dropdown-item {{ app()->getLocale() === 'zh' ? 'active' : '' }}">中文</button></form></li>
        </ul>
      </li>
      <li class="nav-item navbar-dropdown dropdown-user dropdown me-0">
        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 py-2 px-2" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false" id="navbarDropdownUser">
          <div class="avatar avatar-online flex-shrink-0">
            @if(Auth::user()?->avatarUrl())
              <img src="{{ Auth::user()->avatarUrl() }}" alt class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;" />
            @else
              <span class="avatar-initial rounded-circle bg-label-primary" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; font-weight: 600;">{{ strtoupper(substr(Auth::user()?->name ?? 'U', 0, 1)) }}</span>
            @endif
          </div>
          <span class="d-none d-lg-inline text-truncate" style="max-width: 140px;">{{ Auth::user()?->name ?? 'Profile' }}</span>
          <i class="bx bx-chevron-down d-none d-sm-inline opacity-75" style="font-size: 1.1rem;"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="navbarDropdownUser">
          <li class="dropdown-header px-3 py-2">
            <span class="fw-semibold d-block text-body">{{ Auth::user()?->name ?? 'User' }}</span>
            <small class="text-muted">{{ Auth::user()?->email ?? '' }}</small>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
              <i class="bx bx-user me-2" style="font-size: 1.25rem;"></i>
              <span>Profile</span>
            </a>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
              @csrf
              <button type="submit" class="dropdown-item w-100 text-start border-0 bg-transparent">
                <i class="bx bx-power-off me-2"></i>
                <span class="align-middle">Log Out</span>
              </button>
            </form>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>
