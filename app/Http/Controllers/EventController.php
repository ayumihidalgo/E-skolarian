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
        \Log::info('Event update request received', $request->all());
        
        try {
            // Add validation
            $request->validate([
                'id' => 'required|exists:events,id',
                'title' => 'required|string|max:80|min:6',
                'start' => 'required|date',
                'end' => 'nullable|date',
            ]);
            
            // Find the event
            $event = Event::findOrFail($request->id);
            
            // Update event with the correct field names
            $event->update([
                'title' => $request->title,
                'start_date' => $request->start,  // Use start_date instead of start
                'end_date' => $request->end,      // Use end_date instead of end
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Event updated successfully',
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_date->format('Y-m-d\TH:i:s'),
                'end' => $event->end_date ? $event->end_date->format('Y-m-d\TH:i:s') : null,
                'backgroundColor' => '#7A1212',
                'textColor' => '#ffffff',
                'allDay' => $request->input('allDay', false)
            ]);
        } catch (\Exception $e) {
            \Log::error('Event update failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function destroyCalendarEvent(Request $request)
    {
        \Log::info('Event delete request received', $request->all());
        
        try {
            // Find the event
            $event = Event::findOrFail($request->id);
            
            // Use your existing authorization if needed
            // $this->authorize('delete', $event);
            
            // Delete the event
            $event->delete();
            
            return response()->json(['success' => true, 'message' => 'Event deleted successfully']);
        } catch (\Exception $e) {
            \Log::error('Event deletion failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
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
            \Log::info('Fetching ADMIN-APPROVED proposals only');
            
            // Fixed query with correct column names
            $approvedProposals = \DB::table('submitted_documents')
                ->leftJoin('users', 'submitted_documents.user_id', '=', 'users.id') // Fixed: user_id instead of submitted_by
                ->leftJoin('reviews', 'submitted_documents.id', '=', 'reviews.document_id')
                ->leftJoin('users as admin_users', 'reviews.reviewed_by', '=', 'admin_users.id')
                ->where('submitted_documents.type', '=', 'Event Proposal')
                ->where('submitted_documents.status', '=', 'Approved')
                ->where('reviews.status', '=', 'Approved')
                ->whereNotNull('reviews.id')
                ->where('admin_users.role', '=', 'admin')
                ->select(
                    'submitted_documents.id',
                    'submitted_documents.subject',
                    'submitted_documents.created_at',
                    'submitted_documents.summary',
                    'users.organization_acronym', // Now this should work
                    'reviews.reviewed_by',
                    'reviews.status as review_status',
                    'reviews.message as review_message',
                    'admin_users.username as reviewer_name',
                    'admin_users.role as reviewer_role'
                )
                ->distinct()
                ->get();
    
            \Log::info('Approved proposals query result', [
                'count' => $approvedProposals->count(),
                'raw_data' => $approvedProposals->toArray()
            ]);
    
            if ($approvedProposals->isEmpty()) {
                \Log::info('No approved event proposals found');
                return response()->json([]);
            }
    
            $formattedEvents = $approvedProposals->map(function($proposal) {
                \Log::info('Formatting proposal', [
                    'id' => $proposal->id,
                    'subject' => $proposal->subject,
                    'organization' => $proposal->organization_acronym
                ]);
                
                return [
                    'id' => 'proposal_' . $proposal->id,
                    'title' => $proposal->subject . ' (' . ($proposal->organization_acronym ?? 'Unknown') . ')',
                    'start' => \Carbon\Carbon::parse($proposal->created_at)->format('Y-m-d'),
                    'backgroundColor' => '#2563eb',
                    'textColor' => '#ffffff',
                    'allDay' => true,
                    'source' => 'proposal',
                    'editable' => false,
                    'deletable' => false,
                    'organization' => $proposal->organization_acronym ?? 'Unknown',
                    'summary' => $proposal->summary ?? '',
                    'review_message' => $proposal->review_message ?? '',
                    'reviewer' => $proposal->reviewer_name ?? 'Unknown Admin',
                    'review_status' => $proposal->review_status ?? 'Unknown'
                ];
            });
    
            \Log::info('Returning formatted admin-approved events', ['count' => count($formattedEvents)]);
            return response()->json($formattedEvents);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching admin-approved proposals', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([]);
        }
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
            
            // Format manual events
            $formattedManualEvents = $manualEvents->map(function($event) {
                return [
                    'id' => 'manual_' . $event->id,
                    'title' => $event->title,
                    'start' => $event->start_date->format('Y-m-d\TH:i:s'),
                    'end' => $event->end_date ? $event->end_date->format('Y-m-d\TH:i:s') : null,
                    'backgroundColor' => '#7A1212',
                    'textColor' => '#ffffff',
                    'allDay' => !$this->hasTimeComponent($event->start_date),
                    'source' => 'manual',
                    'editable' => true,
                    'deletable' => true
                ];
            });
            
            \Log::info('Returning filtered admin events', ['count' => count($formattedManualEvents)]);
            return response()->json($formattedManualEvents);
        } catch (\Exception $e) {
            \Log::error('Error fetching manual events', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
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

