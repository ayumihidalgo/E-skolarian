<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
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

    public function getAll($userId)
    {
        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($notifications);
    }

    public function getUnread($userId)
    {
        $notifications = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->get();
        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->is_read = true;
        $notification->save();

        return response()->json(['message' => 'Notification marked as read']);
    }
}
?>