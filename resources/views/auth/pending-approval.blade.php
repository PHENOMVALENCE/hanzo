<x-guest-layout>
<div class="text-center">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Account Pending Approval</h1>
    <p class="text-gray-600 mb-6">Your account is pending approval. Please wait for an administrator to approve your account before you can access the platform.</p>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-300">
            Log Out
        </button>
    </form>
</div>
</x-guest-layout>
