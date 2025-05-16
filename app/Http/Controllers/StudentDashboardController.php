<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;

class StudentDashboardController extends Controller
{
    public function showStudentDashboard()
    {
        $latestAnnouncement = Announcement::with('user')->latest()->first();
        return view('student.dashboard', compact('latestAnnouncement'));
    }
}
