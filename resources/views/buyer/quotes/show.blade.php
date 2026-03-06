@extends('layouts.buyer')

@section('title', __('buyer.quotes.title') . ' ' . $quotation->quote_code)

@section('content')
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card mb-4 overflow-hidden">
  <div class="card-header bg-dark text-white py-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(rgba(20,27,45,0.9), rgba(20,27,45,0.95)) !important;">
    <h4 class="mb-0">Quotation #{{ $quotation->quote_code }}</h4>
    @if($quotation->valid_until)
    <span class="badge bg-warning text-dark">Valid for {{ $quotation->valid_until->diffInDays(now()) }} days</span>
    @endif
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-lg-8">
        <div class="hanzo-landed-cost p-4 rounded mb-4">
          <h5 class="mb-3">Cost Breakdown</h5>
          <table class="table table-sm mb-0">
            <tr><td>Product Cost</td><td class="text-end">${{ number_format($quotation->product_cost_usd, 2) }}</td></tr>
            <tr><td>Shipping & Freight</td><td class="text-end">${{ number_format($quotation->freight_cost + $quotation->china_local_shipping + $quotation->export_handling, 2) }}</td></tr>
            <tr><td>Customs & Clearing</td><td class="text-end">${{ number_format($quotation->clearing_cost, 2) }}</td></tr>
            <tr><td>Local Delivery</td><td class="text-end">${{ number_format($quotation->local_delivery_cost, 2) }}</td></tr>
            <tr><td>HANZO Fee</td><td class="text-end">${{ number_format($quotation->hanzo_fee ?? 0, 2) }}</td></tr>
          </table>
          <hr>
          <div class="d-flex justify-content-between align-items-center">
            <strong>Total Landed Cost</strong>
            <span class="fs-4 fw-bold text-primary">${{ number_format($quotation->total_landed_cost, 2) }}</span>
          </div>
        </div>
        <p class="text-muted small mb-0">{{ __('labels.rfq') }}: {{ $quotation->rfq->code }} &bull; {{ __('buyer.quotes.valid_until') }} {{ $quotation->valid_until?->format('M j, Y') }}</p>
      </div>
      <div class="col-lg-4">
        @if($quotation->status === 'sent')
        <div class="d-grid gap-2">
          <form method="POST" action="{{ route('buyer.quotes.accept', $quotation) }}">
            @csrf
            <button type="submit" class="btn btn-warning w-100 text-dark fw-semibold">
              <i class="bx bx-check me-1"></i> Accept Quote
            </button>
          </form>
          <a href="#" class="btn btn-outline-primary w-100">Ask a Question</a>
          <form method="POST" action="{{ route('buyer.quotes.reject', $quotation) }}" class="mt-2" onsubmit="return confirm('Reject this quote?')">
            @csrf
            <button type="submit" class="btn btn-link text-danger p-0 small">Reject Quote</button>
          </form>
        </div>
        @else
        <span class="badge bg-{{ $quotation->status === 'accepted' ? 'success' : 'secondary' }} fs-6">{{ ucfirst($quotation->status) }}</span>
        @endif
      </div>
    </div>
  </div>
</div>

<a href="{{ route('buyer.quotes.index') }}" class="btn btn-outline-secondary">{{ __('buyer.quotes.back') }}</a>
@endsection
