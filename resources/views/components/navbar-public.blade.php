<nav class="navbar navbar-expand-lg navbar-dark hanzo-nav-public fixed-top">
  <div class="container">
    <a class="navbar-brand hanzo-logo d-flex align-items-center gap-2" href="{{ url('/') }}">
      <img src="{{ asset('assets/hanzo/logo.png') }}" alt="HANZO" style="height: 34px; width: auto;">
      <span>HANZO</span>
    </a>
    <button class="navbar-toggler border-light" type="button" data-bs-toggle="collapse" data-bs-target="#hanzoNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse py-2 py-lg-0" id="hanzoNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#categories">{{ __('landing.nav_categories') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('how-it-works') }}">{{ __('landing.nav_how_it_works') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#estimate">{{ __('landing.nav_estimate') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">{{ __('landing.nav_about') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('partner-with-hanzo') }}">{{ __('landing.list_factory') }}</a></li>
      </ul>
      <ul class="navbar-nav align-items-center gap-2">
        @auth
          <li class="nav-item">
            @php $dash = auth()->user()->hasRole('admin') ? route('admin.dashboard') : (auth()->user()->hasRole('factory') ? route('factory.dashboard') : route('buyer.dashboard')); @endphp
            <a class="btn btn-hanzo-primary btn-sm" href="{{ $dash }}">Dashboard</a>
          </li>
        @else
          <li class="nav-item dropdown">
            <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown" title="{{ __('labels.currency') }}">{{ session('currency', 'USD') }}</button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><form method="POST" action="{{ route('currency.switch') }}">@csrf<input type="hidden" name="currency" value="USD"><button type="submit" class="dropdown-item {{ (session('currency', 'USD')) === 'USD' ? 'active' : '' }}">{{ config('currencies.names.USD') }}</button></form></li>
              <li><form method="POST" action="{{ route('currency.switch') }}">@csrf<input type="hidden" name="currency" value="TZS"><button type="submit" class="dropdown-item {{ (session('currency', 'USD')) === 'TZS' ? 'active' : '' }}">{{ config('currencies.names.TZS') }}</button></form></li>
              <li><form method="POST" action="{{ route('currency.switch') }}">@csrf<input type="hidden" name="currency" value="CNY"><button type="submit" class="dropdown-item {{ (session('currency', 'USD')) === 'CNY' ? 'active' : '' }}">{{ config('currencies.names.CNY') }}</button></form></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">{{ app()->getLocale() === 'en' ? 'EN' : (app()->getLocale() === 'sw' ? 'SW' : '中文') }}</button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><form method="POST" action="{{ route('locale.switch') }}">@csrf<input type="hidden" name="locale" value="en"><button type="submit" class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}">English</button></form></li>
              <li><form method="POST" action="{{ route('locale.switch') }}">@csrf<input type="hidden" name="locale" value="sw"><button type="submit" class="dropdown-item {{ app()->getLocale() === 'sw' ? 'active' : '' }}">Kiswahili</button></form></li>
              <li><form method="POST" action="{{ route('locale.switch') }}">@csrf<input type="hidden" name="locale" value="zh"><button type="submit" class="dropdown-item {{ app()->getLocale() === 'zh' ? 'active' : '' }}">中文</button></form></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('landing.log_in') }}</a></li>
          <li class="nav-item"><a class="btn btn-hanzo-primary btn-sm" href="{{ route('register') }}">{{ __('landing.request_quote') }}</a></li>
        @endauth
      </ul>
    </div>
  </div>
</nav>
<style>
.hanzo-nav-public { background: var(--hanzo-navy) !important; backdrop-filter: blur(8px); border-bottom: 1px solid rgba(216,155,43,0.2); box-shadow: 0 2px 12px rgba(9,22,43,0.4); }
.hanzo-logo { font-size: 1.25rem; font-weight: 700; color: var(--hanzo-gold-soft); letter-spacing: 0.1em; }
@media (min-width: 576px) { .hanzo-logo { font-size: 1.5rem; } }
.hanzo-nav-public .nav-link { color: rgba(255,255,255,0.9); transition: color 0.2s; min-height: 44px; display: flex; align-items: center; }
.hanzo-nav-public .nav-link:hover { color: var(--hanzo-gold-soft); }
.hanzo-nav-public .btn-outline-light:hover { color: var(--hanzo-navy) !important; background: var(--hanzo-gold-soft) !important; border-color: var(--hanzo-gold-soft) !important; }
.hanzo-nav-public .btn { min-height: 44px; display: inline-flex; align-items: center; }
.hanzo-nav-public .navbar-toggler { min-width: 44px; min-height: 44px; padding: 0.5rem; }
</style>
