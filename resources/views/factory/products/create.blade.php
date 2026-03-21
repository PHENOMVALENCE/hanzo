@extends('layouts.factory')

@section('title', 'Add Product')

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('factory.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add Product</li>
  </ol>
</nav>

<div class="mb-4">
  <h4 class="fw-bold mb-1">Add Product</h4>
  <p class="text-muted small mb-0">List your products so buyers can find and request quotes. Admin will review before they go live.</p>
</div>

<form method="POST" action="{{ route('factory.products.store') }}" enctype="multipart/form-data">
  @csrf
  <div class="card mb-4">
    <div class="card-body">
      <h6 class="mb-3">Basic info</h6>
      <div class="row g-3 mb-4">
        <div class="col-md-8">
          <label class="form-label">Product name *</label>
          <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror" value="{{ old('title') }}" required maxlength="255" placeholder="e.g. Cotton T-Shirt, Bulk Order">
          <x-input-error :messages="$errors->get('title')" class="mt-1" />
        </div>
        <div class="col-md-4">
          <label class="form-label">Category *</label>
          <select name="category_id" class="form-select" required>
            <option value="">Select category</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12">
          <x-product-image-upload :existing-images="[]" :max-images="5" />
        </div>
        <div class="col-12">
          <label class="form-label">Brief description <span class="text-muted">(optional)</span></label>
          <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2" maxlength="500" placeholder="Key materials, typical use, or specs buyers should know">{{ old('description') }}</textarea>
        </div>
      </div>

      <hr class="my-4">
      <h6 class="mb-3">Pricing & capacity</h6>
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <label class="form-label">Price range (USD) *</label>
          <div class="input-group">
            <input type="number" name="price_min" class="form-control" step="0.01" min="0" value="{{ old('price_min') }}" placeholder="Min" required>
            <span class="input-group-text">–</span>
            <input type="number" name="price_max" class="form-control" step="0.01" min="0" value="{{ old('price_max') }}" placeholder="Max" required>
          </div>
          <small class="text-muted">Per unit, shown to buyers</small>
        </div>
        <div class="col-md-4">
          <label class="form-label">MOQ (min order) *</label>
          <input type="number" name="moq" class="form-control" min="1" value="{{ old('moq', 100) }}" placeholder="e.g. 100" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Lead time (days)</label>
          <input type="number" name="lead_time_days" class="form-control" min="1" value="{{ old('lead_time_days') }}" placeholder="e.g. 14">
        </div>
        <div class="col-12">
          <label class="form-label">Location</label>
          <input type="text" name="location" class="form-control" value="{{ old('location', auth()->user()->factory?->location_china) }}" maxlength="100" placeholder="e.g. Guangdong, China">
        </div>
      </div>

      <hr class="my-4">
      <h6 class="mb-3">Finish</h6>
      <div class="d-flex flex-wrap gap-3 align-items-center">
        <div class="form-check">
          <input type="radio" name="status" id="status_draft" value="draft" class="form-check-input" {{ old('status', 'draft') === 'draft' ? 'checked' : '' }}>
          <label class="form-check-label" for="status_draft">Save as draft</label>
        </div>
        <div class="form-check">
          <input type="radio" name="status" id="status_pending" value="pending_approval" class="form-check-input" {{ old('status') === 'pending_approval' ? 'checked' : '' }}>
          <label class="form-check-label" for="status_pending">Submit for approval</label>
        </div>
        <span class="text-muted small">Draft: edit later. Submit: admin reviews and publishes.</span>
      </div>
    </div>
  </div>
  <button type="submit" class="btn btn-primary btn-lg">Create Product</button>
  <a href="{{ route('factory.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
</form>
@endsection
