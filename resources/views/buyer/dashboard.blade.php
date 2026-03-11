@extends('layouts.buyer')

@section('title', __('buyer.dashboard.title'))

@section('content')
@if(!empty($showWelcomeGuide))
<div class="modal fade show d-block" id="welcomeGuideModal" tabindex="-1" style="background: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title">Welcome to HANZO</h5>
        <button type="button" class="btn-close" id="welcomeGuideClose" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Thanks for joining! Here's a quick guide:</p>
        <ul class="mb-0">
          <li><strong>Request a Quote</strong> – Submit your product requirements; we'll match you with factories.</li>
          <li><strong>Review Quotes</strong> – Receive official quotations and accept when ready.</li>
          <li><strong>Track Orders</strong> – Follow your order from production to delivery.</li>
        </ul>
        <p class="mt-3 mb-0 text-muted small">Get started by requesting a quote below.</p>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-primary" id="welcomeGuideGotIt">Got it</button>
      </div>
    </div>
  </div>
</div>
@push('page-js')
<script>
(function() {
  function closeWelcome() {
    var el = document.getElementById('welcomeGuideModal');
    if (el) { el.style.display = 'none'; }
  }
  document.getElementById('welcomeGuideClose')?.addEventListener('click', closeWelcome);
  document.getElementById('welcomeGuideGotIt')?.addEventListener('click', closeWelcome);
})();
</script>
@endpush
@endif
<div class="row">
  <div class="col-12">
    <h4 class="fw-bold mb-2">{{ __('buyer.dashboard.title') }}</h4>
    <p class="text-muted mb-4">{{ __('buyer.dashboard.welcome', ['name' => auth()->user()->name]) }} {{ __('buyer.dashboard.description') }}</p>
  </div>
</div>

@php
  $rfqCount = \App\Models\Rfq::where('buyer_id', auth()->id())->count();
  $quoteCount = \App\Models\Quotation::whereHas('rfq', fn($q) => $q->where('buyer_id', auth()->id()))->where('status', 'sent')->count();
  $orderCount = \App\Models\Order::where('buyer_id', auth()->id())->count();
  $ordersInProduction = \App\Models\Order::where('buyer_id', auth()->id())->whereIn('milestone_status', ['deposit_paid','in_production'])->count();
  $ordersInTransit = \App\Models\Order::where('buyer_id', auth()->id())->where('milestone_status', 'shipped')->count();
  $ordersDelivered = \App\Models\Order::where('buyer_id', auth()->id())->where('milestone_status', 'delivered')->count();
  $recentQuotes = \App\Models\Quotation::whereHas('rfq', fn($q) => $q->where('buyer_id', auth()->id()))->with('rfq.category')->latest()->take(5)->get();
@endphp

<div class="row mb-4">
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card hanzo-stat-card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <span class="d-block text-muted small text-uppercase letter-spacing">{{ __('buyer.dashboard.my_rfqs') }}</span>
          <h3 class="mb-0 mt-1">{{ $rfqCount }}</h3>
          <a href="{{ route('buyer.rfqs.index') }}" class="btn btn-sm btn-outline-primary mt-2">{{ __('buyer.dashboard.view_rfqs') }}</a>
        </div>
        <span class="avatar avatar-lg rounded bg-label-primary"><i class="bx bx-file bx-lg"></i></span>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card hanzo-stat-card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <span class="d-block text-muted small text-uppercase letter-spacing">{{ __('buyer.dashboard.quotes') }}</span>
          <h3 class="mb-0 mt-1">{{ $quoteCount }}</h3>
          <a href="{{ route('buyer.quotes.index') }}" class="btn btn-sm btn-outline-info mt-2">{{ __('buyer.dashboard.view_quotes') }}</a>
        </div>
        <span class="avatar avatar-lg rounded bg-label-info"><i class="bx bx-list-check bx-lg"></i></span>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card hanzo-stat-card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <span class="d-block text-muted small text-uppercase letter-spacing">{{ __('buyer.dashboard.orders') }}</span>
          <h3 class="mb-0 mt-1">{{ $orderCount }}</h3>
          <a href="{{ route('buyer.orders.index') }}" class="btn btn-sm btn-outline-success mt-2">{{ __('buyer.dashboard.view_orders') }}</a>
        </div>
        <span class="avatar avatar-lg rounded bg-label-success"><i class="bx bx-package bx-lg"></i></span>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card card-verified h-100">
      <div class="card-header">
        <h5 class="mb-0">My Account</h5>
      </div>
      <div class="card-body">
        <p class="text-muted small mb-2">Manage your profile, photo, and settings.</p>
        <a href="{{ route('profile.edit') }}" class="btn btn-primary w-100">
          <i class="bx bx-user me-2"></i> Account Settings
        </a>
        <a href="{{ route('buyer.rfqs.create') }}" class="btn btn-warning w-100 mt-2 text-dark">
          <i class="bx bx-plus me-2"></i> Request A Quote
        </a>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Order Summary</h5>
        <a href="{{ route('buyer.orders.index') }}" class="btn btn-sm btn-link">View all</a>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-4">
            <div class="hanzo-stat-box p-3 rounded bg-label-warning text-center">
              <span class="d-block fw-bold fs-4">{{ $ordersInProduction }}</span>
              <small class="text-muted">In Production</small>
            </div>
          </div>
          <div class="col-4">
            <div class="hanzo-stat-box p-3 rounded bg-label-info text-center">
              <span class="d-block fw-bold fs-4">{{ $ordersInTransit }}</span>
              <small class="text-muted">In Transit</small>
            </div>
          </div>
          <div class="col-4">
            <div class="hanzo-stat-box p-3 rounded bg-label-success text-center">
              <span class="d-block fw-bold fs-4">{{ $ordersDelivered }}</span>
              <small class="text-muted">Delivered</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Recent Quotes</h5>
        <a href="{{ route('buyer.quotes.index') }}" class="btn btn-sm btn-link">View all</a>
      </div>
      <div class="card-body">
        @if($recentQuotes->isEmpty())
          <p class="text-muted mb-0 small">No quotes yet. Submit a request to get started.</p>
        @else
          <ul class="list-unstyled mb-0">
            @foreach($recentQuotes as $q)
            <li class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
              <span>{{ trans_category($q->rfq->category ?? null) ?: __('labels.order') }} — ${{ number_format($q->total_landed_cost ?? 0, 0) }}</span>
              <a href="{{ route('buyer.quotes.show', $q) }}" class="badge bg-{{ $q->status === 'sent' ? 'info' : ($q->status === 'accepted' ? 'success' : 'secondary') }} text-decoration-none">{{ trans_status($q->status) }}</a>
            </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
