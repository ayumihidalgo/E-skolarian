<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'submitted_documents';

    protected $fillable = [
        'received_by',
        'subject',
        'type',
        'control_tag',
        'summary',
        'status',
        'event_start_date',
        'event_end_date',
        'file_path',
    ];
}
