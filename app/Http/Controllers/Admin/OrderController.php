<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function index(): View
    {
        $orders = Order::with(['quotation.rfq', 'buyer'])->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['quotation.rfq', 'buyer', 'payments', 'documents', 'milestones']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateMilestone(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'milestone_status' => ['required', 'string', 'in:deposit_pending,deposit_paid,in_production,quality_control,shipped,in_customs,delivered'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'estimated_arrival' => ['nullable', 'date'],
        ]);

        $this->orderService->updateMilestone($order, $request->milestone_status, null);
        $this->orderService->updateTracking($order, $request->tracking_number, $request->estimated_arrival);

        return back()->with('success', 'Order updated.');
    }
}
