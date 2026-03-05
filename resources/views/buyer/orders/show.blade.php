@extends('layouts.buyer')

@section('title', 'Order ' . $order->order_code)

@section('content')
<h4 class="fw-bold mb-4">Order {{ $order->order_code }}</h4>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="row">
  <div class="col-md-8">
    <div class="card mb-4">
      <div class="card-header">Order Tracking</div>
      <div class="card-body">
        <p><strong>Status:</strong> <span class="badge bg-label-info">{{ $order->milestone_status }}</span></p>
        <p><strong>Tracking:</strong> {{ $order->tracking_number ?? '-' }}</p>
        <p><strong>Est. Arrival:</strong> {{ $order->estimated_arrival?->format('Y-m-d') ?? '-' }}</p>
        <p><strong>Total:</strong> ${{ number_format($order->quotation->total_landed_cost ?? 0, 2) }}</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    @if($order->milestone_status === 'deposit_pending')
    <a href="{{ route('buyer.payments.create', $order) }}" class="btn btn-primary w-100 mb-2">Pay Deposit</a>
    @endif
    <a href="{{ route('buyer.orders.documents', $order) }}" class="btn btn-outline-secondary w-100">Documents</a>
  </div>
</div>
<a href="{{ route('buyer.orders.index') }}" class="btn btn-outline-secondary">Back</a>
@endsection
