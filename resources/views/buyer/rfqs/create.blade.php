@extends('layouts.buyer')

@section('title', __('labels.create_rfq'))

@section('content')
<div class="mb-4">
  <h4 class="fw-bold mb-1">{{ __('labels.create_rfq') }}</h4>
  <p class="text-muted small mb-0">{{ __('labels.rfq_desc') }}</p>
</div>

<form method="POST" action="{{ route('buyer.rfqs.store') }}" enctype="multipart/form-data" id="rfqForm">
  @csrf
  <div class="card mb-4">
    <div class="card-body">
      <!-- Category: Radio card selector -->
      <div class="mb-4">
        <label class="form-label">Category *</label>
        <div class="row g-3">
          @foreach($categories as $c)
          <div class="col-6 col-md-3">
            <input type="radio" name="category_id" id="cat_{{ $c->id }}" value="{{ $c->id }}" class="btn-check" required
              {{ old('category_id') == $c->id ? 'checked' : '' }}
              data-moq="{{ $c->moq_default }}"
              data-slug="{{ $c->slug }}">
            <label class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3 rounded" for="cat_{{ $c->id }}">
              @php
                $icon = match($c->slug) {
                  'fashion' => 'bx-walk',
                  'packaging' => 'bx-package',
                  'consumer-goods' => 'bx-store',
                  'machinery' => 'bx-cog',
                  default => 'bx-category'
                };
              @endphp
              <i class="bx {{ $icon }} bx-lg mb-2"></i>
              <span class="small fw-medium">{{ $c->name }}</span>
            </label>
          </div>
          @endforeach
        </div>
      </div>

      <!-- Product Description -->
      <div class="mb-4">
        <label class="form-label">Product Description *</label>
        <textarea name="description" id="productDescription" class="form-control" rows="4" required maxlength="2000"
          placeholder="Describe your product in detail: materials, dimensions, colors, certifications needed, etc.">{{ old('description') }}</textarea>
        <div class="d-flex justify-content-between mt-1">
          <span class="text-muted small" id="charCount">0 / 2000</span>
        </div>
        <x-input-error :messages="$errors->get('description')" class="mt-1" />
      </div>

      <!-- Quantity + Target Price -->
      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label">Quantity *</label>
          <input type="number" name="quantity" id="quantity" class="form-control" required min="1" value="{{ old('quantity') }}" placeholder="e.g. 1000">
          <div id="moqWarning" class="text-warning small mt-1 d-none">Below typical MOQ. We may still be able to help.</div>
          <x-input-error :messages="$errors->get('quantity')" class="mt-1" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Target Price (optional) <span class="text-muted">USD per unit</span></label>
          <input type="number" name="target_price_per_unit" class="form-control" min="0" step="0.01" value="{{ old('target_price_per_unit') }}" placeholder="e.g. 2.50">
          <x-input-error :messages="$errors->get('target_price_per_unit')" class="mt-1" />
        </div>
      </div>

      <!-- Delivery Location + Timeline -->
      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label">Delivery Location *</label>
          <select name="delivery_city" class="form-select" required>
            <option value="">Select destination</option>
            <option value="Dar es Salaam" {{ old('delivery_city') == 'Dar es Salaam' ? 'selected' : '' }}>Dar es Salaam</option>
            <option value="Nairobi" {{ old('delivery_city') == 'Nairobi' ? 'selected' : '' }}>Nairobi</option>
            <option value="Kampala" {{ old('delivery_city') == 'Kampala' ? 'selected' : '' }}>Kampala</option>
            <option value="Kigali" {{ old('delivery_city') == 'Kigali' ? 'selected' : '' }}>Kigali</option>
            <option value="Other" {{ old('delivery_city') == 'Other' ? 'selected' : '' }}>Other</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Timeline *</label>
          <select name="timeline_weeks" class="form-select" required>
            <option value="">Select timeline</option>
            <option value="4" {{ old('timeline_weeks') == '4' ? 'selected' : '' }}>4 weeks</option>
            <option value="6" {{ old('timeline_weeks') == '6' ? 'selected' : '' }}>6 weeks</option>
            <option value="8" {{ old('timeline_weeks') == '8' ? 'selected' : '' }}>8 weeks</option>
            <option value="12" {{ old('timeline_weeks') == '12' ? 'selected' : '' }}>12+ weeks</option>
          </select>
        </div>
      </div>

      <!-- Estimated Price Range (AJAX) -->
      <div id="estimateBox" class="hanzo-landed-cost p-4 rounded mb-4 d-none">
        <h6 class="mb-3">Estimated Price Range</h6>
        <div id="estimateContent"></div>
        <p class="text-muted small mb-0 mt-3"><em>This is an estimate only. Official quote will be provided by HANZO.</em></p>
      </div>

      <!-- File Uploads -->
      <div>
        <label class="form-label">Attachments <span class="text-muted">(images &amp; PDF, max 5 files, 5MB each)</span></label>
        <input type="file" name="attachments[]" class="form-control" accept="image/*,.pdf" multiple
          data-max-files="5" data-max-size="5120">
        <div id="fileFeedback" class="text-muted small mt-1"></div>
        <x-input-error :messages="$errors->get('attachments.*')" class="mt-1" />
      </div>
    </div>
  </div>
  <button type="submit" class="btn btn-primary">{{ __('labels.submit_rfq') }}</button>
  <a href="{{ route('buyer.rfqs.index') }}" class="btn btn-outline-secondary">Cancel</a>
