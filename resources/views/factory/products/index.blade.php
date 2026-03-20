@extends('layouts.factory')

<<<<<<< HEAD
@section('title', 'Products')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
  <div>
    <h4 class="fw-bold mb-1">Product Listings</h4>
    <p class="text-muted small mb-0">Manage your product catalog. Buyers discover your products here.</p>
  </div>
  <a href="{{ route('factory.products.create') }}" class="btn" style="background: #0d9488; color: #fff;">
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
        <p class="text-muted small">Add your first product to get discovered by buyers.</p>
        <a href="{{ route('factory.products.create') }}" class="btn mt-3" style="background: #0d9488; color: #fff;">Add Product</a>
      </div>
    @else
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th style="width: 60px;">Image</th>
              <th>Name / SKU</th>
              <th>Category</th>
              <th>MOQ</th>
              <th>Price</th>
              <th>Status</th>
              <th>Updated</th>
              <th></th>
=======
@section('title', 'My Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0">My Products</h4>
  <a href="{{ route('factory.products.create') }}" class="btn btn-primary">
    <i class="bx bx-plus me-1"></i> Add Product
  </a>
</div>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="card">
  <div class="card-body">
    @if($products->isEmpty())
      <p class="text-muted mb-0">No products yet. Add your first product to list it in the buyer catalog.</p>
      <a href="{{ route('factory.products.create') }}" class="btn btn-primary mt-3">Add Product</a>
    @else
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Image</th>
              <th>Title</th>
              <th>Category</th>
              <th>Price</th>
              <th>MOQ</th>
              <th>Status</th>
              <th>Actions</th>
>>>>>>> 3a34daee (Hanzo in b2b style)
            </tr>
          </thead>
          <tbody>
            @foreach($products as $p)
            <tr>
              <td>
<<<<<<< HEAD
                @if($p->image_path)
                <img src="{{ $p->image_url }}" alt="" class="rounded" style="width: 48px; height: 48px; object-fit: cover;">
                @else
                <div class="rounded d-flex align-items-center justify-content-center bg-light" style="width: 48px; height: 48px;">
                  <i class="bx bx-package text-muted"></i>
                </div>
                @endif
              </td>
              <td>
                <span class="fw-600">{{ $p->name }}</span>
                @if($p->sku)
                <br><small class="text-muted">{{ $p->sku }}</small>
                @endif
              </td>
              <td>{{ trans_category($p->category) }}</td>
              <td>{{ number_format($p->moq) }}</td>
              <td>{{ $p->price_range ?? '—' }}</td>
              <td>
                @php
                  $badge = match($p->status) {
                    'active' => 'success',
                    'pending_review' => 'warning',
                    'draft' => 'secondary',
                    'rejected' => 'danger',
                    'archived' => 'dark',
                    default => 'secondary',
                  };
                @endphp
                <span class="badge bg-{{ $badge }}">{{ ucfirst(str_replace('_', ' ', $p->status)) }}</span>
              </td>
              <td><small class="text-muted">{{ $p->updated_at->format('M j, Y') }}</small></td>
              <td>
                <div class="d-flex gap-1">
                  <a href="{{ route('factory.products.edit', $p) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                  <form method="POST" action="{{ route('factory.products.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Remove this product?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                  </form>
                </div>
=======
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
                <span class="badge bg-{{ $badge }}">{{ trans_status($p->status) }}</span>
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
>>>>>>> 3a34daee (Hanzo in b2b style)
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
