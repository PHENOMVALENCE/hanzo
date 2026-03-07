@extends('layouts.public')

@section('title', __('landing.how_it_works'))

@section('content')
<div style="padding-top: 5rem;">
  <div class="container py-5">
    <h1 class="text-center mb-5" style="color: var(--hanzo-navy);">{{ __('landing.how_it_works') }}</h1>
    <div class="row g-4 text-center">
      <div class="col-md-6 col-lg-3">
        <div class="hanzo-card p-4 h-100">
          <i class="bx bx-file bx-lg mb-3" style="color: var(--hanzo-gold);"></i>
          <h5 style="color: var(--hanzo-navy);">1. {{ __('landing.step1') }}</h5>
          <p class="text-muted small mb-0">{{ __('landing.step1_desc') }}</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="hanzo-card p-4 h-100">
          <i class="bx bx-message-detail bx-lg mb-3" style="color: var(--hanzo-gold);"></i>
          <h5 style="color: var(--hanzo-navy);">2. {{ __('landing.step2') }}</h5>
          <p class="text-muted small mb-0">{{ __('landing.step2_desc') }}</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="hanzo-card p-4 h-100">
          <i class="bx bx-factory bx-lg mb-3" style="color: var(--hanzo-gold);"></i>
          <h5 style="color: var(--hanzo-navy);">3. {{ __('landing.step3') }}</h5>
          <p class="text-muted small mb-0">{{ __('landing.step3_desc') }}</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="hanzo-card p-4 h-100">
          <i class="bx bx-truck bx-lg mb-3" style="color: var(--hanzo-gold);"></i>
          <h5 style="color: var(--hanzo-navy);">4. {{ __('landing.step4') }}</h5>
          <p class="text-muted small mb-0">{{ __('landing.step4_desc') }}</p>
        </div>
      </div>
    </div>
    <div class="text-center mt-5">
      <a href="{{ route('register') }}" class="btn btn-hanzo-primary">Request Quote</a>
    </div>
  </div>
</div>
@endsection
