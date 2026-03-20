@extends('layouts.factory')

@section('title', 'Factory Dashboard')

@section('vendor-css')
<link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endsection

@section('content')
@php
  $factory = auth()->user()->factory;
  $profileComplete = $factory ? 78 : 0; // Placeholder – implement profile completeness
  $rfqCount = $factory ? \App\Models\Rfq::where('assigned_factory_id', $factory->id)->whereIn('status', ['assigned','pricing_received'])->count() : 0;
  $pendingRfqs = $factory ? \App\Models\Rfq::where('assigned_factory_id', $factory->id)->where('status', 'assigned')->count() : 0;
<<<<<<< HEAD
  $orderCount = $factory ? \App\Models\Order::whereHas('quotation.rfq', fn($q) => $q->where('assigned_factory_id', $factory->id))->count() : 0;
  $ordersInProduction = $factory ? \App\Models\Order::whereHas('quotation.rfq', fn($q) => $q->where('assigned_factory_id', $factory->id))->whereIn('milestone_status', ['deposit_paid','in_production'])->count() : 0;
  $ordersShipped = $factory ? \App\Models\Order::whereHas('quotation.rfq', fn($q) => $q->where('assigned_factory_id', $factory->id))->where('milestone_status', 'shipped')->count() : 0;
  $ordersDelivered = $factory ? \App\Models\Order::whereHas('quotation.rfq', fn($q) => $q->where('assigned_factory_id', $factory->id))->where('milestone_status', 'delivered')->count() : 0;
  $productListings = $factory ? \App\Models\Product::forFactory($factory->id)->where('status', \App\Models\Product::STATUS_ACTIVE)->count() : 0;
  $totalRevenue = 0;   // Placeholder – implement from orders
  $profileViews = 0;   // Placeholder – implement tracking
  $oldInquiries = $pendingRfqs; // RFQs > 24h without response – simplify for now
=======
  $ordersInProduction = $factory ? \App\Models\Order::whereHas('quotation.rfq', fn($q) => $q->where('assigned_factory_id', $factory->id))->whereIn('milestone_status', ['awaiting_factory_approval','in_production'])->count() : 0;
  $ordersShipped = $factory ? \App\Models\Order::whereHas('quotation.rfq', fn($q) => $q->where('assigned_factory_id', $factory->id))->where('milestone_status', 'ready_to_ship')->count() : 0;
  $ordersDelivered = $factory ? \App\Models\Order::whereHas('quotation.rfq', fn($q) => $q->where('assigned_factory_id', $factory->id))->where('milestone_status', 'completed')->count() : 0;
>>>>>>> 3a34daee (Hanzo in b2b style)
@endphp

@if(!$factory)
<div class="alert alert-warning">
  <strong>No factory profile.</strong> Your account is not linked to a factory record. Please contact HANZO Admin.
</div>
@else
{{-- Welcome Banner --}}
<div class="card mb-4" style="background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%); border: 1px solid #99f6e4;">
  <div class="card-body py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <h4 class="mb-0">{{ $factory->factory_name }}</h4>
          @if(($factory->verification_status ?? '') === 'verified')
          <span class="hanzo-action-badge badge-info"><i class="bx bx-check-shield"></i> Verified</span>
          @else
          <span class="hanzo-action-badge badge-warning">Pending verification</span>
          @endif
        </div>
        <p class="text-muted small mb-2">{{ $factory->location_china ?? 'China' }}</p>
        <div class="d-flex align-items-center gap-3">
          <div style="min-width: 180px;">
            <div class="d-flex justify-content-between small mb-1">
              <span class="text-muted">Profile completeness</span>
              <span class="fw-600">{{ $profileComplete }}%</span>
            </div>
            <div class="hanzo-profile-meter">
              <div class="hanzo-profile-meter-fill" style="width: {{ $profileComplete }}%;"></div>
            </div>
          </div>
          @if($profileComplete < 100)
          <span class="small text-muted">Add certifications to get more inquiries</span>
          @endif
        </div>
      </div>
      <a href="{{ route('profile.edit') }}" class="btn btn-sm" style="background: #0d9488; color: #fff;">Complete Profile</a>
    </div>
  </div>
