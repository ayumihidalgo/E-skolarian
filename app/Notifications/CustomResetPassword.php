<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPassword extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $role = $notifiable->role; // raw, lowercase for URL
        $displayRole = ucwords($role); // capitalized for subject

        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
            'role' => $role,  // Add the role here
        ], false));

        return (new MailMessage)
            ->subject("Reset your $displayRole Account Password")
            ->view('emails.custom-reset-password', [
                'url' => $url,
                'displayRole' => $displayRole
            ]);
    }
}
