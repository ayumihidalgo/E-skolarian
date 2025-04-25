@extends('app')

@section('content')
<div class="flex">
    @if(Auth::user()->role === 'admin')
        @include('components.adminNavigation')
    @else
        @include('components.studentNavigation')
    @endif

    <div class="flex-1 ml-[25%] p-8 bg-gray-100">
        <div class="mb-8 flex justify-between items-center">
            <h1 class="text-2xl font-[Marcellus_SC]">Calendar of Activities</h1>
            @if(Auth::user()->role === 'admin')
                <button onclick="openEventModal()" class="bg-[#7A1212] text-white px-4 py-2 rounded hover:bg-[#8A2222]">
                    Add Event
                </button>
            @endif
        </div>

        <!-- Direct calendar container with explicit dimensions -->
        <div id="calendar-container" class="bg-white p-6 rounded-lg shadow-lg" style="width: 100%; min-height: 700px;">
            <div id="calendar"></div>
        </div>
    </div>
</div>

@if(Auth::user()->role === 'admin')
    @include('calendar.partials.event-modal')
@endif
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css">
<style>
    .fc {
        font-family: 'Marcellus SC', serif;
    }
    .fc-toolbar-title {
        font-size: 1.5em !important;
    }
    .fc-button-primary {
        background-color: #7A1212 !important;
        border-color: #7A1212 !important;
    }
    .fc-button-primary:hover {
        background-color: #8A2222 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
    let calendarObj; // Make calendar variable accessible globally
    
    // Wait for everything to load
    window.addEventListener('load', function() {
        console.log('Window loaded, initializing calendar...');
        
        const calendarEl = document.getElementById('calendar');
        
        if (!calendarEl) {
            console.error('Calendar element not found!');
            return;
        }
        
        console.log('Calendar element found, creating calendar instance...');
        
        calendarObj = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: '100%',
            contentHeight: 'auto',
            aspectRatio: 1.35,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: [
                {
                    title: 'Test Event',
                    start: new Date(),
                    backgroundColor: '#7A1212'
                }
            ]
        });
        
        console.log('Rendering calendar...');
        calendarObj.render();
        console.log('Calendar rendered successfully');
    });

    function openEventModal() {
        const modal = document.getElementById('eventModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }
    
    // Add these new functions
    function closeEventModal() {
        const modal = document.getElementById('eventModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }
    
    function saveEvent() {
        console.log('Save event function called');
        
        // Get form values
        const titleEl = document.getElementById('event-title');
        const startEl = document.getElementById('event-start');
        const endEl = document.getElementById('event-end');
        
        console.log('Form elements:', { titleEl, startEl, endEl });
        
        if (!titleEl || !startEl) {
            console.error('Form elements not found!');
            alert('Error: Form elements not found. Please check the console for details.');
            return;
        }
        
        const title = titleEl.value;
        const start = startEl.value;
        const end = endEl ? endEl.value : null;
        
        console.log('Form values:', { title, start, end });
        
        // Validate form
        if (!title || !start) {
            alert('Please fill in required fields');
            return;
        }
        
        // Add event to calendar
        try {
            calendarObj.addEvent({
                title: title,
                start: start,
                end: end || null,
                backgroundColor: '#7A1212'
            });
            console.log('Event added to calendar');
        } catch (error) {
            console.error('Error adding event to calendar:', error);
        }
        
        // Close modal
        closeEventModal();
        
        // You would typically save to database here via AJAX
        console.log('Event saved:', {title, start, end});
    }
</script>
@endpush