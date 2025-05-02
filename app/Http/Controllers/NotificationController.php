<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    // Send Notification (send method)
    public function send(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $notification = Notification::create([
            'user_id' => $validated['user_id'],
            'title' => $validated['title'],
            'message' => $validated['message'],
        ]);

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
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->is_read = true;
        $notification->save();

        return response()->json(['message' => 'Notification marked as read']);
    }

 

}
