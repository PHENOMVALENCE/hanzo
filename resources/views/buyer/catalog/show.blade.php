@extends('layouts.buyer')

@section('title', trans_category($category))

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('buyer.catalog.index') }}">{{ __('Product Catalog') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ trans_category($category) }}</li>
  </ol>
</nav>

<div class="row">
  <div class="col-lg-8">
    {{-- Image placeholder --}}
    <div class="card mb-4">
      <div class="product-image p-5 text-center" style="min-height: 300px; background: #f8fafc;">
        <i class="bx bx-package text-muted" style="font-size: 6rem;"></i>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">{{ __('Description') }}</div>
      <div class="card-body">
        <p class="mb-0">{{ $category->description ?? __('No description available.') }}</p>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">{{ __('Specifications') }}</div>
      <div class="card-body">
        <table class="table table-sm">
          <tbody>
            <tr><td class="text-muted" style="width: 40%;">{{ __('MOQ') }}</td><td>{{ $category->moq_default ? number_format($category->moq_default) . ' units' : '-' }}</td></tr>
            <tr><td class="text-muted">{{ __('Price Range') }}</td>
              <td>
                @if($category->price_min_per_unit && $category->price_max_per_unit)
                ${{ number_format($category->price_min_per_unit, 2) }} - ${{ number_format($category->price_max_per_unit, 2) }} / unit
                @else
                -
                @endif
              </td>
            </tr>
            <tr><td class="text-muted">{{ __('Lead Time') }}</td><td>2-4 weeks</td></tr>
            <tr><td class="text-muted">{{ __('Shipping') }}</td><td>FOB / CIF available</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">{{ __('Related Products') }}</div>
      <div class="card-body">
        <div class="row g-2">
          @foreach(\App\Models\Category::where('active', true)->where('id', '!=', $category->id)->take(4)->get() as $rel)
          <div class="col-6">
            <a href="{{ route('buyer.catalog.show', $rel) }}" class="d-block p-2 rounded border text-decoration-none text-body">{{ trans_category($rel) }}</a>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card sticky-top" style="top: 1rem;">
      <div class="card-header">
        <h5 class="mb-0">{{ trans_category($category) }}</h5>
        <small class="text-muted">SKU: {{ $category->slug }}</small>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <span class="text-muted d-block small">{{ __('MOQ') }}</span>
          <span class="fw-600">{{ $category->moq_default ? number_format($category->moq_default) . ' units' : '-' }}</span>
        </div>
        <div class="mb-3">
          <span class="text-muted d-block small">{{ __('Price') }}</span>
          <span class="fw-600">
            @if($category->price_min_per_unit && $category->price_max_per_unit)
            ${{ number_format($category->price_min_per_unit, 0) }} - ${{ number_format($category->price_max_per_unit, 0) }}/unit
            @else
            {{ __('Request quote') }}
            @endif
          </span>
        </div>
        <div class="mb-3">
          <span class="text-muted d-block small">{{ __('Lead Time') }}</span>
          <span>2-4 weeks</span>
        </div>

        <div class="mb-4 p-3 rounded" style="background: #f8fafc;">
          <div class="d-flex align-items-center gap-2 mb-2">
            <x-buyer.verified-badge />
            <span class="small text-muted">HANZO Verified Supplier</span>
          </div>
          <p class="small text-muted mb-0">{{ __('Response rate: 98%') }} · {{ __('Years active: 5+') }}</p>
        </div>

        <a href="{{ route('buyer.rfqs.create') }}?category={{ $category->id }}" class="btn btn-rfq w-100 btn-lg">
          <i class="bx bx-file me-2"></i> {{ __('Request Quotation') }}
        </a>
        <button type="button" class="btn btn-outline-secondary w-100 mt-2"><i class="bx bx-bookmark me-2"></i> {{ __('Save') }}</button>
      </div>
    </div>
  </div>
</div>
@endsection
