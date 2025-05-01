<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 

class SuperAdminController extends Controller
{
    public function showDashboard(Request $request)
    {
        $sortField = $request->query('sort', 'created_at'); // default sort by created_at
        $sortDirection = $request->query('direction', 'desc'); // default direction desc

        // Fetch all users from the database
        $users = User::orderBy($sortField, $sortDirection)
                    ->paginate(6)
                    ->withQueryString(); // This preserves the query parameters

        return view('super-admin.dashboard', [
            'users' => $users,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection
        ]);
    }
    
}