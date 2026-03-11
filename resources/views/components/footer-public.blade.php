<footer class="hanzo-footer-public py-4 mt-5 px-2 px-md-0">
  <div class="container text-center text-white-50">
    <p class="mb-0 small">
      <img src="{{ asset('assets/hanzo/logo.png') }}" alt="HANZO" style="height: 22px; width: auto; vertical-align: middle; margin-right: 4px; opacity: 0.8;">
      {{ __('landing.footer_version') ?? 'HANZO — Structured Access to Global Manufacturing' }}
    </p>
    <p class="mb-0 mt-1 small"><a href="{{ route('about') }}" class="text-white-50">{{ __('landing.nav_about') }}</a> · <a href="{{ route('about') }}#contact" class="text-white-50">{{ __('landing.nav_contact') }}</a></p>
  </div>
</footer>
<style>
.hanzo-footer-public { background: var(--hanzo-navy); border-top: 1px solid rgba(216,155,43,0.2); }
.hanzo-footer-public a:hover { color: var(--hanzo-gold-soft) !important; }
</style>
