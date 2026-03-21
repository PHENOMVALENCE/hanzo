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
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send notification to user, catching mail transport errors so they don't break the flow.
     */
    private function sendSafe(object $notifiable, object $notification): void
    {
        try {
            $notifiable->notify($notification);
        } catch (\Throwable $e) {
            Log::warning('Notification mail failed (database notification may still be saved)', [
                'notifiable' => $notifiable->id ?? null,
                'notification' => get_class($notification),
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function notifyNewOrder(Order $order): void
    {
        $admins = \App\Models\User::role('admin')->get();
        foreach ($admins as $admin) {
            $this->sendSafe($admin, new NewOrderNotification($order));
        }

        $factoryUser = $order->quotation?->rfq?->assignedFactory?->user;
        if ($factoryUser) {
            $this->sendSafe($factoryUser, new NewOrderNotification($order));
        }
    }

    public function notifyOrderMilestone(Order $order, string $milestone): void
    {
        $notification = new OrderMilestoneNotification($order, $milestone);
        if ($order->buyer) {
            $this->sendSafe($order->buyer, $notification);
        }
        foreach (\App\Models\User::role('admin')->get() as $admin) {
            $this->sendSafe($admin, $notification);
        }
        $factoryUser = $order->quotation?->rfq?->assignedFactory?->user;
        if ($factoryUser) {
            $this->sendSafe($factoryUser, $notification);
        }
    }

    public function notifyPaymentPending(\App\Models\Payment $payment): void
    {
        foreach (\App\Models\User::role('admin')->get() as $admin) {
            $this->sendSafe($admin, new PaymentPendingNotification($payment));
        }
    }

    public function notifyQuoteSent(Quotation $quotation): void
    {
        $buyer = $quotation->rfq?->buyer;
        if ($buyer) {
            $this->sendSafe($buyer, new QuoteSentNotification($quotation));
        }
    }

    public function notifyQuoteRejected(Quotation $quotation): void
    {
        foreach (\App\Models\User::role('admin')->get() as $admin) {
            $this->sendSafe($admin, new QuoteRejectedNotification($quotation));
        }
        $factoryUser = $quotation->rfq?->assignedFactory?->user;
        if ($factoryUser) {
            $this->sendSafe($factoryUser, new QuoteRejectedNotification($quotation));
        }
    }

    public function notifyProductApproved(Product $product): void
    {
        $factoryUser = $product->factory?->user;
        if ($factoryUser) {
            $this->sendSafe($factoryUser, new ProductApprovedNotification($product));
        }
    }

    public function notifyProductSubmitted(Product $product): void
    {
        foreach (\App\Models\User::role('admin')->get() as $admin) {
            $this->sendSafe($admin, new ProductSubmittedNotification($product));
        }
    }

    public function notifyRfqAssigned(Rfq $rfq): void
    {
        $factoryUser = $rfq->assignedFactory?->user;
        if ($factoryUser) {
            $this->sendSafe($factoryUser, new RfqAssignedNotification($rfq));
        }
    }
}
