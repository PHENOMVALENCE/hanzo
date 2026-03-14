@extends('layouts.buyer')

@section('title', __('buyer.rfqs.title'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
  <div>
    <h4 class="fw-bold mb-1">{{ __('RFQ Center') }}</h4>
    <p class="text-muted small mb-0">{{ __('buyer.rfqs.my_rfqs') }}</p>
  </div>
  <a href="{{ route('buyer.rfqs.create') }}" class="btn btn-rfq"><i class="bx bx-plus me-2"></i>{{ __('buyer.rfqs.new_rfq') }}</a>
</div>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="card">
  <div class="card-body">
    @if($rfqs->isEmpty())
      <p class="text-muted mb-0">{{ __('buyer.rfqs.no_rfqs') }}</p>
    @else
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>{{ __('buyer.rfqs.code') }}</th>
              <th>{{ __('labels.description') }}</th>
              <th>{{ __('buyer.rfqs.category') }}</th>
              <th>{{ __('buyer.rfqs.quantity') }}</th>
              <th>{{ __('buyer.rfqs.status') }}</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($rfqs as $rfq)
            <tr>
              <td><span class="fw-medium">{{ $rfq->code }}</span></td>
              <td><span class="text-muted small">{{ Str::limit(strip_tags($rfq->description ?? ''), 35) ?: '-' }}</span></td>
              <td>{{ trans_category($rfq->category) }}</td>
              <td>{{ number_format($rfq->quantity) }}</td>
              <td><span class="badge bg-label-{{ $rfq->status === 'new' ? 'warning' : 'info' }}">{{ trans_status($rfq->status) }}</span></td>
              <td><a href="{{ route('buyer.rfqs.show', $rfq) }}" class="btn btn-sm btn-b2b-primary">{{ __('buyer.rfqs.view') }}</a></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{ $rfqs->links() }}
    @endif
  </div>
</div>
@endsection
