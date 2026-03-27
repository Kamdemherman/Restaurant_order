<?php
// FILE: app/Notifications/OrderStatusUpdated.php
namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        $status = ucfirst($this->order->status);
        return (new MailMessage)
            ->subject("Order {$this->order->order_number} is now {$status}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your order **{$this->order->order_number}** status has been updated to: **{$status}**")
            ->action('View Order', route('orders.show', $this->order));
    }
}
