@extends('layouts.admin')

@section('title', __('admin.dashboard.title'))

@section('vendor-css')
<link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <h4 class="fw-bold mb-2">{{ __('admin.dashboard.title') }}</h4>
    <p class="text-muted mb-4">{{ __('admin.dashboard.subtitle') }}</p>
  </div>
</div>

@php
  $pendingBuyers = \App\Models\User::role('buyer')->where('status', 'pending')->count();
  $pendingFactories = \App\Models\User::role('factory')->where('status', 'pending')->count();
  $pendingApprovals = $pendingBuyers + $pendingFactories;
  $openRfqsNeedingAssignment = \App\Models\Rfq::where('status', 'new')->count();
  $paymentsPending = \App\Models\Payment::where('status', 'pending')->count();
  $totalOrders = \App\Models\Order::count();
  $totalValue = \App\Models\Order::with('quotation')->get()->sum(fn($o) => $o->quotation?->total_landed_cost ?? 0);
  $ordersByStatus = [
    'deposit_pending' => \App\Models\Order::where('milestone_status', 'deposit_pending')->count(),
    'in_production' => \App\Models\Order::whereIn('milestone_status', ['deposit_paid','in_production'])->count(),
    'shipped' => \App\Models\Order::where('milestone_status', 'shipped')->count(),
    'delivered' => \App\Models\Order::where('milestone_status', 'delivered')->count(),
  ];
  $rfqsByStatus = [
    'new' => \App\Models\Rfq::where('status', 'new')->count(),
    'assigned' => \App\Models\Rfq::where('status', 'assigned')->count(),
    'quoted' => \App\Models\Rfq::where('status', 'quoted')->count(),
    'in_production' => \App\Models\Rfq::whereIn('status', ['accepted','in_production'])->count(),
    'delivered' => \App\Models\Rfq::whereIn('status', ['shipped','delivered'])->count(),
  ];
  $activities = [];
  foreach (\App\Models\Rfq::with('buyer','category')->latest()->take(5)->get() as $r) {
    $activities[] = ['time'=>$r->created_at,'text'=>__('admin.dashboard.new_request',['code'=>$r->code,'buyer'=>$r->buyer?->name ?? '?','category'=>trans_category($r->category) ?: '-']),'icon'=>'bx-file'];
  }
  foreach (\App\Models\Payment::with('order')->where('status','pending')->latest()->take(3)->get() as $p) {
    $activities[] = ['time'=>$p->created_at,'text'=>__('admin.dashboard.payment_pending',['amount'=>money($p->amount_usd),'order'=>$p->order?->order_code ?? '?']),'icon'=>'bx-money'];
  }
  foreach (\App\Models\Quotation::with('rfq')->where('status','sent')->latest()->take(3)->get() as $q) {
    $activities[] = ['time'=>$q->updated_at,'text'=>__('admin.dashboard.quote_sent',['code'=>$q->quote_code,'rfq'=>$q->rfq?->code ?? '?']),'icon'=>'bx-send'];
  }
  usort($activities, fn($a,$b) => $b['time']->timestamp <=> $a['time']->timestamp);
  $recentActivities = array_slice($activities, 0, 10);
@endphp

