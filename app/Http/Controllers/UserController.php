<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Mail\UserNotificationMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
        try {
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string',
            'role_name' => 'required|string',
            'organization_acronym' => 'nullable|string|required_if:role,student',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password ?? Str::random(10)), // Generate random password
            'role' => $request->role,
            'role_name' => $request->role_name,
            'organization_acronym' => $request->organization_acronym,
            'active' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully'
        ], 201);

    } catch (\Exception $e) {
        \Log::error('User creation failed: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to create user: ' . $e->getMessage()
        ], 500);
    }

    /**
     * Update the specified user in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
}
    public function update(Request $request, $id)
    {
        // Find the user
        $user = User::findOrFail($id);

        // Store original email to check if it changed
        $originalEmail = $user->email;

        // Validate the request data
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role_name' => 'required|string|max:255',
            'role' => 'required|in:admin,student',
        ]);

        // Update the user
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->role_name = $validated['role_name'];
        $user->role = $validated['role'];
        $user->save();

        // Send notification email to the user's updated email address
        try {
            Mail::to($user->email)->send(new UserNotificationMail($user, 'updated'));

            // If email was changed, send notification to the old email as well
            if ($originalEmail !== $user->email) {
                Mail::to($originalEmail)->send(new UserNotificationMail($user, 'updated'));
            }
        } catch (\Exception $e) {
            // Log the error but don't stop the process
            Log::error('Failed to send email notification: ' . $e->getMessage());
        }

        // Return a JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully!',
                'user' => $user
            ]);
        }

        // For normal form submissions, redirect with a success message
        return redirect()->route('super-admin.dashboard')->with('success', 'User updated successfully!');
    }
    public function deactivatedUsers(Request $request)
{
    $sortField = $request->query('sort', 'created_at');
    $sortDirection = $request->query('direction', 'desc');
    
    $deactivatedUsers = User::where('active', false)
        ->orderBy($sortField, $sortDirection)
        ->paginate(6);
        
    return view('super-admin.deactPage', [
        'users' => $deactivatedUsers,
        'sortField' => $sortField,
        'sortDirection' => $sortDirection
    ]);
}
public function checkEmail(Request $request)
{
    $exists = User::where('email', strtolower($request->email))->exists();
    return response()->json(['exists' => $exists]);
}
public function checkRoles()
{
    $restrictedRoles = ['Student Services', 'Academic Services', 'Administrative Services', 'Campus Director'];
    $existingRoles = User::whereIn('role_name', $restrictedRoles)
                        ->pluck('role_name')
                        ->unique()
                        ->values()
                        ->toArray();
    
    return response()->json(['existingRoles' => $existingRoles]);
}
public function checkUsername(Request $request)
{
    $username = strtolower($request->username);
    
    $exists = User::whereRaw('LOWER(username) = ?', [$username])->exists();
    
    return response()->json([
        'exists' => $exists
    ]);
}
}