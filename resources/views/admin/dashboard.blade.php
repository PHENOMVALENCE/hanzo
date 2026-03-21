@extends('layouts.admin')

@section('title', __('admin.dashboard.title'))

@section('vendor-css')
<link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endsection

@section('content')
@php
  use App\Models\User;
  use App\Models\Factory;
  use App\Models\Product;
  use App\Models\Rfq;
  use App\Models\Order;
  use App\Models\Payment;
  use App\Models\Quotation;

  $totalBuyers = User::role('buyer')->count();
  $verifiedFactories = Factory::where('verification_status', 'verified')->count();
  $activeProducts = Product::where('status', Product::STATUS_LIVE)->count();
  $rfqsToday = Rfq::whereDate('created_at', today())->count();
  $rfqsThisWeek = Rfq::where('created_at', '>=', now()->startOfWeek())->count();
  $ordersInProgress = Order::whereIn('milestone_status', ['deposit_pending', 'deposit_paid', 'in_production', 'shipped'])->count();
  $totalOrders = Order::count();

  $gmvThisMonth = Order::with('quotation')
    ->whereHas('quotation')
    ->whereMonth('created_at', now()->month)
    ->whereYear('created_at', now()->year)
    ->get()
    ->sum(fn($o) => (float) ($o->quotation?->total_landed_cost ?? 0));
  $gmvLastMonth = Order::with('quotation')
    ->whereHas('quotation')
    ->whereMonth('created_at', now()->subMonth()->month)
    ->whereYear('created_at', now()->subMonth()->year)
    ->get()
    ->sum(fn($o) => (float) ($o->quotation?->total_landed_cost ?? 0));

  $hanzoMarginThisMonth = Order::with('quotation')
    ->whereHas('quotation')
    ->whereMonth('created_at', now()->month)
    ->whereYear('created_at', now()->year)
    ->get()
    ->sum(fn($o) => (float) ($o->quotation?->hanzo_fee ?? 0));
  $hanzoMarginLastMonth = Order::with('quotation')
    ->whereHas('quotation')
    ->whereMonth('created_at', now()->subMonth()->month)
    ->whereYear('created_at', now()->subMonth()->year)
    ->get()
    ->sum(fn($o) => (float) ($o->quotation?->hanzo_fee ?? 0));

  $pendingFactoryVerifications = User::role('factory')->where('status', 'pending')->count();
  $productsAwaitingReview = Product::where('status', Product::STATUS_PENDING_APPROVAL)->count();
  $paymentsPendingRelease = Payment::where('status', 'pending')->count();
  $disputesUnresolved = 0; // Placeholder – no Dispute model yet
  $flaggedMessages = 0; // Placeholder
  $flaggedAccounts = 0; // Placeholder

  $activities = [];
  foreach (User::whereHas('roles', fn($q) => $q->whereIn('name', ['buyer', 'factory']))->latest()->take(3)->get() as $u) {
    $role = $u->getRoleNames()->first() ?? 'user';
    $activities[] = ['time' => $u->created_at, 'text' => __('admin.dashboard.new_registration', ['type' => ucfirst($role)]), 'icon' => 'bx-user-plus'];
  }
  foreach (Rfq::with('buyer', 'category')->latest()->take(4)->get() as $r) {
    $activities[] = ['time' => $r->created_at, 'text' => __('admin.dashboard.new_request', ['code' => $r->code ?? '?', 'buyer' => $r->buyer?->name ?? '?', 'category' => trans_category($r->category) ?: '-']), 'icon' => 'bx-file'];
  }
  foreach (Product::where('status', Product::STATUS_PENDING_APPROVAL)->latest()->take(2)->get() as $p) {
    $activities[] = ['time' => $p->created_at, 'text' => __('admin.dashboard.product_submitted') . ': ' . ($p->name ?? 'Product'), 'icon' => 'bx-package'];
  }
  foreach (Payment::with('order')->where('status', 'pending')->latest()->take(3)->get() as $p) {
    $activities[] = ['time' => $p->created_at, 'text' => __('admin.dashboard.payment_pending', ['amount' => money($p->amount_usd ?? 0), 'order' => $p->order?->order_code ?? '?']), 'icon' => 'bx-money'];
  }
  foreach (Order::with('buyer')->latest()->take(2)->get() as $o) {
    $activities[] = ['time' => $o->created_at, 'text' => __('admin.dashboard.order_confirmed') . ': ' . ($o->order_code ?? '?'), 'icon' => 'bx-check-circle'];
  }
  usort($activities, fn($a, $b) => $b['time']->timestamp <=> $a['time']->timestamp);
  $recentActivities = array_slice($activities, 0, 12);

  $revenueToday = Order::with('quotation')
    ->whereHas('quotation')
    ->whereDate('created_at', today())
    ->get()
    ->sum(fn($o) => (float) ($o->quotation?->hanzo_fee ?? 0));
  $revenueThisWeek = Order::with('quotation')
    ->whereHas('quotation')
    ->where('created_at', '>=', now()->startOfWeek())
    ->get()
    ->sum(fn($o) => (float) ($o->quotation?->hanzo_fee ?? 0));
  $revenueLastWeek = Order::with('quotation')
    ->whereHas('quotation')
    ->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
    ->get()
    ->sum(fn($o) => (float) ($o->quotation?->hanzo_fee ?? 0));
