@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
  <div>
    <h4 class="fw-bold mb-1">User Management</h4>
    <p class="text-muted mb-0 small">Manage buyers, factories, and admin accounts</p>
  </div>
  <div class="d-flex gap-2">
    @if(request('role') === 'factory')
    <a href="{{ route('admin.invite-factory.create') }}" class="btn btn-outline-primary">
      <i class="bx bx-plus me-1"></i> Invite Factory
    </a>
    @endif
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
    <i class="bx bx-plus me-1"></i> Add User
    </a>
  </div>
</div>

<div class="mb-4">
  <div class="nav nav-pills nav-fill gap-2" role="tablist">
    <a href="{{ route('admin.users.index') }}" class="nav-link {{ !request('role') ? 'active' : '' }}">All Users</a>
    <a href="{{ route('admin.users.index', ['role' => 'buyer']) }}" class="nav-link {{ request('role') === 'buyer' ? 'active' : '' }}">
      <i class="bx bx-user me-1"></i> Buyers
    </a>
    <a href="{{ route('admin.users.index', ['role' => 'factory']) }}" class="nav-link {{ request('role') === 'factory' ? 'active' : '' }}">
      <i class="bx bx-building me-1"></i> Factories
    </a>
    <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" class="nav-link {{ request('role') === 'admin' ? 'active' : '' }}">
      <i class="bx bx-shield-alt-2 me-1"></i> Admin
    </a>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Photo</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Company</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
          <tr>
            <td>
              @if($user->avatarUrl())
                <img src="{{ $user->avatarUrl() }}" alt="" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
              @else
                <span class="avatar-initial rounded-circle bg-label-primary" style="width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;font-size:0.875rem;">
                  {{ strtoupper(substr($user->name, 0, 1)) }}
                </span>
              @endif
            </td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td><span class="badge bg-label-info">{{ $user->getRoleNames()->first() ?? '-' }}</span></td>
            <td>
              @if($user->status === 'approved')
                <span class="badge bg-success">Approved</span>
              @elseif($user->status === 'pending')
                <span class="badge bg-warning">Pending</span>
              @else
                <span class="badge bg-danger">Suspended</span>
              @endif
            </td>
            <td>{{ $user->company_name ?? '-' }}</td>
            <td>
              <a href="{{ route('admin.users.edit', [$user, 'return_role' => request('role')]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
              @if($user->id !== auth()->id())
              <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                <input type="hidden" name="return_role" value="{{ request('role') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center text-muted">No users found.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3 border-top border-secondary">
      {{ $users->links() }}
    </div>
  </div>
</div>
@endsection
