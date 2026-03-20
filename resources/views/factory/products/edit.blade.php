@extends('layouts.factory')

@section('title', 'Edit Product')

@section('content')
<<<<<<< HEAD
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('factory.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
  </ol>
</nav>

<h4 class="fw-bold mb-1">Edit Product</h4>
<p class="text-muted small mb-4">{{ $product->name }}</p>
=======
<div class="mb-4">
  <h4 class="fw-bold mb-1">Edit Product</h4>
  <p class="text-muted small mb-0">{{ $product->title }}</p>
</div>
>>>>>>> 3a34daee (Hanzo in b2b style)

<form method="POST" action="{{ route('factory.products.update', $product) }}" enctype="multipart/form-data">
  @csrf
  @method('PUT')
  <div class="card mb-4">
    <div class="card-body">
<<<<<<< HEAD
      @if($product->image_path)
      <div class="mb-3">
        <img src="{{ $product->image_url }}" alt="" class="rounded" style="max-height: 120px;">
        <p class="small text-muted mt-1">Current image. Upload a new one to replace.</p>
      </div>
      @endif
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label">Product Name *</label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Category *</label>
          <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
            @foreach($categories as $c)
            <option value="{{ $c->id }}" {{ old('category_id', $product->category_id) == $c->id ? 'selected' : '' }}>{{ trans_category($c) }}</option>
            @endforeach
          </select>
          @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $product->description) }}</textarea>
          @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">MOQ *</label>
          <input type="number" name="moq" class="form-control @error('moq') is-invalid @enderror" value="{{ old('moq', $product->moq) }}" min="1" required>
          @error('moq')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Price per Unit (USD)</label>
          <input type="number" name="price_per_unit" class="form-control @error('price_per_unit') is-invalid @enderror" value="{{ old('price_per_unit', $product->price_per_unit) }}" step="0.01" min="0">
          @error('price_per_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Lead Time (days)</label>
          <input type="number" name="lead_time_days" class="form-control @error('lead_time_days') is-invalid @enderror" value="{{ old('lead_time_days', $product->lead_time_days) }}" min="1">
          @error('lead_time_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <label class="form-label">Replace Image</label>
          <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
          <small class="text-muted">Leave empty to keep current image.</small>
          @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>
  </div>
  <div class="d-flex gap-2">
    <button type="submit" class="btn" style="background: #0d9488; color: #fff;">Save Changes</button>
    <a href="{{ route('factory.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
  </div>
=======
      <h6 class="mb-3">Basic info</h6>
      <div class="row g-3 mb-4">
        <div class="col-12">
          <label class="form-label">Title *</label>
          <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $product->title) }}" required maxlength="255">
          <x-input-error :messages="$errors->get('title')" class="mt-1" />
        </div>
        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" maxlength="5000">{{ old('description', $product->description) }}</textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Category *</label>
          <select name="category_id" class="form-select">
            <option value="">Select category</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}" {{ old('category_id', $product->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12">
          <x-product-image-upload :existing-images="$product->images ?? []" :max-images="5" />
        </div>
      </div>

      <hr class="my-4">
      <h6 class="mb-3">Pricing & capacity</h6>
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <label class="form-label">Min Price (USD) *</label>
          <input type="number" name="price_min" class="form-control" step="0.01" min="0" value="{{ old('price_min', $product->price_min) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Max Price (USD) *</label>
          <input type="number" name="price_max" class="form-control" step="0.01" min="0" value="{{ old('price_max', $product->price_max) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">MOQ *</label>
          <input type="number" name="moq" class="form-control" min="1" value="{{ old('moq', $product->moq) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Lead Time (days)</label>
          <input type="number" name="lead_time_days" class="form-control" min="1" value="{{ old('lead_time_days', $product->lead_time_days) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Location</label>
          <input type="text" name="location" class="form-control" value="{{ old('location', $product->location) }}" maxlength="100">
        </div>
      </div>

      <hr class="my-4">
      <h6 class="mb-3">Status</h6>
      <div>
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="draft" {{ old('status', $product->status) === 'draft' ? 'selected' : '' }}>Draft</option>
          <option value="pending_approval" {{ old('status', $product->status) === 'pending_approval' ? 'selected' : '' }}>Pending approval</option>
          <option value="disabled" {{ old('status', $product->status) === 'disabled' ? 'selected' : '' }}>Disabled (archive)</option>
          @if($product->status === 'live')
          <option value="live" selected>Live (keep as is)</option>
          @endif
        </select>
      </div>
    </div>
  </div>
  <button type="submit" class="btn btn-primary">Update Product</button>
  <a href="{{ route('factory.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
>>>>>>> 3a34daee (Hanzo in b2b style)
</form>
@endsection
