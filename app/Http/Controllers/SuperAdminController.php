<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 

class SuperAdminController extends Controller
{
    public function showDashboard()
    {
        // Fetch all users from the database
        $users = User::orderBy('created_at', 'desc')->paginate(6); // This returns a paginator instance
 

        return view('super-admin.dashboard', compact('users'));
    }
    
}