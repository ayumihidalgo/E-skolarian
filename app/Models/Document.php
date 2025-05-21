<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'submitted_documents';

    protected $fillable = [
        'user_id',
        'received_by',
        'subject',
        'type',
        'control_tag',
        'summary',
        'status',
    ];
}
