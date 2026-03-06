<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Services\QuoteService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class QuoteBuilderController extends Controller
{
    public function __construct(
        private QuoteService $quoteService
    ) {}

    public function edit(Rfq $rfq): View
    {
        $rfq->load(['buyer', 'category', 'assignedFactory', 'factoryQuotes.factory']);
        $quotation = $rfq->quotations()->latest()->first();
        $suggestedProductCost = $this->quoteService->getProductCostFromFactoryQuote($rfq);

        return view('admin.quote-builder.edit', compact('rfq', 'quotation', 'suggestedProductCost'));
    }

    public function store(Request $request, Rfq $rfq): RedirectResponse
    {

        $costBreakdown = [
            'product_cost_usd' => (float) ($request->product_cost_usd ?? 0),
            'china_local_shipping' => (float) ($request->china_local_shipping ?? 0),
            'export_handling' => (float) ($request->export_handling ?? 0),
            'freight_cost' => (float) ($request->freight_cost ?? 0),
            'insurance_cost' => (float) ($request->insurance_cost ?? 0),
            'clearing_cost' => (float) ($request->clearing_cost ?? 0),
            'local_delivery_cost' => (float) ($request->local_delivery_cost ?? 0),
            'hanzo_fee' => (float) ($request->hanzo_fee ?? 0),
            'valid_until' => $request->valid_until ? \Carbon\Carbon::parse($request->valid_until) : now()->addDays(14),
        ];

        $quotation = $rfq->quotations()->latest()->first();

        if ($quotation) {
            $this->quoteService->updateCosts($quotation, $costBreakdown);
        } else {
            $quotation = $this->quoteService->buildFromRfq($rfq, $costBreakdown);
        }

        if ($request->input('action') === 'send') {
            $this->quoteService->sendToBuyer($quotation);
            return redirect()->route('admin.rfqs.show', $rfq)->with('success', 'Quote sent to buyer.');
        }

        return redirect()->route('admin.quote-builder.edit', $rfq)->with('success', 'Quote saved as draft.');
    }
}
