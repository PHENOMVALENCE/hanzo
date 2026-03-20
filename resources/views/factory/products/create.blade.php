@extends('layouts.factory')

@section('title', 'Add Product')

@section('content')
<<<<<<< HEAD
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('factory.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add Product</li>
  </ol>
</nav>

<h4 class="fw-bold mb-1">Add Product</h4>
<p class="text-muted small mb-4">Post a new product to your catalog. Buyers will discover it when browsing.</p>
=======
<div class="mb-4">
  <h4 class="fw-bold mb-1">Add Product</h4>
  <p class="text-muted small mb-0">List your products so buyers can find and request quotes. Admin will review before they go live.</p>
</div>
>>>>>>> 3a34daee (Hanzo in b2b style)

<form method="POST" action="{{ route('factory.products.store') }}" enctype="multipart/form-data">
  @csrf
  <div class="card mb-4">
    <div class="card-body">
<<<<<<< HEAD
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label">Product Name *</label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Cotton T-Shirt">
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Category *</label>
          <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
            <option value="">Select category</option>
            @foreach($categories as $c)
            <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ trans_category($c) }}</option>
            @endforeach
          </select>
          @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Describe materials, dimensions, colors...">{{ old('description') }}</textarea>
          @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">MOQ (Minimum Order) *</label>
          <input type="number" name="moq" class="form-control @error('moq') is-invalid @enderror" value="{{ old('moq', 100) }}" min="1" required>
          @error('moq')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Price per Unit (USD)</label>
          <input type="number" name="price_per_unit" class="form-control @error('price_per_unit') is-invalid @enderror" value="{{ old('price_per_unit') }}" step="0.01" min="0" placeholder="e.g. 5.50">
          @error('price_per_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Lead Time (days)</label>
          <input type="number" name="lead_time_days" class="form-control @error('lead_time_days') is-invalid @enderror" value="{{ old('lead_time_days') }}" min="1" placeholder="e.g. 14">
          @error('lead_time_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <label class="form-label">Product Image</label>
          <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
          <small class="text-muted">JPG, PNG or WebP. Max 2MB.</small>
          @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>
  </div>
  <div class="d-flex gap-2">
    <button type="submit" class="btn" style="background: #0d9488; color: #fff;">Post Product</button>
    <a href="{{ route('factory.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
  </div>
=======
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
>>>>>>> 3a34daee (Hanzo in b2b style)
</form>
@endsection
