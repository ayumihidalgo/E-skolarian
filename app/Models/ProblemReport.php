<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProblemReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'description',
        'file_path',
        'viewed',
    ];

    protected $appends = ['file_url'];

    protected $casts = [
        'viewed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getFileUrlAttribute()
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }
}
