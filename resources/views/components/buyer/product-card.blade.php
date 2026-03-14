@props([
  'name',
  'image' => null,
  'moq' => null,
  'priceRange' => null,
  'supplierName' => null,
  'verified' => false,
  'leadTime' => null,
  'href' => '#',
  'rfqHref' => null,
])

<div class="hanzo-product-card card h-100">
  <a href="{{ $href }}" class="text-decoration-none text-body">
    <div class="product-image">
      @if($image)
      <img src="{{ $image }}" alt="{{ $name }}">
      @else
      <span class="placeholder"><i class="bx bx-package"></i></span>
      @endif
    </div>
    <div class="card-body p-3">
      <h6 class="card-title mb-1 text-body fw-600" style="font-size: 0.95rem;">{{ Str::limit($name, 45) }}</h6>
      @if($moq)
      <p class="small text-muted mb-1">MOQ: {{ $moq }}</p>
      @endif
      @if($priceRange)
      <p class="small text-muted mb-1">{{ $priceRange }}</p>
      @endif
      <div class="d-flex align-items-center justify-content-between gap-1 flex-wrap">
        @if($supplierName)
        <span class="small text-muted">{{ Str::limit($supplierName, 20) }}</span>
        @endif
        @if($verified)
        <x-buyer.verified-badge />
        @endif
      </div>
      @if($leadTime)
      <p class="small text-muted mb-0 mt-1"><i class="bx bx-time-five me-1"></i> {{ $leadTime }}</p>
      @endif
    </div>
  </a>
  <div class="card-footer bg-transparent border-top pt-2 pb-2 px-3 d-flex gap-2">
    <a href="{{ $rfqHref ?? $href }}" class="btn btn-rfq btn-sm flex-grow-1">
      <i class="bx bx-file me-1"></i> {{ __('Request Quote') }}
    </a>
    <button type="button" class="btn btn-outline-secondary btn-sm" title="{{ __('Save') }}"><i class="bx bx-bookmark"></i></button>
  </div>
</div>
