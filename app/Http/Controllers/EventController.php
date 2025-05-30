<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        // Log incoming request for debugging
        \Log::info('Event creation request:', $request->all());
    
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required',
            'end' => 'nullable',
            'allDay' => 'boolean'
        ]);
    
        try {
            // Create the event
            $event = Event::create([
                'title' => $validated['title'],
                'start_date' => $validated['start'],
                'end_date' => $validated['end'] ?? null,
                'created_by' => auth()->id()
            ]);
    
            // Log success
            \Log::info('Event created:', ['id' => $event->id]);
    
            // Return properly formatted event for FullCalendar
            return response()->json([
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_date,
                'end' => $event->end_date,
                'backgroundColor' => '#7A1212',
                'textColor' => '#ffffff',
                'allDay' => $request->input('allDay', false)
            ]);
        } catch (\Exception $e) {
            // Log error
            \Log::error('Event creation failed:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function updateCalendarEvent(Request $request)
    {
        try {
            \Log::info('Update request received', $request->all());
            
            $request->validate([
                'id' => 'required',
                'title' => 'required|string|max:255',
                'start' => 'required|date',
                'end' => 'nullable|date'
            ]);
    
            // Extract numeric ID from potentially prefixed ID
            $eventId = $request->id;
            if (str_contains($eventId, 'manual_')) {
                $eventId = str_replace('manual_', '', $eventId);
            }
    
            \Log::info('Looking for event with ID:', ['id' => $eventId]);
    
            $event = Event::find($eventId);
    
            if (!$event) {
                \Log::error('Event not found', ['id' => $eventId]);
                return response()->json(['error' => 'Event not found'], 404);
            }
    
            \Log::info('Found event, updating...', ['event' => $event->toArray()]);
    
            $event->update([
                'title' => $request->title,
                'start_date' => $request->start,
                'end_date' => $request->end,
            ]);
    
            \Log::info('Event updated successfully', ['event' => $event->fresh()->toArray()]);
    
            return response()->json([
                'success' => true,
                'message' => 'Event updated successfully',
                'event' => $event
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in update', ['errors' => $e->errors()]);
            return response()->json(['error' => 'Validation failed', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating event', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal server error: ' . $e->getMessage()], 500);
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


    public function getApprovedProposals()
    {
        try {
            \Log::info('Fetching events created from approved proposals');
            
            // Get events that originated from approved event proposals
            $approvedProposalEvents = \DB::table('events')
                ->leftJoin('users', 'events.created_by', '=', 'users.id')
                ->leftJoin('submitted_documents', function($join) {
                    $join->on('submitted_documents.subject', '=', 'events.title')
                         ->where('submitted_documents.type', '=', 'Event Proposal')
                         ->where('submitted_documents.status', '=', 'Approved');
                })
                ->leftJoin('reviews', 'submitted_documents.id', '=', 'reviews.document_id')
                ->leftJoin('users as admin_users', 'reviews.reviewed_by', '=', 'admin_users.id')
                ->whereNotNull('submitted_documents.id') // Only events that have corresponding approved proposals
                ->where('reviews.status', '=', 'Approved')
                ->where('admin_users.role', '=', 'admin')
                ->select(
                    'events.id',
                    'events.title',
                    'events.description',
                    'events.start_date',
                    'events.end_date',
                    'events.status as event_status',
                    'events.created_at',
                    'events.updated_at',
                    'users.organization_acronym',
                    'submitted_documents.summary',
                    'reviews.reviewed_by',
                    'reviews.status as review_status',
                    'reviews.message as review_message',
                    'admin_users.username as reviewer_name',
                    'admin_users.role as reviewer_role'
                )
                ->distinct()
                ->get();
    
            \Log::info('Approved proposal events query result', [
                'count' => $approvedProposalEvents->count(),
                'raw_data' => $approvedProposalEvents->toArray()
            ]);
    
            if ($approvedProposalEvents->isEmpty()) {
                \Log::info('No events from approved proposals found');
                return response()->json([]);
            }
    
            $formattedEvents = $approvedProposalEvents->map(function($event) {
                \Log::info('Formatting proposal event', [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start_date' => $event->start_date,
                    'organization' => $event->organization_acronym
                ]);
                
                // Use the actual event dates from the events table
                $startDate = \Carbon\Carbon::parse($event->start_date);
                $endDate = $event->end_date ? \Carbon\Carbon::parse($event->end_date) : null;
                $today = \Carbon\Carbon::today();
                $thisWeek = \Carbon\Carbon::now()->endOfWeek();
                
                // Determine color for approved proposals using the same logic
                $backgroundColor = $this->getProposalEventColor($event, $startDate, $today, $thisWeek);
                
                return [
                    'id' => 'proposal_' . $event->id,
                    'title' => $event->title . ' (' . ($event->organization_acronym ?? 'Unknown') . ')',
                    'start' => $startDate->format('Y-m-d H:i:s'),
                    'end' => $endDate ? $endDate->format('Y-m-d H:i:s') : null,
                    'backgroundColor' => $backgroundColor,
                    'textColor' => '#ffffff',
                    'allDay' => $startDate->format('H:i:s') === '00:00:00' && 
                              (!$endDate || $endDate->format('H:i:s') === '00:00:00'),
                    'source' => 'proposal',
                    'editable' => false,
                    'deletable' => false,
                    'organization' => $event->organization_acronym ?? 'Unknown',
                    'summary' => $event->summary ?? $event->description ?? '',
                    'review_message' => $event->review_message ?? '',
                    'reviewer' => $event->reviewer_name ?? 'Unknown Admin',
                    'review_status' => $event->review_status ?? 'Unknown',
                    'event_status' => $event->event_status ?? 'scheduled',
                    'is_rescheduled' => \Carbon\Carbon::parse($event->updated_at) > \Carbon\Carbon::parse($event->created_at)->addMinutes(5)
                ];
            });
    
            \Log::info('Returning formatted approved proposal events', ['count' => count($formattedEvents)]);
            return response()->json($formattedEvents);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching approved proposal events', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([]);
        }
    }
    
    // Add this new private method for determining proposal event colors
    private function getProposalEventColor($event, $startDate, $today, $thisWeek)
    {
        // Check if event was rescheduled (updated significantly after creation)
        $isRescheduled = \Carbon\Carbon::parse($event->updated_at) > \Carbon\Carbon::parse($event->created_at)->addMinutes(5);
        
        if ($isRescheduled) {
            return '#0085FF'; // Blue - rescheduled (using your blue font color for visibility)
        }
        
        // Check if event is today (on due)
        if ($startDate->isSameDay($today)) {
            return '#00B244'; // Green - on due (happening today, using your green font color)
        }
        
        // Get the start and end of the current calendar week (Sunday to Saturday)
        $startOfWeek = $today->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $endOfWeek = $today->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
        
        // Check if event is in the same calendar week (but not today)
        if ($startDate->between($startOfWeek, $endOfWeek) && !$startDate->isSameDay($today)) {
            return '#FF9F2D'; // Yellow - current event (this calendar week, using your yellow font color)
        }
        
        // Default - Approved proposal events (distinguishable from rescheduled)
        return '#0085FF'; // Blue - approved proposal events
    }
    
    public function getCalendarEvents()
    {
        \Log::info('=== getCalendarEvents() method called ===');
        
        try {
            // Only get events created by admin users (manually created events)
            // This excludes auto-generated events from student proposals
            $manualEvents = Event::whereHas('creator', function($query) {
                $query->where('role', 'admin');
            })->get();
            
            \Log::info('Manual admin events found', ['count' => $manualEvents->count()]);
            
            // Log each event to verify they're admin-created
            foreach ($manualEvents as $event) {
                \Log::info('Admin-created event found', [
                    'id' => $event->id,
                    'title' => $event->title,
                    'created_by' => $event->created_by,
                    'creator_role' => $event->creator ? $event->creator->role : 'Unknown'
                ]);
            }
            
            // Format manual events with color scheme
            $formattedManualEvents = $manualEvents->map(function($event) {
                $startDate = \Carbon\Carbon::parse($event->start_date);
                $endDate = $event->end_date ? \Carbon\Carbon::parse($event->end_date) : null;
                $today = \Carbon\Carbon::today();
                $thisWeek = \Carbon\Carbon::now()->endOfWeek();
                
                // Determine color based on event status and date
                $backgroundColor = $this->getEventColor($event, $startDate, $today, $thisWeek);
                
                return [
                    'id' => 'manual_' . $event->id,
                    'title' => $event->title,
                    'start' => $event->start_date->format('Y-m-d\TH:i:s'),
                    'end' => $event->end_date ? $event->end_date->format('Y-m-d\TH:i:s') : null,
                    'backgroundColor' => $backgroundColor,
                    'textColor' => '#ffffff',
                    'allDay' => !$this->hasTimeComponent($event->start_date),
                    'source' => 'manual',
                    'editable' => true,
                    'deletable' => true,
                    'description' => $event->description ?? '',
                    'status' => $event->status ?? 'scheduled',
                    'created_by' => $event->creator ? $event->creator->username : 'Unknown',
                    'is_rescheduled' => $event->updated_at > $event->created_at->addMinutes(5) // If updated more than 5 minutes after creation
                ];
            });
            
            \Log::info('Returning filtered admin events', ['count' => count($formattedManualEvents)]);
            return response()->json($formattedManualEvents);
        } catch (\Exception $e) {
            \Log::error('Error fetching manual events', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // Add this new private method for determining event colors
        private function getEventColor($event, $startDate, $today, $thisWeek)
    {
        // Check if event was rescheduled (updated significantly after creation)
        $isRescheduled = $event->updated_at > $event->created_at->addMinutes(5);
        
        if ($isRescheduled) {
            return '#0085FF'; // Blue - rescheduled (using your blue font color for visibility)
        }
        
        // Check if event is today (on due)
        if ($startDate->isSameDay($today)) {
            return '#00B244'; // Green - on due (happening today, using your green font color)
        }
        
        // Get the start and end of the current calendar week (Sunday to Saturday)
        $startOfWeek = $today->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $endOfWeek = $today->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
        
        // Check if event is in the same calendar week (but not today)
        if ($startDate->between($startOfWeek, $endOfWeek) && !$startDate->isSameDay($today)) {
            return '#FF9F2D'; // Yellow - current event (this calendar week, using your yellow font color)
        }
        
        // Default - Admin added event (future events)
        return 'rgba(155, 81, 224, 1)'; // Purple - Admin added event (using your purple font color)
    }
    
    // Helper method to check if a date has a time component
    private function hasTimeComponent($dateString)
    {
        return strpos($dateString, ':') !== false;
    }


    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        
        $event->delete();
        return response()->json(['message' => 'Event deleted']);
    }
}

