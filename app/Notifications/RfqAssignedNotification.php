<?php

namespace App\Notifications;

use App\Models\Rfq;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RfqAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Rfq $rfq
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
        $url = route('factory.rfqs.show', $this->rfq);

        return (new MailMessage)
            ->subject('New RFQ assigned: ' . $this->rfq->code)
            ->line('A product request has been assigned to you: ' . $this->rfq->code . '.')
            ->line('Please submit your price and MOQ.')
            ->action('View RFQ', $url)
            ->line('Thank you for using HANZO!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'rfq_assigned',
            'rfq_id' => $this->rfq->id,
            'rfq_code' => $this->rfq->code,
        ];
    }
}
