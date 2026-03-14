@extends('layouts.factory')

@section('title', 'Edit Product')

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('factory.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
  </ol>
</nav>

<h4 class="fw-bold mb-1">Edit Product</h4>
<p class="text-muted small mb-4">{{ $product->name }}</p>

<form method="POST" action="{{ route('factory.products.update', $product) }}" enctype="multipart/form-data">
  @csrf
  @method('PUT')
  <div class="card mb-4">
    <div class="card-body">
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
</form>
@endsection
