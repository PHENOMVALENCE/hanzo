<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Rfq;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderMilestoneNotification;
use App\Notifications\PaymentPendingNotification;
use App\Notifications\ProductApprovedNotification;
use App\Notifications\ProductSubmittedNotification;
use App\Notifications\QuoteRejectedNotification;
use App\Notifications\QuoteSentNotification;
use App\Notifications\RfqAssignedNotification;

class NotificationService
{
    public function notifyNewOrder(Order $order): void
    {
        $admins = \App\Models\User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewOrderNotification($order));
        }

        $factoryUser = $order->quotation?->rfq?->assignedFactory?->user;
        if ($factoryUser) {
            $factoryUser->notify(new NewOrderNotification($order));
        }
    }

    public function notifyOrderMilestone(Order $order, string $milestone): void
    {
        $notification = new OrderMilestoneNotification($order, $milestone);
        $order->buyer?->notify($notification);
        \App\Models\User::role('admin')->get()->each(fn ($u) => $u->notify($notification));
        $factoryUser = $order->quotation?->rfq?->assignedFactory?->user;
        if ($factoryUser) {
            $factoryUser->notify($notification);
        }
    }

    public function notifyPaymentPending(\App\Models\Payment $payment): void
    {
        foreach (\App\Models\User::role('admin')->get() as $admin) {
            $admin->notify(new PaymentPendingNotification($payment));
        }
    }

    public function notifyQuoteSent(Quotation $quotation): void
    {
        $buyer = $quotation->rfq?->buyer;
        if ($buyer) {
            $buyer->notify(new QuoteSentNotification($quotation));
        }
    }

    public function notifyQuoteRejected(Quotation $quotation): void
    {
        foreach (\App\Models\User::role('admin')->get() as $admin) {
            $admin->notify(new QuoteRejectedNotification($quotation));
        }
        $factoryUser = $quotation->rfq?->assignedFactory?->user;
        if ($factoryUser) {
            $factoryUser->notify(new QuoteRejectedNotification($quotation));
        }
    }

    public function notifyProductApproved(Product $product): void
    {
        $factoryUser = $product->factory?->user;
        if ($factoryUser) {
            $factoryUser->notify(new ProductApprovedNotification($product));
        }
    }

    public function notifyProductSubmitted(Product $product): void
    {
        foreach (\App\Models\User::role('admin')->get() as $admin) {
            $admin->notify(new ProductSubmittedNotification($product));
        }
    }

    public function notifyRfqAssigned(Rfq $rfq): void
    {
        $factoryUser = $rfq->assignedFactory?->user;
        if ($factoryUser) {
            $factoryUser->notify(new RfqAssignedNotification($rfq));
        }
    }
}
