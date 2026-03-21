<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Models\Order;
use App\Models\Product;
use App\Models\Rfq;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $monthsBack = min(12, max(3, (int) $request->get('months', 6)));
        $months = collect();
        for ($i = $monthsBack - 1; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->format('Y-m'));
        }

        // Platform KPIs
        $totalBuyers = User::role('buyer')->count();
        $verifiedFactories = Factory::where('verification_status', 'verified')->count();
        $activeProducts = Product::where('status', Product::STATUS_LIVE)->count();
        $totalOrders = Order::count();
        $totalRfqs = Rfq::count();

        // GMV & HANZO margin (last 30 days)
        $gmv30d = Order::with('quotation')
            ->whereHas('quotation')
            ->where('orders.created_at', '>=', now()->subDays(30))
            ->get()
            ->sum(fn ($o) => (float) ($o->quotation?->total_landed_cost ?? 0));

        $hanzoMargin30d = Order::with('quotation')
            ->whereHas('quotation')
            ->where('orders.created_at', '>=', now()->subDays(30))
            ->get()
            ->sum(fn ($o) => (float) ($o->quotation?->hanzo_fee ?? 0));

        // Chart: GMV by month
        $gmvByMonth = Order::with('quotation')
            ->whereHas('quotation')
            ->where('orders.created_at', '>=', now()->subMonths($monthsBack))
            ->get()
            ->groupBy(fn ($o) => $o->created_at->format('Y-m'))
            ->map(fn ($group) => $group->sum(fn ($o) => (float) ($o->quotation?->total_landed_cost ?? 0)))
            ->toArray();

        // Chart: HANZO margin by month
        $hanzoMarginByMonth = Order::with('quotation')
            ->whereHas('quotation')
            ->where('orders.created_at', '>=', now()->subMonths($monthsBack))
            ->get()
            ->groupBy(fn ($o) => $o->created_at->format('Y-m'))
            ->map(fn ($group) => $group->sum(fn ($o) => (float) ($o->quotation?->hanzo_fee ?? 0)))
            ->toArray();

        // Chart: Orders & RFQs by month
        $ordersByMonth = Order::where('created_at', '>=', now()->subMonths($monthsBack))
            ->get()
            ->groupBy(fn ($o) => $o->created_at->format('Y-m'))
            ->map->count()
            ->toArray();

        $rfqsByMonth = Rfq::where('created_at', '>=', now()->subMonths($monthsBack))
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $chartData = $months->mapWithKeys(fn ($m) => [
            $m => [
                'gmv' => $gmvByMonth[$m] ?? 0,
                'hanzo_margin' => $hanzoMarginByMonth[$m] ?? 0,
                'orders' => $ordersByMonth[$m] ?? 0,
                'rfqs' => $rfqsByMonth[$m] ?? 0,
            ],
        ])->toArray();

        // Top buyers by order value (last 90 days)
        $topBuyers = Order::with(['buyer', 'quotation'])
            ->whereHas('quotation')
            ->where('orders.created_at', '>=', now()->subDays(90))
            ->get()
            ->groupBy('buyer_id')
            ->map(fn ($orders, $buyerId) => [
                'buyer' => $orders->first()?->buyer,
                'orders' => $orders->count(),
                'value' => $orders->sum(fn ($o) => (float) ($o->quotation?->total_landed_cost ?? 0)),
            ])
            ->sortByDesc('value')
            ->take(10)
            ->values();

        // Top factories by order value (last 90 days)
        $topFactories = Order::with(['quotation.rfq.assignedFactory', 'quotation.rfq.assignedFactory.user'])
            ->whereHas('quotation.rfq')
            ->where('orders.created_at', '>=', now()->subDays(90))
            ->get()
            ->groupBy(fn ($o) => $o->quotation?->rfq?->assigned_factory_id)
            ->filter(fn ($_, $id) => $id)
            ->map(fn ($orders, $factoryId) => [
                'factory' => $orders->first()?->quotation?->rfq?->assignedFactory,
                'orders' => $orders->count(),
                'value' => $orders->sum(fn ($o) => (float) ($o->quotation?->total_landed_cost ?? 0)),
            ])
            ->sortByDesc('value')
            ->take(10)
            ->values();

        // Order milestone distribution
        $orderPipeline = Order::selectRaw('milestone_status, count(*) as total')
            ->groupBy('milestone_status')
            ->pluck('total', 'milestone_status')
            ->toArray();

        return view('admin.analytics.index', [
            'chartData' => $chartData,
            'months' => $months,
            'monthsBack' => $monthsBack,
            'metrics' => [
                'total_buyers' => $totalBuyers,
                'verified_factories' => $verifiedFactories,
                'active_products' => $activeProducts,
                'total_orders' => $totalOrders,
                'total_rfqs' => $totalRfqs,
                'gmv_30d' => $gmv30d,
                'hanzo_margin_30d' => $hanzoMargin30d,
            ],
            'topBuyers' => $topBuyers,
            'topFactories' => $topFactories,
            'orderPipeline' => $orderPipeline,
        ]);
    }
}
