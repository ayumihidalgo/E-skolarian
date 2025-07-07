<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'reviewed_by',
        'document_id',
        'message',
        'status',
        'forwarded_to',
        'forward_message'
    ];

    /**
     * Get the user who performed the review
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the user to whom the document was forwarded
     */
    public function forwardedToUser()
    {
        return $this->belongsTo(User::class, 'forwarded_to', 'id');
    }


    /**
     * Get the document being reviewed
     */
    public function document()
    {
        return $this->belongsTo(SubmittedDocument::class, 'document_id');
    }
}