<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function create(Order $order): View
    {
        $this->authorize('view', $order);

        return view('buyer.payments.create', compact('order'));
    }

    public function store(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('view', $order);

        $validated = $request->validate([
            'type' => ['required', 'string', 'in:deposit,balance,full'],
            'amount_usd' => ['required', 'numeric', 'min:0.01'],
            'method' => ['nullable', 'string', 'max:50'],
            'reference' => ['nullable', 'string', 'max:255'],
            'proof' => ['required', 'file', 'max:5120'],
        ]);

        $this->paymentService->create(
            $order,
            $validated['type'],
            (float) $validated['amount_usd'],
            $validated['method'] ?? null,
            $validated['reference'] ?? null,
            $request->file('proof')
        );

        return redirect()->route('buyer.orders.show', $order)->with('success', 'Payment submitted for verification.');
    }
}
