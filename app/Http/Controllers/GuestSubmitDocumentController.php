<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Mail\GuestOtpMail;

class GuestSubmitDocumentController extends Controller
{
    // Step 1: Show email input form
    public function showLoginForm()
    {
        return view('guest.guestLogin');
    }

    // Step 2: Validate email and send OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'ends_with:@iskolarngbayan.pup.edu.ph']
        ], [
            'email.required' => 'Please enter a valid PUP Webmail.',
            'email.email' => 'Please enter a valid PUP Webmail.',
            'email.ends_with' => 'Please enter a valid PUP Webmail.',
        ]);

        $otp = random_int(100000, 999999);

        Session::put('guest_webmail', $request->email);
        Session::put('guest_otp', $otp);
        Session::put('otp_expires_at', now()->addMinutes(10));

        // Mail logic
        Mail::to($request->email)->send(new GuestOtpMail($otp));

        return redirect()->route('guest.verifyForm')->with('status', 'OTP sent to your email.');
    }

    // Step 3: Show OTP verification form
    public function showOtpForm()
    {
        if (!Session::has('guest_webmail')) {
            return redirect()->route('guestLogin');
        }

        return view('guest.guestVerifyLogin');
    }

    // Step 4: Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'array', 'size:6'],
            'otp.*' => ['digits:1']
        ]);

        $enteredOtp = preg_replace('/\D/', '', implode('', $request->otp));
        $storedOtp = Session::get('guest_otp');
        $expiresAt = Session::get('otp_expires_at');

        if (!$storedOtp || now()->gt($expiresAt)) {
            return back()->withErrors(['otp' => 'OTP expired. Please request a new one.']);
        }

        if ($enteredOtp != $storedOtp) {
            return back()->withErrors(['otp' => 'Please enter a valid OTP.']);
        }

        Session::put('guest_verified', true);
        return redirect()->route('guest.submissionForm');
    }

    public function resendOtp(Request $request)
    {
        if (!Session::has('guest_webmail')) {
            return redirect()->route('guestLogin');
        }

        $email = Session::get('guest_webmail');

        if (!$email) {
            return redirect()->route('guestLogin')->withErrors(['email' => 'Session expired. Please log in again.']);
        }

        $otp = rand(100000, 999999);

        Session::put('guest_otp', $otp);
        Session::put('otp_expires_at', now()->addMinutes(10));

        Mail::to($email)->send(new GuestOtpMail($otp));

        return back()->with('status', 'A new OTP has been sent to your email.');
    }

    // Step 5: Show document submission form
    public function showSubmissionForm()
    {
        if (!Session::get('guest_verified')) {
            return redirect()->route('guestLogin');
        }

        // Fetch admin users for dropdown
        $adminUsers = \App\Models\User::where('role', 'admin')
            ->where('active', 1)
            ->select('id', 'username', 'role_name')
            ->get();

        // Return view with adminUsers
        return view('guest.guestSubmissionForm', compact('adminUsers'));
    }

    // Step 6: Show document submission success
    public function showSubmissionSuccess()
    {
        if (!Session::get('guest_verified')) {
            return redirect()->route('guestLogin');
        }

        if (!Session::get('guest_submitted')) {
            return redirect()->route('guest.submissionForm');
        }
        
        return view('guest.guestSubmissionSuccess');
    }

    // Back to Login Button Functionality
    public function logout(Request $request)
    {
        Session::forget('guest_webmail');
        Session::forget('guest_otp');
        Session::forget('otp_expires_at');
        Session::forget('guest_verified');        
        Session::flush();   // Clears all session data

        return redirect()->route('landing');    // Sends user to landing page
    }
}
