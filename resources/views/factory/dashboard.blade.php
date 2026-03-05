@extends('layouts.factory')

@section('title', 'Factory Dashboard')

@section('content')
<div class="row">
  <div class="col-12">
    <h4 class="fw-bold mb-4">Factory Dashboard</h4>
    <p class="text-muted">Welcome. View assigned RFQs and orders.</p>
  </div>
</div>
@php
  $factory = auth()->user()->factory;
  $rfqCount = $factory ? \App\Models\Rfq::where('assigned_factory_id', $factory->id)->whereIn('status', ['assigned','pricing_received'])->count() : 0;
  $orderCount = $factory ? \App\Models\Order::whereHas('quotation.rfq', fn($q) => $q->where('assigned_factory_id', $factory->id))->count() : 0;
@endphp
<div class="row">
  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-body">
        <span class="d-block text-muted">Assigned RFQs</span>
        <h3 class="mb-2">{{ $rfqCount }}</h3>
        <a href="{{ route('factory.rfqs.index') }}" class="btn btn-sm btn-primary">View RFQs</a>
      </div>
    </div>
  </div>
  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-body">
        <span class="d-block text-muted">Orders</span>
        <h3 class="mb-2">{{ $orderCount }}</h3>
        <a href="{{ route('factory.orders.index') }}" class="btn btn-sm btn-success">View Orders</a>
      </div>
    </div>
  </div>
</div>
@endsection
