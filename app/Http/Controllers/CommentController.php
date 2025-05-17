<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\SubmittedDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'document_id' => 'required|exists:submitted_documents,id',
                'comment' => 'required|string'
            ]);

            // Get the document to determine the receiver
            $document = SubmittedDocument::findOrFail($validated['document_id']);
            
            // Determine sent_by and received_by based on user role
            $currentUser = Auth::user();
            
            if ($currentUser->role === 'admin') {
                // If admin is commenting, the receiver is the document submitter
                $sentBy = $currentUser->id;
                $receivedBy = $document->user_id;
            } else {
                // If student/submitter is commenting, the receiver is the admin
                $sentBy = $currentUser->id;
                $receivedBy = $document->received_by;
            }

            $comment = Comment::create([
                'document_id' => $validated['document_id'],
                'sent_by' => $sentBy,
                'received_by' => $receivedBy,
                'comment' => $validated['comment']
            ]);

            // Load the sender info for the response
            $commentWithSender = Comment::with('sender')->find($comment->id);

            return response()->json($commentWithSender);
        } catch (\Exception $e) {
            Log::error('Comment creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create comment: ' . $e->getMessage()], 500);
        }
    }

    public function getComments($documentId)
    {
        try {
            $comments = Comment::where('document_id', $documentId)
                ->with(['sender', 'receiver'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($comments);
        } catch (\Exception $e) {
            Log::error('Failed to fetch comments: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch comments'], 500);
        }
    }
}