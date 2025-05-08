<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


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

    /**
     * Update the profile picture.
     */
    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_pic')) {
            $profileFile = $request->profileFile('profile_pic');
            $filename = time() . '_' . $profileFile->getClientOriginalName();
            $profileFile->move(public_path('avatar'), $filename);

            // Optionally delete the old profile picture if exists
            if ($user->profile_pic && file_exists(public_path('avatar' . $user->profile_pic))) {
                unlink(public_path('avatar' . $user->profile_pic));
            }

            $user->profile_pic = $filename;
            $user->save();
        }

        return redirect()->back()->with('success', 'Profile picture updated successfully.');
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