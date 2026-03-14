<x-guest-layout>
  <h4 class="mb-2">Set Up Your Factory Account</h4>
  <p class="text-muted small mb-4">You've been invited to join HANZO. Set a password to complete your account setup.</p>

  <form method="POST" action="{{ route('factory.invite.accept.store') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $invitation->token }}">
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input id="email" type="email" class="form-control" value="{{ $invitation->user->email }}" readonly disabled>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Password *</label>
      <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password" />
      <x-input-error :messages="$errors->get('password')" class="mt-1" />
    </div>
    <div class="mb-4">
      <label for="password_confirmation" class="form-label">Confirm password *</label>
      <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password" />
    </div>
    <button type="submit" class="btn btn-hanzo px-4 py-2 rounded w-100">Set Up Account</button>
  </form>
</x-guest-layout>
