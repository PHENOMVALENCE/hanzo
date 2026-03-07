@extends('layouts.buyer')

@section('title', 'Documents - ' . $order->order_code)

@section('content')
<h4 class="fw-bold mb-4">Documents — Order {{ $order->order_code }}</h4>
<p class="text-muted mb-4">Download invoices, packing lists, BL/AWB, customs papers, and delivery notes for this order.</p>
<div class="card">
  <div class="card-body">
    @if($documents->isEmpty())
      <div class="text-center py-5">
        <i class="bx bx-folder-open bx-lg text-muted mb-3 d-block"></i>
        <p class="text-muted mb-0">No documents for this order yet. Documents will appear here once HANZO uploads them.</p>
      </div>
    @else
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Document Type</th>
              <th>Description</th>
              <th>Uploaded</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($documents as $doc)
            <tr>
              <td>{{ \App\Models\Document::TYPES[$doc->type] ?? $doc->type }}</td>
              <td>{{ $doc->description ?: '—' }}</td>
              <td>{{ $doc->created_at->format('M j, Y') }}</td>
              <td>
                <a href="{{ route('documents.download', $doc) }}" class="btn btn-sm btn-primary" target="_blank">
                  <i class="bx bx-download me-1"></i> Download
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
<a href="{{ route('buyer.orders.show', $order) }}" class="btn btn-outline-secondary mt-3">Back to Order</a>
@endsection
