<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Carbon\Carbon; 


class StudentDashboardController extends Controller
{
    public function showStudentDashboard()
    {
       $sevenDaysAgo = Carbon::now()->subDays(7);

    $latestAnnouncements = Announcement::with('user')
        ->where('created_at', '>=', $sevenDaysAgo)
        ->latest()
        ->get();

    $previousAnnouncements = Announcement::with('user')
        ->where('created_at', '<', $sevenDaysAgo)
        ->latest()
        ->get();

    return view('student.dashboard', compact('latestAnnouncements', 'previousAnnouncements'));
    
    }
}
