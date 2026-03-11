<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = match (true) {
            $notifiable->hasRole('admin') => route('admin.orders.show', $this->order),
            $notifiable->hasRole('factory') => route('factory.orders.show', $this->order),
            default => route('admin.orders.show', $this->order),
        };

        return (new MailMessage)
            ->subject('New order: ' . $this->order->order_code)
            ->line('A new order has been placed: ' . $this->order->order_code)
            ->line('Order: ' . $this->order->displayName())
            ->action('View Order', $url)
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'order_name' => $this->order->displayName(),
        ];
    }
}
