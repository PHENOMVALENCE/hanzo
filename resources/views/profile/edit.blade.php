@php
  $layout = auth()->user()->hasRole('admin') ? 'layouts.admin' : (auth()->user()->hasRole('factory') ? 'layouts.factory' : 'layouts.buyer');
@endphp
@extends($layout)

@section('title', __('profile.title'))

@section('content')
<div class="row">
  <div class="col-12">
    <h4 class="fw-bold mb-4">{{ __('profile.title') }}</h4>
  </div>
</div>

@if(session('status') === 'profile-updated')
  <div class="alert alert-success">{{ __('profile.saved') }}</div>
@endif
@if(session('status') === 'password-updated')
  <div class="alert alert-success">{{ __('profile.password_saved') }}</div>
@endif

{{-- Profile information --}}
<div class="card mb-4 hanzo-profile-card">
  <div class="card-header">
    <h5 class="mb-0">{{ __('profile.info') }}</h5>
  </div>
  <div class="card-body">
    <p class="text-muted small mb-4">{{ __('profile.info_desc') }}</p>
    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="row g-3">
      @csrf
      @method('patch')
      <div class="col-12">
        <label class="form-label">{{ __('profile.photo') }}</label>
        <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3">
          @if($user->avatarUrl())
            <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="rounded-circle flex-shrink-0" style="width:64px;height:64px;object-fit:cover;">
          @else
            <span class="avatar-initial rounded-circle bg-label-primary d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:64px;height:64px;font-size:1.5rem;">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
          @endif
          <input type="file" name="avatar" class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" style="max-width:100%;">
        </div>
        @error('avatar')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label" for="name">{{ __('profile.name') }} *</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label" for="email">{{ __('profile.email') }} *</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-12">
        <button type="submit" class="btn btn-primary w-100 w-md-auto">{{ __('profile.save') }}</button>
      </div>
    </form>
  </div>
</div>

{{-- Update password --}}
<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0">{{ __('profile.password') }}</h5>
  </div>
  <div class="card-body">
    <p class="text-muted small mb-4">{{ __('profile.password_desc') }}</p>
    <form method="post" action="{{ route('password.update') }}" class="row g-3">
      @csrf
      @method('put')
      <div class="col-12 col-md-6">
        <label class="form-label" for="current_password">{{ __('profile.current_password') }}</label>
        <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" id="current_password" name="current_password" autocomplete="current-password">
        @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-12"></div>
      <div class="col-12 col-md-6">
        <label class="form-label" for="password">{{ __('profile.new_password') }}</label>
        <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" id="password" name="password" autocomplete="new-password">
        @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label" for="password_confirmation">{{ __('profile.confirm_password') }}</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
      </div>
      <div class="col-12">
        <button type="submit" class="btn btn-primary w-100 w-md-auto">{{ __('profile.save') }}</button>
      </div>
    </form>
  </div>
</div>

{{-- Delete account --}}
<div class="card">
  <div class="card-header">
    <h5 class="mb-0 text-danger">{{ __('profile.delete_account') }}</h5>
  </div>
  <div class="card-body">
    <p class="text-muted small mb-4">{{ __('profile.delete_desc') }}</p>
    <button type="button" class="btn btn-danger w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#confirmDeleteAccount">
      {{ __('profile.delete_account') }}
    </button>
  </div>
</div>

{{-- Delete confirmation modal --}}
<div class="modal fade" id="confirmDeleteAccount" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="{{ route('profile.destroy') }}">
        @csrf
        @method('delete')
        <div class="modal-header">
          <h5 class="modal-title">{{ __('profile.delete_confirm') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted">{{ __('profile.delete_confirm_desc') }}</p>
          <div class="mb-0">
            <label class="form-label" for="delete_password">{{ __('profile.password_field') }}</label>
            <input type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" id="delete_password" name="password" placeholder="{{ __('Password') }}">
            @error('password', 'userDeletion')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('profile.cancel') }}</button>
          <button type="submit" class="btn btn-danger">{{ __('profile.delete_account') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

@if($errors->userDeletion->isNotEmpty())
@push('page-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
  var m = document.getElementById('confirmDeleteAccount');
  if (m) new bootstrap.Modal(m).show();
});
</script>
@endpush
@endif
@endsection
