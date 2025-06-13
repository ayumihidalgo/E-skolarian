<?php

namespace App\Http\Controllers;

use App\Models\SubmittedDocument;
use Illuminate\Http\Request;

class GuestDocumentController extends Controller
{
    /**
     * Constructor - explicitly don't apply auth middleware
     */
    public function __construct()
    {
        // No auth middleware for this controller
    }

    /**
     * View a guest document using email verification
     */
    public function view(Request $request, $id)
    {
        // Load document with related data including latest version
        $document = SubmittedDocument::with([
            'reviews',
            'timeline',
            'latestVersion'
        ])->findOrFail($id);
        
        // Verify this is a guest submission
        if ($document->user_id !== null) {
            abort(403, 'Invalid access method for this document');
        }
        
        // Verify the email hash matches
        if ($request->email_hash !== hash('sha256', $document->guest_webmail)) {
            abort(403, 'Invalid access credentials');
        }
        
        // Optional: Check if timestamp is recent (within 30 days)
        $linkAge = time() - intval($request->timestamp);
        if ($linkAge > 2592000) { // 30 days in seconds
            abort(403, 'Link has expired');
        }
        
        // Show a read-only view of the document
        return view('guest.document', compact('document'));
    }
}