<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProblemReport;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $query = ProblemReport::query();
        
        // Apply month filter
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        
        // Apply year filter
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }
        
        // Order by latest first
        $query->orderBy('created_at', 'desc');
        
        // Paginate results
        $reports = $query->paginate(8);
        
        return view('super-admin.super-admin-component.reports', compact('reports'));
    }
}