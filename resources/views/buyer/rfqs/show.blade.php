@extends('layouts.buyer')

@section('title', __('labels.rfq') . ' ' . $rfq->code)

@section('content')
<h4 class="fw-bold mb-4">{{ __('labels.rfq') }} {{ $rfq->code }}</h4>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="card mb-4">
  <div class="card-header">
    <span class="badge bg-label-{{ $rfq->status === 'new' ? 'warning' : 'info' }}">{{ $rfq->status }}</span>
    {{ $rfq->category->name }} | Qty: {{ number_format($rfq->quantity) }}
  </div>
  <div class="card-body">
    <p><strong>{{ __('labels.description') }}:</strong> {{ $rfq->description ?? '-' }}</p>
    <p><strong>{{ __('labels.specs') }}:</strong> {{ $rfq->specs ?? '-' }}</p>
    <p><strong>{{ __('labels.delivery') }}:</strong> {{ $rfq->delivery_city ?? '-' }}, {{ $rfq->delivery_country ?? '-' }}</p>
  </div>
</div>
@if($rfq->quotations->isNotEmpty())
<div class="card">
  <div class="card-header">Quotes</div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr><th>Code</th><th>Total (USD)</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
          @foreach($rfq->quotations as $q)
          <tr>
            <td>{{ $q->quote_code }}</td>
            <td>${{ number_format($q->total_landed_cost, 2) }}</td>
            <td><span class="badge bg-{{ $q->status === 'sent' ? 'info' : ($q->status === 'accepted' ? 'success' : 'secondary') }}">{{ $q->status }}</span></td>
            <td>
              @if($q->status === 'sent')
              <a href="{{ route('buyer.quotes.show', $q) }}" class="btn btn-sm btn-primary">View / Accept</a>
              @else
              <a href="{{ route('buyer.quotes.show', $q) }}" class="btn btn-sm btn-outline-primary">View</a>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif
<a href="{{ route('buyer.rfqs.index') }}" class="btn btn-outline-secondary mt-3">Back</a>
@endsection
