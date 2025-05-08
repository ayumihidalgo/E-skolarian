<?php

namespace App\Http\Controllers;

use App\Models\SubmittedDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DocumentReviewController extends Controller
{
    /**
     * Display the document review page with documents that need admin approval
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get the currently logged in user
        $user = Auth::user();
        
        // Get documents with their submitters - using paginate() instead of get()
        // Filter by received_by to only show documents intended for this admin
        $documentsQuery = SubmittedDocument::with(['user'])
            ->select('submitted_documents.*')
            ->where('received_by', $user->id)  // Only show documents intended for this admin
            ->addSelect(DB::raw("
                CASE 
                    WHEN reviews.id IS NULL THEN false
                    ELSE true
                END as is_opened
            "))
            ->leftJoin('reviews', function($join) {
                $join->on('submitted_documents.id', '=', 'reviews.document_id')
                    ->where('reviews.reviewed_by', '=', Auth::id());
            })
            ->orderBy('submitted_documents.created_at', 'desc');
            
        // Paginate the results - this returns a LengthAwarePaginator
        $documents = $documentsQuery->paginate(10);
        
        // Transform each document in the paginated collection
        $documents->getCollection()->transform(function($document) {
            $document->tag = $document->control_tag;
            
            // Properly get the username from the users table via the relationship
            $document->organization = $document->user ? $document->user->username : 'Unknown';
            
            $document->title = $document->subject;
            $document->date = \Carbon\Carbon::parse($document->created_at);
            
            return $document;
        });

        // Define tag colors for different organizations
        $tagColors = [
            'PSY' => 'text-purple-600',
            'ECE' => 'text-blue-600',
            'IT' => 'text-green-600',
            'EDU' => 'text-yellow-600',
            'HR' => 'text-red-600',
            'MAR' => 'text-indigo-600',
            'ACC' => 'text-pink-600',
            'IE' => 'text-cyan-600',
            'AGDS' => 'text-emerald-600',
            'CHO' => 'text-amber-600',
            'SIGMA' => 'text-teal-600',
            'TAP' => 'text-rose-600',
            'OSC' => 'text-orange-600',
            'DOC' => 'text-blue-800',  // Added for DOC prefix seen in your sample data
        ];

        return view('admin.documentReview', compact('documents', 'tagColors'));
    }

    /**
     * Get the details of a specific document
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetails($id)
    {
        $document = SubmittedDocument::with(['user', 'reviews.reviewer'])
            ->where('received_by', Auth::id())  // Only allow access to documents intended for this user
            ->findOrFail($id);
            
        // Transform document for the view
        $documentData = [
            'id' => $document->id,
            'subject' => $document->subject,
            'summary' => $document->summary,
            'type' => $document->type,
            'control_tag' => $document->control_tag,
            'status' => $document->status,
            'file_path' => $document->file_path,
            'created_at' => $document->created_at,
            'organization' => $document->user ? $document->user->username : 'Unknown',
            'reviews' => $document->reviews->map(function($review) {
                return [
                    'reviewer_name' => $review->reviewer ? $review->reviewer->name : 'Unknown',
                    'status' => $review->status,
                    'message' => $review->message,
                    'created_at' => $review->created_at
                ];
            })
        ];
        
        return response()->json($documentData);
    }

    /**
     * Mark a document as opened/reviewed
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsOpened($id)
    {
        try {
            $document = SubmittedDocument::findOrFail($id);
            
            // Ensure the document is intended for this admin
            if ($document->received_by != Auth::id()) {
                return response()->json([
                    'success' => false, 
                    'error' => 'This document is not assigned to you'
                ], 403);
            }
            
            // Check if a review already exists for this document by the current user
            $existingReview = DB::table('reviews')
                ->where('document_id', $id)
                ->where('reviewed_by', Auth::id())
                ->first();
                
            // If no review exists, create one with "Under Review" status
            if (!$existingReview) {
                DB::table('reviews')->insert([
                    'document_id' => $id,
                    'reviewed_by' => Auth::id(),
                    'status' => 'Under Review',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Approve a document
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveDocument(Request $request, $id)
    {
        try {
            $document = SubmittedDocument::where('received_by', Auth::id())
                ->findOrFail($id);
            
            // Update document status
            $document->status = 'Approved';
            $document->save();
            
            // Find the existing review and update it
            $existingReview = DB::table('reviews')
                ->where('document_id', $id)
                ->where('reviewed_by', Auth::id())
                ->first();
                
            if ($existingReview) {
                // Update the existing review
                DB::table('reviews')
                    ->where('id', $existingReview->id)
                    ->update([
                        'status' => 'Approved',
                        'message' => $request->input('message', 'Document approved'),
                        'updated_at' => now()
                    ]);
            } else {
                // Create a new review
                DB::table('reviews')->insert([
                    'document_id' => $id,
                    'reviewed_by' => Auth::id(),
                    'status' => 'Approved',
                    'message' => $request->input('message', 'Document approved'),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            // Handle forwarding logic if needed
            if ($request->has('forward_to')) {
                // Update the document's received_by field to the new admin
                $document->received_by = $request->input('forward_to');
                $document->save();
                
                // Create a record of the forwarding
                DB::table('document_forwards')->insert([
                    'document_id' => $id,
                    'forwarded_by' => Auth::id(),
                    'forwarded_to' => $request->input('forward_to'),
                    'message' => $request->input('forward_message', ''),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Document approved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of admins for forwarding
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAdmins()
    {
        $admins = User::where('role', 'admin')
            ->where('id', '!=', Auth::id()) // Exclude current user
            ->where('active', true) // Only active users
            ->select('id', 'name', 'username')
            ->get();
        
        return response()->json($admins);
    }

    /**
     * Reject a document
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectDocument(Request $request, $id)
    {
        try {
            $document = SubmittedDocument::where('received_by', Auth::id())
                ->findOrFail($id);
            
            // Update document status
            $document->status = 'Rejected';
            $document->save();
            
            // Find the existing review and update it
            $existingReview = DB::table('reviews')
                ->where('document_id', $id)
                ->where('reviewed_by', Auth::id())
                ->first();
                
            if ($existingReview) {
                // Update the existing review
                DB::table('reviews')
                    ->where('id', $existingReview->id)
                    ->update([
                        'status' => 'Rejected',
                        'message' => $request->input('message', 'Document rejected'),
                        'updated_at' => now()
                    ]);
            } else {
                // Create a new review
                DB::table('reviews')->insert([
                    'document_id' => $id,
                    'reviewed_by' => Auth::id(),
                    'status' => 'Rejected',
                    'message' => $request->input('message', 'Document rejected'),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Document rejected successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Request resubmission of a document
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestResubmission(Request $request, $id)
    {
        try {
            $document = SubmittedDocument::where('received_by', Auth::id())
                ->findOrFail($id);
            
            // Update document status
            $document->status = 'Resubmission Requested';
            $document->save();
            
            // Find the existing review and update it
            $existingReview = DB::table('reviews')
                ->where('document_id', $id)
                ->where('reviewed_by', Auth::id())
                ->first();
                
            if ($existingReview) {
                // Update the existing review
                DB::table('reviews')
                    ->where('id', $existingReview->id)
                    ->update([
                        'status' => 'Resubmission Requested',
                        'message' => $request->input('message', 'Please resubmit with changes'),
                        'updated_at' => now()
                    ]);
            } else {
                // Create a new review
                DB::table('reviews')->insert([
                    'document_id' => $id,
                    'reviewed_by' => Auth::id(),
                    'status' => 'Resubmission Requested',
                    'message' => $request->input('message', 'Please resubmit with changes'),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Resubmission requested successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}