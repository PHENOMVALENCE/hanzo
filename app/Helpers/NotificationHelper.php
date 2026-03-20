<?php

namespace App\Helpers;

use Illuminate\Notifications\DatabaseNotification;

class NotificationHelper
{
    /**
     * Get the URL for a notification based on type and user role.
     */
    public static function urlForNotification(DatabaseNotification $n, $user): string
    {
        $type = $n->data['type'] ?? 'order';
        $data = $n->data;

        return match ($type) {
            'order', 'order_milestone' => ! empty($data['order_id'])
                ? self::orderUrl($data['order_id'], $user)
                : '#',
            'quote_sent' => ! empty($data['quotation_id'])
                ? route('buyer.quotes.show', $data['quotation_id'])
                : '#',
            'quote_rejected' => ! empty($data['rfq_id'])
                ? $user->hasRole('admin') ? route('admin.rfqs.show', $data['rfq_id']) : route('factory.rfqs.show', $data['rfq_id'])
                : '#',
            'payment_pending' => ! empty($data['payment_id']) && $user->hasRole('admin')
                ? route('admin.payments.show', $data['payment_id'])
                : '#',
            'product_approved' => $user->hasRole('factory')
                ? route('factory.products.index')
                : '#',
            'product_submitted' => $user->hasRole('admin')
                ? (! empty($data['product_id']) ? route('admin.products.edit', $data['product_id']) : route('admin.products.index', ['status' => 'pending_approval']))
                : '#',
            'rfq_assigned' => ! empty($data['rfq_id']) && $user->hasRole('factory')
                ? route('factory.rfqs.show', $data['rfq_id'])
                : '#',
            'welcome' => route('profile.edit'),
            default => '#',
        };
    }

    /**
     * Get the display title for a notification.
     */
    public static function titleForNotification(DatabaseNotification $n): string
    {
        $type = $n->data['type'] ?? 'order';
        $data = $n->data;

        return match ($type) {
            'welcome' => $data['message'] ?? 'Welcome to ' . config('app.name') . '!',
            'order_milestone' => 'Order ' . ($data['order_code'] ?? '') . ': ' . trans_status($data['milestone'] ?? ''),
            'quote_sent' => 'New quote: ' . ($data['quote_code'] ?? ''),
            'quote_rejected' => 'Quote rejected: ' . ($data['quote_code'] ?? '') . ' by ' . ($data['buyer_name'] ?? ''),
            'payment_pending' => 'Payment pending: ' . money($data['amount'] ?? 0) . ' – ' . ($data['order_code'] ?? ''),
            'product_approved' => 'Product approved: ' . ($data['product_title'] ?? ''),
            'product_submitted' => 'Product pending approval: ' . ($data['product_title'] ?? ''),
            'rfq_assigned' => 'RFQ assigned: ' . ($data['rfq_code'] ?? ''),
            default => $data['order_name'] ?? $data['order_code'] ?? 'New order',
        };
    }

    /**
     * Get the icon for a notification type.
     */
    public static function iconForNotification(string $type): string
    {
        return match ($type) {
            'welcome' => 'bx-user-plus',
            'order_milestone', 'order' => 'bx-package',
            'quote_sent' => 'bx-file',
            'quote_rejected' => 'bx-x-circle',
            'payment_pending' => 'bx-dollar',
            'product_approved', 'product_submitted' => 'bx-box',
            'rfq_assigned' => 'bx-file-blank',
            default => 'bx-package',
        };
    }

    protected static function orderUrl(int $orderId, $user): string
    {
        return match (true) {
            $user->hasRole('admin') => route('admin.orders.show', $orderId),
            $user->hasRole('factory') => route('factory.orders.show', $orderId),
            default => route('buyer.orders.show', $orderId),
        };
    }
}
