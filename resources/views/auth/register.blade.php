<x-guest-layout>
    <p class="text-muted small mb-4">Register as a buyer. Your account will be reviewed by HANZO before you can access the platform.</p>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="row g-3">
            <div class="col-12">
                <label for="name" class="form-label">Name *</label>
                <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>
            <div class="col-12">
                <label for="company" class="form-label">Company *</label>
                <input id="company" type="text" name="company" class="form-control" value="{{ old('company') }}" required />
                <x-input-error :messages="$errors->get('company')" class="mt-1" />
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Email *</label>
                <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>
            <div class="col-md-6">
                <label for="phone" class="form-label">Phone *</label>
                <input id="phone" type="text" name="phone" class="form-control" value="{{ old('phone') }}" required />
                <x-input-error :messages="$errors->get('phone')" class="mt-1" />
            </div>
            <div class="col-12">
                <label for="city" class="form-label">City *</label>
                <input id="city" type="text" name="city" class="form-control" value="{{ old('city') }}" required />
                <x-input-error :messages="$errors->get('city')" class="mt-1" />
            </div>
            <div class="col-12">
                <label for="password" class="form-label">Password *</label>
                <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>
            <div class="col-12">
                <label for="password_confirmation" class="form-label">Confirm Password *</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password" />
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 gap-2">
            <a class="text-muted small" href="{{ route('login') }}">{{ __('Already registered? Log in') }}</a>
            <button type="submit" class="btn btn-hanzo px-4 py-2 rounded">{{ __('Register') }}</button>
        </div>
    </form>
</x-guest-layout>
