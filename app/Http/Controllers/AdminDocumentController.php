<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Document;
use App\LogsActivity; 

class AdminDocumentController extends Controller
{
    use LogsActivity; 

    public function preview($id)
    {
        //$adminId = auth()->id();

        $document = DB::table('submitted_documents')
            ->leftJoin('users as submitter', 'submitted_documents.user_id', '=', 'submitter.id')
            ->leftJoin('users as admin', 'submitted_documents.received_by', '=', 'admin.id')
            ->select('submitted_documents.*', 'submitter.username', 'admin.role_name')
            ->where('submitted_documents.id', $id)
            //->where('submitted_documents.received_by', $adminId) // Only assigned to this admin
            ->first();

        if (!$document) {
            abort(404, 'Document not found');
        }

        // Get all document versions and their attachments, ordered by version (newest first)
        $documentVersions = DB::table('document_versions')
            ->where('document_id', $id)
            ->orderBy('version', 'desc')
            ->get();
        
        // Initialize attachments array and document_url
        $attachments = [];
        $document_url = null; // Initialize this variable
        
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
        } else {
            // Fallback to original document URL if no versions exist
            $document_url = $document->document_url ?? null;
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

        return view('admin.documentPreview', [
            'document' => [
                'id' => $document->id,
                'tag' => $document->control_tag,
                'title' => $document->subject,
                'content' => $document->overview,
                'date' => $document->created_at,
                'type' => $document->type,
                'status' => $document->status,
                'organization' => $document->username,
                'document_url' => $document_url,
                'attachments' => $attachments, // Add the array of all attachments
                'remarks' => $document->remarks ?? null,
                'is_archived' => $isArchived // Add this line
            ]
        ]);
    }

    public function documentHistory(Request $request)
    {
        // Define the standard document types
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

        // Start with a base query - JOIN with both submitter and admin users table
        $query = DB::table('submitted_documents')
            ->leftJoin('users as submitter', 'submitted_documents.user_id', '=', 'submitter.id')
            ->leftJoin('users as admin', 'submitted_documents.received_by', '=', 'admin.id')
            ->select('submitted_documents.*', 'submitter.username', 'admin.role_name')
            ->whereNull('submitted_documents.archived_at')
            ->where('submitted_documents.status', 'Approved')
            ->where('admin.role_name', '!=', 'Student') 
            ->whereNotNull('admin.role_name'); 

        // BUILD AVAILABLE FILTER OPTIONS BEFORE APPLYING FILTERS
        $baseFilterQuery = clone $query;

        // Apply search to base query first if exists
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $baseFilterQuery->where(function($q) use ($search) {
                $q->where('submitted_documents.subject', 'like', "%{$search}%")
                  ->orWhere('submitted_documents.control_tag', 'like', "%{$search}%")
                  ->orWhere('submitted_documents.type', 'like', "%{$search}%")
                  ->orWhere('submitter.username', 'like', "%{$search}%");
            });
        }

        // Get available organizations - CHANGED TO USE USERNAMES like archivePage
        $availableOrganizations = $baseFilterQuery->pluck('submitter.username')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        // Get available types, categorizing non-standard types as "Others"
        $availableTypesRaw = $baseFilterQuery->pluck('submitted_documents.type')->unique()->toArray();
        $availableTypes = [];
        $hasOthers = false;

        foreach ($availableTypesRaw as $type) {
            if (in_array($type, $standardTypes)) {
                $availableTypes[] = $type;
            } else {
                $hasOthers = true;
            }
        }

        // Add "Others" if there are non-standard types
        if ($hasOthers) {
            $availableTypes[] = 'Others';
        }

        $availableTypes = array_unique($availableTypes);
        sort($availableTypes);

        // Get available admin roles (submitted_to values)
        $availableRoles = $baseFilterQuery->pluck('admin.role_name')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        // NOW APPLY THE ACTUAL FILTERS
        // Apply organization filter - CHANGED TO USE USERNAME like archivePage
        if ($request->has('organization') && $request->organization !== 'All' && $request->organization !== 'Organization') {
            $query->where('submitter.username', $request->organization);
        }
        
        // Apply type filter - handle "Others" category
        if ($request->has('type') && $request->type !== 'All' && $request->type !== 'Type') {
            if ($request->type === 'Others') {
                // Filter for documents with types NOT in the standard list
                $query->whereNotIn('submitted_documents.type', $standardTypes);
            } else {
                // Filter for specific standard type
                $query->where('submitted_documents.type', $request->type);
            }
        }

