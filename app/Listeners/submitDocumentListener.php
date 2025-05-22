<?php

namespace App\Listeners;

use App\Events\DocumentSubmitted;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SubmitDocumentListener
{
    public function handle(DocumentSubmitted $event): void
    {
        Log::info('SubmitDocumentListener triggered for document ID: ' . $event->document->id);

        try {
            // Fetch the first admin user
            $admin = User::where('role', 'admin')->first();

            if (!$admin) {
                Log::warning('No admin user found to notify.');
                return;
            }

            Log::info('Notifying admin ID: ' . $admin->id);

            $url = route('admin.documentReview'); // Or add ['document' => $event->document->id] if needed

            Notification::create([
                'user_id' => $admin->id,
                'title' => 'New Document Submission',
                'message' => 'A new document "' . $event->document->subject . '" has been submitted.',
                'is_read' => false,
                'url' => $url,
            ]);

            Log::info('Created notification for admin ID: ' . $admin->id . ' - URL: ' . $url);
        } catch (\Exception $e) {
            Log::error('Error in SubmitDocumentListener: ' . $e->getMessage());
        }
    }
}
