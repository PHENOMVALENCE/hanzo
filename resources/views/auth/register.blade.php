<x-guest-layout>
  <h4 class="mb-2">{{ __('registration.title') }}</h4>
  <p class="text-muted small mb-4">{{ __('registration.subtitle') }}</p>

  <form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="row g-3">
      <div class="col-12">
        <label for="name" class="form-label">{{ __('registration.name') }} *</label>
        <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
      </div>
      <div class="col-12">
        <label for="company_name" class="form-label">{{ __('registration.company_name') }}</label>
        <input id="company_name" type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" />
        <x-input-error :messages="$errors->get('company_name')" class="mt-1" />
      </div>
      <div class="col-md-6">
        <label for="email" class="form-label">{{ __('registration.email') }} *</label>
        <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required />
        <x-input-error :messages="$errors->get('email')" class="mt-1" />
      </div>
      <div class="col-md-6">
        <label for="phone" class="form-label">{{ __('registration.phone') }} *</label>
        <input id="phone" type="text" name="phone" class="form-control" value="{{ old('phone') }}" required />
        <x-input-error :messages="$errors->get('phone')" class="mt-1" />
      </div>
      <div class="col-12">
        <label for="city" class="form-label">{{ __('registration.city') }}</label>
        <input id="city" type="text" name="city" class="form-control" value="{{ old('city') }}" />
        <x-input-error :messages="$errors->get('city')" class="mt-1" />
      </div>
      <div class="col-12">
        <label for="password" class="form-label">{{ __('registration.password') }} *</label>
        <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password" />
        <x-input-error :messages="$errors->get('password')" class="mt-1" />
      </div>
      <div class="col-12">
        <label for="password_confirmation" class="form-label">{{ __('registration.confirm_password') }} *</label>
        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password" />
      </div>
      <div class="col-12">
        <div class="form-check">
          <input id="terms" type="checkbox" name="terms" class="form-check-input" value="1" {{ old('terms') ? 'checked' : '' }} required />
          <label for="terms" class="form-check-label small">{{ __('registration.agree_terms') }}</label>
        </div>
        <x-input-error :messages="$errors->get('terms')" class="mt-1" />
      </div>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4">
      <a class="text-muted small" href="{{ route('login') }}">{{ __('registration.already_registered') }}</a>
      <button type="submit" class="btn btn-hanzo px-4 py-2 rounded">{{ __('registration.submit') }}</button>
    </div>
  </form>
</x-guest-layout>
