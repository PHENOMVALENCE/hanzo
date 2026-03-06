@extends('layouts.buyer')

@section('title', __('buyer.quotes.title'))

@section('content')
<h4 class="fw-bold mb-4">{{ __('buyer.quotes.my_quotes') }}</h4>
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
              <td>${{ number_format($q->total_landed_cost, 2) }}</td>
              <td><span class="badge bg-{{ $q->status === 'sent' ? 'info' : ($q->status === 'accepted' ? 'success' : 'secondary') }}">{{ $q->status }}</span></td>
              <td><a href="{{ route('buyer.quotes.show', $q) }}" class="btn btn-sm btn-primary">{{ __('buyer.rfqs.view') }}</a></td>
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
