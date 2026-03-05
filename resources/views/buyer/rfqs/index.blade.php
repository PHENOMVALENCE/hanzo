@extends('layouts.buyer')

@section('title', 'My RFQs')

@section('content')
<h4 class="fw-bold mb-4">My RFQs</h4>
<a href="{{ route('buyer.rfqs.create') }}" class="btn btn-primary mb-3">New RFQ</a>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="card">
  <div class="card-body">
    @if($rfqs->isEmpty())
      <p class="text-muted mb-0">No RFQs yet. Create one to get started.</p>
    @else
      <div class="table-responsive">
        <table class="table">
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
              <td>{{ $rfq->category->name }}</td>
              <td>{{ number_format($rfq->quantity) }}</td>
              <td><span class="badge bg-label-{{ $rfq->status === 'new' ? 'warning' : 'info' }}">{{ $rfq->status }}</span></td>
              <td><a href="{{ route('buyer.rfqs.show', $rfq) }}" class="btn btn-sm btn-primary">View</a></td>
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
