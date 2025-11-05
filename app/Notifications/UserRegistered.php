<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Mail\WelcomeUserMail;
use App\Mail\AdminNewUserNotification;

class UserRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public User $user
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage|WelcomeUserMail|AdminNewUserNotification
    {
        // If the notifiable is the user themselves, send welcome email
        if ($notifiable->id === $this->user->id) {
            return new WelcomeUserMail($this->user);
        }
        
        // If the notifiable is an admin, send admin notification
        if ($notifiable->hasRole('admin')) {
            return new AdminNewUserNotification($this->user);
        }

        // Fallback to basic notification
        return (new MailMessage)
            ->subject('New User Registration')
            ->line('A new user has registered: ' . $this->user->name)
            ->line('Email: ' . $this->user->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'user_role' => $this->user->role,
            'registered_at' => $this->user->created_at,
        ];
    }
}