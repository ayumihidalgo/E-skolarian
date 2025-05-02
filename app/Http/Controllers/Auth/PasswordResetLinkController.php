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

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:student,admin', // Validate role input
        ]);

        // Retrieve the role from the request
        $role = $request->input('role');

        // Check if the email exists in the users table and matches the role
        $user = User::where('email', $request->email)
            ->where(function ($query) use ($role) {
                if ($role === 'student') {
                    $query->where('role', 'student');
                } elseif ($role === 'admin') {
                    $query->whereIn('role', ['admin', 'super admin']);
                }
            })
            ->first();

        if (!$user) {
            $errorMessage = $role === 'student'
                ? 'We can\'t find a student with that email address.'
                : 'We can\'t find an admin with that email address.';
            return back()->withErrors(['email' => $errorMessage]);
        }

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function edit($token)
    {
        return view('auth.reset-password', ['token' => $token]);
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

        if (!$tokenRecord || !Hash::check($request->token, $tokenRecord->token)) {
            return back()->withErrors(['token' => 'Invalid or expired token.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        // Check if user role is allowed to reset password
        if (!in_array($user->role, ['student', 'admin', 'super admin'])) {
            return back()->withErrors(['email' => 'User role not allowed to reset password.']);
        }

        // Check if new password is same as old
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'The new password must be different from the current password.',
            ]);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Smart: set role for redirection based on user's real role
        $role = $user->role === 'student' ? 'student' : 'admin';

        return redirect()->route('password.reset.confirmation', ['role' => $role]);
    }
    }
