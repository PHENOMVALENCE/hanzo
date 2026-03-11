@extends('layouts.admin')

@section('title', 'Order ' . $order->order_code)

@section('content')
<h4 class="fw-bold mb-4">Order {{ $order->order_code }}</h4>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="row">
  <div class="col-md-8">
    <div class="card mb-4">
      <div class="card-body">
        <p><strong>{{ __('labels.buyer') }}:</strong> {{ $order->buyer->name }} ({{ $order->buyer->email }})</p>
        @php
          $total = (float) ($order->quotation->total_landed_cost ?? 0);
          $paid = $order->payments()->where('status', 'verified')->sum('amount_usd');
          $pending = max(0, $total - $paid);
        @endphp
        <p><strong>{{ __('labels.total') }}:</strong> {{ money($total) }}</p>
        <p><strong>{{ __('labels.amount_paid') }}:</strong> <span class="text-success">{{ money($paid) }}</span></p>
        <p><strong>{{ __('labels.amount_pending') }}:</strong> <span class="text-warning">{{ money($pending) }}</span></p>
        <p><strong>{{ __('labels.status') }}:</strong> <span class="badge bg-label-info">{{ trans_status($order->milestone_status) }}</span></p>
        <p><strong>Tracking:</strong> {{ $order->tracking_number ?? '-' }}</p>
        <p><strong>Est. Arrival:</strong> {{ $order->estimated_arrival?->format('Y-m-d') ?? '-' }}</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-header">Update Milestone</div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.orders.updateMilestone', $order) }}">
          @csrf
          <div class="mb-3">
            <select name="milestone_status" class="form-select" required>
              <option value="deposit_pending" {{ $order->milestone_status === 'deposit_pending' ? 'selected' : '' }}>{{ trans_status('deposit_pending') }}</option>
              <option value="deposit_paid" {{ $order->milestone_status === 'deposit_paid' ? 'selected' : '' }}>{{ trans_status('deposit_paid') }}</option>
              <option value="in_production" {{ $order->milestone_status === 'in_production' ? 'selected' : '' }}>{{ trans_status('in_production') }}</option>
              <option value="shipped" {{ $order->milestone_status === 'shipped' ? 'selected' : '' }}>{{ trans_status('shipped') }}</option>
              <option value="delivered" {{ $order->milestone_status === 'delivered' ? 'selected' : '' }}>{{ trans_status('delivered') }}</option>
            </select>
          </div>
          <div class="mb-3">
            <input type="text" name="tracking_number" class="form-control" placeholder="Tracking #" value="{{ $order->tracking_number }}">
          </div>
          <div class="mb-3">
            <input type="date" name="estimated_arrival" class="form-control" value="{{ $order->estimated_arrival?->format('Y-m-d') }}">
          </div>
          <button type="submit" class="btn btn-primary">Update</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-6">
    <div class="card mb-4">
      <div class="card-header">Payments</div>
      <div class="card-body">
        @if($order->payments->isEmpty())
          <p class="text-muted mb-0">No payments yet.</p>
        @else
          <ul class="list-unstyled mb-0">
            @foreach($order->payments as $p)
            <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
              <span>{{ \App\Models\Payment::TYPES[$p->type] ?? $p->type }} — {{ money($p->amount_usd) }} <span class="badge bg-{{ $p->status === 'verified' ? 'success' : ($p->status === 'rejected' ? 'danger' : 'warning') }} ms-1">{{ trans_status($p->status) }}</span></span>
              <a href="{{ route('admin.payments.show', $p) }}" class="btn btn-sm btn-outline-primary">View</a>
            </li>
            @endforeach
          </ul>
        @endif
        <a href="{{ route('admin.payments.index') }}?order={{ $order->id }}" class="btn btn-sm btn-link mt-2">All Payments</a>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card mb-4">
      <div class="card-header">Documents</div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.documents.upload') }}" enctype="multipart/form-data" class="mb-3">
          @csrf
          <input type="hidden" name="order_id" value="{{ $order->id }}">
          <div class="row g-2 mb-2">
            <div class="col-6">
              <select name="type" class="form-select form-select-sm">
                @foreach(\App\Models\Document::TYPES as $k => $v)
                <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-6">
              <input type="file" name="file" class="form-control form-control-sm" required accept=".pdf,image/*">
            </div>
          </div>
          <input type="text" name="description" class="form-control form-control-sm mb-2" placeholder="Description (optional)">
          <button type="submit" class="btn btn-sm btn-primary">Upload</button>
        </form>
        @if($order->documents->isEmpty())
          <p class="text-muted small mb-0">No documents yet.</p>
        @else
          <ul class="list-unstyled mb-0">
            @foreach($order->documents as $doc)
            <li class="d-flex justify-content-between align-items-center py-1 small">
              <span>{{ \App\Models\Document::TYPES[$doc->type] ?? $doc->type }}</span>
              <a href="{{ route('documents.download', $doc) }}" target="_blank">Download</a>
            </li>
            @endforeach
          </ul>
        @endif
        <a href="{{ route('admin.documents.index') }}" class="btn btn-sm btn-link mt-2">All Documents</a>
      </div>
    </div>
  </div>
</div>

<a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Back</a>
@endsection
