@props([
  'title',
  'footerHref' => null,
  'footerText' => null,
])

<div class="hanzo-widget card h-100" {{ $attributes }}>
  <div class="widget-header card-header bg-transparent d-flex justify-content-between align-items-center">
    <span>{{ $title }}</span>
    @if($footerHref && $footerText)
    <a href="{{ $footerHref }}" class="btn btn-sm btn-link p-0">{{ $footerText }}</a>
    @endif
  </div>
  <div class="widget-body card-body">
    {{ $slot }}
  </div>
</div>
