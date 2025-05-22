<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
     public function showDashboard()
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

    return view('admin.dashboard', compact('latestAnnouncements', 'previousAnnouncements'));
    }
}
