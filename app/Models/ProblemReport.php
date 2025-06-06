<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProblemReport extends Model
{
    protected $fillable = [
        'email', 
        'description', 
        'file_path',
        'viewed'
    ];

    protected $appends = ['file_url'];

    protected $casts = [
        'viewed' => 'boolean'
    ];

    public function getFileUrlAttribute()
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }
}
