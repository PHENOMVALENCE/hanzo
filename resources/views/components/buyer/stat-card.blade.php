@props([
  'label',
  'value',
  'icon' => 'bx-bar-chart',
  'href' => null,
  'trend' => null,
])

<div class="card hanzo-stat-card-b2b h-100">
  <div class="card-body d-flex justify-content-between align-items-center">
    <div>
      <p class="stat-label mb-1">{{ $label }}</p>
      <h3 class="stat-value mb-0">{{ $value }}</h3>
      @if($trend)
      <span class="small {{ str_starts_with($trend, '+') ? 'text-success' : 'text-muted' }}">{{ $trend }}</span>
      @endif
      @if($href)
      <a href="{{ $href }}" class="btn btn-b2b-primary btn-sm mt-2">{{ __('View') }}</a>
      @endif
    </div>
    <div class="stat-icon">
      <i class="bx {{ $icon }} bx-lg"></i>
    </div>
  </div>
</div>
