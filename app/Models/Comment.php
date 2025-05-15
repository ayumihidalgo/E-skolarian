<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['document_id', 'sent_by', 'received_by', 'comment'];
    
    // Relationship with the document
    public function document()
    {
        return $this->belongsTo(SubmittedDocument::class, 'document_id');
    }
    
    // Relationship with the sender (user who sent the comment)
    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
    
    // Relationship with the receiver (user who received the comment)
    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
