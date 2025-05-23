<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubmittedDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            $query->where(function($q) use ($searchTerm) {
                // Updated to search in user's DOC_organization_acronym instead of control_tag
                $q->whereHas('user', function($userQuery) use ($searchTerm) {
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
}
