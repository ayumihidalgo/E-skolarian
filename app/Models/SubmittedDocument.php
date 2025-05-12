<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubmittedDocument;

class SubmittedDocument extends Model
{
    use HasFactory;

    protected $table = 'submitted_documents';

    protected $fillable = [
        'user_id',
        'received_by',
        'subject',
        'summary',
        'type',
        'control_tag',
        'status',
        'file_path',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the user who submitted the document
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who received the document
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Get the reviews for this document
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'document_id');
    }
}
