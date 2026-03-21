@extends('layouts.factory')

@section('title', 'My Products')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
  <div>
    <h4 class="fw-bold mb-1">My Products</h4>
    <p class="text-muted small mb-0">Manage your product catalog. Buyers discover your products here.</p>
  </div>
  <a href="{{ route('factory.products.create') }}" class="btn btn-primary">
    <i class="bx bx-plus me-2"></i> Add Product
  </a>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
  <div class="card-body">
    @if($products->isEmpty())
      <div class="text-center py-5">
        <i class="bx bx-package text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-2 mb-0">No products yet.</p>
        <p class="text-muted small">Add your first product to list it in the buyer catalog.</p>
        <a href="{{ route('factory.products.create') }}" class="btn btn-primary mt-3">Add Product</a>
      </div>
    @else
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th style="width: 60px;">Image</th>
              <th>Title</th>
              <th>Category</th>
              <th>Price</th>
              <th>MOQ</th>
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
              <td>{{ $p->category?->name ?? '—' }}</td>
              <td>{{ $p->priceDisplay() }}</td>
              <td>{{ $p->moq ?? '—' }}</td>
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
                <span class="badge bg-{{ $badge }}">{{ trans_status($p->status) ?? ucfirst(str_replace('_', ' ', $p->status)) }}</span>
              </td>
              <td>
                <a href="{{ route('factory.products.edit', $p) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                @if(!in_array($p->status, ['live']))
                <form method="POST" action="{{ route('factory.products.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Archive this product?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Archive</button>
                </form>
                @endif
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
