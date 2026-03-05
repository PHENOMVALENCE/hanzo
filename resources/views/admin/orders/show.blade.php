@extends('layouts.admin')

@section('title', 'Order ' . $order->order_code)

@section('content')
<h4 class="fw-bold mb-4">Order {{ $order->order_code }}</h4>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="row">
  <div class="col-md-8">
    <div class="card mb-4">
      <div class="card-body">
        <p><strong>Buyer:</strong> {{ $order->buyer->name }}</p>
        <p><strong>Status:</strong> <span class="badge bg-label-info">{{ $order->milestone_status }}</span></p>
        <p><strong>Tracking:</strong> {{ $order->tracking_number ?? '-' }}</p>
        <p><strong>Est. Arrival:</strong> {{ $order->estimated_arrival?->format('Y-m-d') ?? '-' }}</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-header">Update Milestone</div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.orders.updateMilestone', $order) }}">
          @csrf
          <div class="mb-3">
            <select name="milestone_status" class="form-select" required>
              <option value="deposit_pending" {{ $order->milestone_status === 'deposit_pending' ? 'selected' : '' }}>Deposit Pending</option>
              <option value="deposit_paid" {{ $order->milestone_status === 'deposit_paid' ? 'selected' : '' }}>Deposit Paid</option>
              <option value="in_production" {{ $order->milestone_status === 'in_production' ? 'selected' : '' }}>In Production</option>
              <option value="quality_control" {{ $order->milestone_status === 'quality_control' ? 'selected' : '' }}>Quality Control</option>
              <option value="shipped" {{ $order->milestone_status === 'shipped' ? 'selected' : '' }}>Shipped</option>
              <option value="in_customs" {{ $order->milestone_status === 'in_customs' ? 'selected' : '' }}>In Customs</option>
              <option value="delivered" {{ $order->milestone_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
            </select>
          </div>
          <div class="mb-3">
            <input type="text" name="tracking_number" class="form-control" placeholder="Tracking #" value="{{ $order->tracking_number }}">
          </div>
          <div class="mb-3">
            <input type="date" name="estimated_arrival" class="form-control" value="{{ $order->estimated_arrival?->format('Y-m-d') }}">
          </div>
          <button type="submit" class="btn btn-primary">Update</button>
        </form>
      </div>
    </div>
  </div>
</div>
<a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Back</a>
@endsection
