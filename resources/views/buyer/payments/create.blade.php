@extends('layouts.buyer')

@section('title', 'Pay Deposit')

@section('content')
<h4 class="fw-bold mb-4">Pay Deposit - Order {{ $order->order_code }}</h4>
<form method="POST" action="{{ route('buyer.payments.store', $order) }}" enctype="multipart/form-data">
  @csrf
  <div class="card mb-4">
    <div class="card-body">
      <p>Total: ${{ number_format($order->quotation->total_landed_cost ?? 0, 2) }}</p>
      <p class="text-muted">Typically 30% deposit. Enter amount and upload proof.</p>
      <div class="mb-3">
        <label class="form-label">Amount (USD) *</label>
        <input type="number" step="0.01" name="amount_usd" class="form-control" required value="{{ old('amount_usd') }}">
      </div>
      <div class="mb-3">
        <label class="form-label">Payment Method</label>
        <input type="text" name="method" class="form-control" placeholder="e.g. Bank Transfer, PayPal" value="{{ old('method') }}">
      </div>
      <div class="mb-3">
        <label class="form-label">Reference</label>
        <input type="text" name="reference" class="form-control" placeholder="Transaction ID" value="{{ old('reference') }}">
      </div>
      <div class="mb-3">
        <label class="form-label">Proof of Payment *</label>
        <input type="file" name="proof" class="form-control" required accept="image/*,.pdf">
      </div>
      <input type="hidden" name="type" value="deposit">
    </div>
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
  <a href="{{ route('buyer.orders.show', $order) }}" class="btn btn-outline-secondary">Cancel</a>
</form>
@endsection
