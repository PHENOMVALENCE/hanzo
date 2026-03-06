@extends('layouts.buyer')

@section('title', __('buyer.rfqs.title'))

@section('content')
<h4 class="fw-bold mb-4">{{ __('buyer.rfqs.my_rfqs') }}</h4>
<a href="{{ route('buyer.rfqs.create') }}" class="btn btn-primary mb-3">{{ __('buyer.rfqs.new_rfq') }}</a>
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
              <th>{{ __('buyer.rfqs.category') }}</th>
              <th>{{ __('buyer.rfqs.quantity') }}</th>
              <th>{{ __('buyer.rfqs.status') }}</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($rfqs as $rfq)
            <tr>
              <td>{{ $rfq->code }}</td>
              <td>{{ $rfq->category->name }}</td>
              <td>{{ number_format($rfq->quantity) }}</td>
              <td><span class="badge bg-label-{{ $rfq->status === 'new' ? 'warning' : 'info' }}">{{ $rfq->status }}</span></td>
              <td><a href="{{ route('buyer.rfqs.show', $rfq) }}" class="btn btn-sm btn-primary">{{ __('buyer.rfqs.view') }}</a></td>
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
