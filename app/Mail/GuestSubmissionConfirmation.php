<?php

namespace App\Mail;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class GuestSubmissionConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $document;
    public $receiver;

    /**
     * Create a new message instance.
     */
    public function __construct(Document $document, User $receiver)
    {
        $this->document = $document;
        $this->receiver = $receiver;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Document Submission Confirmation')
                    ->view('emails.guestSubmissionConfirmation');
    }
}