<div class="row mb-4">
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card hanzo-stat-card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <span class="d-block text-muted small text-uppercase letter-spacing">{{ __('admin.dashboard.pending_approvals') }}</span>
          <h3 class="mb-0 mt-1">{{ $pendingApprovals }}</h3>
          <small class="text-muted">{{ __('admin.dashboard.buyers_factories', ['buyers' => $pendingBuyers, 'factories' => $pendingFactories]) }}</small>
          <a href="{{ route('admin.approvals.buyers') }}" class="btn btn-sm btn-outline-primary mt-2">{{ __('admin.dashboard.view') }}</a>
        </div>
        <span class="avatar avatar-lg rounded bg-label-primary"><i class="bx bx-user-check bx-lg"></i></span>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card hanzo-stat-card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <span class="d-block text-muted small text-uppercase letter-spacing">{{ __('admin.dashboard.open_requests') }}</span>
          <h3 class="mb-0 mt-1">{{ $openRfqsNeedingAssignment }}</h3>
          <small class="text-muted">{{ __('admin.dashboard.needing_assignment') }}</small>
          <a href="{{ route('admin.rfqs.index') }}" class="btn btn-sm btn-outline-success mt-2">{{ __('admin.dashboard.view') }}</a>
        </div>
        <span class="avatar avatar-lg rounded bg-label-success"><i class="bx bx-file bx-lg"></i></span>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card hanzo-stat-card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <span class="d-block text-muted small text-uppercase letter-spacing">{{ __('admin.dashboard.payments_pending') }}</span>
          <h3 class="mb-0 mt-1">{{ $paymentsPending }}</h3>
          <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-info mt-2">{{ __('admin.dashboard.verify') }}</a>
        </div>
        <span class="avatar avatar-lg rounded bg-label-info"><i class="bx bx-money bx-lg"></i></span>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card hanzo-stat-card card-verified h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <span class="d-block text-muted small text-uppercase letter-spacing">{{ __('admin.dashboard.total_orders') }}</span>
          <h3 class="mb-0 mt-1">{{ $totalOrders }}</h3>
          <small class="text-muted">{{ __('admin.dashboard.value', ['amount' => money($totalValue)]) }}</small>
          <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-warning mt-2 d-block">{{ __('admin.dashboard.view') }}</a>
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
        <h5 class="mb-0">{{ __('admin.dashboard.order_analytics') }}</h5>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-link">{{ __('admin.dashboard.view_all') }}</a>
      </div>
      <div class="card-body">
        <div id="orderAnalyticsChart" style="min-height: 280px;"></div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 mb-4">
    <div class="card card-verified h-100">
      <div class="card-header">
        <h5 class="mb-0">{{ __('admin.dashboard.user_management') }}</h5>
      </div>
      <div class="card-body">
        <p class="text-muted small mb-3">{{ __('admin.dashboard.user_management_desc') }}</p>
        <a href="{{ route('admin.users.index') }}" class="btn btn-primary w-100 mb-2">
          <i class="bx bx-group me-2"></i> {{ __('admin.dashboard.manage_users') }}
        </a>
        <a href="{{ route('admin.approvals.buyers') }}" class="btn btn-outline-primary w-100 mb-2">{{ __('admin.dashboard.pending_buyers', ['count' => $pendingBuyers]) }}</a>
        <a href="{{ route('admin.approvals.factories') }}" class="btn btn-outline-info w-100 mb-4">{{ __('admin.dashboard.pending_factories', ['count' => $pendingFactories]) }}</a>
      </div>
    </div>
    <div class="card mt-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('admin.dashboard.recent_activity') }}</h5>
        <a href="{{ route('admin.rfqs.index') }}" class="btn btn-sm btn-link">{{ __('admin.dashboard.view_all') }}</a>
      </div>
      <div class="card-body">
        @if(empty($recentActivities))
          <p class="text-muted small mb-0">{{ __('admin.dashboard.no_recent_activity') }}</p>
        @else
          <ul class="list-unstyled mb-0">
            @foreach($recentActivities as $a)
            <li class="d-flex align-items-start gap-2 py-2 border-bottom border-light">
              <i class="bx {{ $a['icon'] }} mt-1 text-muted"></i>
              <div>
                <span class="small">{{ $a['text'] }}</span>
                <br><span class="text-muted" style="font-size:0.75rem">{{ $a['time']->diffForHumans() }}</span>
              </div>
            </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">{{ __('admin.dashboard.rfq_pipeline') }}</h5>
      </div>
      <div class="card-body">
        <div id="rfqPipelineChart" style="min-height: 260px;"></div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">{{ __('admin.dashboard.order_milestones') }}</h5>
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
@php
  $orderChartLabels = [
    trans_status('deposit_pending'),
    trans_status('in_production'),
    trans_status('shipped'),
    trans_status('delivered'),
  ];
  $milestoneChartLabels = [
    trans_status('deposit_pending'),
    trans_status('in_production'),
    __('admin.dashboard.in_transit'),
    trans_status('delivered'),
  ];
  $rfqChartLabels = array_map(fn($k) => trans_status($k), array_keys($rfqsByStatus));
@endphp
<script>
document.addEventListener('DOMContentLoaded', function() {
  var orderData = @json(array_values($ordersByStatus));
  var orderLabels = @json($orderChartLabels);
  var orderConfig = {
    chart: { type: 'bar', toolbar: { show: false }, fontFamily: 'Inter' },
    colors: ['#0B1F3A', '#123A6D', '#D89B2B', '#22C55E'],
    plotOptions: { bar: { horizontal: true, barHeight: '60%', borderRadius: 4 } },
    dataLabels: { enabled: true },
    xaxis: { categories: orderLabels },
    yaxis: { labels: { style: { colors: '#697a8d' } } },
    grid: { borderColor: 'rgba(20,27,45,0.06)' },
    series: [{ name: @json(__('admin.dashboard.orders')), data: orderData }],
    responsive: [{ breakpoint: 576, options: { chart: { height: 220 }, dataLabels: { style: { fontSize: '10px' } } } }]
  };
  if (document.querySelector('#orderAnalyticsChart')) {
    new ApexCharts(document.querySelector('#orderAnalyticsChart'), orderConfig).render();
  }

  var rfqData = @json(array_values($rfqsByStatus));
  var rfqLabels = @json($rfqChartLabels);
  var rfqConfig = {
    chart: { type: 'donut', fontFamily: 'Inter' },
    colors: ['#0B1F3A', '#123A6D', '#D89B2B', '#F59E0B', '#22C55E'],
    labels: rfqLabels,
    series: rfqData,
    plotOptions: { pie: { donut: { size: '65%' } } },
    legend: { position: 'bottom' },
    responsive: [{ breakpoint: 576, options: { chart: { height: 220 }, legend: { fontSize: '11px' } } }]
  };
  if (document.querySelector('#rfqPipelineChart')) {
    new ApexCharts(document.querySelector('#rfqPipelineChart'), rfqConfig).render();
  }

  var milestoneConfig = {
    chart: { type: 'donut', fontFamily: 'Inter' },
    colors: ['#F59E0B', '#123A6D', '#D89B2B', '#22C55E'],
    labels: @json($milestoneChartLabels),
    series: [@json($ordersByStatus['deposit_pending']), @json($ordersByStatus['in_production']), @json($ordersByStatus['shipped']), @json($ordersByStatus['delivered'])],
    plotOptions: { pie: { donut: { size: '65%' } } },
    legend: { position: 'bottom' },
    responsive: [{ breakpoint: 576, options: { chart: { height: 220 }, legend: { fontSize: '11px' } } }]
  };
  if (document.querySelector('#orderMilestoneChart')) {
    new ApexCharts(document.querySelector('#orderMilestoneChart'), milestoneConfig).render();
  }
});
</script>
@endsection
