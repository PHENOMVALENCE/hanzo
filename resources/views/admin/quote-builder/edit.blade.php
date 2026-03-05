@extends('layouts.admin')

@section('title', 'Quote Builder - ' . $rfq->code)

@section('content')
<h4 class="fw-bold mb-4">Quote Builder for {{ $rfq->code }}</h4>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
<form method="POST" action="{{ route('admin.quote-builder.store', $rfq) }}" id="quote-form">
  @csrf
  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Product Cost (USD)</label>
          <input type="number" step="0.01" name="product_cost_usd" class="form-control quote-field" value="{{ old('product_cost_usd', $quotation?->product_cost_usd ?? 0) }}">
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
      <hr>
      <div class="d-flex justify-content-between align-items-center">
        <strong>Total Landed Cost (USD):</strong>
        <h5 class="mb-0" id="total-display">0.00</h5>
      </div>
    </div>
  </div>
  <div class="mb-3">
    <label class="form-label">Valid Until</label>
    <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until', ($quotation?->valid_until ?? now()->addDays(14))->format('Y-m-d')) }}">
  </div>
  <button type="submit" name="action" value="save" class="btn btn-secondary">Save Draft</button>
  <button type="submit" name="action" value="send" class="btn btn-primary">Send to Buyer</button>
</form>
<a href="{{ route('admin.rfqs.show', $rfq) }}" class="btn btn-outline-secondary mt-3">Back to RFQ</a>

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
