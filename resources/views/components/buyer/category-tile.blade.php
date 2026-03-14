@props([
  'name',
  'icon' => 'bx-package',
  'count' => null,
  'href' => '#',
])

<a href="{{ $href }}" class="hanzo-category-tile text-decoration-none text-body">
  <div class="tile-icon">
    <i class="bx {{ $icon }} bx-lg"></i>
  </div>
  <div>
    <span class="fw-600 d-block">{{ $name }}</span>
    @if($count !== null && $count > 0)
    <small class="text-muted">{{ $count }} {{ __('requests') }}</small>
    @endif
  </div>
  <i class="bx bx-chevron-right ms-auto text-muted"></i>
</a>
