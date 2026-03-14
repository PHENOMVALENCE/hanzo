@extends('layouts.public')

@section('title', 'Partner With HANZO')

@section('content')
<div class="hanzo-page-top">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="text-center mb-5">
          <h1 class="mb-3" style="color: var(--hanzo-navy);">Partner With HANZO</h1>
          <p class="lead text-muted">We carefully select our supplier partners to maintain the highest quality standards. Submit your expression of interest and our team will review your application.</p>
        </div>

        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="hanzo-card p-4 p-lg-5">
          <form method="POST" action="{{ route('partner-with-hanzo.store') }}">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Company name *</label>
                <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" required />
                @error('company_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Product category *</label>
                <input type="text" name="product_category" class="form-control" value="{{ old('product_category') }}" placeholder="e.g. Electronics, Textiles" required />
                @error('product_category')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Contact email *</label>
                <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email') }}" required />
                @error('contact_email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">WhatsApp</label>
                <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp') }}" placeholder="+86 138 0000 0000" />
              </div>
              <div class="col-12">
                <label class="form-label">Message (optional)</label>
                <textarea name="message" class="form-control" rows="4" placeholder="Tell us about your factory and products...">{{ old('message') }}</textarea>
                @error('message')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-hanzo-primary">Submit</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
