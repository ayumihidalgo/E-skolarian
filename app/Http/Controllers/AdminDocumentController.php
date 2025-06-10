<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Document;

class AdminDocumentController extends Controller
{
    public function preview($id)
    {
        $adminId = auth()->id();

        $document = DB::table('submitted_documents')
            ->where('id', $id)
            ->where('received_by', $adminId) // Only assigned to this admin
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
        $filePath = null;
        
        // Process all versions into attachments array
        if ($documentVersions && count($documentVersions) > 0) {
            foreach ($documentVersions as $version) {
                $attachments[] = [
                    'id' => $version->id,
                    'version' => $version->version,
                    'file_path' => $version->file_path,
                    'comments' => $version->comments ?? null,
                    'submitted_at' => $version->submitted_at,
                    'is_latest' => ($version === $documentVersions->first())
                ];
            }
            
            // Get latest version's file path for display
            $latestVersion = $documentVersions->first();
            $filePath = $latestVersion->file_path;
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
                'content' => $document->summary,
                'date' => $document->created_at,
                'type' => $document->type,
                'status' => $document->status,
                'organization' => $organizationName,
                'file_path' => $filePath,
                'attachments' => $attachments, // Add the array of all attachments
                'remarks' => $document->remarks ?? null,
                'is_archived' => $isArchived // Add this line
            ]
        ]);
    }

    public function documentHistory(Request $request)
    {
        // Start with a base query
        $query = DB::table('submitted_documents')
            ->whereNull('archived_at')
            ->where('status', 'Approved');
        
        // Apply organization filter
        if ($request->has('organization') && $request->organization != 'All' && $request->organization != 'Organization') {
            $query->where('control_tag', 'LIKE', $request->organization . '_%');
        }
        
        // Apply type filter
        if ($request->has('type') && $request->type != 'All' && $request->type != 'Type') {
            $query->where('type', $request->type);
        }
        
        // Apply status filter
        if ($request->has('status') && $request->status != 'All' && $request->status != 'Status') {
            $query->where('status', $request->status);
        }
        
        // ADD DATE FILTERING 
        if ($request->has('start_date') && !empty($request->start_date)) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('created_at', '>=', $startDate);
        }
        
        if ($request->has('end_date') && !empty($request->end_date)) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('created_at', '<=', $endDate);
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
        
        // Apply sorting with special handling for derived fields
        if ($request->has('sort_by')) {
            $column = $request->sort_by;
            $direction = $request->has('sort_dir') ? $request->sort_dir : 'asc';
            
            if ($column === 'organization') {
                // Sort by the organization part of control_tag
                // This uses a substring before the underscore
                $query->orderByRaw("SUBSTRING_INDEX(control_tag, '_', 1) $direction");
            } else {
                // Normal column sort
                $query->orderBy($column, $direction);
            }
        } else {
            // Default sort
            $query->orderBy('created_at', 'desc');
        }
        
        // DEBUG: Add this temporarily to see what's happening
        \Log::info('Document History Query Debug:', [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
            'all_params' => $request->all()
        ]);
        
        // Execute query with pagination and append all query parameters for pagination links
        $documents = $query->paginate(6)->appends($request->all());
        
        // Return appropriate response based on request type
        if ($request->ajax()) {
            return view('admin.documentHistory', compact('documents'))->render();
        }
        
        return view('admin.documentHistory', compact('documents'));
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
            DB::table('submitted_documents')
                ->whereIn('id', $documentIds)
                ->where('status', 'Approved')
                ->update([
                    'archived_at' => now()
                ]);

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

        $query = DB::table('submitted_documents')
            ->whereNotNull('archived_at')
            ->where('received_by', $adminId);

        // Apply filters from request parameters
        if ($request->has('organization') && $request->organization !== 'All' && $request->organization !== 'Organization') {
            $query->where('control_tag', 'like', $request->organization . '_%');
        }

        if ($request->has('type') && $request->type !== 'All' && $request->type !== 'Type') {
            $query->where('type', $request->type);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('control_tag', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        // Apply sorting with special handling for derived fields
        if ($request->has('sort_by')) {
            $column = $request->sort_by;
            $direction = $request->has('sort_dir') ? $request->sort_dir : 'asc';
            
            if ($column === 'organization') {
                // Sort by the organization part of control_tag
                $query->orderByRaw("SUBSTRING_INDEX(control_tag, '_', 1) $direction");
            } else {
                // Normal column sort
                $query->orderBy($column, $direction);
            }
        } else {
            // Default sort
            $query->orderBy('archived_at', 'desc');
        }

        // Fetch archived documents with pagination
        $documents = $query->paginate(6)
                          ->appends($request->except('page'));

        // Your existing organization mapping code
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
            return view('admin.archivePage', compact('documents', 'orgMap', 'tagColors'))->render();
        }

        return view('admin.archivePage', compact('documents', 'orgMap', 'tagColors'));
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
            DB::table('submitted_documents')
                ->whereIn('id', $documentIds)
                ->update([
                    'archived_at' => null
                ]);

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
        // Build the same query as in documentHistory method
        $query = DB::table('submitted_documents')
            ->select('id')
            ->whereNull('archived_at')
            ->where('status', 'Approved');
        
        // Apply the same filters as in documentHistory
        if ($request->has('organization') && $request->organization != 'All' && $request->organization != 'Organization') {
            $query->where('control_tag', 'LIKE', $request->organization . '_%');
        }
        
        if ($request->has('type') && $request->type != 'All' && $request->type != 'Type') {
            $query->where('type', $request->type);
        }
        
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('subject', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('control_tag', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('type', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // Apply date filtering
        if ($request->has('start_date') && !empty($request->start_date)) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('created_at', '>=', $startDate);
        }
        
        if ($request->has('end_date') && !empty($request->end_date)) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('created_at', '<=', $endDate);
        }
        
        // Get all document IDs that match the current filters
        $documentIds = $query->pluck('id')->toArray();
        
        return response()->json([
            'success' => true,
            'document_ids' => $documentIds,
            'total_count' => count($documentIds)
        ]);
    }

    // Add this new method for archived documents select all
    public function selectAllArchivedDocuments(Request $request)
    {
        // Build the same query as in archivePage method
        $query = DB::table('submitted_documents')
            ->select('id')
            ->whereNotNull('archived_at'); // Only archived documents
    
        // Apply the same filters as in archivePage
        if ($request->has('organization') && $request->organization != 'All' && $request->organization != 'Organization') {
            $query->where('control_tag', 'LIKE', $request->organization . '_%');
        }
    
        if ($request->has('type') && $request->type != 'All' && $request->type != 'Type') {
            $query->where('type', $request->type);
        }
    
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('subject', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('control_tag', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('type', 'LIKE', "%{$searchTerm}%");
            });
        }
    
        // Get all archived document IDs that match the current filters
        $documentIds = $query->pluck('id')->toArray();
    
        return response()->json([
            'success' => true,
            'document_ids' => $documentIds,
            'total_count' => count($documentIds)
        ]);
    }
}
