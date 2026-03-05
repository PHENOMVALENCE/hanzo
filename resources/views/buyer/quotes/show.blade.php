@extends('layouts.buyer')

@section('title', 'Quote ' . $quotation->quote_code)

@section('content')
<h4 class="fw-bold mb-4">Quote {{ $quotation->quote_code }}</h4>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif
<div class="card mb-4">
  <div class="card-header">
    <span class="badge bg-{{ $quotation->status === 'sent' ? 'info' : ($quotation->status === 'accepted' ? 'success' : 'secondary') }}">{{ $quotation->status }}</span>
    RFQ: {{ $quotation->rfq->code }} | Valid until: {{ $quotation->valid_until?->format('Y-m-d') }}
  </div>
  <div class="card-body">
    <table class="table table-sm">
      <tr><td>Product Cost</td><td>${{ number_format($quotation->product_cost_usd, 2) }}</td></tr>
      <tr><td>China Local Shipping</td><td>${{ number_format($quotation->china_local_shipping, 2) }}</td></tr>
      <tr><td>Export Handling</td><td>${{ number_format($quotation->export_handling, 2) }}</td></tr>
      <tr><td>Freight Cost</td><td>${{ number_format($quotation->freight_cost, 2) }}</td></tr>
      <tr><td>Insurance</td><td>${{ number_format($quotation->insurance_cost, 2) }}</td></tr>
      <tr><td>Clearing</td><td>${{ number_format($quotation->clearing_cost, 2) }}</td></tr>
      <tr><td>Local Delivery</td><td>${{ number_format($quotation->local_delivery_cost, 2) }}</td></tr>
      <tr><td>HANZO Fee</td><td>${{ number_format($quotation->hanzo_fee, 2) }}</td></tr>
      <tr><th>Total Landed Cost</th><th>${{ number_format($quotation->total_landed_cost, 2) }}</th></tr>
    </table>
    @if($quotation->status === 'sent')
    <form method="POST" action="{{ route('buyer.quotes.accept', $quotation) }}" class="d-inline">
      @csrf
      <button type="submit" class="btn btn-success">Accept Quote</button>
    </form>
    <form method="POST" action="{{ route('buyer.quotes.reject', $quotation) }}" class="d-inline" onsubmit="return confirm('Reject this quote?')">
      @csrf
      <button type="submit" class="btn btn-danger">Reject</button>
    </form>
    @endif
  </div>
</div>
<a href="{{ route('buyer.quotes.index') }}" class="btn btn-outline-secondary">Back</a>
@endsection
