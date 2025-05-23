<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Storage;


class SettingsController extends Controller
{
    /**
     * Show the settings page with current user data.
     */
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
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/',
                'different:current_password', // Ensure new password is different from current password
                function ($attribute, $value, $fail) {
                    if (trim($value) !== $value || preg_match('/^\s*$/', $value)) {
                        $fail('The new password cannot contain leading or trailing spaces or be all spaces.');
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

        return response()->json(['message' => 'Password changed successfully.']);
    }
}