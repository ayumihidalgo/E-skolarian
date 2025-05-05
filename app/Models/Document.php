<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'submitted_documents';

    protected $fillable = [
        'doc_receiver',
        'subject',
        'doc_type',
        'control_tag',
        'summary',
        'event_start_date',
        'event_end_date',
        'file_path',
    ];
}
