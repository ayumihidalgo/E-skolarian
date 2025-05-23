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
use App\Events\DocumentSubmitted;
use App\Models\DocumentVersion;

class DocumentController extends Controller
{
    public function create()
    {
        // Shows the admin users in the receiver dropdown list in the form
        $adminUsers = \App\Models\User::where('role', 'admin')
            ->where('active', 1)
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
                'file_upload' => 'required|array|max:30',
                'file_upload.*' => 'file|mimes:pdf,doc,docx|max:5120',
                'comments' => 'nullable|string|max:500',
            ]);

            $validated['user_id'] = Auth::id();

            // Save first to get the auto-increment ID
            $document = Document::create([
                'user_id' => $validated['user_id'],
                'received_by' => $validated['received_by'],
                'subject' => $validated['subject'],
                'summary' => $validated['summary'],
                'type' => $validated['type'],
            ]);

            // Format: DOC-0001 ("DOC" IS USED FOR A MOMENT, ORGANIZATION NAME OF USER IS NOT YET INCLUDED IN THE FORMAT)
            $document->control_tag = 'DOC-' . str_pad($document->id, 4, '0', STR_PAD_LEFT);

            // Store the validated data with the generated control tag
            $document->save();

            // Handle the uploaded file
            $files = $request->file('file_upload');

            $version = 1;   // NOTE: Version control not implemented yet
            foreach ($files as $file) {
                $originalName = $file->getClientOriginalName();
                // Optionally, you can prepend a unique ID or timestamp to avoid overwriting files with the same name
                $filePath = $file->storeAs('documents', $originalName, 'public');

                DocumentVersion::create([
                    'document_id' => $document->id,
                    'uploaded_by' => Auth::id(),
                    'version' => $version,
                    'file_path' => $filePath,
                    'original_name' => $originalName,
                    'comments' => $request->input('comments'),
                    'submitted_at' => now(),
                ]);
            }

            // Add these lines to dispatch the event
            Log::info('Dispatching DocumentSubmitted event for document ID: ' . $document->id);
            event(new DocumentSubmitted($document)); // Add this line

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
