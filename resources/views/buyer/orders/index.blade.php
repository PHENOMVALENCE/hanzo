@extends('layouts.buyer')

@section('title', __('buyer.orders.title'))

@section('content')
<div class="mb-4">
  <h4 class="fw-bold mb-1">{{ __('Orders & Shipments') }}</h4>
  <p class="text-muted small mb-0">{{ __('buyer.orders.my_orders') }} — {{ __('Track shipments and download invoices.') }}</p>
</div>
<div class="card">
  <div class="card-body">
    @if($orders->isEmpty())
      <p class="text-muted mb-0">{{ __('buyer.orders.no_orders') }}</p>
    @else
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>{{ __('buyer.orders.order_code') }}</th>
              <th>{{ __('buyer.rfqs.status') }}</th>
              <th>{{ __('buyer.quotes.total_usd') }}</th>
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
              <td>{{ money($order->quotation->total_landed_cost ?? 0) }}</td>
              <td><a href="{{ route('buyer.orders.show', $order) }}" class="btn btn-sm btn-b2b-primary">{{ __('buyer.rfqs.view') }}</a></td>
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
