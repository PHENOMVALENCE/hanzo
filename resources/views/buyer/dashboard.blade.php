@extends('layouts.buyer')

@section('title', 'Buyer Dashboard')

@section('content')
<div class="row">
  <div class="col-12">
    <h4 class="fw-bold mb-4">Buyer Dashboard</h4>
    <p class="text-muted">Welcome, {{ auth()->user()->name }}. Manage your RFQs, quotes, and orders.</p>
  </div>
</div>
<div class="row">
  <div class="col-lg-4 mb-4">
    <div class="card">
      <div class="card-body">
        <span class="d-block text-muted">My RFQs</span>
        <h3 class="mb-2">{{ auth()->user()->id ? \App\Models\Rfq::where('buyer_id', auth()->id())->count() : 0 }}</h3>
        <a href="{{ route('buyer.rfqs.index') }}" class="btn btn-sm btn-primary">View RFQs</a>
      </div>
    </div>
  </div>
  <div class="col-lg-4 mb-4">
    <div class="card">
      <div class="card-body">
        <span class="d-block text-muted">Quotes</span>
        <h3 class="mb-2">{{ \App\Models\Quotation::whereHas('rfq', fn($q) => $q->where('buyer_id', auth()->id()))->where('status', 'sent')->count() }}</h3>
        <a href="{{ route('buyer.quotes.index') }}" class="btn btn-sm btn-info">View Quotes</a>
      </div>
    </div>
  </div>
  <div class="col-lg-4 mb-4">
    <div class="card">
      <div class="card-body">
        <span class="d-block text-muted">Orders</span>
        <h3 class="mb-2">{{ \App\Models\Order::where('buyer_id', auth()->id())->count() }}</h3>
        <a href="{{ route('buyer.orders.index') }}" class="btn btn-sm btn-success">View Orders</a>
      </div>
    </div>
  </div>
</div>
@endsection
