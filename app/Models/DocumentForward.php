<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentForward extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'document_id',
        'forwarded_by',
        'forwarded_to',
        'message',
    ];

    /**
     * Get the document that was forwarded.
     */
    public function document()
    {
        return $this->belongsTo(SubmittedDocument::class, 'document_id');
    }

    /**
     * Get the admin who forwarded the document.
     */
    public function forwarder()
    {
        return $this->belongsTo(User::class, 'forwarded_by');
    }

    /**
     * Get the admin who received the forwarded document.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'forwarded_to');
    }
}