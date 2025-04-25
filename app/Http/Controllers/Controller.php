<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController; // Correctly import BaseController

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function preview($id)
    {
        // Mock document data
        $document = [
            'id' => $id,
            'title' => 'Document Title ' . $id,
            'organization' => 'Eligible League of Information',
            'type' => 'Event Proposal',
            'status' => $id % 2 == 0 ? 'Approved' : 'Rejected',
            'date' => now()->toDateString(),
            'content' => 'This is a preview of the document content for document ID ' . $id . '.',
        ];

        // Return the preview view with the document data
        return view('admin.documentPreview', compact('document'));
    }

}
