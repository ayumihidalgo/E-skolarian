<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DocumentSubmitted
{
    use Dispatchable, SerializesModels;

    public $document;

    public function __construct($document)
    {
        $this->document = $document;
        Log::info('DocumentSubmitted event constructed with document ID: ' . $document->id);
    }
}


