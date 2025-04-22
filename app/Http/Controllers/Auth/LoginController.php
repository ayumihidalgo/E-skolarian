<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Manually trim the inputs to remove leading and trailing spaces
        $request->merge([
            'email' => trim($request->input('email')),
            'password' => trim($request->input('password')),
        ]);

        // Validation rules
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:50'], // Email format validation and trimming
            'password' => ['required', 'min:6'], // Password should have at least 6 characters
            'role' => ['required', 'in:student,admin,super admin'], // Role validation for student, admin, or super admin
        ]);

        // Check if the user has exceeded the allowed number of login attempts
        if ($this->hasTooManyLoginAttempts($request)) {
            $seconds = $this->secondsRemainingForLockout($request);
            return back()->withErrors([
                'lockout_time' => $seconds,
            ]);
        }

        // Attempt to authenticate the user
        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            // Allow both admin and super admin to log in when admin is selected
            'role' => $credentials['role'] === 'admin' ? ['admin', 'super admin'] : $credentials['role'],
        ], $request->filled('remember'))) {
            // Regenerate the session if login is successful
            $request->session()->regenerate();

            // Redirect based on the role
            if (Auth::user()->role === 'student') {
                return redirect()->intended('student/dashboard'); // Redirect to student dashboard
            } elseif (Auth::user()->role === 'admin') {
                return redirect()->intended('admin/dashboard'); // Redirect to admin dashboard
            } elseif (Auth::user()->role === 'super admin') {
                return redirect()->intended('super-admin/dashboard'); // Redirect to super admin dashboard
            }
        }

        // Increment the login attempts
        $this->incrementLoginAttempts($request);

        // Return back with errors if credentials are invalid
        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ]);
    }

    // Logout function
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // Check if user has too many login attempts
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return RateLimiter::tooManyAttempts($this->throttleKey($request), 4);
    }

    // Increment the login attempts counter
    protected function incrementLoginAttempts(Request $request)
    {
        // Set the lockout time to 60 seconds (1 minute)
        RateLimiter::hit($this->throttleKey($request), 60);
    }

    // Get the throttle key for the request
    protected function throttleKey(Request $request)
    {
        return 'login:' . $request->ip();
    }

    // Get the remaining lockout time (in seconds) for the user
    protected function secondsRemainingForLockout(Request $request)
    {
        return RateLimiter::availableIn($this->throttleKey($request));
    }
}
