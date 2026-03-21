<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderMilestoneNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
        public string $milestone,
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        if (config('mail.notifications_enabled', true)) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = trans_status($this->milestone);
        $url = match (true) {
            $notifiable->hasRole('admin') => route('admin.orders.show', $this->order),
            $notifiable->hasRole('factory') => route('factory.orders.show', $this->order),
            default => route('buyer.orders.show', $this->order),
        };

        return (new MailMessage)
            ->subject('Order ' . $this->order->order_code . ': ' . $statusLabel)
            ->line('Order ' . $this->order->order_code . ' status: ' . $statusLabel)
            ->line('Order: ' . $this->order->displayName())
            ->action('View Order', $url)
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order_milestone',
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'order_name' => $this->order->displayName(),
            'milestone' => $this->milestone,
        ];
    }
}
