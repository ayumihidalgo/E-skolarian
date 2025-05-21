<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\SubmittedDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    // public function store(Request $request)
    // {
    //     try {
    //         $validated = $request->validate([
    //             'document_id' => 'required|exists:submitted_documents,id',
    //             'comment' => 'required|string'
    //         ]);

    //         // Get the document to determine the receiver
    //         $document = SubmittedDocument::findOrFail($validated['document_id']);

    //         // Determine sent_by and received_by based on user role
    //         $currentUser = Auth::user();

    //         if ($currentUser->role === 'admin') {
    //             // If admin is commenting, the receiver is the document submitter
    //             $sentBy = $currentUser->id;
    //             $receivedBy = $document->user_id;
    //         } else {
    //             // If student/submitter is commenting, the receiver is the admin
    //             $sentBy = $currentUser->id;
    //             $receivedBy = $document->received_by;
    //         }

    //         $comment = Comment::create([
    //             'document_id' => $validated['document_id'],
    //             'sent_by' => $sentBy,
    //             'received_by' => $receivedBy,
    //             'comment' => $validated['comment']
    //         ]);

    //         // Load the sender info for the response
    //         $commentWithSender = Comment::with('sender')->find($comment->id);

    //         return response()->json($commentWithSender);
    //     } catch (\Exception $e) {
    //         Log::error('Comment creation failed: ' . $e->getMessage());
    //         return response()->json(['error' => 'Failed to create comment: ' . $e->getMessage()], 500);
    //     }
    // }

    // public function getComments($documentId)
    // {
    //     try {
    //         $comments = Comment::where('document_id', $documentId)
    //             ->with(['sender', 'receiver'])
    //             ->orderBy('created_at', 'desc')
    //             ->get();

    //         return response()->json($comments);
    //     } catch (\Exception $e) {
    //         Log::error('Failed to fetch comments: ' . $e->getMessage());
    //         return response()->json(['error' => 'Failed to fetch comments'], 500);
    //     }
    // }

    /**
     * Store a newly created comment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'document_id' => 'required|exists:submitted_documents,id',
            'comment' => 'required_without:attachment|string|nullable',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf,docx,doc|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if file size meets minimum requirement
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileSize = $file->getSize();
            $minSize = 1024; // 1KB minimum for testing (change to 5MB in production)
            
            if ($fileSize < $minSize) {
                return response()->json([
                    'success' => false,
                    'errors' => ['attachment' => 'The attachment must be at least 1KB.']
                ], 422);
            }
        }

        try {
            // Check if the document exists
            $document = SubmittedDocument::findOrFail($request->document_id);
            
            // Create a new comment
            $comment = new Comment();
            $comment->document_id = $request->document_id;
            $comment->sent_by = Auth::id();
            // Determine received_by based on user role
            if (Auth::user()->role === 'admin') {
                $comment->received_by = $document->user_id; // If admin is commenting, the receiver is the document submitter
            } else {
                $comment->received_by = $document->received_by ?? null; // If student/submitter is commenting, the receiver is the admin
            }
            $comment->comment = $request->comment ?? '';
            
            // Handle file upload if present
            if ($request->hasFile('attachment')) {
                try {
                    $file = $request->file('attachment');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    
                    // Debug file information
                    Log::info('File information:', [
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime' => $file->getClientMimeType()
                    ]);
                    
                    // Store the file
                    $path = $file->storeAs('comment_attachments', $filename, 'public');
                    
                    // Log storage result
                    Log::info('File stored at: ' . $path);
                    
                    $comment->attachment_path = 'comment_attachments/' . $filename;
                    $comment->attachment_type = $file->getClientMimeType();
                    $comment->attachment_name = $file->getClientOriginalName();
                } catch (\Exception $e) {
                    Log::error('File upload error: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload attachment: ' . $e->getMessage()
                    ], 500);
                }
            }
            
            $comment->save();

            // Load the sender relationship
            $comment->load('sender');
            
            return response()->json([
                'success' => true,
                'comment' => $comment
            ]);
            
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Failed to add comment: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all comments for a document
     *
     * @param  int  $documentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getComments($documentId)
    {
        try {
            $comments = Comment::where('document_id', $documentId)
                ->with('sender')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($comments);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve comments: ' . $e->getMessage()
            ], 500);
        }
    }
}
