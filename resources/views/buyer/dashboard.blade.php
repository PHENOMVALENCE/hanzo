@extends('layouts.buyer')

@section('title', __('buyer.dashboard.title'))

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
        <button type="button" class="btn btn-rfq" id="welcomeGuideGotIt">Got it</button>
      </div>
    </div>
  </div>
</div>
@push('page-js')
<script>
(function() {
  function closeWelcome() {
    var el = document.getElementById('welcomeGuideModal');
    if (el) el.style.display = 'none';
  }
  document.getElementById('welcomeGuideClose')?.addEventListener('click', closeWelcome);
  document.getElementById('welcomeGuideGotIt')?.addEventListener('click', closeWelcome);
})();
</script>
@endpush
@endif

{{-- Welcome Banner --}}
<div class="card mb-4 hanzo-welcome-banner">
  <div class="card-body py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <h4 class="mb-1">{{ __('buyer.dashboard.welcome', ['name' => auth()->user()->name]) }}</h4>
        <p class="text-muted mb-0">{{ auth()->user()->company_name ?? auth()->user()->email }}</p>
        <p class="small text-muted mt-1">{{ __('buyer.dashboard.description') }}</p>
      </div>
      <a href="{{ route('buyer.rfqs.create') }}" class="btn btn-rfq">
        <i class="bx bx-plus me-2"></i> {{ __('Request Quote') }}
      </a>
    </div>
  </div>
</div>

@php
  $rfqCount = \App\Models\Rfq::where('buyer_id', auth()->id())->count();
  $quoteCount = \App\Models\Quotation::whereHas('rfq', fn($q) => $q->where('buyer_id', auth()->id()))->where('status', 'sent')->count();
  $orderCount = \App\Models\Order::where('buyer_id', auth()->id())->count();
  $ordersInTransit = \App\Models\Order::where('buyer_id', auth()->id())->where('milestone_status', 'shipped')->count();
  $recentQuotes = \App\Models\Quotation::whereHas('rfq', fn($q) => $q->where('buyer_id', auth()->id()))->with('rfq.category')->latest()->take(5)->get();
  $trending = $trending ?? collect();
  $popular = $popular ?? collect();
  $recommended = $recommended ?? collect();
@endphp

{{-- Statistics Cards --}}
<div class="row g-3 mb-4">
  <div class="col-xl-3 col-md-6">
    <x-buyer.stat-card label="{{ __('buyer.dashboard.orders') }}" :value="$orderCount" icon="bx-package" :href="route('buyer.orders.index')" />
  </div>
  <div class="col-xl-3 col-md-6">
    <x-buyer.stat-card label="{{ __('Active Quotations') }}" :value="$quoteCount" icon="bx-list-check" :href="route('buyer.quotes.index')" />
  </div>
  <div class="col-xl-3 col-md-6">
    <x-buyer.stat-card label="{{ __('In Transit') }}" :value="$ordersInTransit" icon="bx-send" :href="route('buyer.orders.index')" />
  </div>
  <div class="col-xl-3 col-md-6">
    <x-buyer.stat-card label="{{ __('Saved Suppliers') }}" :value="0" icon="bx-bookmark" :href="route('buyer.saved.index')" />
  </div>
</div>

{{-- Catalog Sections --}}
<div class="mb-4">
  <h5 class="mb-1">{{ __('Trending') }}</h5>
  <p class="text-muted small mb-3">{{ __('Most requested categories this month') }}</p>
  @if($trending->isNotEmpty())
  <div class="hanzo-carousel mb-4">
    @foreach($trending as $cat)
    <div class="hanzo-carousel-item">
      <x-buyer.product-card
        :name="trans_category($cat)"
        :moq="$cat->moq_default ? number_format($cat->moq_default) . ' units' : null"
        :priceRange="($cat->price_min_per_unit && $cat->price_max_per_unit) ? '$' . number_format($cat->price_min_per_unit, 0) . ' - $' . number_format($cat->price_max_per_unit, 0) . ' / unit' : null"
        :href="route('buyer.catalog.show', $cat)"
        :rfq-href="route('buyer.rfqs.create') . '?category=' . $cat->id"
        supplierName="HANZO Verified"
        :verified="true"
      />
    </div>
    @endforeach
  </div>
  @else
  <div class="card mb-4">
    <div class="card-body py-4 text-center">
      <p class="text-muted mb-0">{{ __('No trending categories yet.') }}</p>
      <a href="{{ route('buyer.catalog.index') }}" class="btn btn-b2b-primary btn-sm mt-2">{{ __('Browse Catalog') }}</a>
    </div>
  </div>
  @endif
</div>

