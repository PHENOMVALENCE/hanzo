@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('vendor-css')
<link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <h4 class="fw-bold mb-2">Admin Dashboard</h4>
    <p class="text-muted mb-4">Central command center for HANZO operations. Monitor approvals, RFQs, orders, and platform activity.</p>
  </div>
</div>

@php
  $pendingBuyers = \App\Models\User::role('buyer')->where('status', 'pending')->count();
  $pendingFactories = \App\Models\User::role('factory')->where('status', 'pending')->count();
  $openRfqs = \App\Models\Rfq::whereIn('status', ['new','assigned'])->count();
  $totalOrders = \App\Models\Order::count();
  $totalValue = \App\Models\Order::with('quotation')->get()->sum(fn($o) => $o->quotation?->total_landed_cost ?? 0);
  $ordersByStatus = [
    'deposit_pending' => \App\Models\Order::where('milestone_status', 'deposit_pending')->count(),
    'in_production' => \App\Models\Order::whereIn('milestone_status', ['deposit_paid','in_production','quality_control'])->count(),
    'shipped' => \App\Models\Order::whereIn('milestone_status', ['shipped','in_customs'])->count(),
    'delivered' => \App\Models\Order::where('milestone_status', 'delivered')->count(),
  ];
  $rfqsByStatus = [
    'new' => \App\Models\Rfq::where('status', 'new')->count(),
    'assigned' => \App\Models\Rfq::where('status', 'assigned')->count(),
    'quoted' => \App\Models\Rfq::where('status', 'quoted')->count(),
    'in_production' => \App\Models\Rfq::whereIn('status', ['accepted','in_production'])->count(),
    'delivered' => \App\Models\Rfq::whereIn('status', ['shipped','delivered'])->count(),
  ];
@endphp

<div class="row mb-4">
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card hanzo-stat-card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <span class="d-block text-muted small text-uppercase letter-spacing">Pending Buyers</span>
          <h3 class="mb-0 mt-1">{{ $pendingBuyers }}</h3>
          <a href="{{ route('admin.approvals.buyers') }}" class="btn btn-sm btn-outline-primary mt-2">View</a>
        </div>
        <span class="avatar avatar-lg rounded bg-label-primary"><i class="bx bx-user-check bx-lg"></i></span>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card hanzo-stat-card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <span class="d-block text-muted small text-uppercase letter-spacing">Pending Factories</span>
          <h3 class="mb-0 mt-1">{{ $pendingFactories }}</h3>
          <a href="{{ route('admin.approvals.factories') }}" class="btn btn-sm btn-outline-info mt-2">View</a>
        </div>
        <span class="avatar avatar-lg rounded bg-label-info"><i class="bx bx-building bx-lg"></i></span>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card hanzo-stat-card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <span class="d-block text-muted small text-uppercase letter-spacing">Open RFQs</span>
          <h3 class="mb-0 mt-1">{{ $openRfqs }}</h3>
          <a href="{{ route('admin.rfqs.index') }}" class="btn btn-sm btn-outline-success mt-2">View</a>
        </div>
        <span class="avatar avatar-lg rounded bg-label-success"><i class="bx bx-file bx-lg"></i></span>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card hanzo-stat-card card-verified h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <span class="d-block text-muted small text-uppercase letter-spacing">Total Orders</span>
          <h3 class="mb-0 mt-1">{{ $totalOrders }}</h3>
          <small class="text-muted">${{ number_format($totalValue, 0) }} value</small>
          <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-warning mt-2 d-block">View</a>
        </div>
        <span class="avatar avatar-lg rounded bg-label-warning"><i class="bx bx-package bx-lg"></i></span>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-8 mb-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Order Analytics</h5>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-link">View all</a>
      </div>
      <div class="card-body">
        <div id="orderAnalyticsChart" style="min-height: 280px;"></div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 mb-4">
    <div class="card card-verified h-100">
      <div class="card-header">
        <h5 class="mb-0">User Account Management</h5>
      </div>
      <div class="card-body">
        <p class="text-muted small mb-3">Manage buyers, factories, and admin accounts. Create users, set roles, approve registrations, and upload profile photos.</p>
        <a href="{{ route('admin.users.index') }}" class="btn btn-primary w-100 mb-2">
          <i class="bx bx-group me-2"></i> Manage Users
        </a>
        <a href="{{ route('admin.approvals.buyers') }}" class="btn btn-outline-primary w-100 mb-2">Pending Buyers ({{ $pendingBuyers }})</a>
        <a href="{{ route('admin.approvals.factories') }}" class="btn btn-outline-info w-100">Pending Factories ({{ $pendingFactories }})</a>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">RFQ Pipeline</h5>
      </div>
      <div class="card-body">
        <div id="rfqPipelineChart" style="min-height: 260px;"></div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Order Milestones</h5>
      </div>
      <div class="card-body">
        <div id="orderMilestoneChart" style="min-height: 260px;"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('vendor-js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.0/dist/apexcharts.min.js"></script>
@endsection

@section('page-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
  var orderData = @json(array_values($ordersByStatus));
  var orderLabels = @json(array_keys($ordersByStatus));
  var orderConfig = {
    chart: { type: 'bar', toolbar: { show: false }, fontFamily: 'Public Sans' },
    colors: ['#141B2D', '#1f2b4a', '#FFAB00', '#10b981'],
    plotOptions: { bar: { horizontal: true, barHeight: '60%', borderRadius: 4 } },
    dataLabels: { enabled: true },
    xaxis: { categories: orderLabels.map(l => l.replace(/_/g, ' ')) },
    yaxis: { labels: { style: { colors: '#697a8d' } } },
    grid: { borderColor: 'rgba(20,27,45,0.06)' },
    series: [{ name: 'Orders', data: orderData }]
  };
  if (document.querySelector('#orderAnalyticsChart')) {
    new ApexCharts(document.querySelector('#orderAnalyticsChart'), orderConfig).render();
  }

  var rfqData = @json(array_values($rfqsByStatus));
  var rfqLabels = @json(array_keys($rfqsByStatus));
  var rfqConfig = {
    chart: { type: 'donut', fontFamily: 'Public Sans' },
    colors: ['#141B2D', '#1f2b4a', '#FFAB00', '#0ea5e9', '#10b981'],
    labels: rfqLabels.map(l => l.replace(/_/g, ' ')),
    series: rfqData,
    plotOptions: { pie: { donut: { size: '65%' } } },
    legend: { position: 'bottom' }
  };
  if (document.querySelector('#rfqPipelineChart')) {
    new ApexCharts(document.querySelector('#rfqPipelineChart'), rfqConfig).render();
  }

  var milestoneConfig = {
    chart: { type: 'donut', fontFamily: 'Public Sans' },
    colors: ['#f59e0b', '#0ea5e9', '#8b5cf6', '#10b981'],
    labels: ['Deposit Pending', 'In Production', 'In Transit', 'Delivered'],
    series: [@json($ordersByStatus['deposit_pending']), @json($ordersByStatus['in_production']), @json($ordersByStatus['shipped']), @json($ordersByStatus['delivered'])],
    plotOptions: { pie: { donut: { size: '65%' } } },
    legend: { position: 'bottom' }
  };
  if (document.querySelector('#orderMilestoneChart')) {
    new ApexCharts(document.querySelector('#orderMilestoneChart'), milestoneConfig).render();
  }
});
</script>
@endsection
