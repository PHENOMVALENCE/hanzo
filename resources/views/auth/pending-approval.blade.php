<x-guest-layout>
<div class="text-center">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">
        @if(auth()->user()->status === 'suspended')
            Application Not Approved
        @else
            Account Pending Approval
        @endif
    </h1>
    <p class="text-gray-600 mb-3">
        @if(auth()->user()->status === 'suspended')
            Your application was not approved. You can contact HANZO for more information.
        @else
            Your account is pending approval. Please wait for an administrator to approve your account before you can access the platform.
        @endif
    </p>
    @if(auth()->user()->status === 'suspended' && auth()->user()->approval_message)
    <div class="bg-light rounded p-3 mb-4 text-start">
        <strong>Message from HANZO:</strong><br>
        {{ auth()->user()->approval_message }}
    </div>
    @endif
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-300">
            Log Out
        </button>
    </form>
</div>
</x-guest-layout>
