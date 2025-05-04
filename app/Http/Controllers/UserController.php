<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Store a newly created user in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
{
    // Validate the request data
    $validated = $request->validate([
        'username' => 'required|string|max:255|unique:users,username',
        'email' => 'required|email|max:255|unique:users,email',
        'role' => 'required|in:admin,student', // Changed from 'admin,organization' to match frontend data-role values
        'role_name' => 'required|string|max:255', // Add validation for role_name
    ]);

    // Create the user
    $user = User::create([
        'username' => $validated['username'],
        'email' => $validated['email'],
        'role' => $validated['role'], // This is the actual role (admin or student)
        'role_name' => $validated['role_name'], // This is the displayed role name
        'password' => Hash::make('defaultpassword'), // Set a default password
    ]);

    // Return a JSON response for AJAX requests
    if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'User added successfully!',
            'user' => $user
        ]);
    }

    // For normal form submissions, redirect with a success message
    return redirect()->route('super-admin.dashboard')->with('success', 'User added successfully!');
}
}