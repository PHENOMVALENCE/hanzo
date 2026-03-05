<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::where('buyer_id', auth()->id())
            ->with(['quotation.rfq', 'quotation.factory'])
            ->latest()
            ->paginate(20);

        return view('buyer.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        $order->load(['quotation.rfq', 'quotation.factory', 'payments', 'documents', 'milestones']);

        return view('buyer.orders.show', compact('order'));
    }
}
