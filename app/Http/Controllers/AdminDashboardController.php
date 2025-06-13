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

        // Get IDs of documents that are "Returned" in the review table (for this admin)
        $returnedIds = Review::where('status', 'Returned')
            ->whereHas('document', function ($q) use ($adminId) {
                $q->where('received_by', $adminId);
            })
            ->pluck('document_id')
            ->unique();

        // Get IDs of documents that are "Under Review" in the review table, but only for this admin
        $underReviewIds = Review::where('status', 'Under Review')
            ->whereHas('document', function ($q) use ($adminId) {
                $q->where('received_by', $adminId);
            })
            ->pluck('document_id')
            ->unique();

        // Pending: documents that are pending and NOT under review or returned, and received by this admin
        $pendingCount = SubmittedDocument::where('status', 'pending')
            ->where('received_by', $adminId)
            ->whereNotIn('id', $underReviewIds)
            ->whereNotIn('id', $returnedIds)
            ->count();

        // Under Review: count from review table, only for this admin, and not returned
        $reviewCount = $underReviewIds->diff($returnedIds)->count();

        // Approved: only those received by this admin and not returned
        $approvedCount = SubmittedDocument::where('status', 'approved')
            ->where('received_by', $adminId)
            ->whereNotIn('id', $returnedIds)
            ->count();

        // Total: only those received by this admin and not returned
        $totalCount = SubmittedDocument::where('received_by', $adminId)
            ->whereNotIn('id', $returnedIds)
            ->count();

        // Determine which tab to show
        $showArchive = $request->query('archive', false);

        $recentDocuments = \App\Models\SubmittedDocument::with('user')
            ->where('received_by', Auth::id()) 
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
}
