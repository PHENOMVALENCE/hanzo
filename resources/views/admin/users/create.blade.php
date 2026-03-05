@extends('layouts.admin')

@section('title', 'Create User')

@section('content')
<div class="row">
  <div class="col-12">
    <h4 class="fw-bold mb-4">Create User</h4>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="name">Name *</label>
          <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label" for="email">Email *</label>
          <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label" for="password">Password *</label>
          <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
          @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label" for="password_confirmation">Confirm Password *</label>
          <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="role">Role *</label>
          <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
            <option value="buyer" {{ old('role') === 'buyer' ? 'selected' : '' }}>Buyer</option>
            <option value="factory" {{ old('role') === 'factory' ? 'selected' : '' }}>Factory</option>
            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
          </select>
          @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label" for="status">Status</label>
          <select class="form-select" id="status" name="status">
            <option value="approved" {{ old('status', 'approved') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="company_name">Company Name</label>
          <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label" for="phone">Phone</label>
          <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label" for="country">Country</label>
          <input type="text" class="form-control" id="country" name="country" value="{{ old('country') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label" for="city">City</label>
          <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}">
        </div>
        <div class="col-12">
          <label class="form-label" for="avatar">Profile Photo</label>
          <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
          @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Create User</button>
          <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
