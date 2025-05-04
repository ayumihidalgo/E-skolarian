<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StudentDocumentController extends Controller
{
    public function preview($id)
    {
        // Get parameters from request
        $status = request()->query('status');
        $title = request()->query('title');
        $type = request()->query('type');

        // Create document data
        $document = [
            'id' => $id,
            'title' => $title,
            'type' => $type,
            'status' => $status,
            'date' => now()->toDateString(),
            'content' => 'This is a preview of the document content for document ID ' . $id . '.',
        ];

        return view('student.documentPreview', compact('document'));
    }
}
