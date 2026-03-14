@extends('layouts.public')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/hanzo/hanzo-home.css') }}">
@endpush

@section('title', 'HANZO - Structured Access to Global Manufacturing')

@section('content')
{{-- Hero --}}
<section class="hanzo-home-hero">
  <div class="container position-relative">
    <div class="row align-items-center">
      <div class="col-lg-7">
        <h1 class="hanzo-home-hero-title mb-3">{{ __('landing.tagline') }}</h1>
        <p class="hanzo-home-hero-sub mb-4">{{ __('landing.subtitle') }}</p>
        @guest
        <div class="d-flex flex-wrap gap-3">
          <a href="{{ route('register') }}" class="hanzo-home-hero-cta hanzo-home-hero-cta-primary">{{ __('landing.request_quote') }}</a>
          <a href="#categories" class="hanzo-home-hero-cta hanzo-home-hero-cta-outline">{{ __('landing.explore_categories') }}</a>
          <a href="{{ route('partner-with-hanzo') }}" class="hanzo-home-hero-cta hanzo-home-hero-cta-outline">{{ __('landing.list_factory') }}</a>
        </div>
        @else
        <div class="d-flex flex-wrap gap-3">
          @if(auth()->user()->hasRole('buyer'))
          <a href="{{ route('buyer.rfqs.create') }}" class="hanzo-home-hero-cta hanzo-home-hero-cta-primary">{{ __('landing.request_quote') }}</a>
          @endif
          @php $dash = auth()->user()->hasRole('admin') ? route('admin.dashboard') : (auth()->user()->hasRole('factory') ? route('factory.dashboard') : route('buyer.dashboard')); @endphp
          <a href="{{ $dash }}" class="hanzo-home-hero-cta hanzo-home-hero-cta-outline">Dashboard</a>
        </div>
        @endguest
        <div class="hanzo-home-trust">
          <span class="hanzo-home-trust-item"><i class="bx bx-check-shield"></i> {{ __('landing.verified_factories') }}</span>
          <span class="hanzo-home-trust-item"><i class="bx bx-receipt"></i> {{ __('landing.transparent_pricing') }}</span>
          <span class="hanzo-home-trust-item"><i class="bx bx-package"></i> {{ __('landing.logistics_managed') }}</span>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Categories --}}
<section id="categories" class="hanzo-home-categories">
  <div class="container">
    <h2 class="hanzo-home-section-title text-center">{{ __('landing.categories') }}</h2>
    <div class="row g-4">
      @php
        $cats = [
          ['bx-closet', __('landing.category_fashion'), __('landing.category_fashion_desc')],
          ['bx-package', __('landing.category_packaging'), __('landing.category_packaging_desc')],
          ['bx-home', __('landing.category_consumer'), __('landing.category_consumer_desc')],
          ['bx-cog', __('landing.category_machinery'), __('landing.category_machinery_desc')],
        ];
      @endphp
      @foreach($cats as $c)
      <div class="col-sm-6 col-lg-3">
        <a href="{{ route('categories.index') }}" class="hanzo-home-cat-card">
          <i class="bx {{ $c[0] }} hanzo-home-cat-icon"></i>
          <div class="hanzo-home-cat-title">{{ $c[1] }}</div>
          <div class="hanzo-home-cat-desc">{{ $c[2] }}</div>
        </a>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- How it works --}}
<section id="how-it-works" class="hanzo-home-steps">
  <div class="container">
    <h2 class="hanzo-home-section-title text-center">{{ __('landing.how_it_works') }}</h2>
    <div class="row g-4">
      @foreach([['bx-pencil', __('landing.step1'), __('landing.step1_desc')], ['bx-receipt', __('landing.step2'), __('landing.step2_desc')], ['bx-cog', __('landing.step3'), __('landing.step3_desc')], ['bx-package', __('landing.step4'), __('landing.step4_desc')]] as $i => $step)
      <div class="col-sm-6 col-lg-3">
        <div class="hanzo-home-step">
          <div class="d-flex align-items-center gap-2 mb-2">
            <div class="hanzo-home-step-num">{{ $i + 1 }}</div>
            <i class="bx {{ $step[0] }} text-warning" style="font-size: 1.25rem;"></i>
          </div>
          <div class="hanzo-home-step-title">{{ $step[1] }}</div>
          <p class="hanzo-home-step-desc mb-0">{{ $step[2] }}</p>
        </div>
      </div>
      @endforeach
    </div>
    <div class="text-center mt-4">
      <a href="{{ route('how-it-works') }}" class="btn btn-hanzo-secondary">Learn More</a>
    </div>
  </div>
</section>

{{-- Estimate --}}
<section id="estimate" class="hanzo-home-estimate">
  <div class="container">
    <h2 class="hanzo-home-section-title text-center">{{ __('landing.estimate_costs') }}</h2>
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="hanzo-home-estimate-card">
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

{{-- CTA strip --}}
<section class="hanzo-home-cta-strip">
  <div class="container">
    <h2>{{ __('landing.trusted_trade') }}</h2>
    <p class="mb-0">{{ __('landing.secure_footer') }}</p>
    @guest
    <a href="{{ route('register') }}" class="btn btn-lg hanzo-home-hero-cta hanzo-home-hero-cta-primary">{{ __('landing.request_quote') }}</a>
    @endguest
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
