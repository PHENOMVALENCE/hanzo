@extends('layouts.buyer')

@section('title', 'My Orders')

@section('content')
<h4 class="fw-bold mb-4">My Orders</h4>
<div class="card">
  <div class="card-body">
    @if($orders->isEmpty())
      <p class="text-muted mb-0">No orders yet.</p>
    @else
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Order Code</th>
              <th>Status</th>
              <th>Total (USD)</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($orders as $order)
            <tr>
              <td>{{ $order->order_code }}</td>
              <td><span class="badge bg-label-info">{{ $order->milestone_status }}</span></td>
              <td>${{ number_format($order->quotation->total_landed_cost ?? 0, 2) }}</td>
              <td><a href="{{ route('buyer.orders.show', $order) }}" class="btn btn-sm btn-primary">View</a></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{ $orders->links() }}
    @endif
  </div>
</div>
@endsection
