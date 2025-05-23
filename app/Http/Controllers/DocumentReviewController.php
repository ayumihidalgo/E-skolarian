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

class DocumentReviewController extends Controller
{
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
                // Get documents assigned to this admin or forwarded by this admin
                $query->where('received_by', $user->id)
                    ->orWhereHas('documentForwards', function($q) use ($user) {
                        $q->where('forwarded_by', $user->id);
                    });
            })
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
            // Create a mapping between acronyms and full organization names
            $orgMap = [
                'ACAP' => 'Association of Competent and Aspiring Psychologists',
                'AECES' => 'Association of Electronics and Communications Engineering Students',
                'ELITE' => 'Eligible League of Information Technology Enthusiasts',
                'GIVE' => 'Guild of Imporous and Valuable Educators',
                'JEHRA' => 'Junior Executive of Human Resource Association',
                'JMAP' => 'Junior Marketing Association of the Philippines',
                'JPIA' => 'Junior Philippine Institute of Accountants',
                'PIIE' => 'Philippine Institute of Industrial Engineers',
                'AGDS' => 'Artist Guild Dance Squad',
                'Chorale' => 'PUP SRC Chorale',
                'SIGMA' => "Supreme Innovators' Guild for Mathematics Advancements",
                'TAPNOTCH' => 'Transformation Advocates through Purpose-driven and Noble Objectives Toward Community Holism',
                'OSC' => 'Office of the Student Council'
            ];
            
            // Get full name of organization if acronym is selected
            $fullOrgName = $orgMap[$selectedOrg] ?? $selectedOrg;
            
            $documentsQuery->whereHas('user', function($query) use ($selectedOrg, $fullOrgName) {
                $query->where('username', 'LIKE', "%{$selectedOrg}%")
                      ->orWhere('username', 'LIKE', "%{$fullOrgName}%");
            });
        }
        
        // Apply document type filter if provided
        if ($selectedType && $selectedType !== 'All') {
            // Create a mapping between short types and full document types
            $docTypeMap = [
                'Event Proposal' => 'Event Proposal',
                'General Plan' => 'General Plan of Activities',
                'Calendar' => 'Calendar of Activities',
                'Accomplishment Report' => 'Accomplishment Report',
                'Constitution' => 'Constitution and By-Laws',
                'Request Letter' => 'Request Letter',
                'Off-Campus' => 'Off Campus',
                'Petition' => 'Petition and Concern'
            ];
            
            // Get full document type name
            $fullTypeName = $docTypeMap[$selectedType] ?? $selectedType;
            
            $documentsQuery->where('type', 'LIKE', "%{$fullTypeName}%");
        }
        
        // Order by creation date (newest first)
        $documentsQuery->orderBy('submitted_documents.created_at', 'desc');
            
        // Paginate the results - this returns a LengthAwarePaginator
        $documents = $documentsQuery->paginate(6)->withQueryString();
        
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
            
            // Add forwarded status information
            if (!$document->is_current_receiver) {
                // Find the forward record
                $forwardRecord = DB::table('document_forwards')
                    ->where('document_id', $document->id)
                    ->where('forwarded_by', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
                    
                if ($forwardRecord) {
                    $receiverUser = User::find($forwardRecord->forwarded_to);
                    $document->forwarded_to = $receiverUser ? $receiverUser->username : 'Unknown Admin';
                    $document->forwarded_at = \Carbon\Carbon::parse($forwardRecord->created_at);
                }
            }
            
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
            'DOC' => 'text-blue-800',
        ];

        return view('admin.documentReview', compact('documents', 'tagColors', 'searchTerm', 'selectedOrg', 'selectedType'));
    }

    // /**
    //  * Get the details of a specific document
    //  *
    //  * @param int $id
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function getDetails($id)
    // {
    //     $document = SubmittedDocument::with(['user', 'reviews.reviewer'])
    //         ->where('received_by', Auth::id())  // Only allow access to documents intended for this user
    //         ->findOrFail($id);
            
    //     // Transform document for the view
    //     $documentData = [
    //         'id' => $document->id,
    //         'subject' => $document->subject,
    //         'summary' => $document->summary,
    //         'type' => $document->type,
    //         'control_tag' => $document->control_tag,
    //         'status' => $document->status,
    //         'file_path' => $document->file_path,
    //         'created_at' => $document->created_at,
    //         'organization' => $document->user ? $document->user->username : 'Unknown',
    //         'has_decision' => $document->reviews()->whereIn('status', ['approved', 'rejected', 'resubmission'])->exists(),
    //         'reviews' => $document->reviews->map(function($review) {
    //             return [
    //                 'reviewer_name' => $review->reviewer ? $review->reviewer->username : 'Unknown',
    //                 'status' => $review->status,
    //                 'message' => $review->message,
    //                 'created_at' => $review->created_at,
    //                 'updated_at' => $review->updated_at
    //             ];
    //         })
    //     ];
        
    //     return response()->json($documentData);
    // }

    /**
     * Get the details of a specific document
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetails($id)
    {
        $user = Auth::user();
        
        // Find document that was either received by or forwarded by the current admin
        $document = SubmittedDocument::with([
            'user',
            'reviews.reviewer',
            'documentVersions' => function($query) {
                $query->orderBy('version', 'desc');
            },
            'documentForwards' => function($query) use ($user) {
                $query->where('forwarded_by', $user->id)
                    ->orWhere('forwarded_to', $user->id);
            }
        ])
        ->where(function($query) use ($user) {
            $query->where('received_by', $user->id)
                ->orWhereHas('documentForwards', function($q) use ($user) {
                    $q->where('forwarded_by', $user->id);
                });
        })
        ->findOrFail($id);
            
        // Determine if user is current receiver (for UI permission control)
        $isCurrentReceiver = $document->received_by == $user->id;
        $forwardInfo = null;

        // If not current receiver, get forwarding details
        if (!$isCurrentReceiver) {
            $forwardRecord = $document->documentForwards()
                ->where('forwarded_by', $user->id)
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($forwardRecord) {
                $receiverUser = User::find($forwardRecord->forwarded_to);
                $forwardInfo = [
                    'forwarded_to' => $receiverUser ? $receiverUser->username : 'Unknown Admin',
                    'forwarded_at' => $forwardRecord->created_at,
                    'message' => $forwardRecord->message
                ];
            }
        }
            
        // Transform document for the view
        $documentData = [
            'id' => $document->id,
            'subject' => $document->subject,
            'summary' => $document->summary,
            'type' => $document->type,
            'control_tag' => $document->control_tag,
            'status' => $document->status,
            'created_at' => $document->created_at,
            'organization' => $document->user ? $document->user->username : 'Unknown',
            'has_decision' => $document->reviews()->whereIn('status', ['approved', 'rejected', 'resubmission'])->exists(),
            'is_current_receiver' => $isCurrentReceiver,
            'forward_info' => $forwardInfo,
            'reviews' => $document->reviews->map(function($review) {
                return [
                    'reviewer_name' => $review->reviewer ? $review->reviewer->username : 'Unknown',
                    'status' => $review->status,
                    'message' => $review->message,
                    'created_at' => $review->created_at,
                    'updated_at' => $review->updated_at
                ];
            }),
            'attachments' => []
        ];

        // Get document versions and add them as attachments
        if ($document->documentVersions && $document->documentVersions->count() > 0) {
            foreach ($document->documentVersions as $version) {
                $documentData['attachments'][] = [
                    'id' => $version->id,
                    'version' => $version->version,
                    'file_path' => $version->file_path,
                    'comments' => $version->comments,
                    'submitted_at' => $version->submitted_at,
                    'is_latest' => $version->id === $document->documentVersions->first()->id
                ];
            }
            
            // Set the latest version as the primary file_path
            $latestVersion = $document->documentVersions->first();
            $documentData['file_path'] = $latestVersion->file_path;
            $documentData['version'] = $latestVersion->version;
            $documentData['submitted_at'] = $latestVersion->submitted_at;
        } else {
            // If no versions exist, use the file_path from the document itself (for backward compatibility)
            $documentData['file_path'] = $document->file_path ?? null;
        }
        
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
                    Review::create([
                        'document_id' => $id,
                        'reviewed_by' => $user->id,
                        'status' => 'Under Review',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
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
            $document = SubmittedDocument::where('received_by', Auth::id())
                ->findOrFail($id);
            
            // Update document status
            $document->status = 'Approved';
            $document->save();
            
            // Dispatch event for notification
            // Make sure the event is constructed with the correct parameters as defined in your event class
            event(new DocumentStatusUpdated($document));
           
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
            ->select('id', 'username', 'email')
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

            event(new \App\Events\DocumentStatusUpdated($document));
            
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
            $document->status = 'Resubmit';
            $document->save();


            event(new \App\Events\DocumentStatusUpdated($document));
            
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
                        'status' => 'Resubmit',
                        'message' => $request->input('message', 'Please resubmit with changes'),
                        'updated_at' => now()
                    ]);
            } else {
                // Create a new review
                DB::table('reviews')->insert([
                    'document_id' => $id,
                    'reviewed_by' => Auth::id(),
                    'status' => 'Resubmit',
                    'message' => $request->input('message', 'Please resubmit with changes'),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            // Send notification to student if user exists
            if ($document->user_id) {
                $student = User::find($document->user_id);
                if ($student) {
                    try {
                        $student->notify(new \App\Notifications\DocumentResubmissionRequested([
                            'document_id' => $document->id,
                            'document_title' => $document->subject,
                            'message' => $request->input('message', 'Please resubmit with changes')
                        ]));
                    } catch (\Exception $e) {
                        // Log the error but don't stop the process
                        \Log::error('Failed to send notification: ' . $e->getMessage());
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Resubmission requested successfully'
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
     * Forward a document to another admin
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forwardDocument(Request $request, $id)
    {
        try {
            // Validate request data
            $request->validate([
                'forward_to' => 'required|exists:users,id',
                'message' => 'required|string|max:500',
            ]);

            $document = SubmittedDocument::where('received_by', Auth::id())
                ->findOrFail($id);
            
            // Get the target admin name for notification purposes
            $targetAdmin = User::find($request->input('forward_to'));
            if (!$targetAdmin) {
                return response()->json([
                    'success' => false,
                    'error' => 'Target administrator not found'
                ], 404);
            }

            // Create a review record
            $review = new Review([
                'document_id' => $id,
                'reviewed_by' => Auth::id(),
                'status' => 'Forwarded',
                'message' => 'Document forwarded to ' . $targetAdmin->username . ': ' . $request->input('message'),
            ]);
            $review->save();

            // Record the forwarding action
            $forward = new DocumentForward([
                'document_id' => $id,
                'forwarded_by' => Auth::id(),
                'forwarded_to' => $request->input('forward_to'),
                'message' => $request->input('message'),
            ]);
            $forward->save();

            // Update the document's received_by field
            $document->received_by = $request->input('forward_to');
            $document->save();

            // Trigger notification for the target admin
            event(new DocumentStatusUpdated($document));
            
            return response()->json([
                'success' => true,
                'message' => 'Document successfully forwarded to ' . $targetAdmin->username
            ]);
        } catch (\Exception $e) {
            \Log::error('Document forwarding error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}