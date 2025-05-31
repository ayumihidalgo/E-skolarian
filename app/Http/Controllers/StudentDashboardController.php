<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\SubmittedDocument;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use App\Models\Document; // Add this line

class StudentDashboardController extends Controller
{
    public function showStudentDashboard()
    {
        $userId = Auth::id();
        //Document status counts
        // Get IDs of documents that are "Under Review" in the review table
        $underReviewIds = \App\Models\Review::where('status', 'Under Review')
            ->whereHas('document', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->pluck('document_id')
            ->unique();

        // Pending: documents that are pending and NOT under review
        $pendingCount = \App\Models\SubmittedDocument::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereNotIn('id', $underReviewIds)
            ->count();

        // Under Review: count from review table
        $reviewCount = $underReviewIds->count();

        // Approved: from submitted_documents
        $approvedCount = \App\Models\SubmittedDocument::where('user_id', $userId)
            ->where('status', 'approved')
            ->count();

        // Total: only pending, under_review, approved
        $totalCount = \App\Models\SubmittedDocument::where('user_id', $userId)
            ->whereIn('status', ['pending', 'under_review', 'approved'])
            ->orWhereIn('id', $underReviewIds)
            ->count();

        // Announcements (unchanged)
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $latestAnnouncements = Announcement::with('user')
            ->where('created_at', '>=', $sevenDaysAgo)
            ->latest()
            ->get();
        $previousAnnouncements = Announcement::with('user')
            ->where('created_at', '<', $sevenDaysAgo)
            ->latest()
            ->get();

        // Fetch recent documents
        $recentDocuments = \App\Models\SubmittedDocument::with(['latestVersion', 'receiver'])
            ->where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        return view('student.dashboard', [
            'pendingCount' => $pendingCount,
            'reviewCount' => $reviewCount,
            'approvedCount' => $approvedCount,
            'totalCount' => $totalCount,
            'latestAnnouncements' => $latestAnnouncements,
            'previousAnnouncements' => $previousAnnouncements,
            'recentDocuments' => $recentDocuments, // Add recentDocuments to the view data
        ]);
    }
}
