@extends('layouts.buyer')

@section('title', __('Messages'))

@section('content')
<div class="mb-4">
  <h4 class="fw-bold mb-1">{{ __('Messages') }}</h4>
  <p class="text-muted small mb-0">{{ __('All supplier communication through HANZO. Your conversations are secure.') }}</p>
</div>

<div class="card">
  <div class="card-body text-center py-5">
    <i class="bx bx-message-dots text-muted" style="font-size: 3rem;"></i>
    <p class="text-muted mt-2 mb-0">{{ __('No messages yet.') }}</p>
    <p class="small text-muted">{{ __('Start a conversation from a supplier profile or RFQ.') }}</p>
    <a href="{{ route('buyer.suppliers.index') }}" class="btn btn-b2b-primary mt-2">{{ __('Browse Suppliers') }}</a>
  </div>
</div>
@endsection
