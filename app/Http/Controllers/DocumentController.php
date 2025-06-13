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
use Illuminate\Support\Facades\Session;

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

            $validated = $request->validate([
                'received_by' => 'required|exists:users,id',
                'subject' => 'required|string|max:255',
                'type' => 'required|string|max:50',
                'overview' => 'required|string|max:1000',
                'academic_year' => 'required|string',
                'venue' => 'nullable|string|max:100|required_if:type,Event Proposal',
                'proposed_date_time' => 'nullable|date|required_if:type,Event Proposal',
                'hours' => 'nullable|integer|min:1|max:10|required_if:type,Event Proposal',
                'attendees' => 'nullable|string|max:50|required_if:type,Event Proposal',
                'attendees_range' => 'nullable|required_if:type,Event Proposal|in:10-50,50-100,100-250,250-500,Above 500',
                'fees' => 'nullable|numeric|required_if:type,Event Proposal',
                'file_upload' => 'required|array|max:30',
                'file_upload.*' => 'file|mimes:pdf,doc,docx|max:5120',
                'comments' => 'nullable|string|max:500',
            ]);

            // Determine if guest or authenticated
            $isGuest = !Auth::check();
            $guestWebmail = $isGuest ? Session::get('guest_webmail') : null;
            $userId = $isGuest ? null : Auth::id();

            // Create document
            $document = Document::create([
                'user_id' => $userId,
                'guest_webmail' => $guestWebmail,
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
                'fees' => $validated['fees'],
            ]);

            // Set control tag
            $acronym = $isGuest ? 'GUEST' : (Auth::user()->organization_acronym ?? 'DOC');
            $document->control_tag = $acronym . '-' . str_pad($document->id, 4, '0', STR_PAD_LEFT);
            $document->save();

            // File uploads
            $files = $request->file('file_upload');
            $version = 1;
            foreach ($files as $file) {
                $originalName = $file->getClientOriginalName();
                $filePath = $file->storeAs('documents', $originalName, 'public');

                DocumentVersion::create([
                    'document_id' => $document->id,
                    'uploaded_by' => $userId, // Will be null for guest
                    'version' => $version,
                    'document_url' => $filePath,
                    'original_name' => $originalName,
                    'comments' => $request->input('comments'),
                    'submitted_at' => now(),
                ]);
            }

            Log::info('Dispatching DocumentSubmitted event for document ID: ' . $document->id);
            event(new DocumentSubmitted($document));

            $this->logActivity(
                'Submitted',
                "Document #{$document->id}",
                $userId 
                    ? "{$document->user_id} submitted a document titled '{$document->subject}'."
                    : "Guest {$guestWebmail} submitted a document titled '{$document->subject}'."
            );

            if ($isGuest) {
                return redirect()->route('guest.submissionSuccess');
            }
            return back()->with('success', 'Document submitted successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Document submission failed: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong while submitting the document. Please try again.');
        }
    }
}
