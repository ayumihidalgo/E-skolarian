<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecoveryEmailRemovedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var mixed
     */
    public $user;

    /**
     * Create a new message instance.
     *
     * @param mixed $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
    public function build()
    {
        return $this->subject('Recovery Email Removed')
            ->view('emails.recovery_email_removed')
            ->with(['user' => $this->user]);
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recovery Email Removed Mail',
        );
    }

    /**
     * Get the message content definition.
     */

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