        // Apply submitted_to filter if present
        if ($request->has('submitted_to') && $request->submitted_to !== 'All' && $request->submitted_to !== 'Submitted') {
            $query->where('admin.role_name', $request->submitted_to);
        }
        
        // ADD DATE FILTERING 
        if ($request->has('start_date') && !empty($request->start_date)) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('submitted_documents.created_at', '>=', $startDate);
        }
        
        if ($request->has('end_date') && !empty($request->end_date)) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('submitted_documents.created_at', '<=', $endDate);
        }
        
        // Apply search filter to main query
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('submitted_documents.subject', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('submitted_documents.control_tag', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('submitted_documents.type', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('submitter.username', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // Apply sorting with special handling for derived fields
        if ($request->has('sort_by')) {
            $column = $request->sort_by;
            $direction = $request->has('sort_dir') ? $request->sort_dir : 'asc';
            
            if ($column === 'organization') {
                // Sort by username instead of control_tag
                $query->orderBy('submitter.username', $direction);
            } else {
                // Prefix columns with table name to avoid ambiguity
                $query->orderBy('submitted_documents.' . $column, $direction);
            }
        } else {
            // Default sort
            $query->orderBy('submitted_documents.created_at', 'desc');
        }
        
        // Execute query with pagination and append all query parameters for pagination links
        $documents = $query->paginate(6)->appends($request->all());

        // Organization mapping (for display purposes)
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
        
        // Return appropriate response based on request type
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.documentHistory', compact('documents', 'orgMap', 'tagColors', 'standardTypes'))->render(),
                'availableOrganizations' => $availableOrganizations,
                'availableTypes' => $availableTypes,
                'availableRoles' => $availableRoles,  // Add this
                'debug' => [
                    'total_documents' => $documents->total(),
                    'available_orgs' => $availableOrganizations,
                    'filters_applied' => [
                        'organization' => $request->organization,
                        'type' => $request->type,
                        'submitted_to' => $request->submitted_to, // Add this
                        'search' => $request->search,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date
                    ]
                ]
            ]);
        }
        
        return view('admin.documentHistory', compact(
            'documents', 
            'orgMap', 
            'tagColors', 
            'standardTypes', 
            'availableOrganizations', 
            'availableTypes',
            'availableRoles'  // Add this
        ));
    }

    public function archiveDocuments(Request $request)
    {
        $documentIds = $request->input('document_ids', []);

        if (empty($documentIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No documents selected for archiving'
            ]);
        }

        try {
            // Get document titles before archiving for logging
            $documents = DB::table('submitted_documents')
                ->whereIn('id', $documentIds)
                ->where('status', 'Approved')
                ->get(['id', 'subject', 'control_tag']);

            DB::table('submitted_documents')
                ->whereIn('id', $documentIds)
                ->where('status', 'Approved')
                ->update([
                    'archived_at' => now()
                ]);

            // Log the activity
            $documentTitles = $documents->pluck('subject')->implode(', ');
            $this->logActivity(
                'Archived',
                'Documents',
                "Admin archived " . count($documentIds) . " document(s): " . $documentTitles
            );

            return response()->json([
                'success' => true,
                'message' => count($documentIds) . ' document(s) archived successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to archive documents: ' . $e->getMessage()
            ]);
        }
    }

    public function archivePage(Request $request)
    {
        $adminId = auth()->id();

        // Add logging for debugging
        \Log::info('Archive Page Request:', [
            'admin_id' => $adminId,
            'request_data' => $request->all(),
            'is_ajax' => $request->ajax()
        ]);

        // Define the standard document types
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

        // JOIN with both submitter and admin users table to get admin role_name
        // REMOVED the admin filter to show ALL archived documents
        $query = DB::table('submitted_documents')
            ->leftJoin('users as submitter', 'submitted_documents.user_id', '=', 'submitter.id')
            ->leftJoin('users as admin', 'submitted_documents.received_by', '=', 'admin.id')
            ->select('submitted_documents.*', 'submitter.username', 'admin.role_name')
            ->whereNotNull('submitted_documents.archived_at')
            // ->where('submitted_documents.received_by', $adminId) // REMOVE THIS LINE
            ->where('admin.role_name', '!=', 'Student')
            ->whereNotNull('admin.role_name');

        \Log::info('Base archive query built for all admins');

        // Build a query for getting available filter options BEFORE applying filters
        $baseFilterQuery = clone $query;

        // Apply search filter to base query first
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            \Log::info('Applying search to base query:', ['term' => $search]);
            $baseFilterQuery->where(function($q) use ($search) {
                $q->where('submitted_documents.subject', 'like', "%{$search}%")
                  ->orWhere('submitted_documents.control_tag', 'like', "%{$search}%")
                  ->orWhere('submitted_documents.type', 'like', "%{$search}%")
                  ->orWhere('submitter.username', 'like', "%{$search}%");
            });
        }

        // Get available organizations (use actual usernames from users table)
        $availableOrganizations = $baseFilterQuery->pluck('submitter.username')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        \Log::info('Available organizations in archive:', ['count' => count($availableOrganizations), 'orgs' => $availableOrganizations]);

        // Get available types, categorizing non-standard types as "Others"
        $availableTypesRaw = $baseFilterQuery->pluck('type')->unique()->toArray();
        $availableTypes = [];
        $hasOthers = false;

        foreach ($availableTypesRaw as $type) {
            if (in_array($type, $standardTypes)) {
                $availableTypes[] = $type;
            } else {
                $hasOthers = true;
            }
        }

        // Add "Others" if there are non-standard types
        if ($hasOthers) {
            $availableTypes[] = 'Others';
        }

        $availableTypes = array_unique($availableTypes);
        sort($availableTypes);

        \Log::info('Available types in archive:', ['types' => $availableTypes]);

        // Now apply the actual filters
        // Apply organization filter - filter by username instead of control_tag  
        if ($request->has('organization') && $request->organization !== 'All' && $request->organization !== 'Organization') {
            \Log::info('Applying organization filter:', ['username' => $request->organization]);
            $query->where('submitter.username', $request->organization);
        }

        if ($request->has('type') && $request->type !== 'All' && $request->type !== 'Type') {
            \Log::info('Applying type filter:', ['type' => $request->type]);
            if ($request->type === 'Others') {
                $query->whereNotIn('submitted_documents.type', $standardTypes);
            } else {
                $query->where('submitted_documents.type', $request->type);
            }
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            \Log::info('Applying search filter to main query:', ['term' => $search]);
            $query->where(function($q) use ($search) {
                $q->where('submitted_documents.subject', 'like', "%{$search}%")
                  ->orWhere('submitted_documents.control_tag', 'like', "%{$search}%")
                  ->orWhere('submitted_documents.type', 'like', "%{$search}%")
                  ->orWhere('submitter.username', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        if ($request->has('sort_by')) {
            $column = $request->sort_by;
            $direction = $request->has('sort_dir') ? $request->sort_dir : 'asc';
            
            if ($column === 'organization') {
                $query->orderBy('submitter.username', $direction);
            } else {
                $query->orderBy('submitted_documents.' . $column, $direction);
            }
        } else {
            $query->orderBy('submitted_documents.archived_at', 'desc');
        }

        // Get SQL for debugging
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        \Log::info('Final archive query SQL:', ['sql' => $sql, 'bindings' => $bindings]);

        // Fetch archived documents with pagination
        $documents = $query->paginate(6)->appends($request->except('page'));

        \Log::info('Archive query executed:', [
            'total_documents' => $documents->total(),
            'current_page_count' => $documents->count(),
            'per_page' => $documents->perPage(),
            'current_page' => $documents->currentPage()
        ]);

        // Your existing organization mapping and color coding
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

        // Handle AJAX requests
        if ($request->ajax()) {
            \Log::info('Returning AJAX response for archive page');
            
            try {
                $html = view('admin.archivePage', compact('documents', 'orgMap', 'tagColors', 'standardTypes'))->render();
                \Log::info('Archive view rendered successfully');
                
                return response()->json([
                    'html' => $html,
                    'availableOrganizations' => $availableOrganizations,
                    'availableTypes' => $availableTypes,
                    'debug' => [
                        'total_documents' => $documents->total(),
                        'admin_id' => $adminId,
                        'filters_applied' => [
                            'organization' => $request->organization,
                            'type' => $request->type,
                            'search' => $request->search
                        ]
                    ]
                ]);
            } catch (\Exception $e) {
                \Log::error('Error rendering archive view:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                return response()->json(['error' => 'Failed to render view'], 500);
            }
        }

        return view('admin.archivePage', compact('documents', 'orgMap', 'tagColors', 'standardTypes', 'availableOrganizations', 'availableTypes'));
    }

    public function restoreDocuments(Request $request)
    {
        $documentIds = $request->input('document_ids', []);

        if (empty($documentIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No documents selected for restoration'
            ]);
        }

        try {
            // Get document titles before restoring for logging
            $documents = DB::table('submitted_documents')
                ->whereIn('id', $documentIds)
                ->get(['id', 'subject', 'control_tag']);

            DB::table('submitted_documents')
                ->whereIn('id', $documentIds)
                ->update([
                    'archived_at' => null
                ]);

            // Log the activity
            $documentTitles = $documents->pluck('subject')->implode(', ');
            $this->logActivity(
                'Restored',
                'Documents',
                "Admin restored " . count($documentIds) . " document(s): " . $documentTitles
            );

            return response()->json([
                'success' => true,
                'message' => count($documentIds) . ' document(s) restored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore documents: ' . $e->getMessage()
            ]);
        }
    }

    public function index(Request $request)
    {
        $documents = Document::where('archived', true)
            ->orderBy('archived_at', 'desc')
            ->paginate(6);
        
        if ($request->ajax()) {
            return view('admin.partials.archiveTableContent', compact('documents'))->render();
        }
        
        return view('admin.archivePage', compact('documents'));
    }

    // Add this new method to handle server-side select all
    public function selectAllDocuments(Request $request)
    {
        // Define the standard document types
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

        // Build the same query as in documentHistory method with both JOINs
        $query = DB::table('submitted_documents')
            ->leftJoin('users as submitter', 'submitted_documents.user_id', '=', 'submitter.id')
            ->leftJoin('users as admin', 'submitted_documents.received_by', '=', 'admin.id')
            ->select('submitted_documents.id')
            ->whereNull('submitted_documents.archived_at')
            ->where('submitted_documents.status', 'Approved')
            ->where('admin.role_name', '!=', 'Student') // Exclude Student roles
            ->whereNotNull('admin.role_name'); // Ensure role_name exists

        // Apply the same filters as in documentHistory - CHANGED TO USE USERNAME
        if ($request->has('organization') && $request->organization != 'All' && $request->organization != 'Organization') {
            $query->where('submitter.username', $request->organization);
        }

        // Apply type filter - handle "Others" category
        if ($request->has('type') && $request->type != 'All' && $request->type != 'Type') {
            if ($request->type === 'Others') {
                // Filter for documents with types NOT in the standard list
                $query->whereNotIn('submitted_documents.type', $standardTypes);
            } else {
                // Filter for specific standard type
                $query->where('submitted_documents.type', $request->type);
            }
        }

        // Apply submitted_to filter
        if ($request->has('submitted_to') && $request->submitted_to != 'All' && $request->submitted_to != 'Submitted') {
            $query->where('admin.role_name', $request->submitted_to);
        }

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('submitted_documents.subject', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('submitted_documents.control_tag', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('submitted_documents.type', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('submitter.username', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Apply date filtering
        if ($request->has('start_date') && !empty($request->start_date)) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('submitted_documents.created_at', '>=', $startDate);
        }

        if ($request->has('end_date') && !empty($request->end_date)) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('submitted_documents.created_at', '<=', $endDate);
        }

        // Get all document IDs that match the current filters
        $documentIds = $query->pluck('submitted_documents.id')->toArray();

        return response()->json([
            'success' => true,
            'document_ids' => $documentIds,
            'total_count' => count($documentIds)
        ]);
    }

    // Add this new method for archived documents select all
    public function selectAllArchivedDocuments(Request $request)
    {
        // Define the standard document types
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

        // Build the same query as in archivePage method with both JOINs
        // REMOVED admin filter to get ALL archived documents
        $query = DB::table('submitted_documents')
            ->leftJoin('users as submitter', 'submitted_documents.user_id', '=', 'submitter.id')
            ->leftJoin('users as admin', 'submitted_documents.received_by', '=', 'admin.id')
            ->select('submitted_documents.id')
            ->whereNotNull('submitted_documents.archived_at')
            // ->where('submitted_documents.received_by', $adminId) // REMOVE THIS LINE
            ->where('admin.role_name', '!=', 'Student')
            ->whereNotNull('admin.role_name');

        // Apply the same filters as in archivePage
        if ($request->has('organization') && $request->organization != 'All' && $request->organization != 'Organization') {
            $query->where('submitter.username', $request->organization); // Use username instead of control_tag
        }

        // Apply type filter - handle "Others" category
        if ($request->has('type') && $request->type != 'All' && $request->type != 'Type') {
            if ($request->type === 'Others') {
                $query->whereNotIn('submitted_documents.type', $standardTypes);
            } else {
                $query->where('submitted_documents.type', $request->type);
            }
        }

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('submitted_documents.subject', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('submitted_documents.control_tag', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('submitted_documents.type', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('submitter.username', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Get all archived document IDs that match the current filters
        $documentIds = $query->pluck('submitted_documents.id')->toArray();
    
        return response()->json([
            'success' => true,
            'document_ids' => $documentIds,
            'total_count' => count($documentIds)
        ]);
    }
}
