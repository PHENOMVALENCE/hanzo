@extends('layouts.admin')

@section('title', __('labels.quote_builder') . ' - ' . $rfq->code)

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
      <h4 class="fw-bold mb-0">{{ __('labels.quote_builder') }}</h4>
      <div class="d-flex align-items-center gap-2">
        <label class="form-label small mb-0">Switch request:</label>
        <select class="form-select form-select-sm" style="width:auto" id="rfqSwitcher" onchange="if(this.value) window.location='{{ route('admin.quote-builder.edit', ['rfq' => '__ID__']) }}'.replace('__ID__', this.value)">
          @foreach($rfqSwitcher ?? [] as $r)
          <option value="{{ $r->id }}" {{ $r->id == $rfq->id ? 'selected' : '' }}>{{ $r->code }} — {{ $r->buyer?->name }} ({{ $r->category?->name }})</option>
          @endforeach
        </select>
      </div>
    </div>
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
        @if($rfq->specs ?? null)
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
        <p class="text-success small mb-0 mt-1">Enter factory unit price below to auto-fill Product Cost.</p>
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
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label">Assigned Factory <span class="text-muted small">(internal)</span></label>
              <select class="form-select" disabled>
                @foreach($factories ?? [] as $f)
                <option value="{{ $f->id }}" {{ $rfq->assigned_factory_id == $f->id ? 'selected' : '' }}>{{ $f->factory_name ?? $f->user?->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Factory Unit Price (USD) <span class="text-muted small">→ auto-fills Product Cost</span></label>
              <input type="number" step="0.01" id="factory_unit_price" class="form-control" value="{{ $suggestedProductCost ? round($suggestedProductCost / $rfq->quantity, 2) : '' }}" placeholder="e.g. 2.50">
            </div>
            <div class="col-md-4">
              <label class="form-label">Shipping Method</label>
              <select class="form-select" id="shipping_method">
                <option value="">— Select —</option>
                <option value="sea">Sea</option>
                <option value="air">Air</option>
              </select>
            </div>
          </div>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Product Cost (USD) <span class="text-muted small">factory × qty</span></label>
              <input type="number" step="0.01" name="product_cost_usd" id="product_cost_usd" class="form-control quote-field" value="{{ old('product_cost_usd', $quotation?->product_cost_usd ?? $suggestedProductCost ?? 0) }}">
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
              <label class="form-label">Freight Cost <span class="text-muted small">(auto from rate table)</span></label>
              <input type="number" step="0.01" name="freight_cost" id="freight_cost" class="form-control quote-field" value="{{ old('freight_cost', $quotation?->freight_cost ?? 0) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Insurance <span class="text-muted small">(toggle to auto-calc ~1%)</span></label>
              <div class="d-flex align-items-center gap-2">
                <div class="form-check form-switch mb-0">
                  <input class="form-check-input" type="checkbox" id="insurance_toggle" {{ ($quotation?->insurance_cost ?? 0) > 0 ? 'checked' : '' }}>
                  <label class="form-check-label" for="insurance_toggle">On</label>
                </div>
                <input type="number" step="0.01" name="insurance_cost" id="insurance_cost" class="form-control quote-field" value="{{ old('insurance_cost', $quotation?->insurance_cost ?? 0) }}">
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label">Clearing Cost <span class="text-muted small">(suggest: {{ $clearingSuggest ? '$'.$clearingSuggest['min'].'–$'.$clearingSuggest['max'] : '—' }})</span></label>
              <input type="number" step="0.01" name="clearing_cost" id="clearing_cost" class="form-control quote-field" value="{{ old('clearing_cost', $quotation?->clearing_cost ?? ($clearingSuggest['min'] ?? 0)) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">Local Delivery Cost</label>
              <input type="number" step="0.01" name="local_delivery_cost" class="form-control quote-field" value="{{ old('local_delivery_cost', $quotation?->local_delivery_cost ?? 0) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">HANZO Margin <span class="text-muted small">(admin only, hidden from buyer)</span></label>
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
          <label class="form-label small mb-0">Valid Until (default 14 days)</label>
          <input type="date" name="valid_until" class="form-control" style="width: auto;" value="{{ old('valid_until', ($quotation?->valid_until ?? now()->addDays(14))->format('Y-m-d')) }}">
        </div>
        <div class="d-flex gap-2">
          <button type="submit" name="action" value="save" class="btn btn-secondary">Save Draft</button>
          <button type="button" id="btnPreviewSend" class="btn btn-primary">Preview & Send to Buyer</button>
        </div>
      </div>
    </div>
  </div>
</form>

{{-- Buyer Preview Modal --}}
<div class="modal fade" id="previewModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Buyer-Facing Quotation Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted small">This is what the buyer will see. HANZO Margin is excluded from their view.</p>
        <div class="hanzo-landed-cost p-4 rounded">
          <table class="table table-sm mb-0">
            <tr><td>Product Cost</td><td class="text-end" id="prev-product">$0.00</td></tr>
            <tr><td>Shipping & Freight</td><td class="text-end" id="prev-shipping">$0.00</td></tr>
            <tr><td>Customs & Clearing</td><td class="text-end" id="prev-clearing">$0.00</td></tr>
            <tr><td>Local Delivery</td><td class="text-end" id="prev-local">$0.00</td></tr>
          </table>
          <hr>
          <div class="d-flex justify-content-between align-items-center">
            <strong>Total Landed Cost</strong>
            <span class="fs-4 fw-bold text-primary" id="prev-total">$0.00</span>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="btnConfirmSend" class="btn btn-primary">Send to Buyer</button>
      </div>
    </div>
  </div>
</div>

<a href="{{ route('admin.rfqs.show', $rfq) }}" class="btn btn-outline-secondary">{{ __('labels.back_to_rfq') }}</a>
@endsection

@section('page-js')
<script>
(function() {
  var qty = {{ $rfq->quantity }};
  var freightRates = @json($freightRates ?? []);

  var productCost = document.getElementById('product_cost_usd');
  var factoryUnit = document.getElementById('factory_unit_price');
  var freightCost = document.getElementById('freight_cost');
  var insuranceCost = document.getElementById('insurance_cost');
  var insuranceToggle = document.getElementById('insurance_toggle');
  var shippingMethod = document.getElementById('shipping_method');

  if (factoryUnit) {
    factoryUnit.addEventListener('input', function() {
      var v = parseFloat(this.value) || 0;
      if (productCost) productCost.value = (v * qty).toFixed(2);
      calcTotal();
      updateInsurance();
    });
  }

  if (shippingMethod) {
    shippingMethod.addEventListener('change', function() {
      var method = this.value;
      var dest = '{{ addslashes($rfq->delivery_city ?? '') }}'.toLowerCase();
      if (!method) return;
      var rates = freightRates[method];
      if (!rates || !rates.length) return;
      var r = rates[0];
      var est = 0;
      if (r.rate_type === 'per_cbm') est = qty * 0.001 * parseFloat(r.rate_value);
      else if (r.rate_type === 'per_kg') est = qty * 0.001 * parseFloat(r.rate_value);
      else est = Math.max(parseFloat(r.min_charge) || 0, parseFloat(r.rate_value) || 0);
      freightCost.value = Math.round(est * 100) / 100;
      calcTotal();
    });
  }

  function updateInsurance() {
    var pc = parseFloat(productCost?.value) || 0;
    var prem = Math.round(pc * 0.01 * 100) / 100;
    if (insuranceToggle?.checked) {
      insuranceCost.value = prem;
    }
  }
  if (insuranceToggle) {
    insuranceToggle.addEventListener('change', function() {
      if (this.checked) updateInsurance();
      else insuranceCost.value = 0;
      calcTotal();
    });
  }

  document.querySelectorAll('.quote-field').forEach(function(el) {
    el.addEventListener('input', function() {
      if (el.id === 'product_cost_usd') updateInsurance();
      calcTotal();
    });
  });

  function calcTotal() {
    var sum = 0;
    document.querySelectorAll('.quote-field').forEach(function(f) {
      sum += parseFloat(f.value) || 0;
    });
    var el = document.getElementById('total-display');
    if (el) el.textContent = sum.toFixed(2);
  }

  function getVal(name) {
    var f = document.querySelector('input[name="' + name + '"]');
    return parseFloat(f?.value) || 0;
  }

  document.getElementById('btnPreviewSend').addEventListener('click', function() {
    var product = getVal('product_cost_usd');
    var china = getVal('china_local_shipping');
    var exportH = getVal('export_handling');
    var freight = getVal('freight_cost');
    var insurance = getVal('insurance_cost');
    var clearing = getVal('clearing_cost');
    var local = getVal('local_delivery_cost');
    var total = product + china + exportH + freight + insurance + clearing + local;
    document.getElementById('prev-product').textContent = '$' + product.toFixed(2);
    document.getElementById('prev-shipping').textContent = '$' + (china + exportH + freight + insurance).toFixed(2);
    document.getElementById('prev-clearing').textContent = '$' + clearing.toFixed(2);
    document.getElementById('prev-local').textContent = '$' + local.toFixed(2);
    document.getElementById('prev-total').textContent = '$' + total.toFixed(2);
    new bootstrap.Modal(document.getElementById('previewModal')).show();
  });

  document.getElementById('btnConfirmSend').addEventListener('click', function() {
    var form = document.getElementById('quote-form');
    var inp = document.createElement('input');
    inp.type = 'hidden';
    inp.name = 'action';
    inp.value = 'send';
    form.appendChild(inp);
    form.submit();
  });

  calcTotal();
})();
</script>
@endsection
