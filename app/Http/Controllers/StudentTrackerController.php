<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubmittedDocument;

class StudentTrackerController extends Controller
{
    public function viewStudentTracker()
    {
     $records = SubmittedDocument::paginate('15');
            return view('student.studentTracker', compact('records'));
    }

    public function show($id)
    {
        // Fetch the submitted document by ID or throw a 404 error
        $record = SubmittedDocument::findOrFail($id);

        // Pass the record to the view
        return view('student.components.viewRecordSubmitted', compact('record'));
    }
}
