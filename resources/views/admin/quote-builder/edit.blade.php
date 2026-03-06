@extends('layouts.admin')

@section('title', __('labels.quote_builder') . ' - ' . $rfq->code)

@section('content')
<h4 class="fw-bold mb-3">{{ __('labels.quote_builder') }} — {{ $rfq->code }}</h4>
@if(session('success'))
  <div class="alert alert-success alert-dismissible py-2">{{ session('success') }}<button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button></div>
@endif

{{-- Product Request Summary - compact --}}
<div class="card mb-3 shadow-sm">
  <div class="card-header py-2 px-3">
    <i class="bx bx-info-circle me-2"></i>
    <strong>{{ __('labels.product_request') }} Summary</strong>
  </div>
  <div class="card-body py-2 px-3">
    <div class="row g-2 g-md-2 row-cols-2 row-cols-sm-3 row-cols-lg-4">
      <div><span class="text-muted small">{{ __('labels.request_code') }}:</span> <strong>{{ $rfq->code }}</strong></div>
      <div><span class="text-muted small">{{ __('labels.buyer') }}:</span> {{ $rfq->buyer?->name ?? '-' }}</div>
      <div><span class="text-muted small">{{ __('labels.category') }}:</span> {{ $rfq->category?->name ?? '-' }}</div>
      <div><span class="text-muted small">{{ __('labels.quantity') }}:</span> {{ number_format($rfq->quantity ?? 0) }} units</div>
      <div class="col-12 col-sm-6 col-lg-8"><span class="text-muted small">{{ __('labels.delivery') }}:</span> {{ trim(($rfq->delivery_city ?? '') . ', ' . ($rfq->delivery_country ?? ''), ', ') ?: '-' }}</div>
    </div>
    @if($rfq->description || $rfq->specs)
    <div class="mt-2 pt-2 border-top">
      @if($rfq->description)
      <p class="mb-0 small"><span class="text-muted">{{ __('labels.description') }}:</span> {{ $rfq->description }}</p>
      @endif
      @if($rfq->specs)
      <p class="mb-0 small"><span class="text-muted">{{ __('labels.specs') }}:</span> {{ $rfq->specs }}</p>
      @endif
    </div>
    @endif
    @if($rfq->factoryQuotes->isNotEmpty())
    <div class="mt-2 pt-2 border-top">
      <p class="text-muted small mb-1"><strong>Price(s) Submitted</strong></p>
      <div class="d-flex flex-column gap-1">
        @foreach($rfq->factoryQuotes->sortByDesc('created_at') as $fq)
        <div class="d-flex flex-wrap align-items-center gap-2 small">
          <span>{{ $fq->factory?->factory_name ?? 'Factory' }}</span>
          <strong>${{ number_format($fq->unit_price_usd, 2) }}/unit</strong>
          @if($fq->moq_confirmed)<span class="text-muted">MOQ: {{ number_format($fq->moq_confirmed) }}</span>@endif
          @if($fq->lead_time_days)<span class="text-muted">Lead: {{ $fq->lead_time_days }}d</span>@endif
          <span class="text-muted">→ Total: ${{ number_format($fq->unit_price_usd * $rfq->quantity, 2) }}</span>
        </div>
        @endforeach
      </div>
      @if($suggestedProductCost)
      <p class="text-success small mb-0 mt-1">Select factory price for Product Cost below.</p>
      @endif
    </div>
    @endif
  </div>
</div>

<form method="POST" action="{{ route('admin.quote-builder.store', $rfq) }}" id="quote-form">
  @csrf
  <div class="card mb-3 shadow-sm">
    <div class="card-header py-2 px-3"><strong>Breakdown</strong></div>
    <div class="card-body py-3 px-3">
      <div class="row g-2 row-cols-1 row-cols-sm-2 row-cols-md-3">
        <div class="col">
          <label class="form-label small mb-1">Product Cost (USD) <span class="text-muted">factory × qty</span></label>
          <input type="number" step="0.01" name="product_cost_usd" class="form-control form-control-sm quote-field" value="{{ old('product_cost_usd', $quotation?->product_cost_usd ?? $suggestedProductCost ?? 0) }}">
        </div>
        <div class="col">
          <label class="form-label small mb-1">China Local Shipping</label>
          <input type="number" step="0.01" name="china_local_shipping" class="form-control form-control-sm quote-field" value="{{ old('china_local_shipping', $quotation?->china_local_shipping ?? 0) }}">
        </div>
        <div class="col">
          <label class="form-label small mb-1">Export Handling</label>
          <input type="number" step="0.01" name="export_handling" class="form-control form-control-sm quote-field" value="{{ old('export_handling', $quotation?->export_handling ?? 0) }}">
        </div>
        <div class="col">
          <label class="form-label small mb-1">Freight Cost</label>
          <input type="number" step="0.01" name="freight_cost" class="form-control form-control-sm quote-field" value="{{ old('freight_cost', $quotation?->freight_cost ?? 0) }}">
        </div>
        <div class="col">
          <label class="form-label small mb-1">Insurance Cost</label>
          <input type="number" step="0.01" name="insurance_cost" class="form-control form-control-sm quote-field" value="{{ old('insurance_cost', $quotation?->insurance_cost ?? 0) }}">
        </div>
        <div class="col">
          <label class="form-label small mb-1">Clearing Cost</label>
          <input type="number" step="0.01" name="clearing_cost" class="form-control form-control-sm quote-field" value="{{ old('clearing_cost', $quotation?->clearing_cost ?? 0) }}">
        </div>
        <div class="col">
          <label class="form-label small mb-1">Local Delivery Cost</label>
          <input type="number" step="0.01" name="local_delivery_cost" class="form-control form-control-sm quote-field" value="{{ old('local_delivery_cost', $quotation?->local_delivery_cost ?? 0) }}">
        </div>
        <div class="col">
          <label class="form-label small mb-1">HANZO Fee</label>
          <input type="number" step="0.01" name="hanzo_fee" class="form-control form-control-sm quote-field" value="{{ old('hanzo_fee', $quotation?->hanzo_fee ?? 0) }}">
        </div>
      </div>
      <hr class="my-2">
      <div class="d-flex justify-content-between align-items-center">
        <strong class="small">Total Landed Cost (USD):</strong>
        <h5 class="mb-0 text-primary" id="total-display">0.00</h5>
      </div>
    </div>
  </div>
  <div class="d-flex flex-wrap align-items-center gap-3">
    <div>
      <label class="form-label small mb-0">Valid Until</label>
      <input type="date" name="valid_until" class="form-control form-control-sm" style="width: auto;" value="{{ old('valid_until', ($quotation?->valid_until ?? now()->addDays(14))->format('Y-m-d')) }}">
    </div>
    <div class="d-flex gap-2">
      <button type="submit" name="action" value="save" class="btn btn-secondary btn-sm">Save Draft</button>
      <button type="submit" name="action" value="send" class="btn btn-primary btn-sm">Send to Buyer</button>
    </div>
  </div>
</form>
<a href="{{ route('admin.rfqs.show', $rfq) }}" class="btn btn-outline-secondary btn-sm mt-2 d-inline-block">{{ __('labels.back_to_rfq') }}</a>

@section('page-js')
<script>
document.querySelectorAll('.quote-field').forEach(el => {
  el.addEventListener('input', calcTotal);
});
function calcTotal() {
  let sum = 0;
  document.querySelectorAll('.quote-field').forEach(f => {
    sum += parseFloat(f.value) || 0;
  });
  document.getElementById('total-display').textContent = sum.toFixed(2);
}
calcTotal();
</script>
@endsection
