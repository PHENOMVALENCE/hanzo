@extends('layouts.buyer')

@section('title', __('labels.products') . ' | ' . config('app.name'))

@section('content')
<form action="{{ route('buyer.products.index') }}" method="GET" class="card mb-4">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label small text-muted">{{ __('labels.search') }}</label>
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('labels.search_products') }}" aria-label="Search">
      </div>
      <div class="col-md-3">
        <label class="form-label small text-muted">{{ __('labels.category') }}</label>
        <select name="category" class="form-select">
          <option value="">{{ __('labels.all_categories') }}</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small text-muted">Min $</label>
        <input type="number" name="min_price" value="{{ request('min_price') }}" class="form-control" step="0.01" min="0">
      </div>
      <div class="col-md-2">
        <label class="form-label small text-muted">Max $</label>
        <input type="number" name="max_price" value="{{ request('max_price') }}" class="form-control" step="0.01" min="0">
      </div>
      <div class="col-md-1 d-flex align-items-end">
        <button type="submit" class="btn btn-hanzo-primary w-100">{{ __('labels.search') }}</button>
      </div>
    </div>
  </div>
</form>

<h2 class="buyer-section-title">{{ $products->total() }} {{ __('labels.products') }}</h2>

@if($products->isEmpty())
  <div class="card">
    <div class="card-body text-center py-5">
      <i class="bx bx-package bx-lg text-muted mb-3"></i>
      <p class="text-muted mb-0">{{ __('labels.no_products_match') }}</p>
      <a href="{{ route('buyer.products.index') }}" class="btn btn-outline-secondary mt-3">{{ __('labels.clear_filters') }}</a>
    </div>
  </div>
@else
  <div class="buyer-product-grid mb-4">
    @foreach($products as $product)
    <div class="buyer-product-card">
      <a href="{{ route('buyer.products.show', $product) }}" class="buyer-product-card-image text-decoration-none">
        @if($product->primaryImage())
          <img src="{{ Storage::url($product->primaryImage()) }}" alt="{{ $product->title }}">
        @else
          <div class="d-flex align-items-center justify-content-center h-100 text-muted"><i class="bx bx-image bx-lg"></i></div>
        @endif
      </a>
      <div class="buyer-product-card-body">
        <a href="{{ route('buyer.products.show', $product) }}" class="text-decoration-none text-body">
          <h3 class="buyer-product-card-title">{{ $product->title }}</h3>
          <div class="buyer-product-card-price">{{ $product->priceDisplay() }}</div>
          <div class="buyer-product-card-moq">{{ __('labels.moq') }}: {{ $product->moq ?? '—' }}</div>
        </a>
      </div>
      <div class="buyer-product-card-actions">
        <a href="{{ route('buyer.rfqs.create', ['product_id' => $product->id]) }}" class="btn btn-hanzo-primary">{{ __('labels.request_quote') }}</a>
      </div>
    </div>
    @endforeach
  </div>
  <div class="d-flex justify-content-center mt-4">
    {{ $products->links() }}
  </div>
@endif
@endsection
