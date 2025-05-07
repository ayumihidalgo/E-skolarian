<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentTrackerController extends Controller
{
    public function viewStudentTracker()
    {
        return view('student.studentTracker');
    }
}
