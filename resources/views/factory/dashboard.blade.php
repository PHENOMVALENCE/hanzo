@extends('layouts.factory')

@section('title', __('factory.dashboard.title'))

@section('vendor-css')
<link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endsection

@section('content')
@php
  $factory = auth()->user()->factory;
  $profileComplete = $factory ? 78 : 0; // Placeholder – implement profile completeness
  $rfqCount = $factory ? \App\Models\Rfq::where('assigned_factory_id', $factory->id)->whereIn('status', ['assigned','pricing_received'])->count() : 0;
  $pendingRfqs = $factory ? \App\Models\Rfq::where('assigned_factory_id', $factory->id)->where('status', 'assigned')->count() : 0;
  $orderCount = $factory ? \App\Models\Order::whereHas('quotation.rfq', fn($q) => $q->where('assigned_factory_id', $factory->id))->count() : 0;
  $ordersInProduction = $factory ? \App\Models\Order::whereHas('quotation.rfq', fn($q) => $q->where('assigned_factory_id', $factory->id))->whereIn('milestone_status', ['awaiting_factory_approval','in_production'])->count() : 0;
  $ordersShipped = $factory ? \App\Models\Order::whereHas('quotation.rfq', fn($q) => $q->where('assigned_factory_id', $factory->id))->where('milestone_status', 'ready_to_ship')->count() : 0;
  $ordersDelivered = $factory ? \App\Models\Order::whereHas('quotation.rfq', fn($q) => $q->where('assigned_factory_id', $factory->id))->where('milestone_status', 'completed')->count() : 0;
  $productListings = $factory ? \App\Models\Product::where('factory_id', $factory->id)->where('status', \App\Models\Product::STATUS_LIVE)->count() : 0;
  $totalRevenue = 0;   // Placeholder – implement from orders
  $profileViews = 0;   // Placeholder – implement tracking
@endphp

@if(!$factory)
<div class="alert alert-warning">
  <strong>{{ __('factory.dashboard.no_factory') }}</strong> {{ __('factory.dashboard.no_factory_msg') }}
</div>
@else
{{-- Welcome Banner --}}
<div class="card mb-4 hanzo-factory-welcome" style="background: linear-gradient(135deg, var(--hanzo-offwhite, #F7F8FA) 0%, rgba(13,148,136,0.08) 100%); border: 1px solid var(--hanzo-border, #E5E7EB);">
  <div class="card-body py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <h4 class="mb-0">{{ $factory->factory_name }}</h4>
          @if(($factory->verification_status ?? '') === 'verified')
          <span class="hanzo-action-badge badge-info"><i class="bx bx-check-shield"></i> {{ __('factory.dashboard.verified') }}</span>
          @else
          <span class="hanzo-action-badge badge-warning">{{ __('factory.dashboard.pending_verification') }}</span>
          @endif
        </div>
        <p class="text-muted small mb-2">{{ $factory->location_china ?? 'China' }}</p>
        <div class="d-flex align-items-center gap-3">
          <div style="min-width: 180px;">
            <div class="d-flex justify-content-between small mb-1">
              <span class="text-muted">{{ __('factory.dashboard.profile_completeness') }}</span>
              <span class="fw-600">{{ $profileComplete }}%</span>
            </div>
            <div class="hanzo-profile-meter">
              <div class="hanzo-profile-meter-fill" style="width: {{ $profileComplete }}%;"></div>
            </div>
          </div>
          @if($profileComplete < 100)
          <span class="small text-muted">{{ __('factory.dashboard.add_certs_hint') }}</span>
          @endif
        </div>
      </div>
      <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-hanzo-primary">{{ __('factory.dashboard.complete_profile') }}</a>
    </div>
  </div>
</div>

