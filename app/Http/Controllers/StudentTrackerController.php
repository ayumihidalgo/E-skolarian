<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubmittedDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\DocumentVersion;
use Illuminate\Support\Facades\Storage;
use App\Models\Review;
use App\Models\DocumentTimeline;

class StudentTrackerController extends Controller
{
    public function viewStudentTracker(Request $request)
    {
        // Start with base query for current user's documents
        // Make sure to eager load the user relationship to access DOC_organization_acronym
        $query = SubmittedDocument::with(['user', 'receiver', 'reviews', 'documentVersions'])
            ->where('user_id', Auth::id());

        // Debug: Log the current user ID and initial count
        Log::info('User ID: ' . Auth::id());
        Log::info('Initial record count: ' . $query->count());
        Log::info('Request parameters: ', $request->all());

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                // Updated to search in user's DOC_organization_acronym instead of control_tag
                $q->whereHas('user', function ($userQuery) use ($searchTerm) {
                    $userQuery->where('organization_acronym', 'LIKE', "%{$searchTerm}%");
                })
                    ->orWhere('subject', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('type', 'LIKE', "%{$searchTerm}%");
            });
            Log::info('After search filter count: ' . $query->count());
        }

        // Apply document type filter
        if ($request->filled('document_type') && $request->document_type !== '') {
            $query->where('type', $request->document_type);
            Log::info('After document type filter count: ' . $query->count());
        }

        // Apply status filter
        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
            Log::info('After status filter count: ' . $query->count());
        }

        // Get all unique document types and statuses for debugging
        $allTypes = SubmittedDocument::where('user_id', Auth::id())->distinct()->pluck('type')->toArray();
        $allStatuses = SubmittedDocument::where('user_id', Auth::id())->distinct()->pluck('status')->toArray();

        Log::info('Available document types: ', $allTypes);
        Log::info('Available statuses: ', $allStatuses);

        // Get paginated results
        $records = $query->orderBy('created_at', 'desc')->paginate(10);

        // Debug: Log final count
        Log::info('Final record count: ' . $records->count());

        // Preserve query parameters in pagination links
        $records->appends($request->query());

        // Pass debug data to view
        return view('student.studentTracker', compact('records'))
            ->with('debug', [
                'allTypes' => $allTypes,
                'allStatuses' => $allStatuses,
                'filters' => $request->all(),
                'totalRecords' => SubmittedDocument::where('user_id', Auth::id())->count()
            ]);
    }

    public function show($id)
    {
        // Also ensure the student can only view their own submitted documents
        $record = SubmittedDocument::with(['user', 'receiver', 'reviews', 'documentVersions'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('student.components.viewRecordSubmitted', compact('record'));
    }

    // public function saveReturnedAttachment(Request $request, $id)
    // {
    //     $request->validate([
    //         'returned_attachment' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
    //         'comments' => 'nullable|string',
    //     ]);

    //     // Find the original document
    //     $submittedDocument = \App\Models\SubmittedDocument::where('user_id', Auth::id())->findOrFail($id);

    //     // Determine next version number
    //     $latestVersion = DocumentVersion::where('document_id', $id)->max('version');
    //     $nextVersion = $latestVersion ? $latestVersion + 1 : 2;

    //     // Store the file
    //     $file = $request->file('returned_attachment');
    //     $originalName = $file->getClientOriginalName();
    //     $filePath = $file->storeAs('documents', $originalName, 'public');

    //     // Save as new DocumentVersion
    //     $docVersion = DocumentVersion::create([
    //         'document_id' => $id,
    //         'uploaded_by' => Auth::id(),
    //         'version' => $nextVersion,
    //         'document_url' => $filePath,
    //         'comments' => $request->comments,
    //         'submitted_at' => now(),
    //     ]);

    //     // Update the latest review entry to status 'Under Review'
    //     $review = Review::where('document_id', $id)
    //         ->latest('created_at')
    //         ->first();

    //     if ($review) {
    //         $review->status = 'Under Review';
    //         $review->updated_at = now();
    //         $review->save();
    //     }

    //     // Get timeline data from document_timeline table
    //     $timeline = DocumentTimeline::with(['user', 'forwardedToUser'])
    //         ->where('document_id', $id)
    //         ->orderBy('created_at')
    //         ->get()
    //         ->map(function($entry) {
    //             return [
    //                 'id' => $entry->id,
    //                 'action_type' => $entry->action_type,
    //                 'status' => $entry->status,
    //                 'message' => $entry->message,
    //                 'user_id' => $entry->user_id,
    //                 'user_name' => $entry->user->username ?? 'Unknown',
    //                 'forwarded_to' => $entry->forwarded_to,
    //                 'forwarded_to_name' => $entry->forwardedToUser->username ?? null,
    //                 'created_at' => $entry->created_at,
    //                 'updated_at' => $entry->updated_at,
    //                 'user_role' => $entry->user->role_name ?? 'Unknown'
    //             ];
    //         });

    //     // Update the submitted document's status to 'Under Review'
    //     $submittedDocument->status = 'Under Review';
    //     $submittedDocument->save();

    //     return redirect()->back()->with('success', 'Returned document uploaded as version ' . $nextVersion);
    // }

    public function saveReturnedAttachment(Request $request, $id)
    {
        $request->validate([
            'returned_attachment' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
            'comments' => 'nullable|string',
        ]);

        // Find the original document
        $submittedDocument = \App\Models\SubmittedDocument::where('user_id', Auth::id())->findOrFail($id);

        // Determine next version number
        $latestVersion = DocumentVersion::where('document_id', $id)->max('version');
        $nextVersion = $latestVersion ? $latestVersion + 1 : 2;

        // Store the file
        $file = $request->file('returned_attachment');
        $originalName = $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $originalName, 'public');

        // Save as new DocumentVersion
        $docVersion = DocumentVersion::create([
            'document_id' => $id,
            'uploaded_by' => Auth::id(),
            'version' => $nextVersion,
            'document_url' => $filePath,
            'comments' => $request->comments,
            'submitted_at' => now(),
        ]);

        // Update the latest review entry
        $review = Review::where('document_id', $id)
            ->latest('created_at')
            ->first();

        // Update the submitted document's status to 'Resubmitted'
        $submittedDocument->status = 'Resubmitted';
        $submittedDocument->save();

        // if ($review) {
        //     $review->status = 'Resubmitted';
        //     $review->updated_at = now();
        //     $review->save();
        // }

        // Add a timeline entry for resubmission
        DocumentTimeline::create([
            'document_id' => $id,
            'user_id' => Auth::id(),
            'action_type' => 'resubmission',
            'status' => 'resubmit',
            'message' => 'Student resubmitted the document.',
            'related_review_id' => $review ? $review->id : null,
        ]);

        // Delete the previous review entry for this document
        Review::where('document_id', $id)->delete();

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

        // Update the submitted document's status to 'Under Review'
        $submittedDocument->status = 'Under Review';
        $submittedDocument->save();

        return redirect()->back()->with('success', 'Returned document uploaded as version ' . $nextVersion);
    }
}
