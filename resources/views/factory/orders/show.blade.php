@extends('layouts.factory')

@section('title', 'Order ' . $order->order_code)

@section('content')
<h4 class="fw-bold mb-4">Order {{ $order->order_code }}</h4>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header">Order Details</div>
      <div class="card-body">
        <p><strong>Status:</strong> <span class="badge bg-label-info">{{ str_replace('_', ' ', ucfirst($order->milestone_status)) }}</span></p>
        <p><strong>Tracking:</strong> {{ $order->tracking_number ?? '-' }}</p>
        <p><strong>Est. Arrival:</strong> {{ $order->estimated_arrival?->format('Y-m-d') ?? '-' }}</p>
        @if($order->quotation?->rfq)
        <p><strong>Product:</strong> {{ $order->quotation->rfq->description ?? $order->quotation->rfq->code }}</p>
        <p><strong>Quantity:</strong> {{ number_format($order->quotation->rfq->quantity ?? 0) }}</p>
        @endif
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">Production Updates</div>
      <div class="card-body">
        @if($order->productionUpdates->isEmpty())
          <p class="text-muted mb-0">No production updates yet.</p>
        @else
          <div class="timeline">
            @foreach($order->productionUpdates->sortByDesc('created_at') as $update)
            <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
              <div>
                @if($update->photo_path)
                  <a href="{{ \Illuminate\Support\Facades\Storage::url($update->photo_path) }}" target="_blank" class="d-block">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($update->photo_path) }}" alt="" class="rounded" style="max-width:120px;max-height:90px;object-fit:cover;">
                  </a>
                @endif
              </div>
              <div class="flex-grow-1">
                <span class="badge bg-label-primary mb-1">{{ str_replace('_', ' ', ucfirst($update->status)) }}</span>
                <small class="text-muted d-block">{{ $update->created_at->format('M j, Y H:i') }}</small>
                @if($update->note)
                  <p class="mb-0 mt-1 small">{{ $update->note }}</p>
                @endif
              </div>
            </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card card-verified mb-4">
      <div class="card-header">Submit Production Update</div>
      <div class="card-body">
        <form method="POST" action="{{ route('factory.orders.production-update', $order) }}" enctype="multipart/form-data">
          @csrf
          <div class="mb-3">
            <label class="form-label">Status *</label>
            <select name="status" class="form-select" required>
              <option value="started">Started</option>
              <option value="mid_production">Mid-Production</option>
              <option value="qc_ready">QC Ready</option>
              <option value="packed">Packed</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Note</label>
            <textarea name="note" class="form-control" rows="2" placeholder="Optional progress note">{{ old('note') }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Photo</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
          </div>
          <button type="submit" class="btn btn-primary w-100">Submit Update</button>
        </form>
      </div>
    </div>
  </div>
</div>

<a href="{{ route('factory.orders.index') }}" class="btn btn-outline-secondary">Back</a>
@endsection
