@extends('layouts.admin')

@section('title', __('labels.rfqs'))

@section('content')
<h4 class="fw-bold mb-4">{{ __('labels.rfqs') }}</h4>
<p class="text-muted small mb-3">{{ __('labels.rfq_desc') }}</p>
<div class="card">
  <div class="card-body">
    @if($rfqs->isEmpty())
      <p class="text-muted mb-0">{{ __('labels.no_rfqs') }}</p>
    @else
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>{{ __('labels.code') }}</th>
              <th>{{ __('labels.buyer') }}</th>
              <th>{{ __('labels.category') }}</th>
              <th>{{ __('labels.quantity') }}</th>
              <th>{{ __('labels.status') }}</th>
              <th>{{ __('labels.assigned_factory') }}</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($rfqs as $rfq)
            <tr>
              <td>{{ $rfq->code }}</td>
              <td>{{ $rfq->buyer->name }}</td>
              <td>{{ $rfq->category->name }}</td>
              <td>{{ number_format($rfq->quantity) }}</td>
              <td><span class="badge bg-label-{{ $rfq->status === 'new' ? 'warning' : 'info' }}">{{ $rfq->status }}</span></td>
              <td>{{ $rfq->assignedFactory?->factory_name ?? '-' }}</td>
              <td><a href="{{ route('admin.rfqs.show', $rfq) }}" class="btn btn-sm btn-primary">{{ __('labels.view') }}</a></td>
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
