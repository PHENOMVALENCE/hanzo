<?php

namespace App\Notifications;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteSentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Quotation $quotation
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
        $url = route('buyer.quotes.show', $this->quotation);

        return (new MailMessage)
            ->subject('New quote: ' . $this->quotation->quote_code)
            ->line('You have received a new quotation: ' . $this->quotation->quote_code)
            ->line('Total: $' . number_format($this->quotation->total_landed_cost ?? 0, 2))
            ->action('View Quote', $url)
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'quote_sent',
            'quotation_id' => $this->quotation->id,
            'quote_code' => $this->quotation->quote_code,
            'amount' => $this->quotation->total_landed_cost,
        ];
    }
}
