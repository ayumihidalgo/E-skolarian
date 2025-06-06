<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Mail\NotificationAlertMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{

    // Send Notification (send method)
    public function send(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'url' => 'nullable|string',
        ]);

        $notification = Notification::create([
            'user_id' => $validated['user_id'],
            'title' => $validated['title'],
            'message' => $validated['message'],
            'url' => $validated['url'] ?? null,
            'is_read' => false,
        ]);

        // Send email notification only to students
        if ($notification->user->role === 'student') {
            try {
                Mail::to($notification->user->email)->send(new NotificationAlertMail($notification));
                Log::info('Email notification sent to student: ' . $notification->user->email);
            } catch (\Exception $e) {
                Log::error('Failed to send email notification: ' . $e->getMessage());
            }
        } else {
            Log::info('Email notification skipped for admin user: ' . $notification->user->email);
        }

        return response()->json($notification, 201);
    }

    // Get All Notifications (getAll method)
    // Get Unread Notifications (getUnread method)
    // Get All Notifications for a User
    public function getAll($userId)
    {
        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($notifications);
    }

    // Get Unread Notifications for a User

    public function getUnread($userId)
    {
        $notifications = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->get();
        return response()->json($notifications);
    }

    // Mark Notification as Read
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $notification->is_read = true;
        $notification->save();

        return response()->json(['success' => true]);
    }

    // Toggle Read Status of a Notification
    public function toggleRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->update([
            'is_read' => !$notification->is_read
        ]);

        return response()->json([
            'success' => true,
            'is_read' => $notification->is_read
        ]);
    }

    // Bulk mark as read
    public function markAsReadBulk(Request $request)
    {
        $ids = $request->input('ids', []);
        Notification::whereIn('id', $ids)
            ->where('user_id', auth()->id())
            ->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    // Bulk mark as unread
    public function markAsUnreadBulk(Request $request)
    {
        $ids = $request->input('ids', []);
        Notification::whereIn('id', $ids)
            ->where('user_id', auth()->id())
            ->update(['is_read' => false]);
        return response()->json(['success' => true]);
    }

    // Bulk delete
    public function deleteBulk(Request $request)
    {
        $ids = $request->input('ids', []);
        Notification::whereIn('id', $ids)
            ->where('user_id', auth()->id())
            ->delete();
        return response()->json(['success' => true]);
    }

    // Delete all notifications for the user
    public function deleteAll(Request $request)
    {
        Notification::where('user_id', auth()->id())->delete();
        return response()->json(['success' => true]);
    }
}
