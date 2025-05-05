<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        try {
            Log::info('Document submission attempt', ['user_id' => auth()->id()]);

            // Validate the incoming request
            $validated = $request->validate([
                'doc_receiver' => 'required|string',
                'subject' => 'required|string|max:255',
                'doc_type' => 'required|string',
                'summary' => 'required|string|max:255',
                'eventStartDate' => 'nullable|date|required_if:doc_type,Event Proposal',
                'eventEndDate' => 'nullable|date|after_or_equal:eventStartDate|required_if:doc_type,Event Proposal',
                'file_upload' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            ]);

            // Check for file and store it safely if available
            if ($request->hasFile('file_upload')) {
                try {
                    $file = $request->file('file_upload');
                    $path = $file->store('documents', 'public');

                    $validated['file_path'] = $request->file('file_upload')->store('documents', 'public');
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
