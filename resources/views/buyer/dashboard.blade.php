@extends('layouts.buyer')

@section('title', __('buyer.dashboard.title'))

@section('promo-banner')
<div class="buyer-promo-banner">
  <div class="buyer-promo-inner">
    <span class="buyer-promo-title">{{ __('labels.new_buyer_coupon') ?? 'New buyer coupon' }}</span>
    <a href="{{ route('buyer.rfqs.create') }}" class="buyer-promo-cta">{{ __('labels.sign_in_offer') ?? 'Request a quote to get exclusive offers for your first order.' }}</a>
    <span class="buyer-promo-value">{{ __('labels.first_order_discount') ?? 'Special pricing' }} — {{ __('labels.on_orders_over') ?? 'On orders over MOQ' }}</span>
  </div>
</div>
@endsection

@section('content')
@if(!empty($showWelcomeGuide))
<div class="modal fade show d-block" id="welcomeGuideModal" tabindex="-1" style="background: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title">Welcome to HANZO</h5>
        <button type="button" class="btn-close" id="welcomeGuideClose" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Thanks for joining! Here's a quick guide:</p>
        <ul class="mb-0">
          <li><strong>Browse Catalog</strong> – Discover products by category and request quotes from verified suppliers.</li>
          <li><strong>RFQ Center</strong> – Submit product requirements; we'll match you with factories.</li>
          <li><strong>Review Quotes</strong> – Receive official quotations and accept when ready.</li>
          <li><strong>Track Orders</strong> – Follow your order from production to delivery.</li>
        </ul>
        <p class="mt-3 mb-0 text-muted small">Get started by browsing the catalog or requesting a quote.</p>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-hanzo-primary" id="welcomeGuideGotIt">Got it</button>
      </div>
    </div>
  </div>
</div>
@push('page-js')
<script>
document.getElementById('welcomeGuideClose')?.addEventListener('click', function() { document.getElementById('welcomeGuideModal').style.display = 'none'; });
document.getElementById('welcomeGuideGotIt')?.addEventListener('click', function() { document.getElementById('welcomeGuideModal').style.display = 'none'; });
</script>
@endpush
@endif

<h2 class="buyer-section-title">{{ __('labels.recommended_for_you') }}</h2>

@if($featuredProducts->isEmpty())
  <div class="card">
    <div class="card-body text-center py-5">
      <i class="bx bx-package bx-lg text-muted mb-3"></i>
      <p class="text-muted mb-0">{{ __('labels.no_products_yet') }}</p>
      <a href="{{ route('buyer.rfqs.create') }}" class="btn btn-hanzo-primary mt-3">{{ __('labels.request_quote') }}</a>
    </div>
  </div>
@else
  <div class="buyer-product-grid grid-cards mb-5">
    @foreach($featuredProducts as $p)
    <a href="{{ route('buyer.products.show', $p) }}" class="buyer-product-card">
      <div class="buyer-product-card-image">
        @if($p->primaryImage())
          <img src="{{ Storage::url($p->primaryImage()) }}" alt="{{ $p->title }}">
        @else
          <div class="d-flex align-items-center justify-content-center h-100 text-muted"><i class="bx bx-image bx-lg"></i></div>
        @endif
        <span class="lens-icon"><i class="bx bx-search"></i></span>
      </div>
      <div class="buyer-product-card-body">
        <h3 class="buyer-product-card-title">{{ $p->title }}</h3>
        <div class="buyer-product-card-price">{{ $p->priceDisplay() }}</div>
        <div class="buyer-product-card-moq">{{ __('labels.moq') }}: {{ $p->moq ?? '—' }}</div>
      </div>
    </a>
    @endforeach
  </div>
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
    <a href="{{ route('buyer.products.index') }}" class="btn btn-hanzo-primary">{{ __('labels.view_full_catalog') }}</a>
    @php
      $ordersInProduction = \App\Models\Order::where('buyer_id', auth()->id())->whereIn('milestone_status', ['awaiting_factory_approval', 'in_production'])->count();
      $ordersInTransit = \App\Models\Order::where('buyer_id', auth()->id())->where('milestone_status', 'ready_to_ship')->count();
      $ordersDelivered = \App\Models\Order::where('buyer_id', auth()->id())->where('milestone_status', 'completed')->count();
    @endphp
    <div class="d-flex gap-3">
      <span class="text-muted small"><strong>{{ $ordersInProduction }}</strong> In production</span>
      <span class="text-muted small"><strong>{{ $ordersInTransit }}</strong> In transit</span>
      <span class="text-muted small"><strong>{{ $ordersDelivered }}</strong> Delivered</span>
    </div>
  </div>
@endif
@endsection
