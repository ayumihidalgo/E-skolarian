<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\UserNotificationMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        // Fetch only active users with sorting and pagination
        $users = User::where('active', true)
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


}