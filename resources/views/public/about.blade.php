@extends('layouts.public')

@section('title', __('landing.nav_about') . ' | HANZO')

@section('content')
<div class="hanzo-page-top">
  <div class="container py-4 py-lg-5">
    {{-- Hero --}}
    <div class="text-center mb-5">
      <div class="hanzo-about-hero rounded-3 overflow-hidden mb-4 hanzo-about-hero-responsive" style="height: 180px; background: linear-gradient(rgba(15,27,42,0.75), rgba(15,27,42,0.85)), url('https://images.unsplash.com/photo-1557804506-669a67965ba0?w=1200') center/cover;"></div>
      <h1 class="hanzo-section-title mb-3" style="color: var(--hanzo-navy);">{{ __('landing.nav_about') }} HANZO</h1>
      <p class="lead text-muted mx-auto" style="max-width: 640px;">A controlled B2B trade platform connecting verified Chinese factories to buyers in Tanzania and East Africa, with end-to-end shipping and admin oversight.</p>
    </div>

    {{-- Values --}}
    <div class="row g-4 justify-content-center mb-5">
      <div class="col-md-4">
        <div class="hanzo-card hanzo-step-card p-4 text-center h-100">
          <i class="bx bx-check-shield bx-lg mb-2 hanzo-step-icon"></i>
          <h5 style="color: var(--hanzo-navy);">Verified Factories</h5>
          <p class="text-muted small mb-0">All manufacturing partners are vetted and quality-assured.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="hanzo-card hanzo-step-card p-4 text-center h-100">
          <i class="bx bx-receipt bx-lg mb-2 hanzo-step-icon"></i>
          <h5 style="color: var(--hanzo-navy);">Transparent Pricing</h5>
          <p class="text-muted small mb-0">Clear cost breakdowns from factory to delivery.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="hanzo-card hanzo-step-card p-4 text-center h-100">
          <i class="bx bx-package bx-lg mb-2 hanzo-step-icon"></i>
          <h5 style="color: var(--hanzo-navy);">Logistics Managed</h5>
          <p class="text-muted small mb-0">Shipping and clearing handled by HANZO.</p>
        </div>
      </div>
    </div>

    {{-- Contact Section --}}
    <section id="contact" class="hanzo-about-contact py-4">
      <div class="hanzo-card p-4 p-lg-5 mx-auto" style="max-width: 640px;">
        <h3 class="mb-3" style="color: var(--hanzo-navy);">Partner With Us</h3>
        <p class="text-muted mb-4">Interested in becoming a HANZO factory partner or buyer? Get in touch.</p>
        <div class="row g-3">
          <div class="col-12">
            <div class="d-flex align-items-center gap-3">
              <i class="bx bx-envelope bx-lg" style="color: var(--hanzo-gold);"></i>
              <div>
                <strong>Partnerships</strong>
                <p class="mb-0 small text-muted">partners@hanzo.tz</p>
              </div>
            </div>
          </div>
          <div class="col-12">
            <div class="d-flex align-items-center gap-3">
              <i class="bx bx-support bx-lg" style="color: var(--hanzo-gold);"></i>
              <div>
                <strong>Support</strong>
                <p class="mb-0 small text-muted">support@hanzo.tz</p>
              </div>
            </div>
          </div>
        </div>
        <p class="mt-4 mb-0 small text-muted">We typically respond within 24–48 hours.</p>
        <div class="mt-4">
          <a href="{{ route('register') }}" class="btn btn-hanzo-primary me-2">Request Quote</a>
        </div>
      </div>
    </section>
  </div>
</div>
@endsection
