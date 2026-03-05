<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $factory = auth()->user()->factory;
        $orders = Order::whereHas('quotation.rfq', fn ($q) => $q->where('assigned_factory_id', $factory->id))
            ->with(['quotation.rfq', 'buyer'])
            ->latest()
            ->paginate(20);

        return view('factory.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        $order->load(['quotation.rfq', 'quotation.factory', 'buyer', 'milestones']);

        return view('factory.orders.show', compact('order'));
    }
}
