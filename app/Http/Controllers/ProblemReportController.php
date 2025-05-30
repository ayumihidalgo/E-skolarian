<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProblemReport;
use Illuminate\Support\Facades\Storage;

class ProblemReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
        'email' => 'required|email|max:100',
        'description' => 'required|string|max:1000',
        'screenshot' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048', // allow images, pdf, docx
        ]);


        $path = null;
        if ($request->hasFile('screenshot')) {
            $path = $request->file('screenshot')->store('screenshots', 'public');
        }

        ProblemReport::create([
            'email' => $request->email,
            'description' => $request->description,
            'screenshot_path' => $path,
        ]);

return response()->json(['message' => 'Report submitted']);
    }
}
