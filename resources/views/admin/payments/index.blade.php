@extends('layouts.admin')

@section('title', 'Payments')

@section('content')
<h4 class="fw-bold mb-4">Payments</h4>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="card">
  <div class="card-body">
    @if($payments->isEmpty())
      <p class="text-muted mb-0">No payments.</p>
    @else
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Order</th>
              <th>Type</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($payments as $payment)
            <tr>
              <td>{{ $payment->order->order_code }}</td>
              <td>{{ $payment->type }}</td>
              <td>${{ number_format($payment->amount_usd, 2) }}</td>
              <td><span class="badge bg-{{ $payment->status === 'verified' ? 'success' : ($payment->status === 'rejected' ? 'danger' : 'warning') }}">{{ $payment->status }}</span></td>
              <td>
                @if($payment->status === 'pending')
                <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-success">Verify</button>
                </form>
                <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                </form>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{ $payments->links() }}
    @endif
  </div>
</div>
@endsection
