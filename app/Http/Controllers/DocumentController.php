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
use App\LogsActivity;

class DocumentController extends Controller
{
    use LogsActivity;
    
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
                'subject' => 'required|string|max:255',
                'type' => 'required|string|max:50',
                'overview' => 'required|string|max:255',
                'academic_year' => 'required|string',
                'venue' => 'nullable|string|max:100|required_if:type,Event Proposal',
                'proposed_date_time' => 'nullable|datetime_local|required_if:type,Event Proposal',
                'hours' => 'nullable|integer|required_if:type,Event Proposal',
                'attendees' => 'nullable|string|max:50|required_if:type,Event Proposal',
                'attendees_range' => 'nullable|required_if:type,Event Proposal|in:10-50,50-100,100-250,250-500,Above 500',
                'fees' => 'nullable|float|required_if:type,Event Proposal',
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
                'overview' => $validated['overview'],
                'type' => $validated['type'],
                'academic_year' => $validated['academic_year'],
                'venue' => $validated['venue'],
                'proposed_date_time' => $validated['proposed_date_time'],
                'hours' => $validated['hours'],
                'attendees' => $validated['attendees'],
                'attendees_range' => $validated['attendees_range'],
                'fees' => $validated['fees']
            ]);

            // Defines the control tag of the submitted documents, Example: ELITE-0001
            $acronym = Auth::user()->organization_acronym ?? 'DOC';
            $document->control_tag = $acronym . '-' . str_pad($document->id, 4, '0', STR_PAD_LEFT);

            // Store the validated data with the generated control tag
            $document->save();

            // Handle the uploaded file
            $files = $request->file('file_upload');

            $version = 1;   // NOTE: Version 1 by default
            foreach ($files as $file) {
                $originalName = $file->getClientOriginalName();
                // Optionally, you can prepend a unique ID or timestamp to avoid overwriting files with the same name
                $filePath = $file->storeAs('documents', $originalName, 'public');

                DocumentVersion::create([
                    'document_id' => $document->id,
                    'uploaded_by' => Auth::id(),
                    'version' => $version,
                    'document_url' => $filePath,
                    'original_name' => $originalName,
                    'comments' => $request->input('comments'),
                    'submitted_at' => now(),
                ]);
            }

            // Add these lines to dispatch the event
            Log::info('Dispatching DocumentSubmitted event for document ID: ' . $document->id);
            event(new DocumentSubmitted($document));
                        
            $this->logActivity(
                'Submitted',
                "Document #{$document->id}",
                "{$document->user_id} submitted a document titled '{$document->title}'."
            );

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
