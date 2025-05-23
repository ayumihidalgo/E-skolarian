<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
     public function showDashboard(Request $request)
    {
        $sevenDaysAgo = \Carbon\Carbon::now()->subDays(7);

        $latestAnnouncements = Announcement::with('user')
            ->where('created_at', '>=', $sevenDaysAgo)
            ->where('archived', false)
            ->latest()
            ->get();

        $previousAnnouncements = Announcement::with('user')
            ->where('created_at', '<', $sevenDaysAgo)
            ->where('archived', false)
            ->latest()
            ->get();

        $archivedAnnouncements = Announcement::with('user')
            ->where('archived', true)
            ->latest()
            ->get();

        // Determine which tab to show
        $showArchive = $request->query('archive', false);

        return view('admin.dashboard', compact(
            'latestAnnouncements',
            'previousAnnouncements',
            'archivedAnnouncements',
            'showArchive'
        ));
    }
}
