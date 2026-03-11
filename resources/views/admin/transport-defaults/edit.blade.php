@extends('layouts.admin')

@section('title', 'Transport Defaults')

@section('content')
<h4 class="fw-bold mb-4">Initial Transport Costs (Defaults)</h4>
<p class="text-muted mb-4">Used when no specific freight rate matches the destination. Formula: base + (quantity × per_unit)</p>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('admin.transport-defaults.update') }}">
  @csrf
  @method('PUT')
  <div class="row">
    <div class="col-md-6">
      <div class="card mb-4">
        <div class="card-header bg-primary text-white">Sea Freight</div>
        <div class="card-body">
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label small">Min: Base (USD)</label>
              <input type="number" step="0.01" name="sea_base_min" class="form-control" value="{{ old('sea_base_min', $sea->base_min ?? 200) }}">
            </div>
            <div class="col-6">
              <label class="form-label small">Min: Per Unit (USD)</label>
              <input type="number" step="0.0001" name="sea_per_unit_min" class="form-control" value="{{ old('sea_per_unit_min', $sea->per_unit_min ?? 0.05) }}">
            </div>
            <div class="col-6">
              <label class="form-label small">Max: Base (USD)</label>
              <input type="number" step="0.01" name="sea_base_max" class="form-control" value="{{ old('sea_base_max', $sea->base_max ?? 800) }}">
            </div>
            <div class="col-6">
              <label class="form-label small">Max: Per Unit (USD)</label>
              <input type="number" step="0.0001" name="sea_per_unit_max" class="form-control" value="{{ old('sea_per_unit_max', $sea->per_unit_max ?? 0.1) }}">
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card mb-4">
        <div class="card-header bg-info text-white">Air Freight</div>
        <div class="card-body">
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label small">Min: Base (USD)</label>
              <input type="number" step="0.01" name="air_base_min" class="form-control" value="{{ old('air_base_min', $air->base_min ?? 400) }}">
            </div>
            <div class="col-6">
              <label class="form-label small">Min: Per Unit (USD)</label>
              <input type="number" step="0.0001" name="air_per_unit_min" class="form-control" value="{{ old('air_per_unit_min', $air->per_unit_min ?? 0.15) }}">
            </div>
            <div class="col-6">
              <label class="form-label small">Max: Base (USD)</label>
              <input type="number" step="0.01" name="air_base_max" class="form-control" value="{{ old('air_base_max', $air->base_max ?? 1500) }}">
            </div>
            <div class="col-6">
              <label class="form-label small">Max: Per Unit (USD)</label>
              <input type="number" step="0.0001" name="air_per_unit_max" class="form-control" value="{{ old('air_per_unit_max', $air->per_unit_max ?? 0.3) }}">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <button type="submit" class="btn btn-primary">Save Defaults</button>
  <a href="{{ route('admin.freight-rates.index') }}" class="btn btn-outline-secondary">Back to Freight Rates</a>
</form>
@endsection
