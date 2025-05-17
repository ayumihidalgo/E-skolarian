<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications'; // Explicitly define the table name
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'is_read',
        'url'
    ];
}

