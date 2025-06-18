<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Auth;
use App\Models\Document;
use App\Models\Review;
use App\Models\SubmittedDocument;

class AdminDashboardController extends Controller
{
     public function showDashboard(Request $request)
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);

        $latestAnnouncements = Announcement::with('user')
            ->where(function ($query) use ($sevenDaysAgo) {
                $query->where(function ($q) use ($sevenDaysAgo) {
                    // No deadline: show if within 7 days
                    $q->whereNull('deadline')
                      ->where('created_at', '>=', $sevenDaysAgo);
                })
                ->orWhere(function ($q) {
                    // With deadline: show if deadline not yet passed
                    $q->whereNotNull('deadline')
                      ->where('deadline', '>=', Carbon::now());
                });
            })
            ->where('archived', false)
            ->latest()
            ->get();

        $previousAnnouncements = Announcement::with('user')
            ->where(function ($query) use ($sevenDaysAgo) {
                $query->where(function ($q) use ($sevenDaysAgo) {
                    // No deadline: move to previous after 7 days
                    $q->whereNull('deadline')
                      ->where('created_at', '<', $sevenDaysAgo);
                })
                ->orWhere(function ($q) {
                    // With deadline: move to previous after deadline is over
                    $q->whereNotNull('deadline')
                      ->where('deadline', '<', Carbon::now());
                });
            })
            ->where('archived', false)
            ->latest()
            ->get();

        $archivedAnnouncements = Announcement::with('user')
            ->where('archived', true)
            ->latest()
            ->get();

        // Document status counts
        $adminId = auth()->id();

        // Pending: documents that are Pending and received by this admin, not Returned, not archived
        $pendingCount = SubmittedDocument::where('status', 'Pending')
            ->where('received_by', $adminId)
            ->whereNull('archived_at')
            ->count();

        // Under Review: documents that are Under Review and received by this admin, not Returned, not archived
        $reviewCount = SubmittedDocument::where('status', 'Under Review')
            ->where('received_by', $adminId)
            ->whereNull('archived_at')
            ->count();

        // Approved: documents that are Approved and received by this admin, not Returned, not archived
        $approvedCount = SubmittedDocument::where('status', 'Approved')
            ->where('received_by', $adminId)
            ->whereNull('archived_at')
            ->count();

        // Total: sum of the three counts
        $totalCount = $pendingCount + $reviewCount + $approvedCount;

        // Determine which tab to show
        $showArchive = $request->query('archive', false);

        $recentDocuments = \App\Models\SubmittedDocument::with('user')
            ->where('received_by', Auth::id())
            ->whereNull('archived_at') // Only non-archived documents
            ->latest()
            ->take(5)
            ->get();

        $users = \App\Models\User::all();

        return view('admin.dashboard', compact(
            'latestAnnouncements',
            'previousAnnouncements',
            'archivedAnnouncements',
            'showArchive',
            'pendingCount',
            'reviewCount',
            'approvedCount',
            'totalCount',
            'recentDocuments',
            'users'
        ))->with([
            'currentUserId' => auth()->id(),
            'currentUserRole' => auth()->user()->role,
        ]);
    }
    
    public function getDocumentCounts()
    {
        $adminId = auth()->id();

        // Pending: documents that are Pending and received by this admin, not Returned, not archived
        $pendingCount = SubmittedDocument::where('status', 'Pending')
            ->where('received_by', $adminId)
            ->whereNull('archived_at')
            ->count();

        // Under Review: documents that are Under Review and received by this admin, not Returned, not archived
        $reviewCount = SubmittedDocument::where('status', 'Under Review')
            ->where('received_by', $adminId)
            ->whereNull('archived_at')
            ->count();

        // Approved: documents that are Approved and received by this admin, not Returned, not archived
        $approvedCount = SubmittedDocument::where('status', 'Approved')
            ->where('received_by', $adminId)
            ->whereNull('archived_at')
            ->count();

        // Total: sum of the three counts
        $totalCount = $pendingCount + $reviewCount + $approvedCount;

        return response()->json([
            'pendingCount' => $pendingCount,
            'reviewCount' => $reviewCount,
            'approvedCount' => $approvedCount,
            'totalCount' => $totalCount,
        ]);
    }
}
