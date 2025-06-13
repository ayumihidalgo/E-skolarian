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

        // Get IDs of documents that are "Under Review" in the review table
        $underReviewIds = Review::where('status', 'Under Review')
            ->pluck('document_id')
            ->unique();

        // Pending: documents that are pending and NOT under review
        $pendingCount = SubmittedDocument::where('status', 'pending')
            ->whereNotIn('id', $underReviewIds)
            ->count();

        // Under Review: count from review table
        $reviewCount = $underReviewIds->count();

        // Approved: from submitted_documents
        $approvedCount = SubmittedDocument::where('status', 'approved')->count();

        // Total: only pending, under_review, approved
        $totalCount = SubmittedDocument::whereIn('status', ['pending', 'under_review', 'approved'])
            ->orWhereIn('id', $underReviewIds)
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
