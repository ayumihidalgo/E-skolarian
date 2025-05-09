<?php

namespace App\Listeners;

use App\Events\DocumentSubmitted;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class submitDocumentListener
{
    public function handle(DocumentSubmitted $event): void
    {
        Log::info('submitDocumentListener triggered for document ID: ' . $event->document->id);

        try {
            // Fetch all admin users
            $admins = User::where('role', 'admin')->get();

            if ($admins->isEmpty()) {
                Log::warning('No admin users found to notify.');
                return;
            }

            Log::info('Found ' . $admins->count() . ' admin users to notify');

            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'New Document Submission',
                    'message' => 'A new document "' . $event->document->subject . '" has been submitted.',
                    'is_read' => false,
                ]);
                
                Log::info('Created notification for admin ID: ' . $admin->id);
            }
        } catch (\Exception $e) {
            Log::error('Error in submitDocumentListener: ' . $e->getMessage());
        }
    }
}