{{-- Key Metrics --}}
<div class="row g-3 mb-4">
  <div class="col-xl col-md-4 col-6">
    <div class="card hanzo-factory-stat h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <p class="text-muted small mb-1">{{ __('factory.dashboard.active_listings') }}</p>
          <h4 class="mb-0">{{ $productListings }}</h4>
        </div>
        <div class="stat-icon"><i class="bx bx-package bx-lg"></i></div>
      </div>
    </div>
  </div>
  <div class="col-xl col-md-4 col-6">
    <div class="card hanzo-factory-stat h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <p class="text-muted small mb-1">{{ __('factory.dashboard.open_rfqs') }}</p>
          <h4 class="mb-0">{{ $rfqCount }}</h4>
          @if($pendingRfqs > 0)
          <span class="hanzo-action-badge badge-urgent mt-1">{{ __('factory.dashboard.rfqs_awaiting', ['count' => $pendingRfqs]) }}</span>
          @endif
        </div>
        <div class="stat-icon"><i class="bx bx-task bx-lg"></i></div>
      </div>
    </div>
  </div>
  <div class="col-xl col-md-4 col-6">
    <div class="card hanzo-factory-stat h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <p class="text-muted small mb-1">{{ __('factory.dashboard.pending_orders') }}</p>
          <h4 class="mb-0">{{ $orderCount }}</h4>
        </div>
        <div class="stat-icon"><i class="bx bx-package bx-lg"></i></div>
      </div>
    </div>
  </div>
  <div class="col-xl col-md-4 col-6">
    <div class="card hanzo-factory-stat h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <p class="text-muted small mb-1">{{ __('factory.dashboard.revenue_period') }}</p>
          <h4 class="mb-0">{{ money($totalRevenue) }}</h4>
        </div>
        <div class="stat-icon"><i class="bx bx-dollar bx-lg"></i></div>
      </div>
    </div>
  </div>
  <div class="col-xl col-md-4 col-6">
    <div class="card hanzo-factory-stat h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <p class="text-muted small mb-1">{{ __('factory.dashboard.profile_views') }}</p>
          <h4 class="mb-0">{{ $profileViews }}</h4>
        </div>
        <div class="stat-icon"><i class="bx bx-show bx-lg"></i></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-8">
    {{-- Action Items --}}
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('factory.dashboard.action_items') }}</h5>
        <span class="badge bg-warning">{{ __('factory.dashboard.attention_needed') }}</span>
      </div>
      <div class="card-body">
        <ul class="list-unstyled mb-0">
          @if($pendingRfqs > 0)
          <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <span><i class="bx bx-task text-warning me-2"></i> {{ __('factory.dashboard.rfqs_awaiting', ['count' => $pendingRfqs]) }}</span>
            <a href="{{ route('factory.rfqs.index') }}" class="btn btn-sm btn-hanzo-primary">{{ __('factory.dashboard.respond') }}</a>
          </li>
          @endif
          @if($ordersInProduction > 0)
          <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <span><i class="bx bx-package text-info me-2"></i> {{ __('factory.dashboard.orders_in_production', ['count' => $ordersInProduction]) }}</span>
            <a href="{{ route('factory.orders.index') }}" class="btn btn-sm btn-outline-primary">{{ __('factory.dashboard.view') }}</a>
          </li>
          @endif
          @if($pendingRfqs === 0 && $ordersInProduction === 0)
          <li class="py-3 text-muted text-center">{{ __('factory.dashboard.no_action_items') }}</li>
          @endif
        </ul>
      </div>
    </div>

    {{-- Recent Activity --}}
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('factory.dashboard.recent_activity') }}</h5>
        <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-link p-0">{{ __('factory.dashboard.view_all') }}</a>
      </div>
      <div class="card-body">
        @php
          $activities = collect();
          if ($factory) {
            $recentRfqs = \App\Models\Rfq::where('assigned_factory_id', $factory->id)->latest()->take(3)->get();
            foreach ($recentRfqs as $r) {
              $activities->push(['time' => $r->created_at, 'text' => __('factory.dashboard.new_rfq_assigned', ['code' => $r->code]), 'icon' => 'bx-task', 'url' => route('factory.rfqs.show', $r)]);
            }
            $recentOrders = \App\Models\Order::whereHas('quotation.rfq', fn($q) => $q->where('assigned_factory_id', $factory->id))->latest()->take(2)->get();
            foreach ($recentOrders as $o) {
              $activities->push(['time' => $o->created_at, 'text' => __('factory.dashboard.order_placed', ['code' => $o->order_code]), 'icon' => 'bx-package', 'url' => route('factory.orders.show', $o)]);
            }
            $activities = $activities->sortByDesc('time')->take(6)->values();
          }
        @endphp
        @if($activities->isNotEmpty())
        <ul class="list-unstyled mb-0">
          @foreach($activities as $a)
          <li class="d-flex align-items-start py-2 border-bottom">
            <i class="bx {{ $a['icon'] }} me-2 mt-1 text-muted"></i>
            <div>
              <a href="{{ $a['url'] ?? '#' }}" class="text-decoration-none">{{ $a['text'] }}</a>
              <small class="d-block text-muted">{{ $a['time']->diffForHumans() }}</small>
            </div>
          </li>
          @endforeach
        </ul>
        @else
        <p class="text-muted text-center py-3 mb-0">{{ __('factory.dashboard.no_recent_activity') }}</p>
        @endif
      </div>
    </div>

    {{-- Performance Snapshot --}}
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">{{ __('factory.dashboard.performance_snapshot') }}</h5>
      </div>
      <div class="card-body">
        <div id="factoryPerformanceChart" style="min-height: 240px;"></div>
        <div class="row g-2 mt-3">
          <div class="col-4">
            <div class="text-center p-2 rounded" style="background: var(--hanzo-offwhite, #f8fafc);">
              <span class="d-block fw-600">{{ $rfqCount }}</span>
              <small class="text-muted">{{ __('factory.dashboard.rfqs_received') }}</small>
            </div>
          </div>
          <div class="col-4">
            <div class="text-center p-2 rounded" style="background: var(--hanzo-offwhite, #f8fafc);">
              <span class="d-block fw-600">{{ $orderCount }}</span>
              <small class="text-muted">{{ __('factory.dashboard.orders_closed') }}</small>
            </div>
          </div>
          <div class="col-4">
            <div class="text-center p-2 rounded" style="background: var(--hanzo-offwhite, #f8fafc);">
              <span class="d-block fw-600">{{ $orderCount > 0 && $rfqCount > 0 ? round(($orderCount / $rfqCount) * 100) : 0 }}%</span>
              <small class="text-muted">{{ __('factory.dashboard.conversion') }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    {{-- Quick Links --}}
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">{{ __('factory.dashboard.quick_actions') }}</h5>
      </div>
      <div class="card-body">
        <a href="{{ route('factory.rfqs.index') }}" class="btn btn-hanzo-primary w-100 mb-2">
          <i class="bx bx-task me-2"></i> {{ __('factory.dashboard.respond_to_rfqs') }}
        </a>
        <a href="{{ route('factory.orders.index') }}" class="btn btn-outline-primary w-100 mb-2">
          <i class="bx bx-package me-2"></i> {{ __('factory.dashboard.view_orders') }}
        </a>
        <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary w-100">
          <i class="bx bx-user me-2"></i> {{ __('factory.dashboard.edit_profile') }}
        </a>
      </div>
    </div>

    {{-- Order Pipeline --}}
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">{{ __('factory.dashboard.order_pipeline') }}</h5>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between py-2 border-bottom">
          <span class="text-muted">{{ __('factory.dashboard.in_production') }}</span>
          <span class="fw-600">{{ $ordersInProduction }}</span>
        </div>
        <div class="d-flex justify-content-between py-2 border-bottom">
          <span class="text-muted">{{ __('factory.dashboard.ready_to_ship') }}</span>
          <span class="fw-600">{{ $ordersShipped }}</span>
        </div>
        <div class="d-flex justify-content-between py-2">
          <span class="text-muted">{{ __('factory.dashboard.completed') }}</span>
          <span class="fw-600">{{ $ordersDelivered }}</span>
        </div>
        <a href="{{ route('factory.orders.index') }}" class="btn btn-sm btn-link w-100 mt-2 p-0">{{ __('factory.dashboard.view_all_orders') }}</a>
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
  var opts = {
    chart: { type: 'bar', toolbar: { show: false }, fontFamily: 'Inter' },
    colors: ['#0B1F3A', '#123A6D', '#22C55E'],
    plotOptions: { bar: { horizontal: false, columnWidth: '60%', borderRadius: 4 } },
    dataLabels: { enabled: false },
    xaxis: { categories: @json([__('factory.dashboard.in_production'), __('factory.dashboard.ready_to_ship'), __('factory.dashboard.completed')]) },
    series: [{ name: 'Orders', data: [{{ $ordersInProduction }}, {{ $ordersShipped }}, {{ $ordersDelivered }}] }],
    grid: { borderColor: 'rgba(0,0,0,0.06)' }
  };
  if (document.querySelector('#factoryPerformanceChart')) {
    new ApexCharts(document.querySelector('#factoryPerformanceChart'), opts).render();
  }
});
</script>
@endif
@endsection
