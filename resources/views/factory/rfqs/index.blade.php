@extends('layouts.factory')

@section('title', 'Assigned RFQs')

@section('content')
<h4 class="fw-bold mb-4">Assigned RFQs</h4>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="card">
  <div class="card-body">
    @if($rfqs->isEmpty())
      <p class="text-muted mb-0">No RFQs assigned to you yet.</p>
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
              <td><span class="badge bg-label-{{ $rfq->status === 'assigned' ? 'info' : 'success' }}">{{ $rfq->status }}</span></td>
              <td><a href="{{ route('factory.rfqs.show', $rfq) }}" class="btn btn-sm btn-primary">View / Submit Price</a></td>
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
