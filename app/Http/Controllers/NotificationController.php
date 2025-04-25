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

}
