@extends('layouts.admin')

@section('title', __('admin.analytics.title'))

@section('vendor-css')
<link rel="stylesheet" href="{{ asset('assets/sneat/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endsection

@section('content')
@php
  $chartData = $chartData ?? [];
  $months = $months ?? collect();
  $metrics = $metrics ?? [];
  $topBuyers = $topBuyers ?? collect();
  $topFactories = $topFactories ?? collect();
  $orderPipeline = $orderPipeline ?? [];
@endphp

<div class="row mb-4">
  <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
      <h4 class="fw-bold mb-1">{{ __('admin.analytics.title') }}</h4>
      <p class="text-muted mb-0">{{ __('admin.analytics.subtitle') }}</p>
    </div>
    <form method="GET" class="d-flex align-items-center gap-2">
      <label class="form-label mb-0 small text-muted">{{ __('admin.analytics.period') }}</label>
      <select name="months" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
        <option value="3" {{ ($monthsBack ?? 6) == 3 ? 'selected' : '' }}>3 {{ __('admin.analytics.months') }}</option>
        <option value="6" {{ ($monthsBack ?? 6) == 6 ? 'selected' : '' }}>6 {{ __('admin.analytics.months') }}</option>
        <option value="12" {{ ($monthsBack ?? 6) == 12 ? 'selected' : '' }}>12 {{ __('admin.analytics.months') }}</option>
      </select>
    </form>
  </div>
</div>

{{-- Summary KPIs --}}
<div class="d-flex align-items-center gap-2 mb-3">
  <i class="bx bx-stats text-primary"></i>
  <h6 class="mb-0 text-uppercase fw-semibold text-muted" style="letter-spacing: 0.08em;">{{ __('admin.analytics.platform_summary') }}</h6>
