@extends('base')

@push('styles')
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" as="style">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js" as="script">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css">
    <link rel="stylesheet" href="{{ asset('calendar.css') }}">
@endpush

@section('content')
@if(Auth::user()->role === 'admin')
    @include('components.adminNavBarComponent')
    @include('components.adminSideBarComponent')
@elseif(Auth::user()->role === 'student')
    @include('components.studentNavBarComponent')
    @include('components.studentSideBarComponent')
@elseif(Auth::user()->role === 'teacher')
    @include('components.teacherNavBarComponent')
    @include('components.teacherSideBarComponent')
@endif

<div id="main-content" class="transition-all duration-300 ml-[20%]">
    <!-- Calendar content section -->
    <div class="py-8 px-10">
        <!-- Calendar header with title -->
        <div class="mb-8">
            <h1 style="color: #000; font-family: Manrope, sans-serif; font-size: 32px; font-weight: 800; line-height: normal;">
                Calendar of Activities
            </h1>
        </div>

        <!-- Calendar container with explicit dimensions to ensure visibility -->
        <div id="calendar-container" class="bg-white rounded-lg overflow-hidden shadow-md" style="position: relative; z-index: 5; min-height: 600px;">
            <div id="calendar" style="min-height: 600px; width: 100%;"></div>
        </div>
    </div>

    @if(Auth::user()->role === 'admin')
        <!-- Event Modal -->
        <div id="eventModal" class="fixed inset-0 modal-backdrop z-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md modal-container modal-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Create New Event</h3>
                        <button onclick="closeEventModal()" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>

                    <form id="eventForm" class="space-y-4">
                        <div>
                            <label for="event-title" class="block text-sm font-medium text-gray-700">Event Title</label>
                            <input type="text" id="event-title" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>

                        <div>
                            <label for="event-start" class="block text-sm font-medium text-gray-700">Start Date/Time</label>
                            <input type="datetime-local" id="event-start" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>

                        <div>
                            <label for="event-end" class="block text-sm font-medium text-gray-700">End Date/Time (Optional)</label>
                            <input type="datetime-local" id="event-end" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>

                        <div>
                            <label for="event-color" class="block text-sm font-medium text-gray-700">Event Color</label>
                            <select id="event-color" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                                <option value="#3498db">Blue</option>
                                <option value="#2ecc71">Green</option>
                                <option value="#f1c40f">Yellow</option>
                                <option value="#e74c3c">Red</option>
                                <option value="#f39c12">Orange</option>
                                <option value="#7A1212">Dark Red</option>
                            </select>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="closeEventModal()"
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="button" onclick="saveEvent()"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#7A1212] hover:bg-[#8A2222]">
                                Save Event
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Global calendar object
    let calendarObj;
    
    // Global tracking of current month/year for the Today button
    const now = new Date();
    const currentMonth = now.getMonth();
    const currentYear = now.getFullYear();
    
    // Wait for FullCalendar to load
    document.addEventListener('DOMContentLoaded', function() {
        // Check if FullCalendar is loaded
        if (typeof FullCalendar === 'undefined') {
            // If not loaded yet, wait a bit and try loading the calendar
            const calendarScript = document.createElement('script');
            calendarScript.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js';
            calendarScript.onload = initializeCalendarWhenReady;
            document.head.appendChild(calendarScript);
        } else {
            // FullCalendar is already loaded
            initializeCalendarWhenReady();
        }
    });
    
    // Make sure everything is loaded before initializing
    function initializeCalendarWhenReady() {
        // Small delay to ensure DOM is fully ready
        setTimeout(() => {
            initCalendar();
        }, 100);
    }
    
    // Initialize the calendar
    function initCalendar() {
        const calendarEl = document.getElementById('calendar');
        
        if (!calendarEl) {
            console.error('Calendar element not found');
            return;
        }
        
        try {
            calendarObj = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                initialDate: new Date(),
                height: 'auto',
                aspectRatio: 1.5,
                headerToolbar: {
                    left: '',
                    center: 'prev title next',
                    right: 'today'
                },
                buttonText: {
                    today: 'Today'
                },
                dayHeaderFormat: { weekday: 'short' },
                fixedWeekCount: false,
                // Handle date changes
                datesSet: function() {
                    checkIfCurrentMonth();
                },
                // Handle date clicks
                dateClick: function(info) {
                    @if(Auth::user()->role === 'admin')
                    openEventModal(info.dateStr);
                    @endif
                },
                // Display settings
                eventDisplay: 'block',
                eventMaxStack: 3,
                // Handle long event titles
                eventDidMount: function(info) {
                    // Check if the title is too long
                    const titleLength = info.event.title.length;
                    if (titleLength > 20) {
                        // Add multi-line class for long titles
                        info.el.classList.add('multi-line');
                    }
                },
                // Sample events (replace with your actual events)
                events: [
                    {
                        title: 'School Meeting',
                        start: '2025-05-15',
                        backgroundColor: '#7A1212'
                    },
                    {
                        title: 'Teacher Conference',
                        start: '2025-05-22',
                        end: '2025-05-23',
                        backgroundColor: '#3498db'
                    }
                ]
            });
            
            // Render calendar immediately
            calendarObj.render();
            
            // Add custom buttons after calendar is visible
            addCustomButtons();
        } catch (error) {
            console.error('Error initializing calendar:', error);
            document.getElementById('calendar').innerHTML = 
                '<div class="flex items-center justify-center h-full p-8">' +
                '<div class="text-red-600 text-center">' +
                '<p class="text-xl font-bold">Calendar could not be loaded</p>' +
                '<p class="mt-2">Please try refreshing the page</p>' +
                '</div></div>';
        }
    }
    
    // Add custom buttons to the calendar
    function addCustomButtons() {
        const headerRight = document.querySelector('.fc-toolbar-chunk:last-child');
        
        @if(Auth::user()->role === 'admin')
        // Create event button
        const createEventBtn = document.createElement('button');
        createEventBtn.className = 'custom-create-event';
        createEventBtn.innerHTML = '<svg class="custom-create-event-icon" style="width: 1em; height: 1em; margin-right: 4px;" fill="none" stroke="#FFF" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg> <span class="custom-create-event-text">Create new event</span>';
        createEventBtn.addEventListener('click', () => openEventModal());
        
        if (headerRight) {
            headerRight.appendChild(createEventBtn);
        }
        @endif
        
        // Check current month status
        checkIfCurrentMonth();
    }
    
    // Check if current month is showing
    function checkIfCurrentMonth() {
        if (!calendarObj) return;
        
        const calendarDate = calendarObj.getDate();
        const calendarMonth = calendarDate.getMonth();
        const calendarYear = calendarDate.getFullYear();
        
        const todayButton = document.querySelector('.fc-today-button');
        if (!todayButton) return;
        
        // Hide Today button if already on current month
        todayButton.style.display = 
            (currentMonth === calendarMonth && currentYear === calendarYear) 
            ? 'none' : '';
    }
    
    // Modal functions for event creation
    function openEventModal(dateStr = null) {
        const modal = document.getElementById('eventModal');
        const modalContent = modal.querySelector('.modal-container');
        
        if (modal) {
            // If a date was clicked, set that date in the form
            if (dateStr) {
                const startInput = document.getElementById('event-start');
                if (startInput) {
                    startInput.value = dateStr + 'T09:00';
                }
                
                const endInput = document.getElementById('event-end');
                if (endInput) {
                    endInput.value = dateStr + 'T10:00';
                }
            }
            
            // Show modal with animation
            modal.classList.remove('hidden');
            
            // Trigger animation after a small delay
            setTimeout(() => {
                modalContent.classList.remove('modal-hidden');
                modalContent.classList.add('modal-visible');
            }, 10);
        }
    }

    function closeEventModal() {
        const modal = document.getElementById('eventModal');
        const modalContent = modal.querySelector('.modal-container');
        
        if (modal) {
            // Hide with animation
            modalContent.classList.remove('modal-visible');
            modalContent.classList.add('modal-hidden');
            
            // Completely hide after animation completes
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    }

    function saveEvent() {
        // Get form values
        const titleEl = document.getElementById('event-title');
        const startEl = document.getElementById('event-start');
        const endEl = document.getElementById('event-end');
        const colorEl = document.getElementById('event-color');

        if (!titleEl || !startEl) {
            console.error('Form elements not found!');
            alert('Error: Form elements not found.');
            return;
        }

        const title = titleEl.value;
        const startStr = startEl.value;
        const endStr = endEl && endEl.value ? endEl.value : null;
        const color = colorEl && colorEl.value ? colorEl.value : '#7A1212';

        // Validate form
        if (!title || !startStr) {
            alert('Please fill in required fields');
            return;
        }

        // Check if this event should be all-day
        const hasTimeComponent = startStr.includes('T') || (endStr && endStr.includes('T'));

        // Add event to calendar with properly formatted dates
        try {
            calendarObj.addEvent({
                title: title,
                start: startStr,
                end: endStr,
                allDay: !hasTimeComponent,
                backgroundColor: color,
                textColor: (color === '#f1c40f' || color === '#2ecc71' || color === '#f39c12') ? '#000000' : '#ffffff'
            });
            console.log('Event added successfully');

            // Reset form
            if (document.getElementById('eventForm')) {
                document.getElementById('eventForm').reset();
            }

            // Close modal
            closeEventModal();
        } catch (error) {
            console.error('Error adding event to calendar:', error);
            alert('Error creating event: ' + error.message);
        }
    }
</script>
@endpush

@endsection