@extends('layouts.admin')

@section('title', 'Product Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0">Product Management</h4>
  <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
    <i class="bx bx-plus me-1"></i> Add Platform Product
  </a>
</div>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('admin.products.index') }}" method="GET" class="card mb-4">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label small">Status</label>
        <select name="status" class="form-select">
          <option value="">All</option>
          <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
          <option value="pending_approval" {{ request('status') === 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
          <option value="live" {{ request('status') === 'live' ? 'selected' : '' }}>Live</option>
          <option value="disabled" {{ request('status') === 'disabled' ? 'selected' : '' }}>Disabled</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label small">Factory</label>
        <select name="factory" class="form-select">
          <option value="">All</option>
          @foreach($factories as $f)
            <option value="{{ $f->id }}" {{ request('factory') == $f->id ? 'selected' : '' }}>{{ $f->factory_name ?? $f->user?->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4 d-flex align-items-end">
        <button type="submit" class="btn btn-secondary">Filter</button>
      </div>
    </div>
  </div>
</form>

<div class="card">
  <div class="card-body">
    @if($products->isEmpty())
      <p class="text-muted mb-0">No products found.</p>
    @else
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Image</th>
              <th>Title</th>
              <th>Factory</th>
              <th>Category</th>
              <th>Price</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($products as $p)
            <tr>
              <td>
                @if($p->primaryImage())
                  <img src="{{ Storage::url($p->primaryImage()) }}" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:4px;">
                @else
                  <span class="avatar-initial rounded bg-label-secondary" style="width:48px;height:48px;display:inline-flex;align-items:center;justify-content:center;"><i class="bx bx-image"></i></span>
                @endif
              </td>
              <td>{{ Str::limit($p->title, 40) }}</td>
              <td>{{ $p->factory?->factory_name ?? ($p->is_platform_product ? 'Platform' : '—') }}</td>
              <td>{{ $p->category?->name ?? '—' }}</td>
              <td>{{ $p->priceDisplay() }}</td>
              <td>
                @php
                  $badge = match($p->status) {
                    'draft' => 'secondary',
                    'pending_approval' => 'warning',
                    'live' => 'success',
                    'disabled' => 'danger',
                    default => 'secondary'
                  };
                @endphp
                <span class="badge bg-{{ $badge }}">{{ trans_status($p->status) }}</span>
              </td>
              <td class="text-nowrap">
                <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                @if($p->status === 'pending_approval')
                  <form method="POST" action="{{ route('admin.products.approve', $p) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                  </form>
                @endif
                @if($p->status === 'live')
                  <form method="POST" action="{{ route('admin.products.disable', $p) }}" class="d-inline" onsubmit="return confirm('Disable this product?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-warning">Disable</button>
                  </form>
                @endif
                <form method="POST" action="{{ route('admin.products.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Permanently delete this product?');">
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
      {{ $products->links() }}
    @endif
  </div>
</div>
@endsection
