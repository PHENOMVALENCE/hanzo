@extends('layouts.admin')

@section('title', 'Invite Factory')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
  <div>
    <h4 class="fw-bold mb-1">Invite Factory</h4>
    <p class="text-muted mb-0 small">Create a factory account and send an invitation email. The factory will set their password via the link.</p>
  </div>
  <a href="{{ route('admin.users.index', ['role' => 'factory']) }}" class="btn btn-outline-secondary">Back to Factories</a>
</div>

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('admin.invite-factory.store') }}">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Factory / Company name *</label>
          <input type="text" name="factory_name" class="form-control" value="{{ old('factory_name') }}" required />
          @error('factory_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Primary contact email *</label>
          <input type="email" name="email" class="form-control" value="{{ old('email') }}" required />
          @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Contact person name *</label>
          <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name') }}" required />
          @error('contact_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Contact phone</label>
          <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone') }}" />
        </div>
        <div class="col-12">
          <label class="form-label">Location in China</label>
          <input type="text" name="location_china" class="form-control" value="{{ old('location_china') }}" placeholder="e.g. Guangdong, Shenzhen" />
        </div>
        <div class="col-12">
          <label class="form-label">Product categories (comma-separated)</label>
          <input type="text" name="product_categories" class="form-control" value="{{ old('product_categories') }}" placeholder="e.g. Electronics, Textiles, Packaging" />
        </div>
        <div class="col-12">
          <label class="form-label">Internal admin notes</label>
          <textarea name="admin_notes" class="form-control" rows="3" placeholder="e.g. Met at Canton Fair, specializes in...">{{ old('admin_notes') }}</textarea>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Send Invitation</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
