@extends('layouts.admin')

@section('title', 'RFQ ' . $rfq->code)

@section('content')
<h4 class="fw-bold mb-4">RFQ {{ $rfq->code }}</h4>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="row">
  <div class="col-md-8">
    <div class="card mb-4">
      <div class="card-header">
        <span class="badge bg-label-{{ $rfq->status === 'new' ? 'warning' : 'info' }}">{{ $rfq->status }}</span>
        <span class="ms-2">{{ $rfq->category->name }} | Qty: {{ number_format($rfq->quantity) }}</span>
      </div>
      <div class="card-body">
        <p><strong>Description:</strong> {{ $rfq->description ?? '-' }}</p>
        <p><strong>Specs:</strong> {{ $rfq->specs ?? '-' }}</p>
        <p><strong>Delivery:</strong> {{ $rfq->delivery_city ?? '-' }}, {{ $rfq->delivery_country ?? '-' }}</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-header">Assign Factory</div>
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
      </div>
    </div>
  </div>
</div>
@if($rfq->assigned_factory_id)
<a href="{{ route('admin.quote-builder.edit', $rfq) }}" class="btn btn-primary">Build / Edit Quote</a>
@endif
<a href="{{ route('admin.rfqs.index') }}" class="btn btn-outline-secondary">Back</a>
@endsection
