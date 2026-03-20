@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="mb-4">
  <h4 class="fw-bold mb-1">Edit Product</h4>
  <p class="text-muted small mb-0">{{ $product->title }}</p>
</div>

<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
  @csrf
  @method('PUT')
  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3">
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
          <label class="form-label">Category</label>
          <select name="category_id" class="form-select">
            <option value="">Select category</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}" {{ old('category_id', $product->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Factory (optional)</label>
          <select name="factory_id" class="form-select">
            <option value="">Platform product (no factory)</option>
            @foreach($factories as $f)
              <option value="{{ $f->id }}" {{ old('factory_id', $product->factory_id) == $f->id ? 'selected' : '' }}>{{ $f->factory_name ?? $f->user?->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12">
          <x-product-image-upload :existing-images="$product->images ?? []" :max-images="5" />
        </div>
        <div class="col-md-4">
          <label class="form-label">Min Price (USD)</label>
          <input type="number" name="price_min" class="form-control" step="0.01" min="0" value="{{ old('price_min', $product->price_min) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Max Price (USD)</label>
          <input type="number" name="price_max" class="form-control" step="0.01" min="0" value="{{ old('price_max', $product->price_max) }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">MOQ</label>
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
        <div class="col-12">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="draft" {{ old('status', $product->status) === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="pending_approval" {{ old('status', $product->status) === 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
            <option value="live" {{ old('status', $product->status) === 'live' ? 'selected' : '' }}>Live</option>
            <option value="disabled" {{ old('status', $product->status) === 'disabled' ? 'selected' : '' }}>Disabled</option>
          </select>
        </div>
      </div>
    </div>
  </div>
  <button type="submit" class="btn btn-primary">Update Product</button>
  <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
</form>
@endsection
