<?php
// FILE: app/Notifications/AdminNewUserRegistration.php
namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewUserRegistration extends Notification
{
    use Queueable;

    public function __construct(public User $newUser) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Registration: {$this->newUser->name}")
            ->greeting('Hello Admin,')
            ->line("A new user has registered: **{$this->newUser->name}** ({$this->newUser->email})")
            ->line('This account requires your approval before the user can log in.')
            ->action('Review & Approve', route('admin.users.show', $this->newUser));
    }
}
