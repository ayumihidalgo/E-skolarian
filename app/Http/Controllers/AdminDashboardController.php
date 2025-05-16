<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;

class AdminDashboardController extends Controller
{
     public function showDashboard()
    {
        $latestAnnouncement = Announcement::with('user')->latest()->first();
        return view('admin.dashboard', compact('latestAnnouncement'));
    }
}
