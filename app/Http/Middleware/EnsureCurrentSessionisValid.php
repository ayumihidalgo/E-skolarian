<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EnsureCurrentSessionIsValid
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = $request->user();
            $currentSessionId = Session::getId();

            if ($user->last_session_id && $user->last_session_id !== $currentSessionId) {
                $redirectRoute = 'login';
                if ($user->role === 'admin') {
                    $redirectRoute = 'admin.login.form';
                } elseif ($user->role === 'super admin') {
                    $redirectRoute = 'superadmin.login.form';
                } elseif ($user->role === 'student') {
                    $redirectRoute = 'student.login.form';
                }

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route($redirectRoute)->withErrors([
                    'message' => 'This account has been logged in from another device.',
                ]);
            }
        }

        return $next($request);
    }
}
