<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\LogsActivity;
use Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\RecoveryCodeMail;


class SettingsController extends Controller
{
    /**
     * Show the settings page with current user data.
     */
    use LogsActivity;
    public function viewSettings()
    {
        $user = Auth::user();
        return view('student.studentSettings', compact('user'));
    }
    public function viewAdminSettings()
    {
        $user = Auth::user();
        return view('admin.adminSettings', compact('user'));
    }

    /**
     * Update the profile picture.
     */
    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_image_base64' => 'required|string',
        ]);

        $user = auth()->user();
        $imageData = $request->input('profile_image_base64');

        // Validate base64 format and extract type
        if (!preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            return back()->with('error', 'Invalid image format.');
        }

        $extension = strtolower($type[1]);
        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return back()->with('error', 'Only JPG, JPEG, and PNG images are allowed.');
        }

        $imageData = base64_decode(substr($imageData, strpos($imageData, ',') + 1));

        // Check file size (30MB = 30 * 1024 * 1024 bytes)
        if (strlen($imageData) > 30 * 1024 * 1024) {
            return back()->with('error', 'Image size must not exceed 30MB.');
        }

        $filename = Str::random(20) . '.' . $extension;
        $path = 'images/profiles/' . $filename;

        // Delete the old image if it exists
        if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
            Storage::disk('public')->delete($user->profile_pic);
        }

        Storage::disk('public')->put($path, $imageData);

        $user->profile_pic = $path;
        $user->save();

        $this->logActivity(
            'Updated',
            'Profile Picture',
            "{$user->username} updated their profile picture."
        );

        return back()->with('success', 'Your profile picture has been updated successfully.');
    }
    public function removeProfilePicture(Request $request)
    {

        $user = auth()->user();

        // Delete the old image if it exists
        if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
            Storage::disk('public')->delete($user->profile_pic);
            $user->profile_pic = null;
            $user->save();
        }
        $this->logActivity(
            'Removed',
            'Profile Picture',
            "{$user->username} removed their profile picture."
        );
        return back()->with('success', 'Your profile picture has been removed successfully.');
    }
    /**
     * Change the password.
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => [
                'required',
                'min:8',
                'max:40',
                'confirmed',
                'different:current_password', // Ensure new password is different from current password
                // No spaces allowed, at least one number, one lowercase, one uppercase, one special character
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])([A-Za-z\d@$!%*?&#]{8,40})$/',
                function ($attribute, $value, $fail) {
                    if (preg_match('/\s/', $value)) {
                        $fail('The new password cannot contain spaces.');
                    }
                },
            ],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['errors' => ['current_password' => ['Current password is incorrect.']]], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->password_changed_at = now();
        $user->save();

        $this->logActivity(
            'Changed',
            'Password',
            "{$user->username} changed their password."
        );
        return response()->json(['message' => 'Password changed successfully.']);
    }
    public function sendRecoveryCode(Request $request)
    {
        $request->validate([
            'recovery_email' => 'required|email',
        ], [
            'recovery_email.required' => 'The recovery email is required.',
            'recovery_email.email' => 'Please enter a valid email address.',
        ]);

        $code = rand(100000, 999999);
        session(['recovery_code' => $code, 'pending_recovery_email' => $request->input('recovery_email')]); // Store code and email in session

        \Log::info("Sent code $code to {$request->recovery_email}");
        // Send the recovery code via email
        Mail::to($request->input('recovery_email'))->send(new RecoveryCodeMail($code));
        \Log::info("Recovery code sent to {$request->recovery_email}");
        // return proper response
        return response()->json(['success' => true]);
    }

    public function verifyRecoveryCode(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ], [
            'code.required' => 'The verification code is required.',
            'code.digits' => 'The verification code must be 6 digits.',
        ]);

        $code = $request->input('code');
        $storedCode = session('recovery_code'); // retrieve session code
        $pendingEmail = session('pending_recovery_email'); // retrieve pending email

        \Log::info("Verifying code", ['entered' => $code, 'stored' => $storedCode]);
        if ($code == $storedCode && $pendingEmail) {
            $user = Auth::user();
            $user->recovery_email = $pendingEmail;
            $user->save();

            // Optionally clear session values
            session()->forget(['recovery_code', 'pending_recovery_email']);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid verification code']);
    }
    public function removeRecoveryEmail(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ], [
            'code.required' => 'The verification code is required.',
            'code.digits' => 'The verification code must be 6 digits.',
        ]);

        $code = $request->input('code');
        $storedCode = session('recovery_code');
        $user = Auth::user();

        if ($code == $storedCode && $user->recovery_email) {
            $removedEmail = $user->recovery_email;
            $user->recovery_email = null;
            $user->save();

            // Optionally clear session value
            session()->forget('recovery_code');

            // Notify the email
            Mail::to($removedEmail)->send(new \App\Mail\RecoveryEmailRemovedMail($user));

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid verification code']);
    }
}