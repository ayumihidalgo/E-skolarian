<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\SubmittedDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Events\NewChatMessage;
use App\Models\User;
use App\Services\ProfanityFilter;

class CommentController extends Controller
{
    protected $profanityFilter;

    public function __construct(ProfanityFilter $profanityFilter)
    {
        $this->profanityFilter = $profanityFilter;
    }

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

        // Add debugging for comment text
        if ($request->has('comment') && !empty($request->comment)) {
            if ($this->profanityFilter->hasProfanity($request->comment)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your comment contains inappropriate language. Please revise and try again.'
                ], 422);
            }
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
            $adminRoles = ['admin', 'Student Services', 'Academic Services', 'Administrative Services', 'Campus Director'];
            if (in_array(Auth::user()->role, $adminRoles)) {
                $comment->received_by = $document->user_id; // If admin/staff is commenting, the receiver is the document submitter
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

            // Retrieve the receiver user based on the received_by field
            $receiverUser = $comment->received_by ? User::find($comment->received_by) : null;

            // Trigger the event
            event(new NewChatMessage($comment, $receiverUser));   // Load the sender relationship
            $comment->load('sender');

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully.',
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
     * Store a newly created comment for students.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function studentstore(Request $request)
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
            $adminRoles = ['admin', 'Student Services', 'Academic Services', 'Administrative Services', 'Campus Director'];
            if (in_array(Auth::user()->role, $adminRoles)) {
                $comment->received_by = $document->user_id; // If admin/staff is commenting, the receiver is the document submitter
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

            // Retrieve the receiver user based on the received_by field
            $receiverUser = $comment->received_by ? User::find($comment->received_by) : null;

            // Trigger the event
            event(new NewChatMessage($comment, $receiverUser));

            // Load the sender relationship
            $comment->load('sender');

            // Return JSON response instead of redirecting
            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully.',
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
                ->with(['sender:id,username,role_name,profile_pic'])
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json($comments);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve comments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check for new comments since a given timestamp
     *
     * @param \Illuminate\Http\Request $request
     * @param int $document_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkNewComments(Request $request, $document_id)
    {
        try {
            // Get the timestamp of the last update from the request
            $lastUpdate = $request->input('lastUpdate');
            
            // Query for comments newer than the last update
            $query = Comment::with('sender')
                ->where('document_id', $document_id)
                ->orderBy('created_at', 'desc');
                
            if ($lastUpdate) {
                $query->where('created_at', '>', $lastUpdate);
            }
            
            $comments = $query->get();
            
            // Get the timestamp of the most recent comment for the next check
            $latestTimestamp = $comments->isNotEmpty() 
                ? $comments->first()->created_at 
                : ($lastUpdate ?? now()->toISOString());
                
            return response()->json([
                'hasNewComments' => $comments->isNotEmpty(),
                'comments' => $comments,
                'latestTimestamp' => $latestTimestamp
            ]);
        } catch (\Exception $e) {
            \Log::error('Error checking for new comments: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to check for new comments',
                'details' => $e->getMessage(),
                'hasNewComments' => false,
                'comments' => []
            ], 200); // Return 200 to prevent frontend errors
        }
    }
}
