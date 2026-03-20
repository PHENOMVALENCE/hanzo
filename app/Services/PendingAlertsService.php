<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
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

        // Orders awaiting factory approval
        Order::with('buyer', 'quotation.rfq')
            ->where('milestone_status', 'awaiting_factory_approval')
            ->latest()
            ->limit(5)
            ->get()
            ->each(function (Order $o) use ($alerts) {
                $alerts->push([
                    'type' => 'order_pending',
                    'url' => route('admin.orders.show', $o),
                    'title' => 'Order ' . $o->order_code . ' – awaiting factory approval',
                    'icon' => 'bx-package',
                ]);
            });

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
                    'title' => 'Payment pending: ' . money($p->amount_usd) . ' – ' . ($p->order?->order_code ?? 'Order'),
                    'icon' => 'bx-dollar',
                ]);
            });

        // Products pending approval
        Product::with('factory', 'category')
            ->where('status', Product::STATUS_PENDING_APPROVAL)
            ->latest()
            ->limit(3)
            ->get()
            ->each(function (Product $p) use ($alerts) {
                $alerts->push([
                    'type' => 'product_pending',
                    'url' => route('admin.products.index', ['status' => 'pending_approval']),
                    'title' => 'Product pending: ' . $p->title,
                    'icon' => 'bx-box',
                ]);
            });

        // RFQs awaiting quote
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

        // Orders awaiting factory approval (needs action)
        Order::whereHas('quotation.rfq', fn ($q) => $q->where('assigned_factory_id', $factory->id))
            ->where('milestone_status', 'awaiting_factory_approval')
            ->with('quotation.rfq')
            ->latest()
            ->limit(5)
            ->get()
            ->each(function (Order $o) use ($alerts) {
                $alerts->push([
                    'type' => 'order_pending',
                    'url' => route('factory.orders.show', $o),
                    'title' => 'Order ' . $o->order_code . ' – awaiting your approval',
                    'icon' => 'bx-package',
                ]);
            });

        // Orders in production
        Order::whereHas('quotation.rfq', fn ($q) => $q->where('assigned_factory_id', $factory->id))
            ->where('milestone_status', 'in_production')
            ->with('quotation.rfq')
            ->latest()
            ->limit(5)
            ->get()
            ->each(function (Order $o) use ($alerts) {
                $alerts->push([
                    'type' => 'order_pending',
                    'url' => route('factory.orders.show', $o),
                    'title' => 'Order ' . $o->order_code . ' – ' . trans_status($o->milestone_status),
                    'icon' => 'bx-package',
                ]);
            });

        // Other recent orders
        Order::whereHas('quotation.rfq', fn ($q) => $q->where('assigned_factory_id', $factory->id))
            ->with('quotation.rfq')
            ->latest()
            ->limit(5)
            ->whereNotIn('milestone_status', ['awaiting_factory_approval', 'in_production'])
            ->get()
            ->each(function (Order $o) use ($alerts) {
                $alerts->push([
                    'type' => 'order',
                    'url' => route('factory.orders.show', $o),
                    'title' => 'Order ' . $o->order_code . ' – ' . trans_status($o->milestone_status),
                    'icon' => 'bx-package',
                ]);
            });

        return $alerts->take(10);
    }

    protected function forBuyer($user): Collection
    {
        $alerts = collect();

        // Orders awaiting factory approval (buyer is waiting)
        Order::where('buyer_id', $user->id)
            ->where('milestone_status', 'awaiting_factory_approval')
            ->with('quotation.rfq')
            ->latest()
            ->limit(5)
            ->get()
            ->each(function (Order $o) use ($alerts) {
                $alerts->push([
                    'type' => 'order_pending',
                    'url' => route('buyer.orders.show', $o),
                    'title' => 'Order ' . $o->order_code . ' – awaiting factory approval',
                    'icon' => 'bx-package',
                ]);
            });

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

        // Other orders (in progress, ready to ship, completed)
        Order::where('buyer_id', $user->id)
            ->whereNot('milestone_status', 'awaiting_factory_approval')
            ->with('quotation.rfq')
            ->latest()
            ->limit(3)
            ->get()
            ->each(function (Order $o) use ($alerts) {
                $alerts->push([
                    'type' => 'order',
                    'url' => route('buyer.orders.show', $o),
                    'title' => 'Order ' . $o->order_code . ' – ' . trans_status($o->milestone_status),
                    'icon' => 'bx-package',
                ]);
            });

        return $alerts->take(10);
    }
}
