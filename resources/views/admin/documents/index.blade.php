@extends('layouts.admin')

@section('title', 'Documents')

@section('content')
<h4 class="fw-bold mb-4">Documents</h4>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="card mb-4">
  <div class="card-header">Upload Document</div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.documents.upload') }}" enctype="multipart/form-data">
      @csrf
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Order *</label>
          <select name="order_id" class="form-select" required>
            <option value="">Select Order</option>
            @foreach(\App\Models\Order::with('buyer')->orderBy('order_code')->get() as $o)
            <option value="{{ $o->id }}">{{ $o->order_code }} — {{ $o->buyer?->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Document Type *</label>
          <select name="type" class="form-select" required>
            @foreach(\App\Models\Document::TYPES as $k => $v)
            <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Description</label>
          <input type="text" name="description" class="form-control" placeholder="Optional note">
        </div>
        <div class="col-md-3">
          <label class="form-label">File (PDF, images) *</label>
          <input type="file" name="file" class="form-control" required accept=".pdf,image/*">
        </div>
        <div class="col-md-1 d-flex align-items-end">
          <button type="submit" class="btn btn-primary w-100">Upload</button>
        </div>
      </div>
      <small class="text-muted">Max 10MB. Stored securely. Buyer can download from their order.</small>
    </form>
  </div>
</div>
<div class="card">
  <div class="card-body">
    @if($documents->isEmpty())
      <p class="text-muted mb-0">No documents uploaded yet.</p>
    @else
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Order</th>
              <th>Type</th>
              <th>Description</th>
              <th>Uploaded By</th>
              <th>Date</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($documents as $doc)
            <tr>
              <td>
                <a href="{{ route('admin.orders.show', $doc->order) }}">{{ $doc->order->order_code }}</a>
              </td>
              <td>{{ \App\Models\Document::TYPES[$doc->type] ?? $doc->type }}</td>
              <td>{{ $doc->description ?: '—' }}</td>
              <td>{{ $doc->uploadedBy?->name ?? '—' }}</td>
              <td>{{ $doc->created_at->format('M j, Y H:i') }}</td>
              <td>
                <a href="{{ route('documents.download', $doc) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                  <i class="bx bx-download"></i> Download
                </a>
                <form method="POST" action="{{ route('admin.documents.destroy', $doc) }}" class="d-inline ms-1" onsubmit="return confirm('Delete this document?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{ $documents->links() }}
    @endif
  </div>
</div>
@endsection
