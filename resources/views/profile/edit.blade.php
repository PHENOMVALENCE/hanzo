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
    <form method="post" action="{{ route('profile.update') }}" class="row g-3">
      @csrf
      @method('patch')
      <div class="col-12">
        <label class="form-label">{{ __('profile.photo') }}</label>
        <p class="text-muted small mb-2">{{ __('profile.avatar_choose') }}</p>
        <div class="d-flex flex-wrap gap-2 mb-2 align-items-center">
          @php $presetKeys = array_map('strval', array_keys(config('avatars.presets', []))); $useInitials = !$user->avatar_path || !in_array((string)$user->avatar_path, $presetKeys, true); @endphp
          <label class="avatar-option mb-0 {{ $useInitials ? 'selected' : '' }}" title="{{ __('profile.avatar_initials') }}">
            <input type="radio" name="avatar" value="initials" {{ $useInitials ? 'checked' : '' }} class="visually-hidden" data-preview="initials">
            <span class="avatar-initial rounded-circle d-inline-flex align-items-center justify-content-center bg-label-secondary" style="width:48px;height:48px;font-size:1.25rem;cursor:pointer;border:3px solid transparent;transition: border-color 0.2s;font-weight:600;">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
          </label>
          @foreach(config('avatars.presets', []) as $key => $seed)
          <label class="avatar-option mb-0 {{ ($user->avatar_path === (string)$key) ? 'selected' : '' }}" title="{{ $seed }}">
            <input type="radio" name="avatar" value="{{ $key }}" {{ ($user->avatar_path === (string)$key) ? 'checked' : '' }} class="visually-hidden" data-preview-url="https://api.dicebear.com/7.x/avataaars/svg?seed={{ urlencode($seed) }}">
            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ urlencode($seed) }}" alt="" class="rounded-circle" style="width:48px;height:48px;object-fit:cover;cursor:pointer;border:3px solid transparent;transition: border-color 0.2s;">
          </label>
          @endforeach
        </div>
        <div class="d-flex align-items-center gap-3 mt-2">
          <div id="avatarPreview" class="flex-shrink-0">
            @if($user->avatarUrl() && !$useInitials)
              <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="rounded-circle" style="width:64px;height:64px;object-fit:cover;">
            @else
              <span class="avatar-initial rounded-circle bg-label-primary d-inline-flex align-items-center justify-content-center" style="width:64px;height:64px;font-size:1.5rem;font-weight:600;">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
            @endif
          </div>
          <span class="text-muted small">{{ __('profile.avatar_preview') }}</span>
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

@push('page-css')
<style>.avatar-option.selected img,.avatar-option.selected span.avatar-initial{border-color:#D89B2B!important;box-shadow:0 0 0 2px rgba(216,155,43,0.3);}</style>
@endpush

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

@push('page-js')
<script>
document.querySelectorAll('.avatar-option input[name="avatar"]').forEach(function(radio) {
  radio.addEventListener('change', function() {
    document.querySelectorAll('.avatar-option').forEach(function(l) { l.classList.remove('selected'); });
    this.closest('.avatar-option').classList.add('selected');
    var preview = document.getElementById('avatarPreview');
    if (this.dataset.preview === 'initials') {
      var initials = '{{ strtoupper(substr($user->name, 0, 2)) }}';
      preview.innerHTML = '<span class="avatar-initial rounded-circle bg-label-primary d-inline-flex align-items-center justify-content-center" style="width:64px;height:64px;font-size:1.5rem;font-weight:600;">' + initials + '</span>';
    } else if (this.dataset.previewUrl) {
      preview.innerHTML = '<img src="' + this.dataset.previewUrl + '" alt="" class="rounded-circle" style="width:64px;height:64px;object-fit:cover;">';
    }
  });
});
</script>
@endpush
@endsection
