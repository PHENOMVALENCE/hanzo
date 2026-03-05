@extends('layouts.buyer')

@section('title', 'Quotes')

@section('content')
<h4 class="fw-bold mb-4">My Quotes</h4>
<div class="card">
  <div class="card-body">
    @if($quotations->isEmpty())
      <p class="text-muted mb-0">No quotes yet.</p>
    @else
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Quote Code</th>
              <th>RFQ</th>
              <th>Total (USD)</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($quotations as $q)
            <tr>
              <td>{{ $q->quote_code }}</td>
              <td>{{ $q->rfq->code }}</td>
              <td>${{ number_format($q->total_landed_cost, 2) }}</td>
              <td><span class="badge bg-{{ $q->status === 'sent' ? 'info' : ($q->status === 'accepted' ? 'success' : 'secondary') }}">{{ $q->status }}</span></td>
              <td><a href="{{ route('buyer.quotes.show', $q) }}" class="btn btn-sm btn-primary">View</a></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{ $quotations->links() }}
    @endif
  </div>
</div>
@endsection
