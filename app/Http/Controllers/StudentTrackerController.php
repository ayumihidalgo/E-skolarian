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
}
