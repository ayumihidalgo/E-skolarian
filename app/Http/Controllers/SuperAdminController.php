<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

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

        // Fetch users with sorting and pagination
        $users = User::orderBy($sortField, $sortDirection)
                     ->paginate(6); // Adjust number per page as needed

        // Return the view with the users data and sort parameters
        return view('super-admin.dashboard', compact('users', 'sortField', 'sortDirection'));
    }
    

}
