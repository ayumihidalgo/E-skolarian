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
use Illuminate\Support\Facades\Validator;
use App\LogsActivity;

class UserController extends Controller
{
    /**
     * Store a newly created user in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    use LogsActivity;
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

        // Generate random password
        $password = Str::random(10);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role' => $request->role,
            'role_name' => $request->role_name,
            'organization_acronym' => $request->organization_acronym,
            'active' => true
        ]);

        // Send email notification
        try {
            Mail::to($user->email)->send(new UserNotificationMail($user, 'created', $password));
            \Log::info('Account creation email sent to: ' . $user->email);
        } catch (\Exception $e) {
            \Log::error('Failed to send account creation email: ' . $e->getMessage());
        }

        $this->logActivity(
            'Created',
            'User',
            ($user->role === 'admin' ? 
            "{$user->role_name} account has been created." : 
            "{$user->organization_acronym} account has been created."
        )
        );

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
        try {
            $user = User::findOrFail($id);
            
            // Validate request - Add recovery_email validation
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users,email,' . $id,
                'recovery_email' => 'nullable|email|unique:users,recovery_email,' . $id, // Add this line
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Check if recovery email is same as primary email
            if ($request->recovery_email && $request->recovery_email === $request->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Recovery email cannot be the same as primary email'
                ], 422);
            }

            // Store original values for email notification comparison
            $originalEmail = $user->email;
            $originalRecoveryEmail = $user->recovery_email;

            // Update user - Add recovery_email update
            $user->email = $request->email;
            $user->recovery_email = $request->recovery_email; // Add this line
            $user->save();

            // Send notification email if email or recovery email changed
            if ($originalEmail !== $user->email || $originalRecoveryEmail !== $user->recovery_email) {
                try {
                    Mail::to($user->email)->send(new UserNotificationMail($user, 'updated'));
                    
                    // If recovery email was added/changed, also send to recovery email
                    if ($user->recovery_email && $user->recovery_email !== $originalRecoveryEmail) {
                        Mail::to($user->recovery_email)->send(new UserNotificationMail($user, 'updated'));
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send update notification email: ' . $e->getMessage());
                }
            }

            $this->logActivity(
                'Updated',
                'User',
                ($user->role === 'admin' ? 
                "{$user->role_name} has been updated." : 
                "{$user->organization_acronym} has been updated."
                )
            );

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
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
    $restrictedRoles = ['Office of the Student Services', 'Office of the Academic Services', 'Office of the Administrative Services', 'Office of the Campus Director'];
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
public function checkOrganizations()
{
    try {
        $existingOrganizations = User::where('role', 'student')
            ->pluck('username')  // Using username column since it stores organization names
            ->map(function($name) {
                return strtolower($name);
            })
            ->unique()
            ->values()
            ->toArray();

        return response()->json([
            'success' => true,
            'existingOrganizations' => $existingOrganizations
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch organizations'
        ], 500);
    }
}
}
