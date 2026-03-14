@extends('layouts.buyer')

@section('title', $supplier->display_name ?? 'HANZO Verified Factory')

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('buyer.suppliers.index') }}">{{ __('Suppliers') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $supplier->display_name ?? 'HANZO Verified Factory' }}</li>
  </ol>
</nav>

<div class="row">
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-4">
          <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 64px; height: 64px;">
            <i class="bx bx-buildings text-primary" style="font-size: 2rem;"></i>
          </div>
          <div>
            <h4 class="mb-1">{{ $supplier->display_name ?? 'HANZO Verified Factory' }}</h4>
            @if(($supplier->verification_status ?? '') === 'verified')
            <x-buyer.verified-badge />
            @endif
          </div>
        </div>
        <p class="text-muted mb-0">{{ __('HANZO Verified Factory. General production information. Contact through platform messaging only.') }}</p>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">{{ __('Product Categories') }}</div>
      <div class="card-body">
        @if(is_array($supplier->categories) && count($supplier->categories) > 0)
        <div class="d-flex flex-wrap gap-2">
          @foreach($supplier->categories as $catSlug)
          <a href="{{ route('buyer.catalog.index', ['category' => $catSlug]) }}" class="badge bg-light text-dark text-decoration-none">{{ $catSlug }}</a>
          @endforeach
        </div>
        @else
        <p class="text-muted mb-0">{{ __('Multiple categories') }}</p>
        @endif
      </div>
    </div>

    <div class="card">
      <div class="card-header">{{ __('Stats') }}</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-4">
            <div class="text-center p-2 rounded bg-light">
              <span class="d-block fw-600">98%</span>
              <small class="text-muted">Response rate</small>
            </div>
          </div>
          <div class="col-4">
            <div class="text-center p-2 rounded bg-light">
              <span class="d-block fw-600">5+</span>
              <small class="text-muted">Years active</small>
            </div>
          </div>
          <div class="col-4">
            <div class="text-center p-2 rounded bg-light">
              <span class="d-block fw-600">{{ $supplier->location_region ?? 'China' }}</span>
              <small class="text-muted">Location</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card sticky-top" style="top: 1rem;">
      <div class="card-header">{{ __('Contact') }}</div>
      <div class="card-body">
        <p class="small text-muted">{{ __('All supplier contact goes through HANZO. Never share direct contact details.') }}</p>
        <a href="{{ route('buyer.messages.index') }}?supplier={{ $supplier->id }}" class="btn btn-b2b-primary w-100">
          <i class="bx bx-message-dots me-2"></i> {{ __('Message via Platform') }}
        </a>
        <a href="{{ route('buyer.rfqs.create') }}" class="btn btn-rfq w-100 mt-2">{{ __('Request Quote') }}</a>
      </div>
    </div>
  </div>
</div>
@endsection
