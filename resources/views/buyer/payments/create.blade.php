@extends('layouts.buyer')

@section('title', 'Pay Deposit')

@section('content')
@php
  $total = (float) ($order->quotation->total_landed_cost ?? 0);
  $alreadyPaid = $order->payments()->where('status', 'verified')->sum('amount_usd');
  $remaining = $total - $alreadyPaid;
  $isBalance = request('type') === 'balance';
  $suggestedDeposit = round($total * 0.3, 2);
  $suggestedAmount = $isBalance ? $remaining : min($suggestedDeposit, $remaining);
@endphp
<h4 class="fw-bold mb-4">{{ $isBalance ? 'Pay Balance' : 'Pay Deposit' }} — Order {{ $order->order_code }}</h4>
<form method="POST" action="{{ route('buyer.payments.store', $order) }}" enctype="multipart/form-data">
  @csrf
  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="p-3 rounded bg-light">
            <span class="text-muted small d-block">Total Order Value</span>
            <strong class="fs-4">${{ number_format($total, 2) }}</strong>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-3 rounded bg-light">
            <span class="text-muted small d-block">Typical Deposit (30%)</span>
            <strong class="fs-4">${{ number_format($suggestedDeposit, 2) }}</strong>
          </div>
        </div>
        @if($alreadyPaid > 0)
        <div class="col-md-4">
          <div class="p-3 rounded bg-light">
            <span class="text-muted small d-block">Already Paid</span>
            <strong class="fs-4 text-success">${{ number_format($alreadyPaid, 2) }}</strong>
          </div>
        </div>
        @endif
      </div>
      <hr>
      <div class="mb-3">
        <label class="form-label">Amount (USD) *</label>
        <input type="number" step="0.01" name="amount_usd" class="form-control" required
          value="{{ old('amount_usd', $suggestedAmount > 0 ? $suggestedAmount : $total) }}"
          min="0.01" max="{{ $total }}">
        <small class="text-muted">Enter the amount you are paying now.</small>
      </div>
      <div class="mb-3">
        <label class="form-label">Payment Method *</label>
        <select name="method" class="form-select">
          <option value="">— Select —</option>
          <option value="Bank Transfer" {{ old('method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
          <option value="PayPal" {{ old('method') == 'PayPal' ? 'selected' : '' }}>PayPal</option>
          <option value="M-Pesa" {{ old('method') == 'M-Pesa' ? 'selected' : '' }}>M-Pesa</option>
          <option value="Other" {{ old('method') == 'Other' ? 'selected' : '' }}>Other</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Transaction Reference</label>
        <input type="text" name="reference" class="form-control" placeholder="e.g. TXN123456" value="{{ old('reference') }}">
        <small class="text-muted">Transaction ID or reference from your payment.</small>
      </div>
      <div class="mb-3">
        <label class="form-label">Proof of Payment *</label>
        <input type="file" name="proof" class="form-control" required accept="image/*,.pdf">
        <small class="text-muted">Screenshot or PDF. Max 5MB. Images (JPEG, PNG) or PDF.</small>
        @error('proof')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
      </div>
      <input type="hidden" name="type" value="{{ $isBalance ? 'balance' : 'deposit' }}">
    </div>
  </div>
  <button type="submit" class="btn btn-primary">Submit for Verification</button>
  <a href="{{ route('buyer.orders.show', $order) }}" class="btn btn-outline-secondary">Cancel</a>
</form>
<p class="text-muted small mt-3">HANZO will verify your payment and update the order status. You'll see the order move to "Deposit Paid" once verified.</p>
@endsection
