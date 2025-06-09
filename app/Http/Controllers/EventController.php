<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement; // Make sure this line exists
use Carbon\Carbon;

class EventController extends Controller
{


    // Helper method to check if a date has a time component







    public function index()
    {
        $events = Event::all();
        return view('calendar.index', compact('events'));
    }

    public function storeCalendarEvent(Request $request)
    {
        \Log::info('=== Event creation started ===');
        \Log::info('Request data:', $request->all());
        \Log::info('User ID:', ['id' => auth()->id()]); // Fix: Wrap ID in array
        
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'start' => 'required|date',
                'end' => 'nullable|date|after_or_equal:start',
                'allDay' => 'boolean'
            ]);
            
            \Log::info('Validation passed:', $validated);
    
            $eventData = [
                'title' => $validated['title'],
                'start' => $validated['start'],
                'end' => $validated['end'] ?? null,
                'status' => 'scheduled',
                'created_by' => auth()->id(),
            ];
            
            \Log::info('Event data to create:', $eventData);
    
            $event = Event::create($eventData);
            
            \Log::info('Event created successfully:', ['event_id' => $event->id, 'event' => $event->toArray()]);
            
            return response()->json([
                'success' => true,
                'message' => 'Event created successfully',
                'event' => $event
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error creating event:', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
    public function updateCalendarEvent(Request $request)
    {
        \Log::info('=== Event update started ===');
        \Log::info('Request data:', $request->all());
        
        try {
            $validated = $request->validate([
                'id' => 'required',
                'title' => 'string|max:255',
                'start' => 'required|date',
                'end' => 'nullable|date|after_or_equal:start'
            ]);
            
            // Remove 'manual_' prefix if present
            $eventId = str_replace('manual_', '', $validated['id']);
            
            $event = Event::findOrFail($eventId);
            
            // Check if user can update this event (admin or creator)
            if (auth()->user()->role !== 'admin' && $event->created_by !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $updateData = [
                'start' => $validated['start'],
                'end' => $validated['end'] ?? null,
            ];
            
            // Only update title if provided
            if (isset($validated['title'])) {
                $updateData['title'] = $validated['title'];
            }
            
            $event->update($updateData);
            
            \Log::info('Event updated successfully:', ['event_id' => $event->id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Event updated successfully',
                'event' => $event
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error updating event:', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return response()->json(['error' => 'Failed to update event: ' . $e->getMessage()], 500);
        }
    }
    
    public function destroyCalendarEvent(Request $request)
    {
        try {
            \Log::info('Delete request received', $request->all());
            
            $request->validate([
                'id' => 'required'
            ]);
    
            // Extract numeric ID from potentially prefixed ID
            $eventId = $request->id;
            if (str_contains($eventId, 'manual_')) {
                $eventId = str_replace('manual_', '', $eventId);
            }
    
            \Log::info('Looking for event to delete with ID:', ['id' => $eventId]);
    
            $event = Event::find($eventId);
    
            if (!$event) {
                \Log::error('Event not found for deletion', ['id' => $eventId]);
                return response()->json(['error' => 'Event not found'], 404);
            }
    
            \Log::info('Found event, deleting...', ['event' => $event->toArray()]);
    
            $event->delete();
    
            \Log::info('Event deleted successfully', ['id' => $eventId]);
    
            return response()->json([
                'success' => true,
                'message' => 'Event deleted successfully'
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in delete', ['errors' => $e->errors()]);
            return response()->json(['error' => 'Validation failed', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error deleting event', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:scheduled,completed,cancelled',
        ]);

        $event->update($validated);

        return response()->json($event);
    }


    
    public function getCalendarEvents()
    {
        \Log::info('=== getCalendarEvents() method called ===');
        
        try {
            // Get all events
            $events = Event::with('creator')->get();
            
            \Log::info('Total events found in database:', ['count' => $events->count()]);
            
            // Format events for FullCalendar
            $formattedEvents = $events->map(function($event) {
                $startDate = \Carbon\Carbon::parse($event->start);
                $today = \Carbon\Carbon::today();
                
                // Determine color based on event status and date
                $backgroundColor = $this->getEventColor($event, $startDate, $today);
                
                \Log::info('Formatting event:', [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start,
                    'end' => $event->end
                ]);
                
                return [
                    'id' => 'manual_' . $event->id,
                    'title' => $event->title,
                    'start' => $event->start,
                    'end' => $event->end,
                    'backgroundColor' => $backgroundColor,
                    'textColor' => '#ffffff',
                    'allDay' => !$this->hasTimeComponent($event->start),
                    'source' => 'manual',
                    'editable' => true,
                    'deletable' => true
                ];
            });
            
            \Log::info('Returning formatted events:', [
                'count' => count($formattedEvents)
            ]);
            
            return response()->json($formattedEvents->values());
            
        } catch (\Exception $e) {
            \Log::error('Error fetching calendar events:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // Add this new private method for determining event colors
    private function getEventColor($event, $startDate, $today)
    {
        // Check if event was rescheduled (updated significantly after creation)
        $isRescheduled = $event->updated_at > $event->created_at->addMinutes(5);
        
        if ($isRescheduled) {
            return '#0085FF'; // Blue - rescheduled
        }
        
        // Check if event is today (on due)
        if ($startDate->isSameDay($today)) {
            return '#00B244'; // Green - happening today
        }
        
        // Get the start and end of the current calendar week (Sunday to Saturday)
        $startOfWeek = $today->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $endOfWeek = $today->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
        
        // Check if event is in the same calendar week (but not today)
        if ($startDate->between($startOfWeek, $endOfWeek) && !$startDate->isSameDay($today)) {
            return '#FF9F2D'; // Yellow - current week
        }
        
        // Default - Admin added event (future events)
        return '#7A1212'; // Maroon - default admin event color
    }
    
    // Helper method to check if a date has a time component
    private function hasTimeComponent($dateString)
    {
        if (!$dateString) return false;
        
        try {
            $date = \Carbon\Carbon::parse($dateString);
            return !($date->hour === 0 && $date->minute === 0 && $date->second === 0);
        } catch (\Exception $e) {
            return false;
        }
    }

public function getCalendarAnnouncements()
{
    try {
        \Log::info('=== getCalendarAnnouncements() method called ===');
        
        $userRole = auth()->user()->role;
        \Log::info('User role: ' . $userRole);
        
        // Get announcements that are scheduled (have deadline) and not archived
        $query = Announcement::where('archived', 0) // Use 'archived' not 'is_archived'
            ->whereNotNull('deadline') // Use 'deadline' as the scheduled date
            ->where('deadline', '>=', Carbon::now()); // Only show future/current announcements

        \Log::info('Base query count: ' . $query->count());

        // Filter by audience for students
        if ($userRole === 'student') {
            $userId = auth()->id();
            \Log::info('Filtering for student ID: ' . $userId);
            
            $query->where(function($q) use ($userId) {
                $q->where('audience', 'all')
                  ->orWhere(function($subQ) use ($userId) {
                      $subQ->where('audience', 'custom')
                           ->whereNotNull('audience_students')
                           ->where(function($jsonQ) use ($userId) {
                               // Check if user ID is in the JSON array
                               $jsonQ->whereRaw("JSON_CONTAINS(audience_students, '\"$userId\"')")
                                     ->orWhereRaw("JSON_CONTAINS(audience_students, '$userId')");
                           });
                  });
            });
        }

        $announcements = $query->with('user')->get();
        \Log::info('Final filtered announcements: ' . $announcements->count());
        
        // Log each announcement details
        foreach($announcements as $announcement) {
            \Log::info('Announcement: ' . $announcement->title . ' | Deadline: ' . $announcement->deadline . ' | Audience: ' . $announcement->audience);
        }

        $announcementEvents = $announcements->map(function($announcement) {
            $deadline = Carbon::parse($announcement->deadline);
            

            $title = $announcement->title;
            $maxTitleLength = 60; // Set maximum characters for display
            
            if (strlen($title) > $maxTitleLength) {
                $displayTitle = substr($title, 0, $maxTitleLength) . '...';
            } else {
                $displayTitle = $title;
            }
            return [
                'id' => 'announcement_' . $announcement->id,
                'title' => '📢 ' . $announcement->title,
                'start' => $deadline->format('Y-m-d H:i:s'),
                'end' => $deadline->format('Y-m-d H:i:s'),
                'backgroundColor' => '#FF6347',
                'textColor' => '#ffffff',
                'allDay' => $deadline->format('H:i:s') === '00:00:00',
                'source' => 'announcement',
                'editable' => false,
                'deletable' => false,
                'announcement_id' => $announcement->id,
                'content' => $announcement->content,
                'poster' => $announcement->user->username ?? 'Unknown',
                'deadline_text' => $deadline->format('F j, Y g:i A')
            ];
        });
        
        \Log::info('Returning ' . $announcementEvents->count() . ' announcement events');
        return response()->json($announcementEvents->values());
        
    } catch (\Exception $e) {
        \Log::error('Error in getCalendarAnnouncements: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

private function getScheduledAnnouncements()
{
    try {
        $userRole = auth()->user()->role;
        
        // Get scheduled announcements that are not archived and have future or current deadlines
        $query = Announcement::where('is_archived', false)
            ->whereNotNull('scheduled_date')
            ->where(function($q) {
                $q->whereNull('deadline')
                  ->orWhere('deadline', '>=', Carbon::now());
            });

        // Filter by audience for students
        if ($userRole === 'student') {
            $userId = auth()->id();
            $query->where(function($q) use ($userId) {
                $q->where('audience', 'all');
                // Handle custom audience - check if stored as JSON or has relationship
                $q->orWhere(function($subQ) use ($userId) {
                    $subQ->where('audience', 'custom')
                         ->where(function($jsonQ) use ($userId) {
                             // If stored as JSON in audience_students column
                             $jsonQ->whereRaw("JSON_CONTAINS(audience_students, '\"$userId\"')")
                                   // Or if there's a pivot table relationship
                                   ->orWhereHas('audienceUsers', function($pivotQ) use ($userId) {
                                       $pivotQ->where('user_id', $userId);
                                   });
                         });
                });
            });
        }

        $announcements = $query->with('user')->get();

        \Log::info('Scheduled announcements found', ['count' => $announcements->count()]);

        return $announcements->map(function($announcement) {
            $scheduledDate = Carbon::parse($announcement->scheduled_date);
            $deadline = $announcement->deadline ? Carbon::parse($announcement->deadline) : null;
            
            // Use scheduled date as start, deadline as end (if exists)
            $startDate = $scheduledDate;
            $endDate = $deadline;

            
            
            return [
                'id' => 'announcement_' . $announcement->id,
                'title' => '📢 ' . $announcement->title,
                'start' => $startDate->format('Y-m-d H:i:s'),
                'end' => $endDate ? $endDate->format('Y-m-d H:i:s') : null,
                'backgroundColor' => '#FF6347', // Tomato color for announcements
                'textColor' => '#ffffff',
                'allDay' => $startDate->format('H:i:s') === '00:00:00' && 
                          (!$endDate || $endDate->format('H:i:s') === '00:00:00'),
                'source' => 'announcement',
                'editable' => false,
                'deletable' => false,
                'announcement_id' => $announcement->id,
                'content' => $announcement->content,
                'poster' => $announcement->user->username ?? 'Unknown',
                'deadline_text' => $deadline ? $deadline->format('F j, Y g:i A') : null
            ];
        });
    } catch (\Exception $e) {
        \Log::error('Error fetching scheduled announcements', ['error' => $e->getMessage()]);
        return collect([]);
    }
}
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        
        $event->delete();
        return response()->json(['message' => 'Event deleted']);
    }
}

