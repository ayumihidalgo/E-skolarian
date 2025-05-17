<?php

namespace App\Listeners;

use App\Events\DocumentStatusUpdated;
use App\Models\Notification;
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

        $status = $doc->status; // 'approved' or 'rejected'
        $title = 'Document ' . ucfirst($status);
        $message = "Your document \"{$doc->title}\" (ID: {$doc->id}) was {$status}.";

        try {
            Notification::create([
                'title'   => $title,
                'message' => $message,
                'user_id' => $student->id,
                'is_read' => false,
                'url'     => "/student/documents/{$doc->id}",
            ]);
            Log::info("Student notification created for user_id={$student->id}");
        } catch (\Exception $ex) {
            Log::error("Failed to create notification: " . $ex->getMessage());
        }
    }
}