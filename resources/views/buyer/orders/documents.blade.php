@extends('layouts.buyer')

@section('title', 'Documents - ' . $order->order_code)

@section('content')
<h4 class="fw-bold mb-4">Documents - Order {{ $order->order_code }}</h4>
<div class="card">
  <div class="card-body">
    @if($documents->isEmpty())
      <p class="text-muted mb-0">No documents for this order.</p>
    @else
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr><th>Type</th><th>Date</th><th></th></tr>
          </thead>
          <tbody>
            @foreach($documents as $doc)
            <tr>
              <td>{{ $doc->type }}</td>
              <td>{{ $doc->created_at->format('Y-m-d') }}</td>
              <td><a href="{{ route('buyer.documents.download', $doc) }}" class="btn btn-sm btn-primary">Download</a></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
<a href="{{ route('buyer.orders.show', $order) }}" class="btn btn-outline-secondary">Back</a>
@endsection
