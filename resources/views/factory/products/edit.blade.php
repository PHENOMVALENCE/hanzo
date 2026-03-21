@extends('layouts.factory')

@section('title', 'Edit Product')

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('factory.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
  </ol>
</nav>

<div class="mb-4">
  <h4 class="fw-bold mb-1">Edit Product</h4>
  <p class="text-muted small mb-0">{{ $product->title }}</p>
</div>

<form method="POST" action="{{ route('factory.products.update', $product) }}" enctype="multipart/form-data">
  @csrf
  @method('PUT')
  <div class="card mb-4">
    <div class="card-body">
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
</form>
@endsection
