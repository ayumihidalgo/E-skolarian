<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    protected $table = 'document_versions';

    protected $fillable = [
        'document_id',
        'uploaded_by',
        'version',
        'file_path',
        'comments',
        'submitted_at',
    ];

    public $timestamps = false; // optional, if you don't use created_at/updated_at

    // Relationships (optional)
    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }
}