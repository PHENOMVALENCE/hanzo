<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\Rfq;
use App\Notifications\QuoteSentNotification;
use Illuminate\Support\Str;

class QuoteService
{
    /**
     * Get suggested product cost from the latest factory quote (unit_price × quantity).
     * Returns null if no factory quote exists.
     */
    public function getProductCostFromFactoryQuote(Rfq $rfq): ?float
    {
        $factoryQuote = $rfq->factoryQuotes()
            ->latest()
            ->first();

        if (! $factoryQuote || ! $factoryQuote->unit_price_usd) {
            return null;
        }

        $quantity = (float) ($rfq->quantity ?? 0);
        return round((float) $factoryQuote->unit_price_usd * $quantity, 2);
    }

    public function buildFromRfq(Rfq $rfq, array $costBreakdown): Quotation
    {
        $total = ($costBreakdown['product_cost_usd'] ?? 0)
            + ($costBreakdown['china_local_shipping'] ?? 0)
            + ($costBreakdown['export_handling'] ?? 0)
            + ($costBreakdown['freight_cost'] ?? 0)
            + ($costBreakdown['insurance_cost'] ?? 0)
            + ($costBreakdown['clearing_cost'] ?? 0)
            + ($costBreakdown['local_delivery_cost'] ?? 0)
            + ($costBreakdown['hanzo_fee'] ?? 0);

        return Quotation::create([
            'rfq_id' => $rfq->id,
            'quote_code' => 'QT-' . strtoupper(Str::random(8)),
            'valid_until' => $costBreakdown['valid_until'] ?? now()->addDays(14),
            'status' => 'draft',
            'product_cost_usd' => $costBreakdown['product_cost_usd'] ?? 0,
            'china_local_shipping' => $costBreakdown['china_local_shipping'] ?? 0,
            'export_handling' => $costBreakdown['export_handling'] ?? 0,
            'freight_cost' => $costBreakdown['freight_cost'] ?? 0,
            'insurance_cost' => $costBreakdown['insurance_cost'] ?? 0,
            'clearing_cost' => $costBreakdown['clearing_cost'] ?? 0,
            'local_delivery_cost' => $costBreakdown['local_delivery_cost'] ?? 0,
            'hanzo_fee' => $costBreakdown['hanzo_fee'] ?? 0,
            'total_landed_cost' => $total,
            'factory_id' => $rfq->assigned_factory_id,
        ]);
    }

    public function updateCosts(Quotation $quotation, array $costBreakdown): Quotation
    {
        $total = ($costBreakdown['product_cost_usd'] ?? $quotation->product_cost_usd)
            + ($costBreakdown['china_local_shipping'] ?? $quotation->china_local_shipping)
            + ($costBreakdown['export_handling'] ?? $quotation->export_handling)
            + ($costBreakdown['freight_cost'] ?? $quotation->freight_cost)
            + ($costBreakdown['insurance_cost'] ?? $quotation->insurance_cost)
            + ($costBreakdown['clearing_cost'] ?? $quotation->clearing_cost)
            + ($costBreakdown['local_delivery_cost'] ?? $quotation->local_delivery_cost)
            + ($costBreakdown['hanzo_fee'] ?? $quotation->hanzo_fee);

        $quotation->update(array_merge($costBreakdown, ['total_landed_cost' => $total]));

        return $quotation->fresh();
    }

    public function sendToBuyer(Quotation $quotation): void
    {
        $quotation->update(['status' => 'sent']);
        $quotation->rfq->update(['status' => 'quoted']);

        $buyer = $quotation->rfq?->buyer;
        if ($buyer) {
            $buyer->notify(new QuoteSentNotification($quotation));
        }
    }
}
