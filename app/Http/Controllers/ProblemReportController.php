<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProblemReport;
use App\LogsActivity; // Import LogsActivity trait
use Illuminate\Support\Facades\Storage;

class ProblemReportController extends Controller
{
    use LogsActivity;

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

        // Validate and create the report first
        $report = ProblemReport::create([
            'email' => $request->email,
            'description' => $request->description,
            'file_path' => $path,  // Save the file path in DB under 'file_path'
        ]);

        // Then log the activity
        $this->logActivity(
            'Submitted Problem Report',
            'RPT-00' . $report->id,
            "{$report->email} submitted a problem report."
        );

        return response()->json(['message' => 'Report submitted']);
    }
}
