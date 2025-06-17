<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProblemReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

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
    
    public function serveProfileImage($filename)
    {
        $path = 'public/images/profiles/' . $filename;

        if (!Storage::disk('public')->exists('images/profiles/' . $filename)) {
            abort(404);
        }

        $file = Storage::disk('public')->get('images/profiles/' . $filename);
        $mime = Storage::disk('public')->mimeType('images/profiles/' . $filename);

        return new Response($file, 200, ['Content-Type' => $mime]);
    }
}