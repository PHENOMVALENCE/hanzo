<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Quotation;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Str;

class OrderService
{
    public function createFromQuotation(Quotation $quotation): Order
    {
        $order = Order::create([
            'quotation_id' => $quotation->id,
            'order_code' => 'ORD-' . strtoupper(Str::random(8)),
            'buyer_id' => $quotation->rfq->buyer_id,
            'milestone_status' => 'deposit_pending',
        ]);

        $order->milestones()->create([
            'milestone' => 'deposit_pending',
            'changed_by' => auth()->id(),
            'note' => 'Order created from accepted quotation',
        ]);

        $quotation->update(['status' => 'accepted']);
        $quotation->rfq->update(['status' => 'in_production']);

        $this->notifyNewOrder($order);

        return $order;
    }

    public function updateMilestone(Order $order, string $milestone, ?string $note = null): void
    {
        $order->update(['milestone_status' => $milestone]);
        $order->milestones()->create([
            'milestone' => $milestone,
            'changed_by' => auth()->id(),
            'note' => $note,
        ]);
    }

    public function updateTracking(Order $order, ?string $trackingNumber, ?string $estimatedArrival): void
    {
        $order->update([
            'tracking_number' => $trackingNumber,
            'estimated_arrival' => $estimatedArrival ?: null,
        ]);
    }

    protected function notifyNewOrder(Order $order): void
    {
        $admins = \App\Models\User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewOrderNotification($order));
        }

        $factory = $order->quotation?->rfq?->assignedFactory?->user;
        if ($factory) {
            $factory->notify(new NewOrderNotification($order));
        }
    }
}
