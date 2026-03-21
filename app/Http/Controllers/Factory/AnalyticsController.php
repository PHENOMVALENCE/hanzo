<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Rfq;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        $factory = auth()->user()->factory;
        if (! $factory) {
            return view('factory.analytics.index', [
                'chartData' => [],
                'metrics' => [],
                'byStatus' => ['in_progress' => 0, 'ready_to_ship' => 0, 'completed' => 0],
                'recentOrders' => collect(),
            ]);
        }

        $ordersQuery = Order::whereHas('quotation.rfq', fn ($q) => $q->where('assigned_factory_id', $factory->id));
        $rfqQuery = Rfq::where('assigned_factory_id', $factory->id);

        $ordersLast30 = (clone $ordersQuery)->where('created_at', '>=', now()->subDays(30));
        $revenue = (clone $ordersLast30)->get()->sum(fn ($o) => (float) ($o->quotation->total_landed_cost ?? 0));

        $byStatus = [
            'in_progress' => (clone $ordersQuery)->whereIn('milestone_status', ['awaiting_factory_approval', 'in_production'])->count(),
            'ready_to_ship' => (clone $ordersQuery)->where('milestone_status', 'ready_to_ship')->count(),
            'completed' => (clone $ordersQuery)->where('milestone_status', 'completed')->count(),
        ];

        $rfqsLast6Months = (clone $rfqQuery)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as total")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $ordersLast6Months = (clone $ordersQuery)
            ->whereHas('quotation')
            ->where('orders.created_at', '>=', now()->subMonths(6))
            ->get()
            ->groupBy(fn ($o) => $o->created_at->format('Y-m'))
            ->map->count()
            ->toArray();

        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->format('Y-m'));
        }
        $chartData = $months->mapWithKeys(fn ($m) => [
            $m => [
                'rfqs' => $rfqsLast6Months[$m] ?? 0,
                'orders' => $ordersLast6Months[$m] ?? 0,
            ],
        ])->toArray();

        $metrics = [
            'product_listings' => Product::where('factory_id', $factory->id)->where('status', Product::STATUS_LIVE)->count(),
            'total_orders' => (clone $ordersQuery)->count(),
            'revenue_30d' => $revenue,
            'rfqs_received' => (clone $rfqQuery)->count(),
            'conversion_rate' => $rfqQuery->count() > 0
                ? round(((clone $ordersQuery)->count() / $rfqQuery->count()) * 100, 1)
                : 0,
        ];

        $recentOrders = (clone $ordersQuery)->with('quotation.rfq')->latest()->take(10)->get();

        return view('factory.analytics.index', [
            'chartData' => $chartData,
            'metrics' => $metrics,
            'byStatus' => $byStatus,
            'recentOrders' => $recentOrders,
        ]);
    }
}
