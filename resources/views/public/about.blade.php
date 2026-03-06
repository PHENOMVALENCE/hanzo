@extends('layouts.public')

@section('title', 'About HANZO')

@section('content')
<div style="padding-top: 5rem;">
  <div class="container py-5">
    <div class="text-center mb-5">
      <div class="hanzo-about-hero rounded-3 overflow-hidden mb-4" style="height: 200px; background: linear-gradient(rgba(15,27,42,0.7), rgba(15,27,42,0.8)), url('https://images.unsplash.com/photo-1557804506-669a67965ba0?w=1200') center/cover;"></div>
      <h1 class="hanzo-section-title mb-3">About HANZO</h1>
      <p class="lead text-muted mx-auto" style="max-width: 600px;">A controlled B2B trade platform connecting verified Chinese factories to buyers in Tanzania and East Africa, with end-to-end shipping and admin oversight.</p>
    </div>
    <div class="row g-4 justify-content-center">
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
    <div class="text-center mt-5">
      <a href="{{ route('contact') }}" class="btn btn-hanzo-secondary">Partner With Us</a>
    </div>
  </div>
</div>
@endsection
