@props(['label', 'value', 'icon' => null, 'trend' => null, 'href' => null])

@php
$Tag = $href ? 'a' : 'div';
$attrs = $href ? ['href' => $href, 'class' => 'text-decoration-none text-body'] : [];
@endphp

<div class="card hanzo-stat-card h-100">
  <div class="card-body">
    <{{ $Tag }} {{ $attributes->merge($attrs) }} class="d-flex align-items-center justify-content-between">
      <div>
        <p class="hanzo-metric-label mb-1">{{ $label }}</p>
        <h3 class="hanzo-metric mb-0">{{ $value }}</h3>
        @if($trend)
        <span class="small {{ str_starts_with($trend, '+') ? 'text-success' : 'text-muted' }}">{{ $trend }}</span>
        @endif
      </div>
      @if($icon)
      <div class="avatar">
        <i class="bx {{ $icon }} bx-lg"></i>
      </div>
      @endif
    </{{ $Tag }}>
  </div>
</div>
