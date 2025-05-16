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
    public $isNewUser;
    public $isDeactivated;
    public $isReactivated;
    public $actionType;
    public $password;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param string $actionType ('created', 'updated', or 'deactivated')
     * @param string|null $password Generated password for new users
     * @return void
     */
    public function __construct(User $user, string $actionType, ?string $password = null)
    {
        $this->user = $user;
        $this->actionType = $actionType;
        $this->isNewUser = ($actionType === 'created');
        $this->isDeactivated = ($actionType === 'deactivated');
        $this->isReactivated = ($actionType === 'reactivated');
        $this->password = $password;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $subject = match($this->actionType) {
            'created' => 'Welcome to E-skolarian - Your Account Has Been Created',
            'updated' => 'E-skolarian - Your Account Has Been Updated',
            'deactivated' => 'E-skolarian - Your Account Has Been Deactivated',
            'reactivated' => 'E-skolarian - Your Account Has Been Reactivated',
            default => 'E-skolarian - Account Notification'
        };

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