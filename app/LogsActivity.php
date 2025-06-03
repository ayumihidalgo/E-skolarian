<?php

namespace App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Models\ActivityLog;
trait LogsActivity
{
    public function logActivity($action, $target, $description, $user = null)
{
    if (!$user) {
        $user = Auth::user();
    }

    ActivityLog::create([
        'user_id' => $user?->id,
        'user_name' => $user?->username,
        'user_role_name' => $user?->role_name,
        'role' => $user?->role,
        'action' => $action,
        'target' => $target,
        'description' => $description,
        'ip_address' => Request::ip(),
    ]);
}

}
