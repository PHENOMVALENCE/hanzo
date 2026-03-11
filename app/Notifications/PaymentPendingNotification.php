<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentPendingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Payment $payment
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->payment->order;
        $url = route('admin.payments.show', $this->payment);

        return (new MailMessage)
            ->subject('Payment pending verification: ' . ($order->order_code ?? 'Order'))
            ->line('A payment of $' . number_format($this->payment->amount_usd, 2) . ' has been submitted for ' . ($order->order_code ?? 'order') . '.')
            ->action('Verify Payment', $url)
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        $order = $this->payment->order;

        return [
            'type' => 'payment_pending',
            'payment_id' => $this->payment->id,
            'order_id' => $this->payment->order_id,
            'order_code' => $order?->order_code ?? 'Order',
            'amount' => $this->payment->amount_usd,
        ];
    }
}
