@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row">
  <div class="col-12">
    <h4 class="fw-bold mb-4">Admin Dashboard</h4>
  </div>
</div>
<div class="row">
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <span class="d-block text-muted">Pending Buyers</span>
            <h4 class="mb-0">{{ \App\Models\User::role('buyer')->where('status', 'pending')->count() }}</h4>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-user"></i></span>
          </div>
        </div>
        <a href="{{ route('admin.approvals.buyers') }}" class="btn btn-sm btn-outline-primary mt-2">View</a>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <span class="d-block text-muted">Pending Factories</span>
            <h4 class="mb-0">{{ \App\Models\User::role('factory')->where('status', 'pending')->count() }}</h4>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-info"><i class="bx bx-building"></i></span>
          </div>
        </div>
        <a href="{{ route('admin.approvals.factories') }}" class="btn btn-sm btn-outline-info mt-2">View</a>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <span class="d-block text-muted">Open RFQs</span>
            <h4 class="mb-0">{{ \App\Models\Rfq::whereIn('status', ['new','assigned'])->count() }}</h4>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-file"></i></span>
          </div>
        </div>
        <a href="{{ route('admin.rfqs.index') }}" class="btn btn-sm btn-outline-success mt-2">View</a>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <span class="d-block text-muted">Total Orders</span>
            <h4 class="mb-0">{{ \App\Models\Order::count() }}</h4>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-package"></i></span>
          </div>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-warning mt-2">View</a>
      </div>
    </div>
  </div>
</div>
@endsection
