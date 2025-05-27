<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class AdminPasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('auth.admin-forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:admin', // Only admin allowed
        ]);

        // Always use admin role
        $role = 'admin';

        // Check if the email exists in the users table and is admin
        $user = User::where('email', $request->email)
            ->where('active', 1)
            ->where('role', 'admin')
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We can\'t find an admin with that email address.']);
        }

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function edit($token)
    {
        return view('auth.admin-reset-password', ['token' => $token]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&#]/',
            ],
        ]);

        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (
            !$tokenRecord ||
            !Hash::check($request->token, $tokenRecord->token) ||
            Carbon::parse($tokenRecord->created_at)->addMinutes(15)->isPast()
        ) {
            return back()->withErrors(['token' => 'Invalid or expired token. Try to request a new password reset link.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        // Only allow admin role
        if ($user->role !== 'admin') {
            return back()->withErrors(['email' => 'User role not allowed to reset password.']);
        }

        // Check if new password is same as old
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'The new password must be different from the current password.',
            ]);
        }

        $user->password = bcrypt($request->password);
        $user->password_changed_at = now();
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Always redirect as admin
        return redirect()->route('admin.password.reset.confirmation');
    }
    }
