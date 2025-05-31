<?php

namespace App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Models\ActivityLog;
trait LogsActivity
{
    public function logActivity($action, $target, $description)
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->username,
            'user_role_name' => $user?->role_name, // adapt based on your user model
            'role' => $user?->role,      // optional, if you have separate role column
            'action' => $action,
            'target' => $target,
            'description' => $description,
            'ip_address' => Request::ip(),
        ]);
    }
}
