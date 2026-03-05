@extends('layouts.admin')

@section('title', 'Add Freight Rate')

@section('content')
<h4 class="fw-bold mb-4">Add Freight Rate</h4>
<form method="POST" action="{{ route('admin.freight-rates.store') }}">
  @csrf
  <div class="card">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Method</label>
          <select name="method" class="form-select" required>
            <option value="sea">Sea</option>
            <option value="air">Air</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Destination Port</label>
          <input type="text" name="destination_port" class="form-control" value="{{ old('destination_port') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Destination City</label>
          <input type="text" name="destination_city" class="form-control" value="{{ old('destination_city') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Rate Type</label>
          <select name="rate_type" class="form-select" required>
            <option value="per_cbm">Per CBM</option>
            <option value="per_kg">Per KG</option>
            <option value="per_container">Per Container</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Rate Value</label>
          <input type="number" step="0.01" name="rate_value" class="form-control" required value="{{ old('rate_value') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Min Charge</label>
          <input type="number" step="0.01" name="min_charge" class="form-control" value="{{ old('min_charge', 0) }}">
        </div>
        <div class="col-12">
          <label class="form-check">
            <input type="checkbox" name="active" value="1" class="form-check-input" {{ old('active', true) ? 'checked' : '' }}>
            <span class="form-check-label">Active</span>
          </label>
        </div>
      </div>
    </div>
  </div>
  <button type="submit" class="btn btn-primary mt-2">Create</button>
  <a href="{{ route('admin.freight-rates.index') }}" class="btn btn-outline-secondary mt-2">Cancel</a>
</form>
@endsection
