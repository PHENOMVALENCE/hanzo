@extends('layouts.buyer')

@section('title', __('buyer.quotes.title'))

@section('content')
<div class="mb-4">
  <h4 class="fw-bold mb-1">{{ __('Quotation Inbox') }}</h4>
  <p class="text-muted small mb-0">{{ __('buyer.quotes.my_quotes') }}</p>
</div>
<div class="card">
  <div class="card-body">
    @if($quotations->isEmpty())
      <p class="text-muted mb-0">{{ __('buyer.quotes.no_quotes') }}</p>
    @else
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>{{ __('buyer.quotes.quote_code') }}</th>
              <th>{{ __('buyer.quotes.rfq') }}</th>
              <th>{{ __('buyer.quotes.total_usd') }}</th>
              <th>{{ __('buyer.rfqs.status') }}</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($quotations as $q)
            <tr>
              <td>{{ $q->quote_code }}</td>
              <td>{{ $q->rfq->code }}</td>
              <td>{{ money($q->total_landed_cost) }}</td>
              <td><span class="badge bg-{{ $q->status === 'sent' ? 'info' : ($q->status === 'accepted' ? 'success' : 'secondary') }}">{{ trans_status($q->status) }}</span></td>
              <td>
  <a href="{{ route('buyer.quotes.show', $q) }}" class="btn btn-sm btn-b2b-primary me-1">{{ __('buyer.rfqs.view') }}</a>
  @if($q->status === 'sent')
  <a href="{{ route('buyer.quotes.show', $q) }}#accept" class="btn btn-sm btn-rfq">{{ __('buyer.quotes.accept') }}</a>
  @endif
</td>
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
