<?php

namespace App\Listeners;

use App\Events\DocumentStatusUpdated;
use App\Models\Notification;
use App\Mail\NotificationAlertMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendDocumentStatusNotification
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\DocumentStatusUpdated  $event
     * @return void
     */
    public function handle(DocumentStatusUpdated $e)
    {
        $doc = $e->document;
        Log::info("SendDocumentStatusNotification listener triggered for doc {$doc->id}");

        // Use the user relationship for clarity (optional)
        $student = $doc->user; // returns User model

        if (!$student) {
            Log::error("No student found for document ID {$doc->id}");
            return;
        }

        $status = $doc->status; // 'approved', 'rejected', 'Under Review', etc.
        $title = match($status) {
            'Under Review' => 'Document Under Review',
            'Approved' => 'Document Approved',
            'Rejected' => 'Document Rejected', 
            'Returned' => 'Document Returned',
            default => 'Document ' . ucfirst(strtolower($status))
        };
        
        $message = match($status) {
            'Under Review' => "Your document for subject \"{$doc->subject}\" (Type: {$doc->type}, ID: {$doc->id}) is now under review.",
            'Approved' => "Your document for subject \"{$doc->subject}\" (Type: {$doc->type}, ID: {$doc->id}) was approved.",
            'Rejected' => "Your document for subject \"{$doc->subject}\" (Type: {$doc->type}, ID: {$doc->id}) was rejected.",
            'Returned' => "Your document for subject \"{$doc->subject}\" (Type: {$doc->type}, ID: {$doc->id}) was returned for revision.",
            default => "Your document for subject \"{$doc->subject}\" (Type: {$doc->type}, ID: {$doc->id}) status changed to {$status}."
        };

        try {
            $notification = Notification::create([
                'title'   => $title,
                'message' => $message,
                'user_id' => $student->id,
                'is_read' => false,
                'url'     => route('records.show', ['id' => $doc->id]),
            ]);
            Log::info("Student notification created for user_id={$student->id}");

            // Send email notification only to students
            if ($student->role === 'student') {
                try {
                    Mail::to($student->email)->send(new NotificationAlertMail($notification));
                    Log::info('Email notification sent to student: ' . $student->email);
                } catch (\Exception $e) {
                    Log::error('Failed to send email notification: ' . $e->getMessage());
                }
            } else {
                Log::info('Email notification skipped for non-student user: ' . $student->email);
            }
        } catch (\Exception $ex) {
            Log::error("Failed to create notification: " . $ex->getMessage());
        }
    }
}