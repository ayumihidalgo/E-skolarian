<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Notification;
use App\Events\NewAnnouncement;
use App\Mail\AnnouncementMail;
use Illuminate\Support\Facades\Mail;

class NewAnnouncementListener
{
    
    public function handle(NewAnnouncement $event): void
    {
        \Log::info('NewAnnouncementListener::handle() method called!');
        
        try {
            \Log::info('Handling NewAnnouncement event for audience: ' . $event->audience);

            if ($event->audience === 'all') {
                \Log::info('Fetching all students for announcement: ' . $event->announcement->title);
                $students = \App\Models\User::where('role', 'student')->get();
                foreach ($students as $student) {
                    Notification::create([
                        'user_id' => $student->id,
                        'title' => 'New Announcement',
                        'message' => 'You have a new announcement: ' . $event->announcement->title,
                        'url' => route('student.dashboard'),
                        'is_read' => false,
                    ]);
                    
                    // Send email notification
                    try {
                        Mail::to($student->email)->send(new AnnouncementMail($event->announcement));
                        \Log::info('Email sent to: ' . $student->email . ' for announcement: ' . $event->announcement->title);
                    } catch (\Exception $e) {
                        \Log::error('Failed to send email to ' . $student->email . ': ' . $e->getMessage());
                    }
                }
            } elseif ($event->audience === 'custom' && $event->audience_students) {
                \Log::info('Fetching specific students for announcement: ' . $event->announcement->title);
                $student_ids = json_decode($event->audience_students);
                if (is_array($student_ids)) {
                    foreach ($student_ids as $student_id) {
                        $student = \App\Models\User::find($student_id);
                        if ($student) {
                            Notification::create([
                                'user_id' => $student_id,
                                'title' => 'New Announcement',
                                'message' => 'You have a new announcement: ' . $event->announcement->title,
                                'url' => route('student.dashboard'),
                                'is_read' => false,
                            ]);
                            
                            // Send email notification
                            try {
                                Mail::to($student->email)->send(new AnnouncementMail($event->announcement));
                                \Log::info('Email sent to: ' . $student->email . ' for announcement: ' . $event->announcement->title);
                            } catch (\Exception $e) {
                                \Log::error('Failed to send email to ' . $student->email . ': ' . $e->getMessage());
                            }
                        }
                    }
                } else {
                    \Log::error('Invalid audience_students data for announcement: ' . $event->announcement->title);
                }
            } else {
                \Log::warning('No valid audience specified for announcement: ' . $event->announcement->title);
            }
        } catch (\Exception $e) {
            \Log::error('Error handling NewAnnouncement event: ' . $e->getMessage());
        }
    }
}
