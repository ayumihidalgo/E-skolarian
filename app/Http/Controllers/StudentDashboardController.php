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

        // Pending: documents that are Pending for this student, not archived
        $pendingCount = \App\Models\SubmittedDocument::where('user_id', $userId)
            ->where('status', 'Pending')
            ->whereNull('archived_at')
            ->count();

        // Under Review: documents that are Under Review for this student, not archived
        $reviewCount = \App\Models\SubmittedDocument::where('user_id', $userId)
            ->where('status', 'Under Review')
            ->whereNull('archived_at')
            ->count();

        // Approved: documents that are Approved for this student, not archived
        $approvedCount = \App\Models\SubmittedDocument::where('user_id', $userId)
            ->where('status', 'Approved')
            ->whereNull('archived_at')
            ->count();

        // Total: sum of the three counts
        $totalCount = $pendingCount + $reviewCount + $approvedCount;

        // Announcements: Only show if for all or for this student
        $sevenDaysAgo = Carbon::now()->subDays(7);

        $latestAnnouncements = Announcement::with('user')
             ->where('archived', 0)
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
             ->where('archived', 0)
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

        // Fetch recent documents (only non-archived)
        $recentDocuments = \App\Models\SubmittedDocument::with(['latestVersion', 'receiver'])
            ->where('user_id', auth()->id())
            ->whereNull('archived_at') // Only non-archived documents
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
