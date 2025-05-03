<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminDocumentController extends Controller
{
    public function preview($id)
    {
        // Get all parameters from the request
        $status = request()->query('status');
        $organization = request()->query('organization');
        $title = request()->query('title');
        $type = request()->query('type');

        // Mock document data with passed values
        $document = [
            'id' => $id,
            'title' => $title,
            'organization' => $organization,
            'type' => $type,
            'status' => $status,
            'date' => now()->toDateString(),
            'content' => 'This is a preview of the document content for document ID ' . $id . '.',
        ];

        // Return the preview view with the document data
        return view('admin.documentPreview', compact('document'));
    }
}
