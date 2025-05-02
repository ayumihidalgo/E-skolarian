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
    // Validate the incoming request
    $validated = $request->validate([
        'username' => 'required|string|max:255|unique:users,username',
        'email' => 'required|email|max:255|unique:users,email',
        'role_name' => 'required|string',
    ]);

    // Determine role based on role_name
    $studentRoles = ['Academic Organization', 'Non-Academic Organization'];
    $role = in_array($validated['role_name'], $studentRoles) ? 'student' : 'admin';

    // Create the user
    $user = User::create([
        'username' => $validated['username'],
        'email' => $validated['email'],
        'role' => $role,
        'role_name' => $validated['role_name'],
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
