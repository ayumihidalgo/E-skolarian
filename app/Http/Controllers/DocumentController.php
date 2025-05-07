<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;

class DocumentController extends Controller
{
    public function create()
    {
        // Shows the admin users in the receiver dropdown list in the form
        $adminUsers = \App\Models\User::where('role', 'admin')
            ->select('id', 'username', 'role_name')
            ->get();

        return view('student.submit-documents', compact('adminUsers'));
    }

    public function store(Request $request)
    {
        try {
            Log::info('Store method hit', $request->all());
            Log::info('Document submission attempt', ['user_id' => Auth::id()]);

            // Validate the incoming request
            $validated = $request->validate([
                'received_by' => 'required|exists:users,id',
                'subject' => 'required|string|max:50',
                'type' => 'required|in:Event Proposal,General Plan of Activities,Calendar of Activities,Accomplishment Report,Constitution and By-Laws,Request Letter,Off Campus,Petition and Concern',
                'summary' => 'required|string|max:255',
                'eventStartDate' => 'nullable|date|required_if:type,Event Proposal',
                'eventEndDate' => 'nullable|date|after_or_equal:eventStartDate|required_if:type,Event Proposal',
                'event-title' => 'nullable|string|max:50|required_if:type,Event Proposal',
                'event-desc' => 'nullable|string|max:255|required_if:type,Event Proposal',
                'file_upload' => 'required|file|mimes:pdf,doc,docx|max:5120',
            ]);

            $validated['user_id'] = Auth::id();

            // Check for file and store it safely if available
            if ($request->hasFile('file_upload')) {
                try {
                    $file = $request->file('file_upload');
                    $validated['file_path'] = $file->store('documents', 'public');
                } catch (\Exception $e) {
                    return back()->withErrors(['file_upload' => 'Failed to upload file. Please try again.']);
                }
            }

            // Save first to get the auto-increment ID
            $document = Document::create($validated);

            // Format: DOC-0001 ("DOC" IS USED FOR A MOMENT, ORGANIZATION NAME OF USER IS NOT YET INCLUDED IN THE FORMAT)
            $document->control_tag = 'DOC-' . str_pad($document->id, 4, '0', STR_PAD_LEFT);

            // Store the validated data in the database.
            $document->save();

            // If this is an Event Proposal, create a corresponding event
            if ($validated['type'] === 'Event Proposal') {
                Event::create([
                    'title' => $validated['event-title'],
                    'description' => $validated['event-desc'],
                    'start_date' => $validated['eventStartDate'],
                    'end_date' => $validated['eventEndDate'],
                    'created_by' => Auth::id(),
                ]);
            }

            return back()->with('success', 'Document submitted successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            // Log the actual error message for debugging
            Log::error('Document submission failed: ' . $e->getMessage());

            return back()->with('error', 'Something went wrong while submitting the document. Please try again.');
        }
    }
}
