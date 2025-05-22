<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\UserNotificationMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    public function showDashboard(Request $request)
    {
        // Get sort parameters
        $sortField = $request->query('sort', 'created_at');
        $sortDirection = $request->query('direction', 'desc');

        // Valid sort fields to prevent SQL injection
        $validSortFields = ['username', 'role', 'role_name', 'created_at'];

        // Ensure sort field is valid
        if (!in_array($sortField, $validSortFields)) {
            $sortField = 'created_at';
        }

        // Fetch active users with ID > 1 (excluding super admin) with sorting and pagination
        $users = User::where('active', true)
            ->where('id', '>', 1) // Exclude super admin
            ->orderBy($sortField, $sortDirection)
            ->paginate(6); // Adjust number per page as needed

        // Return the view with the users data and sort parameters
        return view('super-admin.dashboard', compact('users', 'sortField', 'sortDirection'));
    }

    /**
     * Deactivate a user account
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivateUser(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'email' => 'required|email'
            ]);

            // Find user and deactivate
            $user = User::findOrFail($request->user_id);

            // Make sure email matches
            if ($user->email !== $request->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email does not match the user account'
                ]);
            }

            // Store email before deactivation for notification purposes
            $userEmail = $user->email;

            // Perform deactivation logic
            $user->active = false;
            $user->save();

            // Send deactivation notification email
            try {
                Mail::to($userEmail)->send(new UserNotificationMail($user, 'deactivated'));
            } catch (\Exception $e) {
                // Log the error but don't stop the process
                Log::error('Failed to send deactivation email notification: ' . $e->getMessage());
            }

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'User has been deactivated successfully'
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('User deactivation failed: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to deactivate user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reactivateUser(Request $request)
{
    try {
        // Log the incoming request data
        \Log::info('Reactivation request received:', $request->all());

        // Validate request data
        $validated = $request->validate([
            'user_id' => 'required',
            'email' => 'required|email'
        ]);

        // Find the user
        $user = User::where('id', $validated['user_id'])
                    ->where('email', $validated['email'])
                    ->first();

        if (!$user) {
            \Log::warning('User not found:', $validated);
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        if ($user->active) {
            return response()->json([
                'success' => false,
                'message' => 'User is already active'
            ], 400);
        }

        // Store email before reactivation for notification purposes
        $userEmail = $user->email;

        // Reactivate the user
        $user->active = true;
        $user->save();

         // Generate new random password
        $newPassword = Str::random(10);
        
        // Update user with new password and active status
        $user->password = Hash::make($newPassword);
        $user->active = true;
        $user->save();

        // Send reactivation notification email
        try {
            Mail::to($user->email)->send(new UserNotificationMail($user, 'reactivated', $newPassword));
            \Log::info('Reactivation email sent to: ' . $user->email);
        } catch (\Exception $e) {
            \Log::error('Failed to send reactivation email: ' . $e->getMessage());
        }

        \Log::info('User reactivated successfully:', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User successfully reactivated and notification sent'
        ]);

    } catch (\Exception $e) {
        \Log::error('Reactivation error:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while reactivating the user: ' . $e->getMessage()
        ], 500);
    }
}

}