@endphp

<div class="row mb-4">
  <div class="col-12">
    <h4 class="fw-bold mb-1">{{ __('admin.dashboard.title') }}</h4>
    <p class="text-muted mb-0">{{ __('admin.dashboard.subtitle') }}</p>
  </div>
</div>

{{-- Platform-wide KPIs --}}
<div class="d-flex align-items-center gap-2 mb-3">
  <i class="bx bx-pie-chart-alt-2 text-primary"></i>
  <h6 class="mb-0 text-uppercase fw-semibold text-muted" style="letter-spacing: 0.08em;">Platform Overview</h6>
</div>
<div class="row g-3 mb-5">
  <div class="col-xxl-2 col-xl-3 col-md-4 col-6">
    <div class="card hanzo-admin-kpi h-100">
      <div class="card-body d-flex align-items-start gap-2">
        <div class="kpi-icon rounded-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(96, 165, 250, 0.15);"><i class="bx bx-user text-primary"></i></div>
        <div class="flex-grow-1 min-w-0">
          <div class="kpi-label">{{ __('admin.dashboard.total_buyers') }}</div>
          <div class="kpi-value mt-1">{{ number_format($totalBuyers) }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xxl-2 col-xl-3 col-md-4 col-6">
    <div class="card hanzo-admin-kpi h-100">
      <div class="card-body d-flex align-items-start gap-2">
        <div class="kpi-icon rounded-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(34, 197, 94, 0.15);"><i class="bx bx-buildings text-success"></i></div>
        <div class="flex-grow-1 min-w-0">
          <div class="kpi-label">{{ __('admin.dashboard.verified_factories') }}</div>
          <div class="kpi-value mt-1">{{ number_format($verifiedFactories) }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xxl-2 col-xl-3 col-md-4 col-6">
    <div class="card hanzo-admin-kpi h-100">
      <div class="card-body d-flex align-items-start gap-2">
        <div class="kpi-icon rounded-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(251, 191, 36, 0.15);"><i class="bx bx-package text-warning"></i></div>
        <div class="flex-grow-1 min-w-0">
          <div class="kpi-label">{{ __('admin.dashboard.active_products') }}</div>
          <div class="kpi-value mt-1">{{ number_format($activeProducts) }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xxl-2 col-xl-3 col-md-4 col-6">
    <div class="card hanzo-admin-kpi h-100">
      <div class="card-body d-flex align-items-start gap-2">
        <div class="kpi-icon rounded-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(139, 92, 246, 0.15);"><i class="bx bx-task" style="color: #a78bfa;"></i></div>
        <div class="flex-grow-1 min-w-0">
          <div class="kpi-label">{{ __('admin.dashboard.rfqs_today') }}</div>
          <div class="kpi-value mt-1">{{ $rfqsToday }}</div>
          <div class="kpi-meta">{{ __('admin.dashboard.rfqs_week') }}: {{ $rfqsThisWeek }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xxl-2 col-xl-3 col-md-4 col-6">
    <div class="card hanzo-admin-kpi h-100">
      <div class="card-body d-flex align-items-start gap-2">
        <div class="kpi-icon rounded-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(34, 197, 94, 0.15);"><i class="bx bx-package text-success"></i></div>
        <div class="flex-grow-1 min-w-0">
          <div class="kpi-label">{{ __('admin.dashboard.orders_in_progress') }}</div>
          <div class="kpi-value mt-1">{{ $ordersInProgress }}</div>
          <div class="kpi-meta">{{ $totalOrders }} total</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xxl-2 col-xl-3 col-md-4 col-6">
    <div class="card hanzo-admin-kpi h-100">
      <div class="card-body d-flex align-items-start gap-2">
        <div class="kpi-icon rounded-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(96, 165, 250, 0.15);"><i class="bx bx-dollar-circle text-primary"></i></div>
        <div class="flex-grow-1 min-w-0">
          <div class="kpi-label">{{ __('admin.dashboard.platform_gmv') }}</div>
          <div class="kpi-value mt-1">{{ money($gmvThisMonth) }}</div>
          <div class="kpi-meta">{{ __('admin.dashboard.hanzo_margin_revenue') }}: {{ money($hanzoMarginThisMonth) }}</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="d-flex align-items-center gap-2 mb-3">
  <i class="bx bx-pulse text-primary"></i>
  <h6 class="mb-0 text-uppercase fw-semibold text-muted" style="letter-spacing: 0.08em;">Activity & Actions</h6>
</div>
<div class="row g-4 mb-5">
  {{-- Live activity feed --}}
  <div class="col-xl-5 mb-4 mb-xl-0">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 d-flex align-items-center gap-2"><i class="bx bx-time-five text-primary"></i>{{ __('admin.dashboard.live_activity') }}</h5>
        <a href="{{ route('admin.rfqs.index') }}" class="btn btn-sm btn-link">{{ __('admin.dashboard.view_all') }}</a>
      </div>
      <div class="card-body overflow-auto py-3" style="max-height: 400px;">
        @if(empty($recentActivities))
          <p class="text-muted mb-0">{{ __('admin.dashboard.no_recent_activity') }}</p>
        @else
          <ul class="list-unstyled mb-0">
            @foreach($recentActivities as $a)
            <li class="hanzo-admin-activity-item d-flex align-items-start gap-2">
              <i class="bx {{ $a['icon'] }} mt-1 text-primary"></i>
              <div class="flex-grow-1">
                <span class="small">{{ $a['text'] }}</span>
                <div class="activity-time">{{ $a['time']->diffForHumans() }}</div>
              </div>
            </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>

  {{-- Alert & action queue --}}
  <div class="col-xl-4 mb-4 mb-xl-0">
    <div class="card h-100">
      <div class="card-header py-3">
        <h5 class="mb-0 d-flex align-items-center gap-2"><i class="bx bx-list-check text-primary"></i>{{ __('admin.dashboard.action_queue') }}</h5>
      </div>
      <div class="card-body py-3">
        <a href="{{ route('admin.approvals.factories') }}" class="hanzo-admin-alert alert-urgent text-decoration-none d-block">
          <i class="bx bx-building-house text-danger"></i>
          <div>
            <strong class="text-light">{{ __('admin.dashboard.pending_approvals') }}</strong>
            <div class="small text-muted">{{ $pendingFactoryVerifications }} {{ __('menu.factories') }}</div>
          </div>
        </a>
        <a href="{{ route('admin.products.index', ['status' => 'pending_approval']) }}" class="hanzo-admin-alert alert-warning text-decoration-none d-block">
          <i class="bx bx-package text-warning"></i>
          <div>
            <strong class="text-light">{{ __('admin.menu.product_review_queue') }}</strong>
            <div class="small text-muted">{{ $productsAwaitingReview }} pending</div>
          </div>
        </a>
        <a href="{{ route('admin.payments.index') }}" class="hanzo-admin-alert alert-info text-decoration-none d-block">
          <i class="bx bx-money text-info"></i>
          <div>
            <strong class="text-light">{{ __('admin.dashboard.payments_pending') }}</strong>
            <div class="small text-muted">{{ $paymentsPendingRelease }} to verify</div>
          </div>
        </a>
        @if($disputesUnresolved > 0)
        <a href="#" class="hanzo-admin-alert alert-urgent text-decoration-none d-block">
          <i class="bx bx-error-circle text-danger"></i>
          <div>
            <strong class="text-light">{{ __('admin.dashboard.dispute_raised') }}</strong>
            <div class="small text-muted">{{ $disputesUnresolved }} unresolved</div>
          </div>
        </a>
        @endif
      </div>
    </div>
  </div>

  {{-- Revenue snapshot --}}
  <div class="col-xl-3">
    <div class="card h-100">
      <div class="card-header py-3">
        <h5 class="mb-0 d-flex align-items-center gap-2"><i class="bx bx-trending-up text-primary"></i>{{ __('admin.dashboard.revenue_snapshot') }}</h5>
      </div>
      <div class="card-body py-3">
        <div id="revenueChart" style="min-height: 180px;"></div>
        <div class="mt-2 d-flex justify-content-between small text-muted">
          <span>{{ __('admin.dashboard.this_period') }}</span>
          <span>{{ __('admin.dashboard.last_period') }}</span>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="d-flex align-items-center gap-2 mb-3 mt-4">
  <i class="bx bx-map-alt text-primary"></i>
  <h6 class="mb-0 text-uppercase fw-semibold text-muted" style="letter-spacing: 0.08em;">Geographic Distribution</h6>
</div>
<div class="row">
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 d-flex align-items-center gap-2"><i class="bx bx-map-pin text-primary"></i>{{ __('admin.dashboard.map_distribution') }}</h5>
      </div>
      <div class="card-body p-0">
        <div class="hanzo-admin-map">
          <div class="text-center py-5">
            <i class="bx bx-map-pin bx-lg mb-2"></i>
            <p class="mb-0">Buyer locations & order origins</p>
            <small>Map integration coming soon</small>
          </div>
        </div>
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
  var data = [{{ $revenueThisWeek }}, {{ $revenueLastWeek }}, {{ $hanzoMarginThisMonth }}, {{ $hanzoMarginLastMonth }}];
  var fmt = function(v) {
    if (v >= 1000000) return (v/1e6).toFixed(1) + 'M';
    if (v >= 1000) return (v/1000).toFixed(1) + 'K';
    return v > 0 ? v.toFixed(2) : '';
  };
  var revenueConfig = {
    chart: { type: 'bar', toolbar: { show: false }, fontFamily: 'Inter', background: 'transparent' },
    colors: ['#0d9488'],
    plotOptions: { bar: { horizontal: false, borderRadius: 6, columnWidth: '55%' } },
    dataLabels: { enabled: true, formatter: function(v) { return fmt(v); } },
    series: [{ name: '{{ __("admin.dashboard.hanzo_margin_revenue") }}', data: data }],
    xaxis: { categories: ['{{ __("admin.dashboard.this_period") }} (w)', '{{ __("admin.dashboard.last_period") }} (w)', '{{ __("admin.dashboard.this_period") }} (m)', '{{ __("admin.dashboard.last_period") }} (m)'], labels: { style: { colors: '#64748b', fontSize: '11px' } } },
    yaxis: { labels: { style: { colors: '#64748b' }, formatter: fmt } },
    grid: { borderColor: '#e2e8f0', strokeDashArray: 4 },
    tooltip: { theme: 'light', y: { formatter: function(v) { return '$' + Number(v).toLocaleString('en-US', {minimumFractionDigits: 2}); } } }
  };
  if (document.querySelector('#revenueChart')) {
    new ApexCharts(document.querySelector('#revenueChart'), revenueConfig).render();
  }
});
</script>
@endsection
