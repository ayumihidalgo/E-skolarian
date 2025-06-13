<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\SubmittedDocument;

class DocumentResubmissionMail extends Mailable
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
        return $this->subject('Document Resubmission Required - ' . $this->document->title)
                   ->markdown('emails.resubmission')
                   ->with([
                       'document' => $this->document,
                       'message' => $this->message,
                   ]);
    }
}