@extends('layouts.public')

@section('title', __('landing.categories'))

@section('content')
<div class="hanzo-page-top">
  <div class="container py-4 py-lg-5">
    <h1 class="text-center mb-5" style="color: var(--hanzo-navy);">{{ __('landing.categories') }}</h1>
    <div class="row g-4">
      @forelse($categories as $cat)
      <div class="col-12 col-sm-6 col-lg-3">
        <a href="{{ route('categories.show', $cat) }}" class="text-decoration-none hanzo-category-card-link">
          <div class="hanzo-card hanzo-category-card p-0 h-100 overflow-hidden">
            <div class="hanzo-category-img" style="background-image: url('https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?w=400'); height: 120px; background-size: cover; background-position: center;"></div>
            <div class="p-4 text-center">
              <i class="bx bx-package bx-lg mb-2 hanzo-category-icon"></i>
            <h5 class="hanzo-category-title">{{ $cat->name }}</h5>
            @if($cat->moq_default)
            <small class="text-muted">{{ __('labels.quantity') }}: {{ number_format($cat->moq_default) }}+</small>
            @endif
              <p class="small text-muted mt-2 mb-0">{{ Str::limit($cat->description ?? '', 60) }}</p>
            </div>
          </div>
        </a>
      </div>
      @empty
      <div class="col-12 text-center">
        <p class="text-muted">No categories yet.</p>
        <a href="{{ url('/') }}#estimate" class="btn btn-hanzo-primary">Request Quote</a>
      </div>
      @endforelse
    </div>
  </div>
</div>
@endsection
