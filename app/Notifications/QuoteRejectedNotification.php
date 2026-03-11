<?php

namespace App\Notifications;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Quotation $quotation
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('admin.rfqs.show', $this->quotation->rfq);
        $buyer = $this->quotation->rfq?->buyer?->name ?? 'Buyer';
        $reason = $this->quotation->rejection_reason
            ? "Reason: {$this->quotation->rejection_reason}"
            : 'No reason provided.';

        return (new MailMessage)
            ->subject('Quote rejected: ' . $this->quotation->quote_code)
            ->line("{$buyer} has rejected quote {$this->quotation->quote_code}.")
            ->line($reason)
            ->action('View RFQ', $url)
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'quote_rejected',
            'quotation_id' => $this->quotation->id,
            'quote_code' => $this->quotation->quote_code,
            'rfq_id' => $this->quotation->rfq_id,
            'buyer_name' => $this->quotation->rfq?->buyer?->name ?? 'Buyer',
        ];
    }
}
