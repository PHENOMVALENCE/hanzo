<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Services\OrderService;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class QuoteController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function index(): View
    {
        $quotations = Quotation::whereHas('rfq', fn ($q) => $q->where('buyer_id', auth()->id()))
            ->with(['rfq', 'factory'])
            ->latest()
            ->paginate(20);

        return view('buyer.quotes.index', compact('quotations'));
    }

    public function show(Quotation $quotation): View
    {
        $this->authorize('view', $quotation);

        $quotation->load(['rfq.buyer', 'rfq.category', 'factory']);

        return view('buyer.quotes.show', compact('quotation'));
    }

    public function accept(Quotation $quotation): RedirectResponse
    {
        $this->authorize('update', $quotation);

        if ($quotation->status !== 'sent') {
            return back()->with('error', 'This quotation cannot be accepted.');
        }

        $order = $this->orderService->createFromQuotation($quotation);

        return redirect()->route('buyer.orders.show', $order)->with('success', 'Order created. Please proceed to payment.');
    }

    public function reject(Quotation $quotation): RedirectResponse
    {
        $this->authorize('update', $quotation);

        if ($quotation->status !== 'sent') {
            return back()->with('error', 'This quotation cannot be rejected.');
        }

        $quotation->update(['status' => 'rejected']);

        return back()->with('success', 'Quotation rejected.');
    }
}
