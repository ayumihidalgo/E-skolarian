<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:60',
            'content' => 'required|string|max:5000',
        ]);

        Announcement::create([
            'user_id' => Auth::id(), // assumes user is authenticated
            'title'   => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->back()->with('success', 'Announcement posted successfully!');
    }

    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:60',
            'content' => 'required|string|max:5000',
        ]);

        $announcement->title = $request->input('title');
        $announcement->content = $request->input('content');
        $announcement->save();

        // Set a specific session message for editing
        return redirect()->back()->with('success', 'Announcement changed successfully!');
    }
}
