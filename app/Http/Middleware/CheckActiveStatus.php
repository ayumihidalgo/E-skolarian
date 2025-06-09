<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckActiveStatus
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->active == 0) {
            $role = Auth::user()->role ?? null; // Adjust if your role field is named differently

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($role === 'student') {
                return redirect()->route('student.login.form')->withErrors(['Your account has been deactivated.']);
            } elseif ($role === 'admin') {
                return redirect()->route('admin.login.form')->withErrors(['Your account has been deactivated.']);
            } else {
                return redirect()->route('login')->withErrors(['Your account has been deactivated.']);
            }
        }
        return $next($request);
    }
}
