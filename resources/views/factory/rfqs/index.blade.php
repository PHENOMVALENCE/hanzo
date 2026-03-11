@extends('layouts.factory')

@section('title', __('labels.assigned_rfqs'))

@section('content')
<h4 class="fw-bold mb-4">{{ __('labels.assigned_rfqs') }}</h4>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="card">
  <div class="card-body">
    @if($rfqs->isEmpty())
      <p class="text-muted mb-0">{{ __('labels.no_assigned_rfqs') }}</p>
    @else
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Code</th>
              <th>Category</th>
              <th>Quantity</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($rfqs as $rfq)
            <tr>
              <td>{{ $rfq->code }}</td>
              <td>{{ trans_category($rfq->category) }}</td>
              <td>{{ number_format($rfq->quantity) }}</td>
              <td><span class="badge bg-label-{{ $rfq->status === 'assigned' ? 'info' : 'success' }}">{{ trans_status($rfq->status) }}</span></td>
              <td><a href="{{ route('factory.rfqs.show', $rfq) }}" class="btn btn-sm btn-primary">{{ __('labels.view_submit_price') }}</a></td>
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
