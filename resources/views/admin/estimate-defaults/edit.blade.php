@extends('layouts.admin')

@section('title', 'Estimate defaults')

@section('content')
<div class="mb-4">
  <h4 class="fw-bold mb-1">RFQ Estimate Defaults</h4>
  <p class="text-muted small mb-0">Set the price ranges buyers see on new RFQs before you send an official quote.</p>
</div>

<form method="POST" action="{{ route('admin.estimate-defaults.update') }}">
  @csrf
  @method('PUT')

  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Factory price per unit (min – max)</label>
          <div class="d-flex align-items-center gap-2">
            <span class="text-muted">$</span>
            <input type="number" name="factory_min" step="0.01" min="0" class="form-control" style="max-width:120px"
              value="{{ old('factory_min', $estimate->factory_min) }}" placeholder="Min">
            <span class="text-muted">–</span>
            <input type="number" name="factory_max" step="0.01" min="0" class="form-control" style="max-width:120px"
              value="{{ old('factory_max', $estimate->factory_max) }}" placeholder="Max">
            <span class="text-muted">USD</span>
          </div>
        </div>
        <div class="col-12">
          <label class="form-label">Sea freight (min – max)</label>
          <div class="d-flex align-items-center gap-2">
            <span class="text-muted">$</span>
            <input type="number" name="freight_min" step="0.01" min="0" class="form-control" style="max-width:120px"
              value="{{ old('freight_min', $estimate->freight_min) }}">
            <span class="text-muted">–</span>
            <input type="number" name="freight_max" step="0.01" min="0" class="form-control" style="max-width:120px"
              value="{{ old('freight_max', $estimate->freight_max) }}">
          </div>
        </div>
        <div class="col-12">
          <label class="form-label">Customs & clearing (min – max)</label>
          <div class="d-flex align-items-center gap-2">
            <span class="text-muted">$</span>
            <input type="number" name="customs_min" step="0.01" min="0" class="form-control" style="max-width:120px"
              value="{{ old('customs_min', $estimate->customs_min) }}">
            <span class="text-muted">–</span>
            <input type="number" name="customs_max" step="0.01" min="0" class="form-control" style="max-width:120px"
              value="{{ old('customs_max', $estimate->customs_max) }}">
          </div>
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Total landed cost (min – max)</label>
          <div class="d-flex align-items-center gap-2">
            <span class="text-muted">$</span>
            <input type="number" name="total_min" step="0.01" min="0" class="form-control" style="max-width:140px"
              value="{{ old('total_min', $estimate->total_min) }}">
            <span class="text-muted">–</span>
            <input type="number" name="total_max" step="0.01" min="0" class="form-control" style="max-width:140px"
              value="{{ old('total_max', $estimate->total_max) }}">
          </div>
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label">Typical MOQ</label>
          <div class="input-group" style="max-width:140px">
            <input type="number" name="moq" min="1" class="form-control"
              value="{{ old('moq', $estimate->moq) }}">
            <span class="input-group-text">units</span>
          </div>
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label">Currency</label>
          <input type="text" name="currency" class="form-control" style="max-width:80px"
            value="{{ old('currency', $estimate->currency ?? 'usd') }}" maxlength="10">
        </div>
        <div class="col-12">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="activeSwitch" name="active" value="1"
              {{ old('active', $estimate->active) ? 'checked' : '' }}>
            <label class="form-check-label" for="activeSwitch">Use these values for all RFQ estimates</label>
          </div>
        </div>
      </div>
    </div>
  </div>

  <button type="submit" class="btn btn-primary">Save</button>
  <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
</form>
@endsection