</form>

@push('page-js')
<script>
(function() {
  const desc = document.getElementById('productDescription');
  const charCount = document.getElementById('charCount');
  const quantity = document.getElementById('quantity');
  const moqWarning = document.getElementById('moqWarning');
  const estimateBox = document.getElementById('estimateBox');
  const estimateContent = document.getElementById('estimateContent');
  let estimateTimer = null;

  if (desc) {
    function updateCharCount() {
      charCount.textContent = (desc.value || '').length + ' / 2000';
    }
    desc.addEventListener('input', updateCharCount);
    updateCharCount();
  }

  document.querySelectorAll('input[name="category_id"]').forEach(function(radio) {
    radio.addEventListener('change', checkMoq);
  });
  if (quantity) quantity.addEventListener('input', checkMoq);
  function checkMoq() {
    const sel = document.querySelector('input[name="category_id"]:checked');
    if (!sel || !quantity) return;
    const moq = parseInt(sel.dataset.moq || 100, 10);
    const qty = parseInt(quantity.value, 10) || 0;
    moqWarning.classList.toggle('d-none', qty >= moq || qty === 0);
  }
  checkMoq();

  function fetchEstimate() {
    const cat = document.querySelector('input[name="category_id"]:checked');
    const dest = document.querySelector('select[name="delivery_city"]');
    const qty = parseInt(quantity?.value, 10);
    if (!cat || !dest?.value || !qty) {
      estimateBox.classList.add('d-none');
      return;
    }
    fetch('/api/estimate?category=' + encodeURIComponent(cat.value) + '&qty=' + qty + '&destination=' + encodeURIComponent(dest.value))
      .then(r => r.json())
      .then(function(data) {
        if (data.error) {
          estimateContent.innerHTML = '<p class="text-muted mb-0">' + (data.message || 'Unable to load estimate.') + '</p>';
        } else {
          estimateContent.innerHTML =
            '<table class="table table-sm mb-0">' +
            (data.factory_min != null ? '<tr><td>Factory price range (per unit)</td><td class="text-end">$' + data.factory_min + ' – $' + data.factory_max + '</td></tr>' : '') +
            (data.freight_min != null ? '<tr><td>Sea freight (est.)</td><td class="text-end">$' + data.freight_min + ' – $' + data.freight_max + '</td></tr>' : '') +
            (data.customs_min != null ? '<tr><td>Customs &amp; clearing (est.)</td><td class="text-end">$' + data.customs_min + ' – $' + data.customs_max + '</td></tr>' : '') +
            (data.total_min != null ? '<tr class="fw-bold"><td>Total landed cost (est.)</td><td class="text-end">$' + data.total_min + ' – $' + data.total_max + '</td></tr>' : '') +
            (data.moq ? '<tr><td>Typical MOQ</td><td class="text-end">' + data.moq + ' units</td></tr>' : '') +
            '</table>';
        }
        estimateBox.classList.remove('d-none');
      })
      .catch(function() {
        estimateContent.innerHTML = '<p class="text-muted mb-0">Estimate unavailable.</p>';
        estimateBox.classList.remove('d-none');
      });
  }

  function scheduleEstimate() {
    clearTimeout(estimateTimer);
    estimateTimer = setTimeout(fetchEstimate, 400);
  }
  document.querySelectorAll('input[name="category_id"], select[name="delivery_city"]').forEach(function(el) {
    el.addEventListener('change', scheduleEstimate);
  });
  if (quantity) quantity.addEventListener('input', scheduleEstimate);
})();
</script>
@endpush
@endsection
