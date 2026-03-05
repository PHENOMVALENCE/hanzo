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
      <div class="row g-2">
        <div class="col-md-3">
          <select name="order_id" class="form-select" required>
            <option value="">Select Order</option>
            @foreach(\App\Models\Order::all() as $o)
            <option value="{{ $o->id }}">{{ $o->order_code }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <select name="type" class="form-select" required>
            <option value="invoice">Invoice</option>
            <option value="packing_list">Packing List</option>
            <option value="bl_awb">BL/AWB</option>
            <option value="customs">Customs</option>
            <option value="delivery">Delivery</option>
          </select>
        </div>
        <div class="col-md-4">
          <input type="file" name="file" class="form-control" required>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary">Upload</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="card">
  <div class="card-body">
    @if($documents->isEmpty())
      <p class="text-muted mb-0">No documents.</p>
    @else
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Order</th>
              <th>Type</th>
              <th>Uploaded</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($documents as $doc)
            <tr>
              <td>{{ $doc->order->order_code }}</td>
              <td>{{ $doc->type }}</td>
              <td>{{ $doc->created_at->format('Y-m-d') }}</td>
              <td>
                <form method="POST" action="{{ route('admin.documents.destroy', $doc) }}" class="d-inline" onsubmit="return confirm('Delete?')">
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
