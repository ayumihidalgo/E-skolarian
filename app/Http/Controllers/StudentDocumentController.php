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
        // Fetch the document from the database
        $document = DB::table('submitted_documents')
            ->where('id', $id)
            ->where('user_id', Auth::id()) // Ensure student can only view their own document
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

        return view('student.documentPreview', [
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

    public function documentArchive()
    {
        $userId = Auth::id();

        $documents = DB::table('submitted_documents')
            ->where('user_id', $userId)
            ->whereNull('archived_at') // Exclude archived documents
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        return view('student.documentHistory', compact('documents'));
    }
}