<div class="row g-4 mb-4">
  <div class="col-lg-8">
    {{-- Popular Categories --}}
    <x-buyer.widget title="{{ __('Popular Categories') }}" :footer-href="route('buyer.catalog.index')" footer-text="{{ __('View all') }}">
      @if($popular->isNotEmpty())
      <div class="row g-3">
        @foreach($popular as $cat)
        <div class="col-md-4 col-6">
          <x-buyer.category-tile :name="trans_category($cat)" icon="bx-package" :href="route('buyer.catalog.show', $cat)" :count="$cat->rfqs_count ?? $cat->rfq_count ?? 0" />
        </div>
        @endforeach
      </div>
      @else
      <p class="text-muted small mb-0">{{ __('No categories yet.') }} <a href="{{ route('buyer.rfqs.create') }}">{{ __('Request a quote') }}</a></p>
      @endif
    </x-buyer.widget>

    {{-- Recommended for You --}}
    <x-buyer.widget title="{{ __('Recommended for You') }}" :footer-href="route('buyer.catalog.index')" footer-text="{{ __('Browse catalog') }}" class="mt-4">
      @if($recommended->isNotEmpty())
      <div class="row g-3">
        @foreach($recommended as $cat)
        <div class="col-md-6 col-xl-4">
          <x-buyer.product-card
            :name="trans_category($cat)"
            :moq="$cat->moq_default ? number_format($cat->moq_default) . ' units' : null"
            :priceRange="($cat->price_min_per_unit && $cat->price_max_per_unit) ? '$' . number_format($cat->price_min_per_unit, 0) . ' - $' . number_format($cat->price_max_per_unit, 0) . ' / unit' : null"
            :href="route('buyer.catalog.show', $cat)"
            :rfq-href="route('buyer.rfqs.create') . '?category=' . $cat->id"
            supplierName="HANZO Verified"
            :verified="true"
          />
        </div>
        @endforeach
      </div>
      @else
      <p class="text-muted small mb-0">{{ __('Browse the full catalog to discover products.') }}</p>
      <a href="{{ route('buyer.catalog.index') }}" class="btn btn-b2b-primary btn-sm mt-2">{{ __('Product Catalog') }}</a>
      @endif
    </x-buyer.widget>
  </div>

  <div class="col-lg-4">
    {{-- Announcements --}}
    <x-buyer.widget title="{{ __('Announcements') }}" :footer-href="route('notifications.index')" footer-text="{{ __('View all') }}">
      <ul class="list-unstyled mb-0">
        <li class="py-2 border-bottom border-light">
          <small class="text-muted">{{ now()->format('M d') }}</small>
          <p class="mb-0 small">{{ __('Platform updates: New RFQ workflow and faster quote responses.') }}</p>
        </li>
        <li class="py-2">
          <small class="text-muted">{{ now()->subDays(2)->format('M d') }}</small>
          <p class="mb-0 small">{{ __('All supplier communications now go through HANZO messaging.') }}</p>
        </li>
      </ul>
    </x-buyer.widget>

    {{-- RFQ Tracker --}}
    <x-buyer.widget title="{{ __('RFQ Tracker') }}" :footer-href="route('buyer.rfqs.index')" footer-text="{{ __('View all') }}">
      <div class="d-flex justify-content-between mb-2">
        <span class="small text-muted">{{ __('buyer.dashboard.my_rfqs') }}</span>
        <span class="fw-600">{{ $rfqCount }}</span>
      </div>
      <div class="d-flex justify-content-between mb-2">
        <span class="small text-muted">{{ __('Quotes Pending') }}</span>
        <span class="fw-600">{{ $quoteCount }}</span>
      </div>
      <div class="d-flex justify-content-between">
        <span class="small text-muted">{{ __('Orders') }}</span>
        <span class="fw-600">{{ $orderCount }}</span>
      </div>
      <a href="{{ route('buyer.rfqs.create') }}" class="btn btn-rfq w-100 mt-3">{{ __('Create RFQ') }}</a>
    </x-buyer.widget>

    {{-- Recent Quotes --}}
    <x-buyer.widget title="{{ __('Recent Quotes') }}" :footer-href="route('buyer.quotes.index')" footer-text="{{ __('View all') }}" class="mt-4">
      @if($recentQuotes->isEmpty())
      <p class="text-muted small mb-0">{{ __('No quotes yet.') }} <a href="{{ route('buyer.rfqs.create') }}">{{ __('Submit a request') }}</a></p>
      @else
      <ul class="list-unstyled mb-0">
        @foreach($recentQuotes as $q)
        <li class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
          <span class="small">{{ trans_category($q->rfq->category ?? null) ?: '-' }} — ${{ number_format($q->total_landed_cost ?? 0, 0) }}</span>
          <a href="{{ route('buyer.quotes.show', $q) }}" class="badge bg-{{ $q->status === 'sent' ? 'info' : ($q->status === 'accepted' ? 'success' : 'secondary') }} text-decoration-none">{{ trans_status($q->status) }}</a>
        </li>
        @endforeach
      </ul>
      @endif
    </x-buyer.widget>
  </div>
</div>
@endsection
