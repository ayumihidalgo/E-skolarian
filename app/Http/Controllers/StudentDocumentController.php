<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StudentDocumentController extends Controller
{
    public function preview($id)
    {
        // Fetch the document from the database with LEFT JOIN to get username
        $document = DB::table('submitted_documents')
            ->leftJoin('users', 'submitted_documents.user_id', '=', 'users.id')
            ->select('submitted_documents.*', 'users.username')
            ->where('submitted_documents.id', $id)
            ->where('submitted_documents.user_id', Auth::id()) // Ensure student can only view their own document
            ->first();

        if (!$document) {
            abort(404, 'Document not found');
        }

        // Get all document versions and their attachments, ordered by version (newest first)
        $documentVersions = DB::table('document_versions')
            ->where('document_id', $id)
            ->orderBy('version', 'desc')
            ->get();
        
        // Initialize attachments array
        $attachments = [];
        $document_url = null;
        
        // Process all versions into attachments array
        if ($documentVersions && count($documentVersions) > 0) {
            foreach ($documentVersions as $version) {
                $attachments[] = [
                    'id' => $version->id,
                    'version' => $version->version,
                    'document_url' => $version->document_url,
                    'comments' => $version->comments ?? null,
                    'submitted_at' => $version->submitted_at,
                    'is_latest' => ($version === $documentVersions->first())
                ];
            }
            
            // Get latest version's file path for display
            $latestVersion = $documentVersions->first();
            $document_url = $latestVersion->document_url;
        }

        // Check if document is archived
        $isArchived = DB::table('submitted_documents')
            ->where('id', $id)
            ->whereNotNull('archived_at')
            ->exists();

        return view('student.documentPreview', [
            'document' => [
                'id' => $document->id,
                'tag' => $document->control_tag,
                'title' => $document->subject,
                'content' => $document->overview,
                'date' => $document->created_at,
                'type' => $document->type,
                'status' => $document->status,
                'organization' => $document->username ?? 'Unknown User',
                'document_url' => $document_url,
                'attachments' => $attachments,
                'remarks' => $document->remarks ?? null,
                'is_archived' => $isArchived
            ]
        ]);
    }

    public function documentArchive(Request $request)
    {
        $userId = Auth::id();
        
        // Define the standard document types - matching AdminDocumentController
        $standardTypes = [
            'Event Proposal',
            'General Plan of Activities',
            'Reports of Proceedings',
            'Constitution and By-Laws',
            'Fundraising Activities',
            'Request Letter',
            'Petition and Concern',
            'Memorandum of Agreement',
            'Off Campus Activities'
        ];

        // Build the base query
        $query = DB::table('submitted_documents')
            ->where('user_id', $userId)
            ->whereNull('archived_at') // Exclude archived documents
            ->whereIn('status', ['Approved']); // show Approved 
    
        // Apply type filter - handle "Others" category like admin does
        if ($request->has('type') && $request->type != 'All' && $request->type != 'Type') {
            if ($request->type === 'Others') {
                // Filter for documents with types NOT in the standard list
                $query->whereNotIn('type', $standardTypes);
            } else {
                // Filter for specific standard type
                $query->where('type', $request->type);
            }
        }
        
        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('subject', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('control_tag', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('type', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // Apply sorting similar to admin version
        if ($request->has('sort_by')) {
            $column = $request->sort_by;
            $direction = $request->has('sort_dir') ? $request->sort_dir : 'asc';
            $query->orderBy($column, $direction);
        } else {
            // Default sort
            $query->orderBy('created_at', 'desc');
        }
        
        // Execute query with pagination and keep filter parameters in pagination links
        $documents = $query->paginate(6)
                          ->appends($request->except('page'));
        
        // Pass standard types to the view
        $standardTypes = $standardTypes;
        
        // Handle AJAX requests
        if ($request->ajax()) {
            return view('student.documentHistory', compact('documents', 'standardTypes'))->render();
        }
            
        return view('student.documentHistory', compact('documents', 'standardTypes'));
    }

    public function archivePage(Request $request)
    {
        $userId = Auth::id();
        
        // Define the standard document types - matching AdminDocumentController and documentHistory
        $standardTypes = [
            'Event Proposal',
            'General Plan of Activities',
            'Reports of Proceedings',
            'Constitution and By-Laws',
            'Fundraising Activities',
            'Request Letter',
            'Petition and Concern',
            'Memorandum of Agreement',
            'Off Campus Activities'
        ];

        // Build query for archived documents that belong to this student
        $query = DB::table('submitted_documents')
            ->where('user_id', $userId)
            ->whereNotNull('archived_at'); // Only archived documents

        // Apply type filter - handle "Others" category like admin and documentHistory does
        if ($request->has('type') && $request->type !== 'All' && $request->type !== 'Type') {
            if ($request->type === 'Others') {
                // Filter for documents with types NOT in the standard list
                $query->whereNotIn('type', $standardTypes);
            } else {
                // Filter for specific standard type
                $query->where('type', $request->type);
            }
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")        // Title search
                  ->orWhere('control_tag', 'like', "%{$search}%")   // Tag search
                  ->orWhere('type', 'like', "%{$search}%");         // Type search
            });
        }

        // Apply sorting
        if ($request->has('sort_by')) {
            $column = $request->sort_by;
            $direction = $request->has('sort_dir') ? $request->sort_dir : 'asc';
            $query->orderBy($column, $direction);
        } else {
            // Default sort by archived date
            $query->orderBy('archived_at', 'desc');
        }

        // Get paginated results
        $documents = $query->paginate(6)->appends($request->except('page'));

        // Organization mapping for student (assuming ELITE)
        $orgMap = [
            'ELITE' => 'Eligible League of Information Technology Enthusiasts',
        ];

        $tagColors = [
            'IT' => 'text-orange-500',
        ];

        // Handle AJAX requests
        if ($request->ajax()) {
            return view('student.archivePage', compact('documents', 'orgMap', 'tagColors', 'standardTypes'))->render();
        }

        return view('student.archivePage', compact('documents', 'orgMap', 'tagColors', 'standardTypes'));
    }

    public function documentHistoryPreview($id)
    {
        $document = DB::table('submitted_documents')
            ->where('id', $id)
            ->where('status', 'Approved')
            ->whereNull('archived_at')
            ->first();

        if (!$document) {
            abort(404, 'Document not found');
        }

        // Get all document versions and their attachments, ordered by version (newest first)
        $documentVersions = DB::table('document_versions')
            ->where('document_id', $id)
            ->orderBy('version', 'desc')
            ->get();
        
        // Initialize attachments array
        $attachments = [];
        $document_url = null;
        
        // Process all versions into attachments array
        if ($documentVersions && count($documentVersions) > 0) {
            foreach ($documentVersions as $version) {
                $attachments[] = [
                    'id' => $version->id,
                    'version' => $version->version,
                    'document_url' => $version->document_url,
                    'comments' => $version->comments ?? null,
                    'submitted_at' => $version->submitted_at,
                    'is_latest' => ($version === $documentVersions->first())
                ];
            }
            
            // Get latest version's file path for display
            $latestVersion = $documentVersions->first();
            $document_url = $latestVersion->document_url;
        }

        // Organization mapping
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
            'SIGMA' => 'Supreme Innovators Guild for Mathematics Advancements',
            'TAPNOTCH' => 'Transformation Advocates through Purpose-driven and Noble Objectives Toward Community Holism',
            'OSC' => 'Office of the Student Council',
        ];

        // Extract organization acronym from control tag
        $parts = explode('_', $document->control_tag);
        $acronym = count($parts) > 0 ? $parts[0] : '';
        $organizationName = isset($orgMap[$acronym]) ? $orgMap[$acronym] : $acronym;

        // Check if document is archived
        $isArchived = DB::table('submitted_documents')
            ->where('id', $id)
            ->whereNotNull('archived_at')
            ->exists();

        return view('student.documentPreview', [
            'document' => [
                'id' => $document->id,
                'tag' => $document->control_tag,
                'title' => $document->subject,
                'content' => $document->overview,
                'date' => $document->created_at,
                'type' => $document->type,
                'status' => $document->status,
                'organization' => $organizationName,
                'document_url' => $document_url,
                'attachments' => $attachments, // This is the key missing piece
                'remarks' => $document->remarks ?? null,
                'is_archived' => $isArchived
            ]
        ]);
    }
}
