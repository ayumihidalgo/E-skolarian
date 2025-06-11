<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;

class SuperAdminLoginController extends Controller
{
    // Lockout parameters
    protected $maxAttempts = 4;
    protected $decayMinutes = 5;

    public function showLoginForm()
    {
        return view('auth.SuperAdminlogin');
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
            'email' => ['required', 'email', 'max:50'],
            'password' => ['required', 'max:50'],
        ]);

        // Check if the user has exceeded the allowed number of login attempts
        if ($this->hasTooManyLoginAttempts($request)) {
            $seconds = $this->secondsRemainingForLockout($request);

            if ($seconds != 300) {
                $seconds = $this->decayMinutes * 60;
            }

            return back()->withErrors([
                'lockout_time' => $seconds,
            ]);
        }

        $remember = $request->has('remember');

        // Try to find the user by either email or recovery_email
        $user = \App\Models\User::where(function ($query) use ($request) {
            $query->where('email', $request->email)
                  ->orWhere('recovery_email', $request->email);
        })->where('role', 'super admin')
          ->where('active', 1)
          ->first();

        if ($user && Auth::attempt(['email' => $user->email,
        'password' => $request->password,
        'role' => 'super admin',
        'active' => 1],
        $remember)) {
            $this->clearLoginAttempts($request);
            $request->session()->regenerate();

            $authenticatedUser = $request->user();
            $currentSessionId = Session::getId();

            $authenticatedUser->last_session_id = $currentSessionId;
            $authenticatedUser->save();

            $request->session()->put('user_id', $authenticatedUser->id);
            $request->session()->put('user_role', $authenticatedUser->role);
            $request->session()->put('user_email', $authenticatedUser->email);

            return redirect('/super-admin/dashboard');
        }

        // Increment login attempts with 5 minutes decay time
        $this->incrementLoginAttempts($request);

        // Calculate remaining attempts
        $key = $this->throttleKey($request);
        $attempts = RateLimiter::attempts($key);
        $remaining = max(0, $this->maxAttempts - $attempts);

        return back()->withErrors([
            'email' => '*Incorrect email or password. You only have ' . ($remaining + 1) . ' remaining attempts before lockout.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/superadmin/login');
    }

    // Check if user has too many login attempts
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return RateLimiter::tooManyAttempts($this->throttleKey($request), $this->maxAttempts);
    }

    // Increment login attempts and set decay time (lockout duration)
    protected function incrementLoginAttempts(Request $request)
    {
        $key = $this->throttleKey($request);
        $attempts = RateLimiter::attempts($key);
        $decay = $this->decayMinutes * 60 + 5;

        if ($attempts + 1 >= $this->maxAttempts) {
            // Extend the decay time to full duration now
            RateLimiter::clear($key); // Reset attempts to start fresh
            for ($i = 0; $i < $this->maxAttempts; $i++) {
                RateLimiter::hit($key, $decay);
            }
        } else {
            // Normal increment
            RateLimiter::hit($key, $decay);
        }
    }

    // Clear login attempts after successful login
    protected function clearLoginAttempts(Request $request)
    {
        RateLimiter::clear($this->throttleKey($request));
    }

    // Unique key for rate limiting per IP
    protected function throttleKey(Request $request)
    {
        return 'login:superadmin:' . $request->ip();
    }

    // Remaining seconds for lockout
    protected function secondsRemainingForLockout(Request $request)
    {
        return RateLimiter::availableIn($this->throttleKey($request));
    }
}
