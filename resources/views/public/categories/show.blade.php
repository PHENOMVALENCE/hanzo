@extends('layouts.public')

@section('title', $category->name)

@section('content')
<div style="padding-top: 5rem;">
  <div class="container py-5">
    <h1 style="color: var(--hanzo-navy);">{{ $category->name }}</h1>
    @if($category->description)
    <p class="text-muted mb-4">{{ $category->description }}</p>
    @endif
    @if($category->moq_default)
    <p class="mb-4"><strong>Typical MOQ:</strong> {{ number_format($category->moq_default) }} units</p>
    @endif
    <a href="{{ route('register') }}?category={{ $category->slug }}" class="btn btn-hanzo-primary">Request Quote</a>
    <a href="{{ route('categories.index') }}" class="btn btn-hanzo-muted ms-2">Back to Categories</a>
  </div>
</div>
@endsection
