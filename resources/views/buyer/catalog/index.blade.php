@extends('layouts.buyer')

@section('title', __('Product Catalog'))

@section('content')
<div class="mb-4">
  <h4 class="fw-bold mb-1">{{ __('Product Catalog') }}</h4>
  <p class="text-muted small mb-0">{{ __('Browse categories and request quotes from verified suppliers.') }}</p>
</div>

{{-- Search and Filters --}}
<div class="card mb-4">
  <div class="card-body">
    <form action="{{ route('buyer.catalog.index') }}" method="GET" class="row g-3">
      <div class="col-md-6">
        <div class="hanzo-b2b-search d-flex align-items-center">
          <i class="bx bx-search ms-2 text-muted"></i>
          <input type="search" name="q" value="{{ is_array($q = request('q')) ? '' : e($q ?? '') }}" class="form-control border-0 shadow-none" placeholder="Search products...">
        </div>
      </div>
      <div class="col-md-3">
        <select name="category" class="form-select">
          <option value="">{{ __('All Categories') }}</option>
          @foreach(\App\Models\Category::where('active', true)->get() as $c)
          <option value="{{ $c->slug }}" {{ request('category') == $c->slug ? 'selected' : '' }}>{{ trans_category($c) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <select name="sort" class="form-select">
          <option value="name">{{ __('Name') }}</option>
          <option value="moq" {{ request('sort') == 'moq' ? 'selected' : '' }}>{{ __('MOQ') }}</option>
        </select>
      </div>
      <div class="col-12">
        <button type="submit" class="btn btn-b2b-primary btn-sm">{{ __('Apply Filters') }}</button>
      </div>
    </form>
  </div>
</div>

{{-- Category Tree (simplified) --}}
<div class="row">
  <div class="col-lg-3 mb-4">
    <div class="card">
      <div class="card-header">{{ __('landing.nav_categories') }}</div>
      <div class="list-group list-group-flush">
        <a href="{{ route('buyer.catalog.index') }}" class="list-group-item list-group-item-action {{ !request('category') ? 'active' : '' }}">{{ __('All') }}</a>
        @foreach(\App\Models\Category::where('active', true)->orderBy('name')->get() as $c)
        <a href="{{ route('buyer.catalog.index', ['category' => $c->slug]) }}" class="list-group-item list-group-item-action {{ request('category') == $c->slug ? 'active' : '' }}">{{ trans_category($c) }}</a>
        @endforeach
      </div>
    </div>
  </div>
  <div class="col-lg-9">
    <div class="row g-4">
      @forelse($categories as $cat)
      <div class="col-md-6 col-xl-4">
        <x-buyer.product-card
          :name="trans_category($cat)"
          :moq="$cat->moq_default ? number_format($cat->moq_default) . ' units' : null"
          :priceRange="($cat->price_min_per_unit && $cat->price_max_per_unit) ? '$' . number_format($cat->price_min_per_unit, 0) . ' - $' . number_format($cat->price_max_per_unit, 0) . ' / unit' : null"
          :leadTime="'2-4 weeks'"
          supplierName="HANZO Verified"
          :verified="true"
          :href="route('buyer.catalog.show', $cat)"
          :rfq-href="route('buyer.rfqs.create') . '?category=' . $cat->id"
        />
      </div>
      @empty
      <div class="col-12">
        <div class="card">
          <div class="card-body text-center py-5">
            <i class="bx bx-search-alt text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2 mb-0">{{ __('No products found.') }}</p>
            <a href="{{ route('buyer.rfqs.create') }}" class="btn btn-rfq mt-3">{{ __('Request a Quote') }}</a>
          </div>
        </div>
      </div>
      @endforelse
    </div>
  </div>
</div>
@endsection
