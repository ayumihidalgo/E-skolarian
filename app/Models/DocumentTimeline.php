<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTimeline extends Model
{
    use HasFactory;
    
    protected $table = 'document_timeline';
    
    protected $fillable = [
        'document_id',
        'user_id',
        'action_type',
        'status',
        'message',
        'forwarded_to',
        'related_review_id'
    ];

    /**
     * Get the document for this timeline entry
     */
    public function document()
    {
        return $this->belongsTo(SubmittedDocument::class, 'document_id');
    }

    /**
     * Get the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the user to whom the document was forwarded
     */
    public function forwardedToUser()
    {
        return $this->belongsTo(User::class, 'forwarded_to');
    }
    
    /**
     * Get related review record
     */
    public function review()
    {
        return $this->belongsTo(Review::class, 'related_review_id');
    }
}