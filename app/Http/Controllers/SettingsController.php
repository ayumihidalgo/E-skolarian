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

        // Ensure it's a valid base64 image
        if (!preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            return back()->with('error', 'Invalid image format.');
        }

        $extension = strtolower($type[1]);
        $imageData = base64_decode(substr($imageData, strpos($imageData, ',') + 1));
        $filename = Str::random(20) . '.' . $extension;
        $path = 'images/profiles/' . $filename;

        // Delete the old image if it exists
        if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
            Storage::disk('public')->delete($user->profile_pic);
        }

        Storage::disk('public')->put($path, $imageData);

        $user->profile_pic = $path;
        $user->save();

        return back()->with('success', 'Profile picture updated.');
    }

    /**
     * Change the password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password changed successfully.');
    }
}