</div>
<div class="row g-3 mb-5">
  <div class="col-xxl-2 col-xl-3 col-md-4 col-6">
    <div class="card hanzo-admin-kpi h-100">
      <div class="card-body d-flex align-items-start gap-2">
        <div class="kpi-icon rounded-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(13, 148, 136, 0.12);"><i class="bx bx-user text-primary"></i></div>
        <div class="flex-grow-1 min-w-0">
          <div class="kpi-label">{{ __('admin.dashboard.total_buyers') }}</div>
          <div class="kpi-value mt-1">{{ number_format($metrics['total_buyers'] ?? 0) }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xxl-2 col-xl-3 col-md-4 col-6">
    <div class="card hanzo-admin-kpi h-100">
      <div class="card-body d-flex align-items-start gap-2">
        <div class="kpi-icon rounded-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(34, 197, 94, 0.12);"><i class="bx bx-buildings text-success"></i></div>
        <div class="flex-grow-1 min-w-0">
          <div class="kpi-label">{{ __('admin.dashboard.verified_factories') }}</div>
          <div class="kpi-value mt-1">{{ number_format($metrics['verified_factories'] ?? 0) }}</div>
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
          <div class="kpi-value mt-1">{{ number_format($metrics['active_products'] ?? 0) }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xxl-2 col-xl-3 col-md-4 col-6">
    <div class="card hanzo-admin-kpi h-100">
      <div class="card-body d-flex align-items-start gap-2">
        <div class="kpi-icon rounded-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(139, 92, 246, 0.12);"><i class="bx bx-task" style="color: #8b5cf6;"></i></div>
        <div class="flex-grow-1 min-w-0">
          <div class="kpi-label">{{ __('admin.analytics.total_rfqs') }}</div>
          <div class="kpi-value mt-1">{{ number_format($metrics['total_rfqs'] ?? 0) }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xxl-2 col-xl-3 col-md-4 col-6">
    <div class="card hanzo-admin-kpi h-100">
      <div class="card-body d-flex align-items-start gap-2">
        <div class="kpi-icon rounded-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(13, 148, 136, 0.12);"><i class="bx bx-dollar-circle text-primary"></i></div>
        <div class="flex-grow-1 min-w-0">
          <div class="kpi-label">{{ __('admin.analytics.gmv_30d') }}</div>
          <div class="kpi-value mt-1">{{ money($metrics['gmv_30d'] ?? 0) }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xxl-2 col-xl-3 col-md-4 col-6">
    <div class="card hanzo-admin-kpi h-100">
      <div class="card-body d-flex align-items-start gap-2">
        <div class="kpi-icon rounded-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(34, 197, 94, 0.12);"><i class="bx bx-trending-up text-success"></i></div>
        <div class="flex-grow-1 min-w-0">
          <div class="kpi-label">{{ __('admin.analytics.hanzo_margin_30d') }}</div>
          <div class="kpi-value mt-1">{{ money($metrics['hanzo_margin_30d'] ?? 0) }}</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  {{-- GMV & Revenue chart --}}
  <div class="col-xl-8 mb-4">
    <div class="card h-100">
      <div class="card-header py-3">
        <h5 class="mb-0 d-flex align-items-center gap-2"><i class="bx bx-line-chart text-primary"></i>{{ __('admin.analytics.gmv_revenue_trend') }}</h5>
      </div>
      <div class="card-body py-3">
        <div id="gmvRevenueChart" style="min-height: 320px;"></div>
      </div>
    </div>
  </div>
  {{-- Order pipeline --}}
  <div class="col-xl-4 mb-4">
    <div class="card h-100">
      <div class="card-header py-3">
        <h5 class="mb-0 d-flex align-items-center gap-2"><i class="bx bx-git-branch text-primary"></i>{{ __('admin.analytics.order_pipeline') }}</h5>
      </div>
      <div class="card-body py-3">
        @php
          $statusLabels = [
            'deposit_pending' => __('admin.analytics.status_deposit_pending'),
            'deposit_paid' => __('admin.analytics.status_deposit_paid'),
            'in_production' => __('admin.analytics.status_in_production'),
            'ready_to_ship' => __('admin.analytics.status_ready_to_ship'),
            'shipped' => __('admin.analytics.status_shipped'),
            'completed' => __('admin.analytics.status_completed'),
          ];
        @endphp
        @forelse($orderPipeline as $status => $count)
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
          <span class="text-muted">{{ $statusLabels[$status] ?? $status }}</span>
          <span class="fw-600">{{ $count }}</span>
        </div>
        @empty
        <p class="text-muted small mb-0">{{ __('admin.analytics.no_orders') }}</p>
        @endforelse
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-link w-100 mt-2 p-0">{{ __('admin.dashboard.view_all') }} →</a>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  {{-- Orders & RFQs trend --}}
  <div class="col-xl-8 mb-4">
    <div class="card h-100">
      <div class="card-header py-3">
        <h5 class="mb-0 d-flex align-items-center gap-2"><i class="bx bx-bar-chart-alt text-primary"></i>{{ __('admin.analytics.orders_rfqs_trend') }}</h5>
      </div>
      <div class="card-body py-3">
        <div id="ordersRfqsChart" style="min-height: 280px;"></div>
      </div>
    </div>
  </div>
  {{-- Top buyers --}}
  <div class="col-xl-4 mb-4">
    <div class="card h-100">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 d-flex align-items-center gap-2"><i class="bx bx-user-check text-primary"></i>{{ __('admin.analytics.top_buyers') }}</h5>
        <span class="badge bg-label-secondary">{{ __('admin.analytics.last_90_days') }}</span>
      </div>
      <div class="card-body py-3">
        @if($topBuyers->isEmpty())
        <p class="text-muted small mb-0">{{ __('admin.analytics.no_data') }}</p>
        @else
        <ul class="list-unstyled mb-0">
          @foreach($topBuyers->take(8) as $item)
          <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
            <div class="text-truncate me-2">
              @if($item['buyer']?->id)
              <a href="{{ route('admin.users.edit', $item['buyer']->id) }}" class="text-decoration-none">{{ $item['buyer']->name }}</a>
              @else
              <span>{{ __('admin.analytics.unknown') }}</span>
              @endif
              <small class="d-block text-muted">{{ $item['orders'] }} {{ __('menu.orders') }}</small>
            </div>
            <span class="fw-600">{{ money($item['value']) }}</span>
          </li>
          @endforeach
        </ul>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Top factories --}}
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 d-flex align-items-center gap-2"><i class="bx bx-buildings text-primary"></i>{{ __('admin.analytics.top_factories') }}</h5>
        <span class="badge bg-label-secondary">{{ __('admin.analytics.last_90_days') }}</span>
      </div>
      <div class="card-body py-3">
        @if($topFactories->isEmpty())
        <p class="text-muted small mb-0">{{ __('admin.analytics.no_data') }}</p>
        @else
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead>
              <tr>
                <th>{{ __('menu.factories') }}</th>
                <th class="text-end">{{ __('admin.analytics.orders_count') }}</th>
                <th class="text-end">{{ __('admin.analytics.total_value') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($topFactories as $item)
              <tr>
                <td>
                  @if($item['factory'] && $item['factory']->user_id)
                  <a href="{{ route('admin.users.edit', $item['factory']->user_id) }}" class="text-decoration-none">{{ $item['factory']->factory_name ?? $item['factory']->user?->name ?? __('admin.analytics.unknown') }}</a>
                  @elseif($item['factory'])
                  <span>{{ $item['factory']->factory_name ?? $item['factory']->user?->name ?? __('admin.analytics.unknown') }}</span>
                  @else
                  —
                  @endif
                </td>
                <td class="text-end">{{ $item['orders'] }}</td>
                <td class="text-end fw-600">{{ money($item['value']) }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @endif
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
  var chartData = @json($chartData);
  var months = @json($months->values()->toArray());

  if (months.length && Object.keys(chartData).length) {
    var gmv = months.map(function(m) { return chartData[m]?.gmv ?? 0; });
    var hanzoMargin = months.map(function(m) { return chartData[m]?.hanzo_margin ?? 0; });
    var orders = months.map(function(m) { return chartData[m]?.orders ?? 0; });
    var rfqs = months.map(function(m) { return chartData[m]?.rfqs ?? 0; });

    // GMV & Revenue chart
    if (document.querySelector('#gmvRevenueChart')) {
      new ApexCharts(document.querySelector('#gmvRevenueChart'), {
        chart: { type: 'area', stacked: false, toolbar: { show: false }, fontFamily: 'Inter' },
        colors: ['#0d9488', '#22c55e'],
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', opacity: [0.25, 0.2] },
        xaxis: { categories: months },
        series: [
          { name: '{{ __("admin.analytics.platform_gmv") }}', data: gmv },
          { name: '{{ __("admin.dashboard.hanzo_margin_revenue") }}', data: hanzoMargin }
        ],
        legend: { position: 'top' },
        grid: { borderColor: '#e2e8f0', strokeDashArray: 4 },
        tooltip: { theme: 'light', y: { formatter: function(v) { return '$' + Number(v).toLocaleString('en-US', {minimumFractionDigits: 0}); } } },
        yaxis: { labels: { formatter: function(v) { return v >= 1000 ? (v/1000)+'K' : v; } } }
      }).render();
    }

    // Orders & RFQs chart
    if (document.querySelector('#ordersRfqsChart')) {
      new ApexCharts(document.querySelector('#ordersRfqsChart'), {
        chart: { type: 'bar', toolbar: { show: false }, fontFamily: 'Inter' },
        colors: ['#0d9488', '#8b5cf6'],
        plotOptions: { bar: { horizontal: false, columnWidth: '60%', borderRadius: 4 } },
        xaxis: { categories: months },
        series: [
          { name: '{{ __("menu.orders") }}', data: orders },
          { name: '{{ __("admin.analytics.rfqs") }}', data: rfqs }
        ],
        legend: { position: 'top' },
        grid: { borderColor: '#e2e8f0', strokeDashArray: 4 },
        tooltip: { theme: 'light' }
      }).render();
    }
  }
});
</script>
@endsection
