@extends('layouts.buyer')

@section('title', __('buyer.orders.title') . ' ' . $order->order_code)

@section('content')
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card mb-4 overflow-hidden">
  <div class="card-header bg-dark text-white py-4" style="background: linear-gradient(rgba(20,27,45,0.9), rgba(20,27,45,0.95)) !important;">
    <h4 class="mb-0">Order #{{ $order->order_code }}</h4>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-lg-8">
        <h5 class="mb-3">Order Tracking</h5>
        @php
          $milestones = ['deposit_pending','deposit_paid','in_production','quality_control','shipped','in_customs','delivered'];
          $current = array_search($order->milestone_status, $milestones);
          if ($current === false) { $current = 0; }
        @endphp
        <div class="hanzo-stepper mb-4">
          @foreach($milestones as $i => $m)
          <div class="step {{ $i < $current ? 'completed' : '' }} {{ $i === $current ? 'active' : '' }}">
            <div class="step-circle">{{ $i + 1 }}</div>
            <div class="small mt-1 text-muted">{{ str_replace('_',' ', ucfirst($m)) }}</div>
          </div>
          @endforeach
        </div>
        <div class="row g-2">
          <div class="col-md-6">
            <p class="mb-1"><strong>Status</strong></p>
            <span class="badge bg-label-info">{{ str_replace('_',' ', ucfirst($order->milestone_status)) }}</span>
          </div>
          <div class="col-md-6">
            <p class="mb-1"><strong>Tracking</strong></p>
            <span>{{ $order->tracking_number ?? '—' }}</span>
          </div>
          <div class="col-md-6">
            <p class="mb-1"><strong>Est. Arrival</strong></p>
            <span>{{ $order->estimated_arrival?->format('M j, Y') ?? '—' }}</span>
          </div>
          <div class="col-md-6">
            <p class="mb-1"><strong>Total</strong></p>
            <span class="fw-semibold">${{ number_format($order->quotation->total_landed_cost ?? 0, 2) }}</span>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="d-grid gap-2">
          @if($order->milestone_status === 'deposit_pending')
          <a href="{{ route('buyer.payments.create', $order) }}" class="btn btn-primary">Pay Deposit</a>
          @endif
          <a href="{{ route('buyer.orders.documents', $order) }}" class="btn btn-outline-primary">
            <i class="bx bx-folder me-1"></i> Documents
          </a>
          @if($order->tracking_number)
          <a href="#" class="btn btn-outline-secondary" title="Track your shipment">
            <i class="bx bx-navigation me-1"></i> Track Shipment
          </a>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<a href="{{ route('buyer.orders.index') }}" class="btn btn-outline-secondary">{{ __('buyer.orders.back') }}</a>
@endsection
