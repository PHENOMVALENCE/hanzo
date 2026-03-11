<nav
  class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
  id="layout-navbar"
>
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)" title="Toggle sidebar">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <ul class="navbar-nav flex-row align-items-center ms-auto">
      @if(Auth::user()?->hasRole('admin') || Auth::user()?->hasRole('factory'))
      <li class="nav-item dropdown me-2">
        <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bx bx-bell bx-sm"></i>
          @php $unread = Auth::user()?->unreadNotifications->count(); @endphp
          @if($unread > 0)
            <span class="badge rounded-pill bg-danger position-absolute top-0 end-0 translate-middle" style="font-size: 0.6rem; padding: 0.15em 0.4em;">{{ $unread > 9 ? '9+' : $unread }}</span>
          @endif
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="min-width: 320px; max-height: 400px; overflow-y: auto;">
          <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
            <span class="fw-semibold">Notifications</span>
            @if($unread > 0)
              <form method="POST" action="{{ route('notifications.readAll') }}" class="d-inline">@csrf<button type="submit" class="btn btn-link btn-sm p-0">Mark all read</button></form>
            @endif
          </li>
          @forelse(Auth::user()?->notifications->take(10) ?? [] as $n)
          <li>
            <a class="dropdown-item py-2 {{ $n->read_at ? '' : 'bg-light' }}" href="{{ $n->data['order_id'] ?? null ? (Auth::user()->hasRole('admin') ? route('admin.orders.show', $n->data['order_id']) : route('factory.orders.show', $n->data['order_id'])) : '#' }}">
              <div class="d-flex">
                <i class="bx bx-package me-2 mt-1 text-primary"></i>
                <div>
                  <span class="d-block small">{{ $n->data['order_name'] ?? $n->data['order_code'] ?? 'New order' }}</span>
                  <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
                </div>
              </div>
            </a>
          </li>
          @empty
          <li class="dropdown-item text-muted py-3 text-center">No notifications</li>
          @endforelse
          <li class="dropdown-footer text-center border-top">
            <a class="dropdown-item small py-2" href="{{ route('notifications.index') }}">View all</a>
          </li>
        </ul>
      </li>
      @endif
      <li class="nav-item dropdown me-2">
        <a class="nav-link dropdown-toggle py-2" href="#" data-bs-toggle="dropdown" aria-expanded="false">
          {{ app()->getLocale() === 'en' ? 'English' : (app()->getLocale() === 'sw' ? 'Kiswahili' : '中文') }}
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><form method="POST" action="{{ route('locale.switch') }}">@csrf<input type="hidden" name="locale" value="en"><button type="submit" class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}">English</button></form></li>
          <li><form method="POST" action="{{ route('locale.switch') }}">@csrf<input type="hidden" name="locale" value="sw"><button type="submit" class="dropdown-item {{ app()->getLocale() === 'sw' ? 'active' : '' }}">Kiswahili</button></form></li>
          <li><form method="POST" action="{{ route('locale.switch') }}">@csrf<input type="hidden" name="locale" value="zh"><button type="submit" class="dropdown-item {{ app()->getLocale() === 'zh' ? 'active' : '' }}">中文</button></form></li>
        </ul>
      </li>
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            @if(Auth::user()?->avatarUrl())
              <img src="{{ Auth::user()->avatarUrl() }}" alt class="w-px-40 h-auto rounded-circle" />
            @else
              <span class="avatar-initial rounded-circle bg-label-primary">{{ strtoupper(substr(Auth::user()?->name ?? 'U', 0, 1)) }}</span>
            @endif
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="{{ route('profile.edit') }}">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    @if(Auth::user()?->avatarUrl())
                      <img src="{{ Auth::user()->avatarUrl() }}" alt class="w-px-40 h-auto rounded-circle" />
                    @else
                      <span class="avatar-initial rounded-circle bg-label-primary">{{ strtoupper(substr(Auth::user()?->name ?? 'U', 0, 1)) }}</span>
                    @endif
                  </div>
                </div>
                <div class="flex-grow-1">
                  <span class="fw-semibold d-block">{{ Auth::user()?->name ?? 'User' }}</span>
                  <small class="text-muted">{{ Auth::user()?->email ?? '' }}</small>
                </div>
              </div>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
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
