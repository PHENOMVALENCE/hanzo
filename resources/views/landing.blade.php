@extends('layouts.public')

@section('title', 'HANZO - Structured Access to Global Manufacturing')

@section('content')
<section class="hanzo-hero hanzo-hero-overlay hanzo-hero-responsive py-4 py-lg-5">
  <div class="hanzo-hero-pattern"></div>
  <div class="container position-relative">
    <div class="row align-items-center">
      <div class="col-lg-7">
        <h1 class="display-5 fw-bold text-white mb-3 hanzo-hero-title hanzo-hero-title-responsive">{{ __('landing.tagline') }}</h1>
        <p class="lead text-white-50 mb-4 hanzo-hero-sub hanzo-hero-sub-responsive">{{ __('landing.subtitle') }}</p>
        @guest
        <div class="d-flex flex-wrap gap-2 hanzo-hero-btns">
          <a href="{{ route('register') }}" class="btn btn-hanzo-primary btn-lg hanzo-btn-touch">{{ __('landing.request_quote') }}</a>
          <a href="#categories" class="btn btn-outline-light btn-lg hanzo-btn-touch">{{ __('landing.explore_categories') }}</a>
        </div>
        @else
        <div class="d-flex flex-wrap gap-2 hanzo-hero-btns">
          @if(auth()->user()->hasRole('buyer'))
          <a href="{{ route('buyer.rfqs.create') }}" class="btn btn-hanzo-primary btn-lg hanzo-btn-touch">{{ __('landing.request_quote') }}</a>
          @endif
          @php $dash = auth()->user()->hasRole('admin') ? route('admin.dashboard') : (auth()->user()->hasRole('factory') ? route('factory.dashboard') : route('buyer.dashboard')); @endphp
          <a href="{{ $dash }}" class="btn btn-outline-light btn-lg hanzo-btn-touch">Dashboard</a>
        </div>
        @endguest
      </div>
    </div>
  </div>
</section>

