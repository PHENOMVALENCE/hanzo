@extends('layouts.buyer')

@section('title', __('buyer.orders.title') . ' ' . $order->order_code)

@section('content')
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card mb-4 overflow-hidden">
  <div class="card-header text-white py-4" style="background: linear-gradient(135deg, #0B1F3A 0%, #123A6D 100%) !important; border: none;">
    <h4 class="mb-0">Order #{{ $order->order_code }}</h4>
    <p class="mb-0 opacity-75 small">{{ $order->displayName() }}</p>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-lg-8">
        <h5 class="mb-3">Order Timeline</h5>
        <x-order-timeline :order="$order" />
        <div class="row g-2">
          <div class="col-md-6">
            <p class="mb-1"><strong>Status</strong></p>
            <span class="badge bg-label-info">{{ trans_status($order->milestone_status) }}</span>
          </div>
          <div class="col-md-6">
            <p class="mb-1"><strong>Tracking</strong></p>
            <span>{{ $order->tracking_number ?? '—' }}</span>
          </div>
          <div class="col-md-6">
            <p class="mb-1"><strong>Est. Arrival</strong></p>
            <span>{{ $order->estimated_arrival?->format('M j, Y') ?? '—' }}</span>
          </div>
          @php
            $totalOrder = (float) ($order->quotation->total_landed_cost ?? 0);
            $paid = $order->payments()->where('status', 'verified')->sum('amount_usd');
            $remaining = max(0, $totalOrder - $paid);
          @endphp
          <div class="col-md-6">
            <p class="mb-1"><strong>Total</strong></p>
            <span class="fw-semibold">{{ money($totalOrder) }}</span>
          </div>
          <div class="col-md-6">
            <p class="mb-1"><strong>Amount Paid</strong></p>
            <span class="text-success fw-semibold">{{ money($paid) }}</span>
          </div>
          <div class="col-md-6">
            <p class="mb-1"><strong>Amount Pending</strong></p>
            <span class="text-warning fw-semibold">{{ money($remaining) }}</span>
          </div>
        </div>
        @php $verifiedPayments = $order->payments()->where('status', 'verified')->get(); $pendingPayments = $order->payments()->where('status', 'pending')->get(); @endphp
        @if($verifiedPayments->isNotEmpty() || $pendingPayments->isNotEmpty())
        <hr class="my-3">
        <p class="mb-2"><strong>Payment History</strong></p>
        <ul class="list-unstyled small mb-0">
          @foreach($order->payments as $p)
          <li class="d-flex justify-content-between py-1">
            <span>{{ \App\Models\Payment::TYPES[$p->type] ?? $p->type }} — {{ money($p->amount_usd) }}</span>
            <span class="badge bg-{{ $p->status === 'verified' ? 'success' : ($p->status === 'rejected' ? 'danger' : 'warning') }}">{{ trans_status($p->status) }}</span>
          </li>
          @if($p->status === 'rejected' && $p->rejection_reason)
          <li class="text-danger small mb-1">{{ $p->rejection_reason }}</li>
          @endif
          @endforeach
        </ul>
        @endif
      </div>
      <div class="col-lg-4">
        <div class="d-grid gap-2">
          @if($remaining > 0 && !in_array($order->milestone_status, ['completed']))
          <a href="{{ route('buyer.payments.create', $order) }}" class="btn btn-primary">Upload Payment Proof</a>
          <p class="text-muted small mb-0">Submit payment proof for verification. Admin will verify before order proceeds.</p>
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
