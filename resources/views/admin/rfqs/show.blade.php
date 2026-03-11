@extends('layouts.admin')

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
        <span class="badge bg-label-{{ $rfq->status === 'new' ? 'warning' : 'info' }}">{{ trans_status($rfq->status) }}</span>
        <span class="ms-2">{{ trans_category($rfq->category) }} | {{ __('labels.quantity') }}: {{ number_format($rfq->quantity) }}</span>
      </div>
      <div class="card-body">
        <p><strong>{{ __('labels.description') }}:</strong> {{ $rfq->description ?? '-' }}</p>
        <p><strong>{{ __('labels.specs') }}:</strong> {{ $rfq->specs ?? '-' }}</p>
        <p><strong>{{ __('labels.delivery') }}:</strong> {{ $rfq->delivery_city ?? '-' }}, {{ $rfq->delivery_country ?? '-' }}</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-verified">
      <div class="card-header">{{ __('labels.assign_factory') }}</div>
      <div class="card-body">
        @if($rfq->status === 'new')
        <form method="POST" action="{{ route('admin.rfqs.assign', $rfq) }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Factory</label>
            <select name="factory_id" class="form-select" required>
              <option value="">Select...</option>
              @foreach(\App\Models\Factory::with('user')->get() as $f)
              <option value="{{ $f->id }}">{{ $f->factory_name ?? $f->user->name }}</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Assign</button>
        </form>
        @else
        <p class="mb-0">Assigned to: {{ $rfq->assignedFactory?->factory_name ?? '-' }}</p>
        @endif
        @if($rfq->factoryQuotes->isNotEmpty())
        <hr class="my-3">
        <p class="small mb-2"><strong>Factory Price(s) Submitted</strong></p>
        @foreach($rfq->factoryQuotes->sortByDesc('created_at') as $fq)
        <p class="small mb-1">
          ${{ number_format($fq->unit_price_usd, 2) }}/unit → Total: ${{ number_format($fq->unit_price_usd * $rfq->quantity, 2) }}
          @if($fq->moq_confirmed)
          | MOQ: {{ number_format($fq->moq_confirmed) }}
          @endif
          @if($fq->lead_time_days)
          | Lead: {{ $fq->lead_time_days }}d
          @endif
        </p>
        @endforeach
        @endif
      </div>
    </div>
  </div>
</div>
@if($rfq->assigned_factory_id)
<a href="{{ route('admin.quote-builder.edit', $rfq) }}" class="btn btn-primary">{{ __('labels.build_edit_quote') }}</a>
@endif
<a href="{{ route('admin.rfqs.index') }}" class="btn btn-outline-secondary">Back</a>
@endsection
