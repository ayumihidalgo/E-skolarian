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
            'content' => 'required|string|max:1000',
            'audience' => 'required|in:all,custom',
            'audience_students.*' => 'exists:users,id',
            'schedule_date' => 'nullable|date',
            'schedule_time' => 'nullable'
        ]);

        $announcement = new Announcement();
        $announcement->title = $request->title;
        $announcement->content = $request->content;
        $announcement->user_id = auth()->id();
        $announcement->audience = $request->audience;

        if ($request->audience === 'custom') {
            $announcement->audience_students = json_encode($request->audience_students);
        } else {
            $announcement->audience_students = null;
        }

        // Save deadline if scheduled
        if ($request->schedule && $request->schedule_date) {
            $deadline = $request->schedule_date;
            if ($request->schedule_time) {
                $deadline .= ' ' . $request->schedule_time;
            } else {
                $deadline .= ' 00:00:00';
            }
            $announcement->deadline = $deadline;
        } else {
            $announcement->deadline = null;
        }

        $announcement->save();

        return redirect()->route('admin.dashboard')->with('success', 'Announcement posted!');
    }

    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:60',
            'content' => 'required|string|max:1000',
            'audience' => 'required|in:all,custom',
            'audience_students.*' => 'exists:users,id',
            'schedule_date' => 'nullable|date',
            'schedule_time' => 'nullable'
        ]);

        $announcement->title = $request->input('title');
        $announcement->content = $request->input('content');
        $announcement->audience = $request->input('audience');

        if ($request->audience === 'custom') {
            $announcement->audience_students = json_encode($request->audience_students);
        } else {
            $announcement->audience_students = null;
        }

        // Save deadline if scheduled
        if ($request->schedule && $request->schedule_date) {
            $deadline = $request->schedule_date;
            if ($request->schedule_time) {
                $deadline .= ' ' . $request->schedule_time;
            } else {
                $deadline .= ' 00:00:00';
            }
            $announcement->deadline = $deadline;
        } else {
            $announcement->deadline = null;
        }

        $announcement->save();

        return redirect()->back()->with('success', 'Announcement changed successfully!');
    }

    public function archive(Request $request)
    {
        // Redirect to admin dashboard with archive tab active
        return redirect()->route('admin.dashboard', ['archive' => 1]);
    }

    public function moveToArchive($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->archived = true;
        $announcement->save();

        // Redirect to dashboard with archive tab active
        return redirect()->route('admin.dashboard', ['archive' => 1])
            ->with('success', 'Announcement moved to archive!');
    }

    public function restore($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->archived = false;
        $announcement->save();

        return redirect()->route('admin.dashboard', ['archive' => 1])
            ->with('success', 'Announcement restored successfully!');
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return redirect()->route('admin.dashboard', ['archive' => 1])
            ->with('success', 'Announcement permanently deleted!');
    }
}
