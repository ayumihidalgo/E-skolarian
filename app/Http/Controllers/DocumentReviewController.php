<?php

namespace App\Http\Controllers;

use App\Models\SubmittedDocument;
use App\Models\Review;
use App\Models\DocumentForward;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Notifications\DocumentResubmissionRequested;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events\DocumentStatusUpdated;
use App\Models\DocumentTimeline;
use App\LogsActivity;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DocumentReviewController extends Controller
{
    use LogsActivity;

    /**
     * Display the document review page with documents that need admin approval
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get the currently logged in user
        $user = Auth::user();
        
        // Get search parameters from the request
        $searchTerm = $request->input('search');
        $selectedOrg = $request->input('organization');
        $selectedType = $request->input('documentType');
        
        // Base query - documents with their submitters 
        $documentsQuery = SubmittedDocument::with(['user'])
            ->select('submitted_documents.*')
            ->where(function($query) use ($user) {
                // Get documents assigned to this admin
                $query->where('received_by', $user->id);
            })
            ->whereNotIn('submitted_documents.status', ['Approved', 'Returned'])
            ->addSelect(DB::raw("
                CASE 
                    WHEN reviews.id IS NULL THEN false
                    ELSE true
                END as is_opened
            "))
            ->addSelect(DB::raw("
                CASE 
                    WHEN submitted_documents.received_by = " . $user->id . " THEN true
                    ELSE false
                END as is_current_receiver
            "))
            ->leftJoin('reviews', function($join) {
                $join->on('submitted_documents.id', '=', 'reviews.document_id')
                    ->where('reviews.reviewed_by', '=', Auth::id());
            });
            
        // Apply search filter if provided
        if ($searchTerm) {
            $documentsQuery->where(function($query) use ($searchTerm) {
                $query->where('control_tag', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('subject', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('type', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('user', function($q) use ($searchTerm) {
                        $q->where('username', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }

        // Handle full date search (MM/DD/YYYY)
        if ($request->filled('fullDate')) {
            $datePattern = $request->input('fullDate');
            list($month, $day, $year) = explode('/', $datePattern);
            
            $formattedDate = "{$year}-{$month}-{$day}";
            $documentsQuery->whereDate('submitted_documents.created_at', $formattedDate);
        }

        // Handle month/day pattern search
        elseif ($request->filled('monthDayPattern')) {
            $pattern = $request->input('monthDayPattern');
            list($month, $day) = explode('/', $pattern);
            
            // Search for the specific month and day across any year
            $documentsQuery->whereMonth('submitted_documents.created_at', $month)
                        ->whereDay('submitted_documents.created_at', $day);
        }
        
        // Apply organization filter if provided
        if ($selectedOrg && $selectedOrg !== 'All') {
            $documentsQuery->whereHas('user', function($query) use ($selectedOrg) {
            $query->where('organization_acronym', $selectedOrg);
            });
        }

        // Get all unique organizations from the users table
        $organizations = User::where('role_name', '!=', 'admin')
                            ->where(function($query) {
                            $query->where('role_name', 'Academic Organization')
                                ->orWhere('role_name', 'Non-Academic Organization');
                            })
                            ->whereNotNull('organization_acronym')
                            ->where('active', true)
                            ->orderBy('organization_acronym')
                            ->distinct()
                            ->pluck('organization_acronym')
                            ->filter()  // Remove any null values
                            ->unique()  // Remove duplicates
                            ->values(); // Reset array keys
        
        // Apply document type filter if provided
        if ($selectedType && $selectedType !== 'All') {
            // Check if we're filtering for "Others"
            if ($selectedType === 'Others') {
                $excludeTypes = $request->input('excludeTypes');
                if ($excludeTypes) {
                    $typesToExclude = explode(',', $excludeTypes);
                    $documentsQuery->where(function($query) use ($typesToExclude) {
                        foreach ($typesToExclude as $excludeType) {
                            $query->where('type', 'NOT LIKE', "%{$excludeType}%");
                        }
                    });
                }
            } else {
                // Original code for specific document type filtering
                $docTypeMap = [
                    'Event Proposal' => 'Event Proposal',
                    'General Plan' => 'General Plan of Activities',
                    'Reports' => 'Reports of Proceedings',
                    'Constitution' => 'Constitution and By-Laws',
                    'Fundraising' => 'Fundraising Activities',
                    'Request Letter' => 'Request Letter',
                    'Petition' => 'Petition and Concern',
                    'Memorandum' => 'Memorandum of Agreement',
                    'Off-Campus' => 'Off Campus Activities'
                ];
                
                // Get full document type name
                $fullTypeName = $docTypeMap[$selectedType] ?? $selectedType;
                
                $documentsQuery->where('type', 'LIKE', "%{$fullTypeName}%");
            }
        }
        
        // Order by updated date (latest first)
        $documentsQuery->orderBy('submitted_documents.updated_at', 'desc');
            
        // Paginate the results - this returns a LengthAwarePaginator
        $documents = $documentsQuery->paginate(7)->withQueryString();
        
        // Transform each document in the paginated collection
        $documents->getCollection()->transform(function($document) use ($user) {
            $document->tag = $document->control_tag;
            
            // Properly get the username from the users table via the relationship
            $document->organization = $document->user ? $document->user->username : 'Unknown';
            
            $document->title = $document->subject;
            $document->date = \Carbon\Carbon::parse($document->created_at);
            
            // Add flag to indicate if document has already been reviewed with a decision
            $document->has_decision = $document->reviews()->whereIn('status', ['approved', 'rejected', 'resubmission'])->exists();

            // Add flag to indicate if the current user is the receiver or just a previous forwarder
            $document->is_current_receiver = $document->received_by == $user->id;
            
            return $document;
        });

        // Define tag colors for different organizations
        $tagColors = [
            'OSC' => 'text-blue-500',
            'ECE' => 'text-red-500',
            'PSY' => 'text-purple-500',
            'IT' => 'text-orange-500',
            'HR' => 'text-pink-400',
            'ACC' => 'text-pink-400',
            'EDU' => 'text-blue-500',
            'MAR' => 'text-yellow-500',
            'IE' => 'text-green-500',
            'TAP' => 'text-green-500',
            'SIGMA' => 'text-yellow-900',
            'AGDS' => 'text-yellow-900',
            'CHO' => 'text-blue-500',
        ];

        // Check if this is an AJAX request
        if ($request->ajax()) {
            return view('admin.documentReview', compact('documents', 'tagColors', 'searchTerm', 'selectedOrg', 'selectedType', 'organizations'));
        }

        return view('admin.documentReview', compact('documents', 'tagColors', 'searchTerm', 'selectedOrg', 'selectedType', 'organizations'));
    }

    public function getDetails($id)
    {
        $user = Auth::user();
        
        // Find document that was either received by or forwarded by the current admin
        $document = SubmittedDocument::with([
            'user:id,username,profile_pic,role_name,organization_acronym',
            'reviews.reviewer',
            'documentVersions' => function($query) {
                $query->orderBy('version', 'desc');
            }
        ])
        ->where(function($query) use ($user) {
            $query->where('received_by', $user->id);
        })
        ->findOrFail($id);
        
        // Determine if user is current receiver (for UI permission control)
        $isCurrentReceiver = $document->received_by == $user->id;
        
        // Get timeline data from document_timeline table
        $timeline = DocumentTimeline::with(['user', 'forwardedToUser'])
            ->where('document_id', $id)
            ->orderBy('created_at')
            ->get()
            ->map(function($entry) {
                return [
                    'id' => $entry->id,
                    'action_type' => $entry->action_type,
                    'status' => $entry->status,
                    'message' => $entry->message,
                    'user_id' => $entry->user_id,
                    'user_name' => $entry->user->username ?? 'Unknown',
                    'forwarded_to' => $entry->forwarded_to,
                    'forwarded_to_name' => $entry->forwardedToUser->username ?? null,
                    'created_at' => $entry->created_at,
                    'updated_at' => $entry->updated_at,
                    'user_role' => $entry->user->role_name ?? 'Unknown'
                ];
            });
            
        // Transform document for the view
        $documentData = [
            'id' => $document->id,
            'guest_webmail' => $document->guest_webmail,
            'subject' => $document->subject,
            'summary' => $document->overview,
            'academic_year' => $document->academic_year,
            'venue' => $document->venue,
            'proposed_date_time' => $document->proposed_date_time,
            'hours' => $document->hours,
            'attendees' => $document->attendees,
            'attendees_range' => $document->attendees_range,
            'fees' => $document->fees,
            'type' => $document->type,
            'control_tag' => $document->control_tag,
            'status' => $document->status,
            'created_at' => $document->created_at,
            'organization' => $document->user ? $document->user->username : 'Unknown',
            'user' => $document->user ? [
                'id' => $document->user->id,
                'username' => $document->user->username,
                'profile_pic' => $document->user->profile_pic,
                'role_name' => $document->user->role_name,
                'organization_acronym' => $document->user->organization_acronym 
            ] : null,
            'has_decision' => $document->reviews()->whereIn('status', ['approved', 'rejected', 'resubmission'])->exists(),
            'is_current_receiver' => $isCurrentReceiver,
            'reviews' => $document->reviews->map(function($review) {
                // Return formatted review data
                return [
                    'reviewer_name' => $review->reviewer ? $review->reviewer->username : 'Unknown',
                    'status' => $review->status,
                    'message' => $review->message,
                    'created_at' => $review->created_at,
                    'updated_at' => $review->updated_at
                ];
            }),
            'timeline' => $timeline, // Add timeline data to the response
            'attachments' => []
        ];

        // Get document versions and add them as attachments
        if ($document->documentVersions && $document->documentVersions->count() > 0) {
            foreach ($document->documentVersions as $version) {
                $documentData['attachments'][] = [
                    'id' => $version->id,
                    'version' => $version->version,
                    'uploaded_by' => $version->uploaded_by,
                    'document_url' => $version->document_url,
                    'comments' => $version->comments,
                    'submitted_at' => $version->submitted_at,
                    'is_latest' => $version->id === $document->documentVersions->first()->id
                ];
            }
            
            // Set the latest version as the primary file_path
            $latestVersion = $document->documentVersions->first();
            $documentData['document_url'] = $latestVersion->document_url;
            $documentData['version'] = $latestVersion->version;
            $documentData['submitted_at'] = $latestVersion->submitted_at;
        } else {
            // If no versions exist, use the file_path from the document itself (for backward compatibility)
            $documentData['document_url'] = $document->document_url ?? null;
        }
        
        return response()->json($documentData);
    }

    public function markAsOpened($id)
    {
        try {
            $user = Auth::user();
            $document = SubmittedDocument::findOrFail($id);
            
            // Check if the document is either directly assigned to this admin
            // OR if this admin has forwarded the document to someone else
            $canAccess = ($document->received_by == $user->id) || 
                        DocumentForward::where('document_id', $id)
                                    ->where('forwarded_by', $user->id)
                                    ->exists();
            
            if (!$canAccess) {
                return response()->json([
                    'success' => false, 
                    'error' => 'This document is not assigned to you'
                ], 403);
            }
            
            // Only create a review record if this user is the current receiver
            if ($document->received_by == $user->id) {
                // Check if a review already exists for this document by the current user
                $existingReview = Review::where('document_id', $id)
                    ->where('reviewed_by', $user->id)
                    ->first();
                    
                // If no review exists, create one with "Under Review" status
                if (!$existingReview) {
                    $review = Review::create([
                        'document_id' => $id,
                        'reviewed_by' => $user->id,
                        'status' => 'Under Review',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    // Update document status to Under Review
                    $document->status = 'Under Review';
                    $document->save();
                    
                    // Add this to the timeline
                    DocumentTimeline::create([
                        'document_id' => $id,
                        'user_id' => $user->id,
                        'action_type' => 'review',
                        'status' => 'under_review',
                        'message' => 'Document opened for review',
                        'related_review_id' => $review->id
                    ]);
                    
                    // Dispatch event for notification
                    event(new DocumentStatusUpdated($document));
                }
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
            $user = Auth::user();
            $document = SubmittedDocument::where('received_by', Auth::id())
                ->findOrFail($id);
            
            // Update document status
            $document->status = 'Approved';
            $document->save();
            
            // Find the existing review and update it
            $existingReview = Review::where('document_id', $id)
                ->where('reviewed_by', Auth::id())
                ->first();
                
            if ($existingReview) {
                // Update the existing review
                $existingReview->status = 'Approved';
                $existingReview->message = $request->input('message', 'Document approved');
                $existingReview->updated_at = now();
                $existingReview->save();
                $reviewId = $existingReview->id;
            } else {
                // Create a new review
                $review = Review::create([
                    'document_id' => $id,
                    'reviewed_by' => Auth::id(),
                    'status' => 'Approved',
                    'message' => $request->input('message', 'Document approved'),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $reviewId = $review->id;
            }

            // Log the activity
            $this->logActivity(
                'Approved',
                'Submission #' . $document->id,
                "{$user->username} approved submission titled '{$document->subject}'."
            );
            
            // Add to timeline - ONLY create the timeline entry here, not in the event
            DocumentTimeline::create([
                'document_id' => $id,
                'user_id' => Auth::id(),
                'action_type' => 'approve',
                'status' => 'approved',
                'message' => $request->input('message', 'Document approved'),
                'related_review_id' => $reviewId
            ]);
            
            // Dispatch event for notification - AFTER creating the timeline entry
            // This event should NOT create another timeline entry
            event(new DocumentStatusUpdated($document));

            // Check if this is a guest submission (user_id is null)
            if ($document->user_id) {
                // Regular user notification through the application
                // $document->user->notify(new \App\Notifications\DocumentApproved($document, $request->message));
            } else if ($document->guest_webmail) {
                // Guest user notification via email
                \Mail::to($document->guest_webmail)->send(new \App\Mail\DocumentApprovedMail($document, $request->message));
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
     * Request resubmission of a document
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestResubmission(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $document = SubmittedDocument::where('received_by', Auth::id())
                ->findOrFail($id);
            
            // Update document status
            $document->status = 'Returned';
            $document->save();

            // Create or update review entry
            $existingReview = Review::where('document_id', $id)
                ->where('reviewed_by', Auth::id())
                ->first();
                
            $reviewId = null;    
            if ($existingReview) {
                // Update the existing review
                $existingReview->status = 'Returned';
                $existingReview->message = $request->input('message', 'Document returned for revision. Please review the feedback and resubmit.');
                $existingReview->updated_at = now();
                $existingReview->save();
                $reviewId = $existingReview->id;
            } else {
                // Create a new review
                $review = Review::create([
                    'document_id' => $id,
                    'reviewed_by' => Auth::id(),
                    'status' => 'Returned',
                    'message' => $request->input('message', 'Document returned for revision. Please review the feedback and resubmit.'),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $reviewId = $review->id;
            }

            // Log the activity
            $this->logActivity(
                'Returned',
                'Submission #' . $document->id,
                "{$user->username} returned document titled '{$document->subject}'."
            );
            
            // Add to timeline - ONLY create the timeline entry here
            DocumentTimeline::create([
                'document_id' => $id,
                'user_id' => Auth::id(),
                'action_type' => 'return',
                'status' => 'returned',
                'message' => $request->input('message', 'Document returned for revision. Please review the feedback and resubmit.'),
                'related_review_id' => $reviewId
            ]);
            
            // Dispatch event for notification
            event(new \App\Events\DocumentStatusUpdated($document));
            
            // Send notification to student if user exists
            if ($document->user_id) {
                $student = User::find($document->user_id);
                if ($student) {
                    try {
                        $student->notify(new \App\Notifications\DocumentResubmissionRequested([
                            'document_id' => $document->id,
                            'document_title' => $document->subject,
                            'message' => $request->input('message', 'Document returned for revision. Please review the feedback and resubmit.')
                        ]));
                    } catch (\Exception $e) {
                        // Log the error but don't stop the process
                        \Log::error('Failed to send notification: ' . $e->getMessage());
                    }
                }
            } else if ($document->guest_webmail) {
                // Guest user notification via email
                \Mail::to($document->guest_webmail)->send(new \App\Mail\DocumentResubmissionMail($document, $request->message));
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Document returned for revision successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Document resubmission error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check for document updates and return any new or modified documents
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkForUpdates(Request $request)
    {
        try {
            // Get the currently logged in user
            $user = Auth::user();
            
            // Get the timestamp of the last update from the request
            $lastUpdate = $request->input('lastUpdate');
            $lastUpdateTime = $lastUpdate ? Carbon::parse($lastUpdate) : null;
            
            // Base query - Get documents updated since last check
            $documentsQuery = SubmittedDocument::with(['user'])
                ->select('submitted_documents.*')
                ->where('received_by', $user->id)
                ->whereNotIn('submitted_documents.status', ['Approved', 'Returned']);
                
            // Add is_opened field through join (potential source of error)
            try {
                $documentsQuery->addSelect(DB::raw("
                    CASE 
                        WHEN reviews.id IS NULL THEN false
                        ELSE true
                    END as is_opened
                "))
                ->addSelect(DB::raw("
                    CASE 
                        WHEN submitted_documents.received_by = " . $user->id . " THEN true
                        ELSE false
                    END as is_current_receiver
                "))
                ->leftJoin('reviews', function($join) {
                    $join->on('submitted_documents.id', '=', 'reviews.document_id')
                        ->where('reviews.reviewed_by', '=', Auth::id());
                });
            } catch (\Exception $e) {
                // Fallback if the complex query fails
                $documentsQuery = SubmittedDocument::with(['user'])
                    ->where('received_by', $user->id)
                    ->whereNotIn('status', ['Approved', 'Returned'])
                    ->orderBy('updated_at', 'desc');
            }
            
            // Only get documents updated after the last check
            if ($lastUpdateTime) {
                $documentsQuery->where('submitted_documents.updated_at', '>', $lastUpdateTime);
            }
            
            // Order by updated date (latest first)
            $documentsQuery->orderBy('submitted_documents.updated_at', 'desc');
            
            // Get the documents
            $documents = $documentsQuery->get();
            
            // Transform the documents for the response
            $updatedDocuments = $documents->map(function($document) {
                try {
                    $document->tag = $document->control_tag;
                    $document->organization = $document->user ? $document->user->username : 'Unknown';
                    $document->title = $document->subject;
                    $document->date = Carbon::parse($document->created_at);
                    $document->formatted_date = $document->date->format('n/j/Y');
                    $document->is_opened = $document->is_opened ?? false;
                    
                    return $document;
                } catch (\Exception $e) {
                    Log::error("Error transforming document {$document->id}: " . $e->getMessage());
                    return null;
                }
            })->filter();
            
            // Return the documents and current server time for next update check
            return response()->json([
                'documents' => $updatedDocuments,
                'currentTime' => now()->toIso8601String(),
                'hasUpdates' => $documents->count() > 0
            ]);
        } catch (\Exception $e) {
            Log::error("Document update check failed: " . $e->getMessage());
            
            // Return a friendly error response
            return response()->json([
                'error' => 'Could not check for updates',
                'details' => $e->getMessage(),
                'currentTime' => now()->toIso8601String(),
                'hasUpdates' => false,
                'documents' => []
            ], 200); // Return 200 to prevent frontend errors
        }
    }
}