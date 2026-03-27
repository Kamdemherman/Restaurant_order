<?php
// FILE: app/Notifications/OrderConfirmed.php
namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmed extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Order Confirmed - {$this->order->order_number}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your order **{$this->order->order_number}** has been received.")
            ->line("Total: **€" . number_format($this->order->total, 2) . "** (Cash on Delivery)")
            ->action('View Order', route('orders.show', $this->order))
            ->line('Thank you for ordering with us!');
    }
}
