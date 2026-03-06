<?php

namespace App\Http\Controllers\Factory;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductionUpdate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $factory = auth()->user()->factory;
        if (! $factory) {
            return view('factory.orders.index', [
                'orders' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
            ]);
        }
        $orders = Order::whereHas('quotation.rfq', fn ($q) => $q->where('assigned_factory_id', $factory->id))
            ->with(['quotation.rfq', 'buyer'])
            ->latest()
            ->paginate(20);

        return view('factory.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        $order->load(['quotation.rfq', 'quotation.factory', 'buyer', 'milestones', 'productionUpdates']);

        return view('factory.orders.show', compact('order'));
    }

    public function submitProductionUpdate(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('view', $order);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:started,mid_production,qc_ready,packed'],
            'note' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store("production/{$order->id}", 'public');
        }

        ProductionUpdate::create([
            'order_id' => $order->id,
            'status' => $validated['status'],
            'note' => $validated['note'] ?? null,
            'photo_path' => $photoPath,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Production update submitted.');
    }
}
