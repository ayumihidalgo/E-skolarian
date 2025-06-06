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
        $userId = (string) Auth::id();
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

        // Announcements: Only show if for all or for this student
        $sevenDaysAgo = Carbon::now()->subDays(7);

        $latestAnnouncements = Announcement::with('user')
            ->where(function($query) use ($userId, $sevenDaysAgo) {
                $query->where(function($q) use ($userId, $sevenDaysAgo) {
                    // No deadline: show if within 7 days
                    $q->where(function($subQ) use ($userId, $sevenDaysAgo) {
                        $subQ->where(function($audQ) use ($userId) {
                                $audQ->where('audience', 'all')
                                     ->orWhere(function($q2) use ($userId) {
                                         $q2->where('audience', 'custom')
                                            ->whereJsonContains('audience_students', $userId);
                                     });
                            })
                            ->whereNull('deadline')
                            ->where('created_at', '>=', $sevenDaysAgo);
                    });
                })
                ->orWhere(function($q) use ($userId) {
                    // With deadline: show if deadline not yet passed
                    $q->where(function($audQ) use ($userId) {
                            $audQ->where('audience', 'all')
                                 ->orWhere(function($q2) use ($userId) {
                                     $q2->where('audience', 'custom')
                                        ->whereJsonContains('audience_students', $userId);
                                 });
                        })
                        ->whereNotNull('deadline')
                        ->where('deadline', '>=', Carbon::now());
                });
            })
            ->latest()
            ->get();

        $previousAnnouncements = Announcement::with('user')
            ->where(function($query) use ($userId, $sevenDaysAgo) {
                $query->where(function($q) use ($userId, $sevenDaysAgo) {
                    // No deadline: move to previous after 7 days
                    $q->where(function($audQ) use ($userId) {
                            $audQ->where('audience', 'all')
                                 ->orWhere(function($q2) use ($userId) {
                                     $q2->where('audience', 'custom')
                                        ->whereJsonContains('audience_students', $userId);
                                 });
                        })
                        ->whereNull('deadline')
                        ->where('created_at', '<', $sevenDaysAgo);
                })
                ->orWhere(function($q) use ($userId) {
                    // With deadline: move to previous after deadline is over
                    $q->where(function($audQ) use ($userId) {
                            $audQ->where('audience', 'all')
                                 ->orWhere(function($q2) use ($userId) {
                                     $q2->where('audience', 'custom')
                                        ->whereJsonContains('audience_students', $userId);
                                 });
                        })
                        ->whereNotNull('deadline')
                        ->where('deadline', '<', Carbon::now());
                });
            })
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