</div>

{{-- Key Metrics --}}
<div class="row g-3 mb-4">
  <div class="col-xl col-md-4 col-6">
    <div class="card hanzo-factory-stat h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <p class="text-muted small mb-1">Active Product Listings</p>
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
          <p class="text-muted small mb-1">Open RFQs</p>
          <h4 class="mb-0">{{ $rfqCount }}</h4>
          @if($pendingRfqs > 0)
          <span class="hanzo-action-badge badge-urgent mt-1">{{ $pendingRfqs }} to respond</span>
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
          <p class="text-muted small mb-1">Pending Orders</p>
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
          <p class="text-muted small mb-1">Revenue (this period)</p>
          <h4 class="mb-0">${{ number_format($totalRevenue, 0) }}</h4>
        </div>
        <div class="stat-icon"><i class="bx bx-dollar bx-lg"></i></div>
      </div>
    </div>
  </div>
  <div class="col-xl col-md-4 col-6">
    <div class="card hanzo-factory-stat h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <p class="text-muted small mb-1">Profile views (week)</p>
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
        <h5 class="mb-0">Action Items</h5>
        <span class="badge bg-warning">Attention needed</span>
      </div>
      <div class="card-body">
        <ul class="list-unstyled mb-0">
          @if($pendingRfqs > 0)
          <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <span><i class="bx bx-task text-warning me-2"></i> {{ $pendingRfqs }} RFQ(s) awaiting your response</span>
            <a href="{{ route('factory.rfqs.index') }}" class="btn btn-sm" style="background: #0d9488; color: #fff;">Respond</a>
          </li>
          @endif
          @if($ordersInProduction > 0)
          <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <span><i class="bx bx-package text-info me-2"></i> {{ $ordersInProduction }} order(s) in production</span>
            <a href="{{ route('factory.orders.index') }}" class="btn btn-sm btn-outline-primary">View</a>
          </li>
          @endif
          @if($pendingRfqs === 0 && $ordersInProduction === 0)
          <li class="py-3 text-muted text-center">No urgent action items</li>
          @endif
        </ul>
      </div>
    </div>

    {{-- Recent Activity --}}
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Recent Activity</h5>
        <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-link p-0">View all</a>
      </div>
      <div class="card-body">
        @php
          $activities = collect();
          if ($factory) {
            $recentRfqs = \App\Models\Rfq::where('assigned_factory_id', $factory->id)->latest()->take(3)->get();
            foreach ($recentRfqs as $r) {
              $activities->push(['time' => $r->created_at, 'text' => 'New RFQ assigned: ' . $r->code, 'icon' => 'bx-task', 'url' => route('factory.rfqs.show', $r)]);
            }
            $recentOrders = \App\Models\Order::whereHas('quotation.rfq', fn($q) => $q->where('assigned_factory_id', $factory->id))->latest()->take(2)->get();
            foreach ($recentOrders as $o) {
              $activities->push(['time' => $o->created_at, 'text' => 'Order ' . $o->order_code . ' placed', 'icon' => 'bx-package', 'url' => route('factory.orders.show', $o)]);
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
        <p class="text-muted text-center py-3 mb-0">No recent activity</p>
        @endif
      </div>
    </div>

    {{-- Performance Snapshot --}}
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Performance Snapshot (Last 30 days)</h5>
      </div>
      <div class="card-body">
        <div id="factoryPerformanceChart" style="min-height: 240px;"></div>
        <div class="row g-2 mt-3">
          <div class="col-4">
            <div class="text-center p-2 rounded" style="background: #f0fdfa;">
              <span class="d-block fw-600">{{ $rfqCount }}</span>
              <small class="text-muted">RFQs received</small>
            </div>
          </div>
          <div class="col-4">
            <div class="text-center p-2 rounded" style="background: #f0fdfa;">
              <span class="d-block fw-600">{{ $orderCount }}</span>
              <small class="text-muted">Orders closed</small>
            </div>
          </div>
          <div class="col-4">
            <div class="text-center p-2 rounded" style="background: #f0fdfa;">
              <span class="d-block fw-600">{{ $orderCount > 0 && $rfqCount > 0 ? round(($orderCount / $rfqCount) * 100) : 0 }}%</span>
              <small class="text-muted">Conversion</small>
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
        <h5 class="mb-0">Quick Actions</h5>
      </div>
      <div class="card-body">
        <a href="{{ route('factory.rfqs.index') }}" class="btn w-100 mb-2" style="background: #0d9488; color: #fff;">
          <i class="bx bx-task me-2"></i> Respond to RFQs
        </a>
        <a href="{{ route('factory.orders.index') }}" class="btn btn-outline-primary w-100 mb-2">
          <i class="bx bx-package me-2"></i> View Orders
        </a>
        <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary w-100">
          <i class="bx bx-user me-2"></i> Edit Profile
        </a>
      </div>
    </div>

    {{-- Order Pipeline --}}
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Order Pipeline</h5>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between py-2 border-bottom">
          <span class="text-muted">In Production</span>
          <span class="fw-600">{{ $ordersInProduction }}</span>
        </div>
        <div class="d-flex justify-content-between py-2 border-bottom">
          <span class="text-muted">Shipped</span>
          <span class="fw-600">{{ $ordersShipped }}</span>
        </div>
        <div class="d-flex justify-content-between py-2">
          <span class="text-muted">Delivered</span>
          <span class="fw-600">{{ $ordersDelivered }}</span>
        </div>
        <a href="{{ route('factory.orders.index') }}" class="btn btn-sm btn-link w-100 mt-2 p-0">View all orders</a>
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
<<<<<<< HEAD
    colors: ['#0d9488', '#14b8a6'],
    plotOptions: { bar: { horizontal: false, columnWidth: '60%', borderRadius: 4 } },
    dataLabels: { enabled: false },
    xaxis: { categories: ['In Production', 'Shipped', 'Delivered'] },
=======
    colors: ['#0B1F3A', '#123A6D', '#22C55E'],
    plotOptions: { bar: { horizontal: true, barHeight: '60%', borderRadius: 4 } },
    dataLabels: { enabled: true },
    xaxis: { categories: ['In Progress', 'Ready to Ship', 'Completed'] },
>>>>>>> 3a34daee (Hanzo in b2b style)
    series: [{ name: 'Orders', data: [{{ $ordersInProduction }}, {{ $ordersShipped }}, {{ $ordersDelivered }}] }],
    grid: { borderColor: 'rgba(0,0,0,0.06)' }
  };
<<<<<<< HEAD
  if (document.querySelector('#factoryPerformanceChart')) {
    new ApexCharts(document.querySelector('#factoryPerformanceChart'), opts).render();
=======
  if (document.querySelector('#factoryOrderChart')) {
    new ApexCharts(document.querySelector('#factoryOrderChart'), barConfig).render();
  }
  var donutConfig = {
    chart: { type: 'donut', fontFamily: 'Inter' },
    colors: ['#D89B2B', '#123A6D', '#22C55E'],
    labels: ['In Progress', 'Ready to Ship', 'Completed'],
    series: [{{ $ordersInProduction }}, {{ $ordersShipped }}, {{ $ordersDelivered }}],
    plotOptions: { pie: { donut: { size: '65%' } } },
    legend: { position: 'bottom' }
  };
  if (document.querySelector('#factoryOrderDonut')) {
    new ApexCharts(document.querySelector('#factoryOrderDonut'), donutConfig).render();
>>>>>>> 3a34daee (Hanzo in b2b style)
  }
});
</script>
@endif
@endsection
