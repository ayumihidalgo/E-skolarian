<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    // these fields for mass assignment
    protected $fillable = [
        'user_id',
        'title',
        'content',
    ];
}
