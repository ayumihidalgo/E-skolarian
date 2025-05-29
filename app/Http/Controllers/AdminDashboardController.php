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
        //announcements
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

        return view('admin.dashboard', compact(
            'latestAnnouncements',
            'previousAnnouncements',
            'archivedAnnouncements',
            'showArchive',
            'pendingCount',
            'reviewCount',
            'approvedCount',
            'totalCount'
        ));
    }
}
