@extends('layouts.buyer')

@section('title', __('Suppliers'))

@section('content')
<div class="mb-4">
  <h4 class="fw-bold mb-1">{{ __('Supplier Directory') }}</h4>
  <p class="text-muted small mb-0">{{ __('Discover and connect with verified suppliers. All communication through HANZO.') }}</p>
</div>

<form action="{{ route('buyer.suppliers.index') }}" method="GET" class="mb-4">
  <div class="row g-2">
    <div class="col-md-8">
      <div class="hanzo-b2b-search d-flex align-items-center">
        <i class="bx bx-search ms-2 text-muted"></i>
        <input type="search" name="q" value="{{ request('q') }}" class="form-control border-0 shadow-none" placeholder="Search suppliers...">
      </div>
    </div>
    <div class="col-md-4 d-flex gap-2">
      <label class="d-flex align-items-center gap-2 mb-0">
        <input type="checkbox" name="verified" value="1" {{ request('verified') ? 'checked' : '' }}> {{ __('Verified only') }}
      </label>
      <button type="submit" class="btn btn-b2b-primary btn-sm">{{ __('Search') }}</button>
    </div>
  </div>
</form>

<div class="row g-4">
  @forelse($suppliers as $supplier)
  <div class="col-md-6 col-lg-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <h5 class="card-title mb-0">{{ $supplier->display_name ?? 'HANZO Verified Factory' }}</h5>
          @if(($supplier->verification_status ?? '') === 'verified')
          <x-buyer.verified-badge />
          @endif
        </div>
        <p class="text-muted small mb-2">
          <i class="bx bx-map-pin me-1"></i> {{ $supplier->location_region ?? 'China' }}
        </p>
        @if(is_array($supplier->categories ?? null) && count($supplier->categories) > 0)
        <div class="d-flex flex-wrap gap-1 mb-3">
          @foreach(array_slice($supplier->categories, 0, 3) as $catSlug)
          <span class="badge bg-light text-dark">{{ $catSlug }}</span>
          @endforeach
        </div>
        @endif
        <p class="small text-muted mb-0">Response rate: 95%+ · 5+ years active</p>
        <div class="d-flex gap-2 mt-3">
          <a href="{{ route('buyer.suppliers.show', $supplier->id) }}" class="btn btn-b2b-primary btn-sm flex-grow-1">{{ __('View Profile') }}</a>
          <a href="{{ route('buyer.messages.index') }}?supplier={{ $supplier->id }}" class="btn btn-outline-secondary btn-sm" title="{{ __('Message via platform') }}"><i class="bx bx-message-dots"></i></a>
        </div>
      </div>
    </div>
  </div>
  @empty
  <div class="col-12">
    <div class="card">
      <div class="card-body text-center py-5">
        <i class="bx bx-buildings text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-2 mb-0">{{ __('No suppliers found.') }}</p>
      </div>
    </div>
  </div>
  @endforelse
</div>

{{ $suppliers->links() }}
@endsection
