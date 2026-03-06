@extends('layouts.admin')

@section('title', __('labels.quote_builder') . ' - ' . $rfq->code)

@section('content')
<div class="row">
  <div class="col-12">
    <h4 class="fw-bold mb-4">{{ __('labels.quote_builder') }} — {{ $rfq->code }}</h4>
  </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible py-2 mb-4">
  {{ session('success') }}
  <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
  <div class="col-12">
    {{-- Product Request Summary --}}
    <div class="card mb-4">
      <div class="card-header">
        <i class="bx bx-info-circle me-2"></i>
        <strong>{{ __('labels.product_request') }} Summary</strong>
      </div>
      <div class="card-body">
        <div class="row g-2 mb-2">
          <div class="col-sm-6 col-md-3">
            <span class="text-muted small">{{ __('labels.request_code') }}:</span>
            <strong>{{ $rfq->code }}</strong>
          </div>
          <div class="col-sm-6 col-md-3">
            <span class="text-muted small">{{ __('labels.buyer') }}:</span>
            {{ $rfq->buyer?->name ?? '-' }}
          </div>
          <div class="col-sm-6 col-md-3">
            <span class="text-muted small">{{ __('labels.category') }}:</span>
            {{ $rfq->category?->name ?? '-' }}
          </div>
          <div class="col-sm-6 col-md-3">
            <span class="text-muted small">{{ __('labels.quantity') }}:</span>
            {{ number_format($rfq->quantity ?? 0) }} units
          </div>
          <div class="col-12">
            <span class="text-muted small">{{ __('labels.delivery') }}:</span>
            {{ trim(($rfq->delivery_city ?? '') . ', ' . ($rfq->delivery_country ?? ''), ', ') ?: '-' }}
          </div>
        </div>
        @if($rfq->description || $rfq->specs)
        <hr class="my-2">
        @if($rfq->description)
        <p class="mb-1 small"><span class="text-muted">{{ __('labels.description') }}:</span> {{ $rfq->description }}</p>
        @endif
        @if($rfq->specs)
        <p class="mb-0 small"><span class="text-muted">{{ __('labels.specs') }}:</span> {{ $rfq->specs }}</p>
        @endif
        @endif
        @if($rfq->factoryQuotes->isNotEmpty())
        <hr class="my-2">
        <p class="text-muted small mb-1"><strong>Price(s) Submitted</strong></p>
        @foreach($rfq->factoryQuotes->sortByDesc('created_at') as $fq)
        <div class="d-flex flex-wrap align-items-center gap-2 small mb-1">
          <span>{{ $fq->factory?->factory_name ?? 'Factory' }}</span>
          <strong>${{ number_format($fq->unit_price_usd, 2) }}/unit</strong>
          @if($fq->moq_confirmed)<span class="text-muted">MOQ: {{ number_format($fq->moq_confirmed) }}</span>@endif
          @if($fq->lead_time_days)<span class="text-muted">Lead: {{ $fq->lead_time_days }}d</span>@endif
          <span class="text-muted">→ Total: ${{ number_format($fq->unit_price_usd * $rfq->quantity, 2) }}</span>
        </div>
        @endforeach
        @if($suggestedProductCost)
        <p class="text-success small mb-0 mt-1">Select factory price for Product Cost below.</p>
        @endif
        @endif
      </div>
    </div>
  </div>
</div>

<form method="POST" action="{{ route('admin.quote-builder.store', $rfq) }}" id="quote-form">
  @csrf
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header"><strong>Breakdown</strong></div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Product Cost (USD) <span class="text-muted small">factory × qty</span></label>
              <input type="number" step="0.01" name="product_cost_usd" class="form-control quote-field" value="{{ old('product_cost_usd', $quotation?->product_cost_usd ?? $suggestedProductCost ?? 0) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">China Local Shipping</label>
              <input type="number" step="0.01" name="china_local_shipping" class="form-control quote-field" value="{{ old('china_local_shipping', $quotation?->china_local_shipping ?? 0) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Export Handling</label>
              <input type="number" step="0.01" name="export_handling" class="form-control quote-field" value="{{ old('export_handling', $quotation?->export_handling ?? 0) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Freight Cost</label>
              <input type="number" step="0.01" name="freight_cost" class="form-control quote-field" value="{{ old('freight_cost', $quotation?->freight_cost ?? 0) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Insurance Cost</label>
              <input type="number" step="0.01" name="insurance_cost" class="form-control quote-field" value="{{ old('insurance_cost', $quotation?->insurance_cost ?? 0) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Clearing Cost</label>
              <input type="number" step="0.01" name="clearing_cost" class="form-control quote-field" value="{{ old('clearing_cost', $quotation?->clearing_cost ?? 0) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Local Delivery Cost</label>
              <input type="number" step="0.01" name="local_delivery_cost" class="form-control quote-field" value="{{ old('local_delivery_cost', $quotation?->local_delivery_cost ?? 0) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">HANZO Fee</label>
              <input type="number" step="0.01" name="hanzo_fee" class="form-control quote-field" value="{{ old('hanzo_fee', $quotation?->hanzo_fee ?? 0) }}">
            </div>
          </div>
          <hr class="my-3">
          <div class="d-flex justify-content-between align-items-center">
            <strong>Total Landed Cost (USD):</strong>
            <h5 class="mb-0 text-primary" id="total-display">0.00</h5>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
        <div>
          <label class="form-label small mb-0">Valid Until</label>
          <input type="date" name="valid_until" class="form-control" style="width: auto;" value="{{ old('valid_until', ($quotation?->valid_until ?? now()->addDays(14))->format('Y-m-d')) }}">
        </div>
        <div class="d-flex gap-2">
          <button type="submit" name="action" value="save" class="btn btn-secondary">Save Draft</button>
          <button type="submit" name="action" value="send" class="btn btn-primary">Send to Buyer</button>
        </div>
      </div>
    </div>
  </div>
</form>

<a href="{{ route('admin.rfqs.show', $rfq) }}" class="btn btn-outline-secondary">{{ __('labels.back_to_rfq') }}</a>
@endsection

@section('page-js')
<script>
document.querySelectorAll('.quote-field').forEach(function(el) {
  el.addEventListener('input', calcTotal);
});
function calcTotal() {
  var sum = 0;
  document.querySelectorAll('.quote-field').forEach(function(f) {
    sum += parseFloat(f.value) || 0;
  });
  document.getElementById('total-display').textContent = sum.toFixed(2);
}
calcTotal();
</script>
@endsection
