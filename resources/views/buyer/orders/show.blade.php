@extends('layouts.buyer')

@section('title', __('buyer.orders.title') . ' ' . $order->order_code)

@section('content')
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card mb-4 overflow-hidden">
  <div class="card-header text-white py-4" style="background: linear-gradient(135deg, #0f172a 0%, #0d9488 100%) !important; border: none;">
    <h4 class="mb-0">Order #{{ $order->order_code }}</h4>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-lg-8">
        <h5 class="mb-3">Order Tracking</h5>
        @php
          $milestones = [
            'deposit_pending' => 'Quote Accepted',
            'deposit_paid' => 'Deposit Paid',
            'in_production' => 'In Production',
            'quality_control' => 'Quality Control',
            'shipped' => 'Shipped',
            'in_customs' => 'In Customs',
            'delivered' => 'Delivered',
          ];
          $orderMilestones = array_keys($milestones);
          $current = array_search($order->milestone_status, $orderMilestones);
          if ($current === false) { $current = 0; }
        @endphp
        <div class="hanzo-stepper mb-4">
          @foreach($orderMilestones as $i => $m)
          <div class="step {{ $i < $current ? 'completed' : '' }} {{ $i === $current ? 'active' : '' }}">
            <div class="step-circle">{{ $i + 1 }}</div>
            <div class="small mt-1 {{ $i <= $current ? 'text-body' : 'text-muted' }}">{{ $milestones[$m] }}</div>
          </div>
          @endforeach
        </div>
        <div class="row g-2">
          <div class="col-md-6">
            <p class="mb-1"><strong>Status</strong></p>
            <span class="badge bg-label-info">{{ $milestones[$order->milestone_status] ?? $order->milestone_status }}</span>
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
        @php $verifiedPayments = $order->payments()->where('status', 'verified')->get(); $pendingPayments = $order->payments()->where('status', 'pending')->get(); @endphp
        @if($verifiedPayments->isNotEmpty() || $pendingPayments->isNotEmpty())
        <hr class="my-3">
        <p class="mb-2"><strong>Payment History</strong></p>
        <ul class="list-unstyled small mb-0">
          @foreach($order->payments as $p)
          <li class="d-flex justify-content-between py-1">
            <span>{{ \App\Models\Payment::TYPES[$p->type] ?? $p->type }} — ${{ number_format($p->amount_usd, 2) }}</span>
            <span class="badge bg-{{ $p->status === 'verified' ? 'success' : ($p->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($p->status) }}</span>
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
          @php $totalOrder = (float)($order->quotation->total_landed_cost ?? 0); $paid = $order->payments()->where('status','verified')->sum('amount_usd'); $remaining = $totalOrder - $paid; @endphp
          @if($order->milestone_status === 'deposit_pending' && $remaining > 0)
          <a href="{{ route('buyer.payments.create', $order) }}" class="btn btn-primary">Pay Deposit</a>
          <p class="text-muted small mb-0">Submit payment proof for verification. Typically 30% deposit.</p>
          @elseif($remaining > 0 && in_array($order->milestone_status, ['deposit_paid','in_production','quality_control','shipped','in_customs']))
          <a href="{{ route('buyer.payments.create', $order) }}?type=balance" class="btn btn-outline-primary">Pay Balance (${{ number_format($remaining, 2) }})</a>
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
