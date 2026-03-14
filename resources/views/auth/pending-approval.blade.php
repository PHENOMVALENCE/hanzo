<x-guest-layout>
  <div class="text-center max-w-xl mx-auto">
    <div class="mb-4">
      <i class="bx bx-time-five" style="font-size: 4rem; color: var(--hanzo-gold);"></i>
    </div>
    <h1 class="h3 fw-bold mb-3">
      @if(auth()->user()->status === 'suspended')
        Application Not Approved
      @else
        Account Pending Approval
      @endif
    </h1>
    <p class="text-muted mb-4">
      @if(auth()->user()->status === 'suspended')
        Your application was not approved. {{ auth()->user()->approval_message ? 'Please see the message below.' : 'You can contact HANZO for more information.' }}
      @else
        Your registration has been received. We'll review your account within 24–48 hours.
      @endif
    </p>

    @if(auth()->user()->status === 'suspended' && auth()->user()->approval_message)
      <div class="bg-light rounded p-4 mb-4 text-start">
        <strong>Message from HANZO:</strong><br>
        {{ auth()->user()->approval_message }}
      </div>
    @endif

    @if(auth()->user()->status === 'pending')
      <div class="bg-light rounded p-4 mb-4 text-start">
        <p class="mb-2 fw-semibold">While you wait:</p>
        <ul class="mb-0 ps-3">
          @if(auth()->user()->hasRole('buyer'))
          <li>Browse product categories (view-only, no prices yet)</li>
          <li>Complete your company profile</li>
          <li>Upload KYC documents proactively</li>
          @else
          <li>Complete your factory profile</li>
          <li>Add product listings (admin will review before publish)</li>
          @endif
        </ul>
      </div>
      @if(auth()->user()->hasRole('buyer'))
      <p class="small text-muted mb-4">You cannot post RFQs, browse prices, or contact factories until approved.</p>
      @else
      <p class="small text-muted mb-4">Your factory profile and products will go live once admin approves.</p>
      @endif
      <a href="{{ route('profile.edit') }}" class="btn btn-hanzo-primary mb-3 d-inline-block">Complete Your Profile</a>
      <br>
      @if(auth()->user()->hasRole('buyer'))
      <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary mb-3 d-inline-block">Browse Categories</a>
      @endif
    @endif

    <div class="mt-4 pt-3 border-top">
      <form method="POST" action="{{ route('logout') }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-outline-secondary btn-sm">Log Out</button>
      </form>
    </div>
  </div>
</x-guest-layout>
