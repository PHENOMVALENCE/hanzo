@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="row">
  <div class="col-12">
    <h4 class="fw-bold mb-4">Edit User: {{ $user->name }}</h4>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Current Photo</label>
          <div class="d-flex align-items-center gap-3">
            @if($user->avatarUrl())
              <img src="{{ $user->avatarUrl() }}" alt="" class="rounded-circle" style="width:64px;height:64px;object-fit:cover;">
            @else
              <span class="avatar-initial rounded-circle bg-label-primary" style="width:64px;height:64px;display:inline-flex;align-items:center;justify-content:center;font-size:1.5rem;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
              </span>
            @endif
            <span class="text-muted small">Upload new photo to replace</span>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="name">Name *</label>
          <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label" for="email">Email *</label>
          <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label" for="password">New Password</label>
          <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
          <small class="text-muted">Leave blank to keep current</small>
          @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label" for="password_confirmation">Confirm New Password</label>
          <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
        </div>
        <div class="col-md-6">
          <label class="form-label" for="role">Role *</label>
          <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
            <option value="buyer" {{ old('role', $user->getRoleNames()->first()) === 'buyer' ? 'selected' : '' }}>Buyer</option>
            <option value="factory" {{ old('role', $user->getRoleNames()->first()) === 'factory' ? 'selected' : '' }}>Factory</option>
            <option value="admin" {{ old('role', $user->getRoleNames()->first()) === 'admin' ? 'selected' : '' }}>Admin</option>
          </select>
          @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label" for="status">Status *</label>
          <select class="form-select" id="status" name="status" required>
            <option value="approved" {{ old('status', $user->status) === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="pending" {{ old('status', $user->status) === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="company_name">Company Name</label>
          <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $user->company_name) }}">
        </div>
        @if($user->factory)
        <div class="col-md-6">
          <label class="form-label" for="factory_name">Factory Name</label>
          <input type="text" class="form-control" id="factory_name" name="factory_name" value="{{ old('factory_name', $user->factory->factory_name) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label" for="location_china">Location in China</label>
          <input type="text" class="form-control" id="location_china" name="location_china" value="{{ old('location_china', $user->factory->location_china) }}">
        </div>
        @else
        <div class="col-md-6 factory-fields">
          <label class="form-label" for="factory_name">Factory Name <span class="text-muted">(when role is Factory)</span></label>
          <input type="text" class="form-control" id="factory_name" name="factory_name" value="{{ old('factory_name') }}">
        </div>
        <div class="col-md-6 factory-fields">
          <label class="form-label" for="location_china">Location in China</label>
          <input type="text" class="form-control" id="location_china" name="location_china" value="{{ old('location_china') }}">
        </div>
        @endif
        <div class="col-md-6">
          <label class="form-label" for="phone">Phone</label>
          <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label" for="country">Country</label>
          <input type="text" class="form-control" id="country" name="country" value="{{ old('country', $user->country) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label" for="city">City</label>
          <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $user->city) }}">
        </div>
        <div class="col-12">
          <label class="form-label" for="avatar">Replace Profile Photo</label>
          <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
          @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Update User</button>
          <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
