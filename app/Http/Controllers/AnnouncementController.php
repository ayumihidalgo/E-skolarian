<?php

namespace App\Http\Controllers;

use App\LogsActivity;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    use LogsActivity;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role === 'admin' || auth()->user()->role === 'super admin') {
                return $next($request);
            }
            return abort(403);
        });
    }

    private function getRedirectRoute()
    {
        return auth()->user()->role === 'super admin'
            ? 'super-admin.dashboard'
            : 'admin.dashboard';
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:60',
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

        if ($request->schedule && $request->schedule_date) {
            $deadline = $request->schedule_date;
            $deadline .= $request->schedule_time ? ' ' . $request->schedule_time : ' 00:00:00';
            $announcement->deadline = $deadline;
        } else {
            $announcement->deadline = null;
        }

        $announcement->save();

        \Log::info('Announcement created', [
            'title' => $announcement->title,
            'content' => $announcement->content,
            'user_id' => $announcement->user_id,
            'audience' => $announcement->audience,
            'audience_students' => $announcement->audience_students,
            'deadline' => $announcement->deadline,
        ]);

        event(new \App\Events\NewAnnouncement($announcement, $announcement->audience, $announcement->audience_students));
        $user = auth()->user();

        $this->logActivity(
            'Created',
            'Announcement',
            ($user->role === 'admin'
                ? "{$user->role_name} posted an announcement: {$announcement->title}"
                : "{$user->organization_acronym} posted an announcement: {$announcement->title}"
            )
        );
        return redirect()->route($this->getRedirectRoute())->with('success', 'Announcement posted!');
    }

    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        // Only the owner (admin) or a super admin can edit
        if (
            auth()->user()->role !== 'super admin' &&
            $announcement->user_id !== auth()->id()
        ) {
            abort(403, 'You are not authorized to edit this announcement.');
        }

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

        if ($request->schedule && $request->schedule_date) {
            $deadline = $request->schedule_date;
            $deadline .= $request->schedule_time ? ' ' . $request->schedule_time : ' 00:00:00';
            $announcement->deadline = $deadline;
        } else {
            $announcement->deadline = null;
        }

        $announcement->save();
        $user = auth()->user();

        $this->logActivity(
            'Updated',
            'Announcement',
            ($user->role === 'admin'
                ? "{$user->role_name} updated an announcement: {$announcement->title}"
                : "{$user->organization_acronym} updated an announcement: {$announcement->title}"
            )
        );
        return redirect()->route($this->getRedirectRoute())->with('success', 'Announcement changed successfully!');
    }

    public function archive(Request $request)
    {
        return redirect()->route($this->getRedirectRoute(), ['archive' => 1]);
    }

    public function moveToArchive($id)
    {
        $announcement = Announcement::findOrFail($id);

        // Only the owner (admin) or a super admin can archive
        if (
            auth()->user()->role !== 'super admin' &&
            $announcement->user_id !== auth()->id()
        ) {
            abort(403, 'You are not authorized to archive this announcement.');
        }

        $announcement->archived = true;
        $announcement->save();
        $user = auth()->user();

        $this->logActivity(
            'Archived',
            'Announcement',
            ($user->role === 'admin'
                ? "{$user->role_name} archived an announcement: {$announcement->title}"
                : "{$user->organization_acronym} archived an announcement: {$announcement->title}"
            )
        );
        return redirect()->route($this->getRedirectRoute(), ['archive' => 1])
            ->with('success', 'Announcement moved to archive!');
    }

    public function restore($id)
    {
        $announcement = Announcement::findOrFail($id);

        // Only the owner (admin) or a super admin can restore
        if (
            auth()->user()->role !== 'super admin' &&
            $announcement->user_id !== auth()->id()
        ) {
            abort(403, 'You are not authorized to restore this announcement.');
        }

        $announcement->archived = false;
        $announcement->save();
        $user = auth()->user();

        $this->logActivity(
            'Restored',
            'Announcement',
            ($user->role === 'admin'
                ? "{$user->role_name} restored an announcement: {$announcement->title}"
                : "{$user->organization_acronym} restored an announcement: {$announcement->title}"
            )
        );
        return redirect()->route($this->getRedirectRoute(), ['archive' => 1])
            ->with('success', 'Announcement restored successfully!');
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);

        // Only the owner (admin) or a super admin can delete
        if (
            auth()->user()->role !== 'super admin' &&
            $announcement->user_id !== auth()->id()
        ) {
            abort(403, 'You are not authorized to delete this announcement.');
        }

        $announcement->delete();
        $user = auth()->user();

        $this->logActivity(
            'Deleted',
            'Announcement',
            ($user->role === 'admin'
                ? "{$user->role_name} permanently deleted an announcement: {$announcement->title}"
                : "{$user->organization_acronym} permanently deleted an announcement: {$announcement->title}"
            )
        );
        return redirect()->route($this->getRedirectRoute(), ['archive' => 1])
            ->with('success', 'Announcement permanently deleted!');
    }
}
