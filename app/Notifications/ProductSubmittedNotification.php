<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Product $product
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('admin.products.index', ['status' => 'pending_approval']);

        return (new MailMessage)
            ->subject('Product pending approval: ' . $this->product->title)
            ->line('A factory has submitted a product for approval: "' . $this->product->title . '".')
            ->action('Review Products', $url)
            ->line('Thank you for using HANZO!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'product_submitted',
            'product_id' => $this->product->id,
            'product_title' => $this->product->title,
        ];
    }
}
