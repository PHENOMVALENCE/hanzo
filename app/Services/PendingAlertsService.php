<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\Rfq;
use Illuminate\Support\Collection;

class PendingAlertsService
{
    /**
     * Get pending items that need the user's attention (orders, payments, quotes, etc.).
     */
    public function forUser(): Collection
    {
        $user = auth()->user();
        if (! $user) {
            return collect();
        }

        return match (true) {
            $user->hasRole('admin') => $this->forAdmin(),
            $user->hasRole('factory') => $this->forFactory($user),
            $user->hasRole('buyer') => $this->forBuyer($user),
            default => collect(),
        };
    }

    protected function forAdmin(): Collection
    {
        $alerts = collect();

        // Pending payments to verify
        Payment::with('order')
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get()
            ->each(function (Payment $p) use ($alerts) {
                $alerts->push([
                    'type' => 'payment_pending',
                    'url' => route('admin.payments.show', $p),
                    'title' => 'Payment pending: $' . number_format($p->amount_usd, 2) . ' – ' . ($p->order?->order_code ?? 'Order'),
                    'icon' => 'bx-dollar',
                ]);
            });

        // RFQs awaiting quote (assigned or pricing received, no quote sent yet)
        Rfq::with('category')
            ->whereIn('status', ['assigned', 'pricing_received'])
            ->whereNotNull('assigned_factory_id')
            ->whereDoesntHave('quotations', fn ($q) => $q->whereIn('status', ['sent', 'accepted']))
            ->latest()
            ->limit(3)
            ->get()
            ->each(function (Rfq $r) use ($alerts) {
                $alerts->push([
                    'type' => 'rfq',
                    'url' => route('admin.rfqs.show', $r),
                    'title' => 'RFQ ' . $r->code . ' – quote needed',
                    'icon' => 'bx-file',
                ]);
            });

        return $alerts->take(10);
    }

    protected function forFactory($user): Collection
    {
        $factory = $user->factory;
        if (! $factory) {
            return collect();
        }

        $alerts = collect();

        Order::whereHas('quotation.rfq', fn ($q) => $q->where('assigned_factory_id', $factory->id))
            ->with('quotation.rfq')
            ->latest()
            ->limit(10)
            ->get()
            ->each(function (Order $o) use ($alerts) {
                $alerts->push([
                    'type' => 'order',
                    'url' => route('factory.orders.show', $o),
                    'title' => 'Order ' . $o->order_code,
                    'icon' => 'bx-package',
                ]);
            });

        return $alerts;
    }

    protected function forBuyer($user): Collection
    {
        $alerts = collect();

        // Quotes awaiting response
        Quotation::whereHas('rfq', fn ($q) => $q->where('buyer_id', $user->id))
            ->where('status', 'sent')
            ->with('rfq')
            ->latest()
            ->limit(5)
            ->get()
            ->each(function (Quotation $q) use ($alerts) {
                $alerts->push([
                    'type' => 'quote_sent',
                    'url' => route('buyer.quotes.show', $q),
                    'title' => 'Quote ' . $q->quote_code . ' – review',
                    'icon' => 'bx-file',
                ]);
            });

        // Orders
        Order::where('buyer_id', $user->id)
            ->with('quotation.rfq')
            ->latest()
            ->limit(5)
            ->get()
            ->each(function (Order $o) use ($alerts) {
                $alerts->push([
                    'type' => 'order',
                    'url' => route('buyer.orders.show', $o),
                    'title' => 'Order ' . $o->order_code,
                    'icon' => 'bx-package',
                ]);
            });

        return $alerts->take(10);
    }
}
