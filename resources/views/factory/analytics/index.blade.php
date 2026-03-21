@extends('layouts.factory')

@section('title', __('factory.dashboard.analytics') ?? 'Analytics')

@section('vendor-css')
<link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endsection

@section('content')
@php
  $metrics = $metrics ?? [];
  $chartData = $chartData ?? [];
  $byStatus = $byStatus ?? ['in_progress' => 0, 'ready_to_ship' => 0, 'completed' => 0];
  $recentOrders = $recentOrders ?? collect();
@endphp

<h4 class="fw-bold mb-4">{{ __('factory.dashboard.analytics') ?? 'Analytics' }}</h4>
<p class="text-muted small mb-4">{{ __('Factory performance metrics and trends.') }}</p>

@if(empty(auth()->user()->factory))
<div class="alert alert-warning">
  {{ __('factory.dashboard.no_factory_msg') }}
</div>
@else
{{-- Metrics --}}
<div class="row g-3 mb-4">
  <div class="col-md-6 col-xl">
    <div class="card hanzo-factory-stat h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <p class="text-muted small mb-1">{{ __('factory.dashboard.active_listings') }}</p>
          <h4 class="mb-0">{{ $metrics['product_listings'] ?? 0 }}</h4>
        </div>
        <div class="stat-icon"><i class="bx bx-package bx-lg"></i></div>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl">
    <div class="card hanzo-factory-stat h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <p class="text-muted small mb-1">{{ __('factory.dashboard.orders_closed') }}</p>
          <h4 class="mb-0">{{ $metrics['total_orders'] ?? 0 }}</h4>
        </div>
        <div class="stat-icon"><i class="bx bx-package bx-lg"></i></div>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl">
    <div class="card hanzo-factory-stat h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <p class="text-muted small mb-1">{{ __('factory.dashboard.revenue_30d') ?? 'Revenue (30 days)' }}</p>
          <h4 class="mb-0">{{ money($metrics['revenue_30d'] ?? 0) }}</h4>
        </div>
        <div class="stat-icon"><i class="bx bx-dollar bx-lg"></i></div>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl">
    <div class="card hanzo-factory-stat h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <p class="text-muted small mb-1">{{ __('factory.dashboard.rfqs_received') }}</p>
          <h4 class="mb-0">{{ $metrics['rfqs_received'] ?? 0 }}</h4>
        </div>
        <div class="stat-icon"><i class="bx bx-task bx-lg"></i></div>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl">
    <div class="card hanzo-factory-stat h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <p class="text-muted small mb-1">{{ __('factory.dashboard.conversion') }}</p>
          <h4 class="mb-0">{{ $metrics['conversion_rate'] ?? 0 }}%</h4>
        </div>
        <div class="stat-icon"><i class="bx bx-trending-up bx-lg"></i></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">{{ __('factory.dashboard.performance_snapshot') }}</h5>
      </div>
      <div class="card-body">
        <div id="analyticsChart" style="min-height: 280px;"></div>
        <div class="row g-2 mt-3">
          <div class="col-6">
            <div class="text-center p-2 rounded" style="background: var(--hanzo-offwhite, #f8fafc);">
              <span class="d-block fw-600 text-primary">{{ __('factory.dashboard.rfqs_received') }}</span>
              <small class="text-muted">—</small>
            </div>
          </div>
          <div class="col-6">
            <div class="text-center p-2 rounded" style="background: var(--hanzo-offwhite, #f8fafc);">
              <span class="d-block fw-600 text-success">{{ __('factory.dashboard.orders_closed') }}</span>
              <small class="text-muted">—</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">{{ __('factory.dashboard.order_pipeline') }}</h5>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between py-2 border-bottom">
          <span class="text-muted">{{ __('factory.dashboard.in_production') }}</span>
          <span class="fw-600">{{ $byStatus['in_progress'] ?? 0 }}</span>
        </div>
        <div class="d-flex justify-content-between py-2 border-bottom">
          <span class="text-muted">{{ __('factory.dashboard.ready_to_ship') }}</span>
          <span class="fw-600">{{ $byStatus['ready_to_ship'] ?? 0 }}</span>
        </div>
        <div class="d-flex justify-content-between py-2">
          <span class="text-muted">{{ __('factory.dashboard.completed') }}</span>
          <span class="fw-600">{{ $byStatus['completed'] ?? 0 }}</span>
        </div>
        <a href="{{ route('factory.orders.index') }}" class="btn btn-sm btn-link w-100 mt-2 p-0">{{ __('factory.dashboard.view_all_orders') }}</a>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">{{ __('Recent orders') }}</h5>
      </div>
      <div class="card-body">
        @if($recentOrders->isEmpty())
        <p class="text-muted small mb-0">{{ __('No orders yet.') }}</p>
        @else
        <ul class="list-unstyled mb-0">
          @foreach($recentOrders->take(5) as $o)
          <li class="d-flex justify-content-between py-2 border-bottom">
            <a href="{{ route('factory.orders.show', $o) }}" class="text-decoration-none">{{ $o->order_code ?? '-' }}</a>
            <small class="text-muted">{{ $o->created_at->format('M j') }}</small>
          </li>
          @endforeach
        </ul>
        @endif
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
@if(!empty($chartData))
<script>
document.addEventListener('DOMContentLoaded', function() {
  var chartData = @json($chartData);
  var months = Object.keys(chartData);
  var rfqs = months.map(function(m) { return chartData[m]?.rfqs ?? 0; });
  var orders = months.map(function(m) { return chartData[m]?.orders ?? 0; });
  var opts = {
    chart: { type: 'line', toolbar: { show: false }, fontFamily: 'Inter' },
    colors: ['#0B1F3A', '#22C55E'],
    stroke: { curve: 'smooth', width: 2 },
    xaxis: { categories: months },
    series: [
      { name: '{{ __("factory.dashboard.rfqs_received") }}', data: rfqs },
      { name: '{{ __("factory.dashboard.orders_closed") }}', data: orders }
    ],
    legend: { position: 'top' },
    grid: { borderColor: 'rgba(0,0,0,0.06)' }
  };
  if (document.querySelector('#analyticsChart')) {
    new ApexCharts(document.querySelector('#analyticsChart'), opts).render();
  }
});
</script>
@endif
@endsection
