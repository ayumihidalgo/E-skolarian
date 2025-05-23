<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
            ]
        ]);
    }

    public function documentHistory(Request $request)
    {
        $adminId = auth()->id(); // Get current admin's user ID

        // Start with base query
        $query = DB::table('submitted_documents')
            ->whereNull('archived_at')
            ->where('received_by', $adminId); // Only documents assigned to this admin

        // Apply filters from request parameters
        if ($request->has('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        if ($request->has('organization') && $request->organization !== 'All') {
            // Filter by organization prefix in control_tag
            $query->where('control_tag', 'like', $request->organization . '_%');
        }

        if ($request->has('type') && $request->type !== 'All') {
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

        // Fetch documents with pagination
        $documents = $query->orderBy('created_at', 'desc')
                          ->paginate(6)
                          ->appends($request->except('page')); // Important: maintain filters across pagination

        // Get organization mapping for display purposes (your existing code)
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

        return view('admin.documentHistory', compact('documents', 'orgMap', 'tagColors'));
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
            ->where('received_by', $adminId); // Only documents assigned to this admin

        // Apply filters from request parameters
        if ($request->has('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        if ($request->has('organization') && $request->organization !== 'All') {
            $query->where('control_tag', 'like', $request->organization . '_%');
        }

        if ($request->has('type') && $request->type !== 'All') {
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

        // Fetch archived documents with pagination
        $documents = $query->orderBy('archived_at', 'desc')
                          ->paginate(6)
                          ->appends($request->except('page')); // Maintain filters across pagination

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
}
