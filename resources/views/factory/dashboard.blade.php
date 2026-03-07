@extends('layouts.factory')

@section('title', 'Factory Dashboard')

@section('vendor-css')
<link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <h4 class="fw-bold mb-2">Factory Dashboard</h4>
    <p class="text-muted mb-4">Your secure portal for HANZO-assigned requests. Submit pricing, upload production updates, and manage your orders.</p>
  </div>
</div>

@php
  $factory = auth()->user()->factory;
  $rfqCount = $factory ? \App\Models\Rfq::where('assigned_factory_id', $factory->id)->whereIn('status', ['assigned','pricing_received'])->count() : 0;
  $orderCount = $factory ? \App\Models\Order::whereHas('quotation.rfq', fn($q) => $q->where('assigned_factory_id', $factory->id))->count() : 0;
  $pendingRfqs = $factory ? \App\Models\Rfq::where('assigned_factory_id', $factory->id)->where('status', 'assigned')->count() : 0;
  $ordersInProduction = $factory ? \App\Models\Order::whereHas('quotation.rfq', fn($q) => $q->where('assigned_factory_id', $factory->id))->whereIn('milestone_status', ['deposit_paid','in_production','quality_control'])->count() : 0;
  $ordersShipped = $factory ? \App\Models\Order::whereHas('quotation.rfq', fn($q) => $q->where('assigned_factory_id', $factory->id))->whereIn('milestone_status', ['shipped','in_customs'])->count() : 0;
  $ordersDelivered = $factory ? \App\Models\Order::whereHas('quotation.rfq', fn($q) => $q->where('assigned_factory_id', $factory->id))->where('milestone_status', 'delivered')->count() : 0;
@endphp

@if(!$factory)
<div class="alert alert-warning">
  <strong>No factory profile.</strong> Your account is not linked to a factory record. Please contact HANZO Admin.
</div>
@endif

<div class="row mb-4">
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card hanzo-stat-card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <span class="d-block text-muted small text-uppercase letter-spacing">Assigned RFQs</span>
          <h3 class="mb-0 mt-1">{{ $rfqCount }}</h3>
          @if($pendingRfqs > 0)
            <span class="badge bg-warning mt-1">{{ $pendingRfqs }} pending</span>
          @endif
          <a href="{{ route('factory.rfqs.index') }}" class="btn btn-sm btn-outline-primary mt-2 d-block">View RFQs</a>
        </div>
        <span class="avatar avatar-lg rounded bg-label-primary"><i class="bx bx-task bx-lg"></i></span>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card hanzo-stat-card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <span class="d-block text-muted small text-uppercase letter-spacing">Active Orders</span>
          <h3 class="mb-0 mt-1">{{ $orderCount }}</h3>
          <a href="{{ route('factory.orders.index') }}" class="btn btn-sm btn-outline-success mt-2 d-block">View Orders</a>
        </div>
        <span class="avatar avatar-lg rounded bg-label-success"><i class="bx bx-package bx-lg"></i></span>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card hanzo-stat-card card-verified h-100">
      <div class="card-body">
        <span class="d-block text-muted small text-uppercase letter-spacing">Your Factory</span>
        <p class="mb-0 fw-semibold mt-1">{{ $factory?->factory_name ?? 'Not set' }}</p>
        @if($factory?->location_china)
          <small class="text-muted">{{ $factory->location_china }}</small>
        @endif
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card card-verified h-100">
      <div class="card-header">
        <h5 class="mb-0">Account</h5>
      </div>
      <div class="card-body">
        <p class="text-muted small mb-2">Manage your profile and settings.</p>
        <a href="{{ route('profile.edit') }}" class="btn btn-primary w-100">
          <i class="bx bx-user me-2"></i> My Account
        </a>
      </div>
    </div>
  </div>
</div>

@if($factory)
<div class="row">
  <div class="col-lg-8 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Order Report</h5>
      </div>
      <div class="card-body">
        <div id="factoryOrderChart" style="min-height: 260px;"></div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Order Status</h5>
      </div>
      <div class="card-body">
        <div id="factoryOrderDonut" style="min-height: 260px;"></div>
      </div>
    </div>
  </div>
</div>
@endif
@endsection

@section('vendor-js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.0/dist/apexcharts.min.js"></script>
@endsection

@section('page-js')
@if($factory)
<script>
document.addEventListener('DOMContentLoaded', function() {
  var barConfig = {
    chart: { type: 'bar', toolbar: { show: false }, fontFamily: 'Public Sans' },
    colors: ['#0d9488', '#14b8a6', '#10b981'],
    plotOptions: { bar: { horizontal: true, barHeight: '60%', borderRadius: 4 } },
    dataLabels: { enabled: true },
    xaxis: { categories: ['In Production', 'Shipped', 'Delivered'] },
    series: [{ name: 'Orders', data: [{{ $ordersInProduction }}, {{ $ordersShipped }}, {{ $ordersDelivered }}] }],
    grid: { borderColor: 'rgba(20,27,45,0.06)' }
  };
  if (document.querySelector('#factoryOrderChart')) {
    new ApexCharts(document.querySelector('#factoryOrderChart'), barConfig).render();
  }
  var donutConfig = {
    chart: { type: 'donut', fontFamily: 'Public Sans' },
    colors: ['#FFAB00', '#0ea5e9', '#10b981'],
    labels: ['In Production', 'Shipped', 'Delivered'],
    series: [{{ $ordersInProduction }}, {{ $ordersShipped }}, {{ $ordersDelivered }}],
    plotOptions: { pie: { donut: { size: '65%' } } },
    legend: { position: 'bottom' }
  };
  if (document.querySelector('#factoryOrderDonut')) {
    new ApexCharts(document.querySelector('#factoryOrderDonut'), donutConfig).render();
  }
});
</script>
@endif
@endsection
