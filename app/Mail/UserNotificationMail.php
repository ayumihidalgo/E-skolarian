<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $actionType;
    public $isNewUser;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param string $actionType ('created' or 'updated')
     * @return void
     */
    public function __construct(User $user, string $actionType)
    {
        $this->user = $user;
        $this->actionType = $actionType;
        $this->isNewUser = ($actionType === 'created');
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $subject = $this->isNewUser
            ? 'Welcome to Our Platform'
            : 'Your Account Has Been Updated';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.user-notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}