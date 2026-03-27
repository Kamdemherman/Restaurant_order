<?php
// FILE: app/Notifications/AccountApproved.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification
{
    use Queueable;

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your account has been approved!')
            ->greeting("Hello {$notifiable->name}!")
            ->line('Your account has been approved. You can now log in and start ordering.')
            ->action('Login Now', route('login'));
    }
}
