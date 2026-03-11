@extends('layouts.factory')

@section('title', __('labels.rfq') . ' ' . $rfq->code)

@section('content')
<h4 class="fw-bold mb-4">{{ __('labels.rfq') }} {{ $rfq->code }}</h4>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="row">
  <div class="col-md-8">
    <div class="card mb-4">
      <div class="card-header">
        <span class="badge bg-label-info">{{ trans_status($rfq->status) }}</span>
        {{ trans_category($rfq->category) }} | {{ __('labels.quantity') }}: {{ number_format($rfq->quantity) }}
      </div>
      <div class="card-body">
        <p><strong>{{ __('labels.description') }}:</strong> {{ $rfq->description ?? '-' }}</p>
        <p><strong>{{ __('labels.specs') }}:</strong> {{ $rfq->specs ?? '-' }}</p>
        <p><strong>{{ __('labels.delivery') }}:</strong> {{ $rfq->delivery_city ?? '-' }}, {{ $rfq->delivery_country ?? '-' }}</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-header">{{ __('labels.submit_your_price') }}</div>
      <div class="card-body">
        <form method="POST" action="{{ route('factory.rfqs.submit-price', $rfq) }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Unit Price (USD) *</label>
            <input type="number" step="0.01" name="unit_price_usd" class="form-control" required value="{{ old('unit_price_usd') }}">
          </div>
          <div class="mb-3">
            <label class="form-label" title="{{ __('labels.moq_desc') }}">{{ __('labels.moq_confirmed') }}</label>
            <input type="number" name="moq_confirmed" class="form-control" value="{{ old('moq_confirmed') }}">
          </div>
          <div class="mb-3">
            <label class="form-label">Lead Time (days)</label>
            <input type="number" name="lead_time_days" class="form-control" value="{{ old('lead_time_days') }}">
          </div>
          <div class="mb-3">
            <label class="form-label">Internal Notes</label>
            <textarea name="notes_internal" class="form-control" rows="2">{{ old('notes_internal') }}</textarea>
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>
<a href="{{ route('factory.rfqs.index') }}" class="btn btn-outline-secondary">Back</a>
@endsection