<section id="categories" class="py-4 py-lg-5 hanzo-section-categories">
  <div class="container">
    <h2 class="text-center mb-5 hanzo-section-title">{{ __('landing.categories') }}</h2>
    <div class="row g-4">
      @php
        $categoryImages = [
          'fashion' => ['bx-closet', 'https://images.unsplash.com/photo-1558171813-4c088753af8f?w=400', __('landing.category_fashion'), __('landing.category_fashion_desc')],
          'packaging' => ['bx-package', 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400', __('landing.category_packaging'), __('landing.category_packaging_desc')],
          'consumer' => ['bx-home', 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400', __('landing.category_consumer'), __('landing.category_consumer_desc')],
          'machinery' => ['bx-cog', 'https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?w=400', __('landing.category_machinery'), __('landing.category_machinery_desc')]
        ];
      @endphp
      @foreach($categoryImages as $slug => $data)
      <div class="col-md-6 col-lg-3">
        <a href="{{ route('categories.index') }}#{{ $slug }}" class="text-decoration-none hanzo-category-card-link">
          <div class="hanzo-card hanzo-category-card p-0 h-100 overflow-hidden">
            <div class="hanzo-category-img" style="background-image: url('{{ $data[1] }}'); height: 140px; background-size: cover; background-position: center;"></div>
            <div class="p-4 text-center">
              <i class="bx {{ $data[0] }} bx-lg mb-2 hanzo-category-icon"></i>
              <h5 class="hanzo-category-title">{{ $data[2] }}</h5>
              <small class="text-muted">{{ $data[3] }}</small>
            </div>
          </div>
        </a>
      </div>
      @endforeach
    </div>
  </div>
</section>

<section id="how-it-works" class="py-4 py-lg-5 hanzo-section-steps">
  <div class="container">
    <h2 class="text-center mb-5 hanzo-section-title hanzo-section-title-dark">{{ __('landing.how_it_works') }}</h2>
    <div class="row g-4 text-center">
      @foreach([['bx-file', __('landing.step1'), __('landing.step1_desc')], ['bx-message-detail', __('landing.step2'), __('landing.step2_desc')], ['bx-factory', __('landing.step3'), __('landing.step3_desc')], ['bx-truck', __('landing.step4'), __('landing.step4_desc')]] as $i => $step)
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="hanzo-card hanzo-step-card p-4 h-100">
          <div class="hanzo-step-number">{{ $i+1 }}</div>
          <i class="bx {{ $step[0] }} bx-lg mb-2 hanzo-step-icon"></i>
          <h5 class="hanzo-step-title">{{ $step[1] }}</h5>
          <p class="text-muted small mb-0">{{ $step[2] }}</p>
        </div>
      </div>
      @endforeach
    </div>
    <div class="text-center mt-4">
      <a href="{{ route('how-it-works') }}" class="btn btn-hanzo-secondary">Learn More</a>
    </div>
  </div>
</section>

<section id="estimate" class="py-4 py-lg-5 hanzo-section-estimate">
  <div class="container">
    <h2 class="text-center mb-5 hanzo-section-title hanzo-section-title-dark">{{ __('landing.estimate_costs') }}</h2>
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="hanzo-card p-4 p-lg-5 hanzo-estimate-card">
          <form id="hanzo-estimate-form" class="row g-3">
            <div class="col-md-6">
              <label class="form-label">{{ __('landing.category') }}</label>
              <select class="form-select" name="category" id="est-category">
                @forelse(\App\Models\Category::all() as $cat)
                <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                @empty
                <option value="electronics">Electronics</option>
                <option value="textiles">Textiles</option>
                <option value="hardware">Hardware</option>
                @endforelse
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('landing.quantity') }}</label>
              <input type="number" class="form-control" name="quantity" id="est-quantity" value="5000" min="100">
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('landing.shipping_method') }}</label>
              <select class="form-select" name="method" id="est-method">
                <option value="sea">Sea Freight</option>
                <option value="air">Air Freight</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('landing.destination') }}</label>
              <select class="form-select" name="destination" id="est-destination">
                <option value="dar_es_salaam">Dar es Salaam</option>
                <option value="nairobi">Nairobi</option>
                <option value="kampala">Kampala</option>
                <option value="kigali">Kigali</option>
              </select>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-hanzo-primary w-100">{{ __('landing.calculate') }}</button>
            </div>
          </form>
          <div id="hanzo-estimate-result" class="mt-4 d-none">
            <hr>
            <h5 class="mb-3" style="color: var(--hanzo-navy);">{{ __('landing.estimated_costs') }}</h5>
            <ul class="list-unstyled mb-3" id="estimate-details"></ul>
            <p class="mb-2"><strong>{{ __('landing.estimated_total') }}:</strong> <span id="estimate-total" class="text-primary fs-4"></span></p>
            @guest
            <a href="{{ route('register') }}" class="btn btn-hanzo-primary">{{ __('landing.request_official_quote') }}</a>
            @else
            @if(auth()->user()->hasRole('buyer'))
            <a href="{{ route('buyer.rfqs.create') }}" class="btn btn-hanzo-primary">{{ __('landing.request_official_quote') }}</a>
            @endif
            @endguest
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-4 py-lg-5 hanzo-section-trust">
  <div class="container">
    <h2 class="text-center text-white mb-4">{{ __('landing.trusted_trade') }}</h2>
    <div class="row justify-content-center g-4">
      @foreach([__('landing.verified_factories'), __('landing.transparent_pricing'), __('landing.logistics_managed')] as $item)
      <div class="col-md-4 text-center">
        <div class="d-flex align-items-center justify-content-center gap-2 text-white">
          <i class="bx bx-check-circle" style="color: var(--hanzo-gold-soft); font-size: 1.25rem;"></i>
          <span>{{ $item }}</span>
        </div>
      </div>
      @endforeach
    </div>
    <p class="text-center text-white-50 mt-4 mb-0 small">{{ __('landing.secure_footer') }}</p>
  </div>
</section>
@endsection

@push('scripts')
<script>
document.getElementById('hanzo-estimate-form')?.addEventListener('submit', function(e) {
  e.preventDefault();
  var cat = document.getElementById('est-category').value;
  var qty = document.getElementById('est-quantity').value;
  var method = document.getElementById('est-method').value;
  var dest = document.getElementById('est-destination').value;
  fetch('/api/estimate?category=' + cat + '&qty=' + qty + '&method=' + method + '&destination=' + dest)
    .then(r => r.json())
    .then(data => {
      var details = document.getElementById('estimate-details');
      var methodLabel = method === 'sea' ? 'Sea' : 'Air';
      details.innerHTML = '<li>Shipping (' + methodLabel + '): $' + (data.min || 0) + ' - $' + (data.max || 0) + '</li>';
      if (data.message) details.innerHTML += '<li class="text-muted">' + data.message + '</li>';
      document.getElementById('estimate-total').textContent = data.min || data.max ? '$' + (data.min || 0) + ' - $' + (data.max || 0) : 'Contact HANZO for estimate';
      document.getElementById('hanzo-estimate-result').classList.remove('d-none');
    })
    .catch(function() {
      document.getElementById('estimate-details').innerHTML = '<li class="text-muted">Enter details and calculate for an estimate.</li>';
      document.getElementById('estimate-total').textContent = '—';
      document.getElementById('hanzo-estimate-result').classList.remove('d-none');
    });
});
</script>
@endpush
