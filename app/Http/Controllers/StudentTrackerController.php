<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubmittedDocument;

class StudentTrackerController extends Controller
{
    public function viewStudentTracker()
    {
        $records = SubmittedDocument::with(['user', 'receiver', 'reviews'])->paginate(15);
        return view('student.studentTracker', compact('records'));
    }

    public function show($id)
    {
        $record = SubmittedDocument::with(['user', 'receiver', 'reviews',])->findOrFail($id);
        return view('student.components.viewRecordSubmitted', compact('record'));
    }
}
