@extends('layouts.buyer')

@section('title', __('Saved Items'))

@section('content')
<div class="mb-4">
  <h4 class="fw-bold mb-1">{{ __('Saved Products & Collections') }}</h4>
  <p class="text-muted small mb-0">{{ __('Your saved products and supplier favorites.') }}</p>
</div>

<div class="row">
  <div class="col-lg-3 mb-4">
    <div class="card">
      <div class="card-header">{{ __('Collections') }}</div>
      <div class="list-group list-group-flush">
        <a href="#" class="list-group-item list-group-item-action active">All Saved</a>
        <a href="#" class="list-group-item list-group-item-action">Favorites</a>
        <a href="#" class="list-group-item list-group-item-action">Compare List</a>
      </div>
    </div>
  </div>
  <div class="col-lg-9">
    <div class="card">
      <div class="card-body text-center py-5">
        <i class="bx bx-bookmark text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-2 mb-0">{{ __('No saved items yet.') }}</p>
        <p class="small text-muted">{{ __('Save products from the catalog to compare and request quotes later.') }}</p>
        <a href="{{ route('buyer.catalog.index') }}" class="btn btn-b2b-primary mt-2">{{ __('Browse Catalog') }}</a>
      </div>
    </div>
  </div>
</div>
@endsection
