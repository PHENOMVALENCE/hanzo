@extends('layouts.buyer')

@section('title', $product->title . ' | ' . config('app.name'))

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('buyer.products.index') }}">{{ __('labels.products') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($product->title, 40) }}</li>
  </ol>
</nav>

<div class="row">
  <div class="col-lg-5 mb-4">
    <div class="card">
      <div class="card-body p-0 position-relative" style="min-height: 300px; background: var(--hanzo-surface, #1a1f2e);">
        @php $productImages = $product->images ?? []; @endphp
        @if(!empty($productImages))
          <img id="product-main-image" src="{{ Storage::url($productImages[0]) }}" alt="{{ $product->title }}" class="w-100 rounded-start" style="object-fit: contain; max-height: 400px;">
          @if(count($productImages) > 1)
            <div class="d-flex gap-2 p-2 flex-wrap" style="border-top: 1px solid var(--hanzo-border, #e5e7eb);">
              @foreach($productImages as $idx => $imgPath)
              <button type="button" class="product-thumb-btn border rounded p-1 {{ $idx === 0 ? 'border-primary' : '' }}" data-src="{{ Storage::url($imgPath) }}" style="width: 50px; height: 50px; overflow: hidden;">
                <img src="{{ Storage::url($imgPath) }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
              </button>
              @endforeach
            </div>
            @push('page-js')
            <script>
            document.querySelectorAll('.product-thumb-btn').forEach(function(btn) {
              btn.addEventListener('click', function() {
                document.getElementById('product-main-image').src = this.dataset.src;
                document.querySelectorAll('.product-thumb-btn').forEach(function(b) { b.classList.remove('border-primary'); });
                this.classList.add('border-primary');
              });
            });
            </script>
            @endpush
          @endif
        @else
          <div class="d-flex align-items-center justify-content-center h-100 min-vh-25 text-muted">
            <i class="bx bx-image bx-lg"></i>
          </div>
        @endif
      </div>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="card mb-4">
      <div class="card-body">
        <span class="badge bg-label-info mb-2">{{ trans_category($product->category) }}</span>
        <h3 class="fw-bold mb-3">{{ $product->title }}</h3>
        @if($product->description)
          <div class="mb-4">{!! nl2br(e($product->description)) !!}</div>
        @endif

        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <div class="hanzo-stat-box p-3 rounded bg-label-primary">
              <small class="text-muted d-block">{{ __('labels.moq') }}</small>
              <span class="fw-bold">{{ $product->moq ?? '—' }}</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="hanzo-stat-box p-3 rounded bg-label-info">
              <small class="text-muted d-block">Price range</small>
              <span class="fw-bold text-primary">{{ $product->priceDisplay() }}</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="hanzo-stat-box p-3 rounded bg-label-success">
              <small class="text-muted d-block">Lead time</small>
              <span class="fw-bold">{{ $product->lead_time_days ? $product->lead_time_days . ' days' : '—' }}</span>
            </div>
          </div>
        </div>

        @if($product->specs && count($product->specs) > 0)
          <h6 class="mb-2">{{ __('labels.specs') }}</h6>
          <ul class="list-unstyled small text-muted mb-0">
            @foreach($product->specs as $key => $val)
              <li><strong>{{ is_numeric($key) ? '' : $key }}:</strong> {{ $val }}</li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('buyer.rfqs.create', ['product_id' => $product->id]) }}" class="btn btn-hanzo-primary">
        <i class="bx bx-message-square-detail me-1"></i> {{ __('labels.request_quote') }}
      </a>
      <a href="{{ route('buyer.products.index') }}" class="btn btn-outline-secondary">{{ __('labels.back_to_catalog') ?? 'Back to catalog' }}</a>
    </div>
  </div>
</div>
@endsection
