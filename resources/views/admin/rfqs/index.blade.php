@extends('layouts.admin')

@section('title', 'RFQs')

@section('content')
<h4 class="fw-bold mb-4">RFQs</h4>
<div class="card">
  <div class="card-body">
    @if($rfqs->isEmpty())
      <p class="text-muted mb-0">No RFQs yet.</p>
    @else
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Code</th>
              <th>Buyer</th>
              <th>Category</th>
              <th>Quantity</th>
              <th>Status</th>
              <th>Assigned Factory</th>
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
              <td><a href="{{ route('admin.rfqs.show', $rfq) }}" class="btn btn-sm btn-primary">View</a></td>
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
