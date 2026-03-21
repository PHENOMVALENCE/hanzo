<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Product $product
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
        $url = route('factory.products.index');

        return (new MailMessage)
            ->subject('Product approved: ' . $this->product->title)
            ->line('Your product "' . $this->product->title . '" has been approved and is now live in the buyer catalog.')
            ->action('View My Products', $url)
            ->line('Thank you for using HANZO!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'product_approved',
            'product_id' => $this->product->id,
            'product_title' => $this->product->title,
        ];
    }
}
