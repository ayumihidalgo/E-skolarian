<?php


namespace App\Events;

use App\Models\SubmittedDocument;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentStatusUpdated
{
    use Dispatchable, SerializesModels;

    public $document;

    public function __construct(SubmittedDocument $document)
    {
        $this->document = $document;
    }
}