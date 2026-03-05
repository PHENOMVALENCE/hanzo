@extends('layouts.factory')

@section('title', 'Order ' . $order->order_code)

@section('content')
<h4 class="fw-bold mb-4">Order {{ $order->order_code }}</h4>
<div class="card">
  <div class="card-body">
    <p><strong>Status:</strong> <span class="badge bg-label-info">{{ $order->milestone_status }}</span></p>
    <p><strong>Tracking:</strong> {{ $order->tracking_number ?? '-' }}</p>
    <p><strong>Est. Arrival:</strong> {{ $order->estimated_arrival?->format('Y-m-d') ?? '-' }}</p>
  </div>
</div>
<a href="{{ route('factory.orders.index') }}" class="btn btn-outline-secondary">Back</a>
@endsection
