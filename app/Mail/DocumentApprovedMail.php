<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\SubmittedDocument;

class DocumentApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $document;
    public $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SubmittedDocument $document, $message)
    {
        $this->document = $document;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Document Approved - ' . $this->document->title)
                   ->markdown('emails.approved')
                   ->with([
                       'document' => $this->document,
                       'message' => $this->message,
                   ]);
    }
}