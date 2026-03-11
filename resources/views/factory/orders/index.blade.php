@extends('layouts.factory')

@section('title', 'Orders')

@section('content')
<h4 class="fw-bold mb-4">Orders</h4>
<div class="card">
  <div class="card-body">
    @if($orders->isEmpty())
      <p class="text-muted mb-0">No orders yet.</p>
    @else
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>{{ __('labels.order_code') }}</th>
              <th>{{ __('labels.status') }}</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($orders as $order)
            <tr>
              <td>
                <span class="fw-medium">{{ $order->order_code }}</span>
                <span class="d-block small text-muted">{{ Str::limit($order->displayName(), 40) }}</span>
              </td>
              <td><span class="badge bg-label-info">{{ trans_status($order->milestone_status) }}</span></td>
              <td><a href="{{ route('factory.orders.show', $order) }}" class="btn btn-sm btn-primary">View</a></td>
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
