@extends('base')
@section('content')
@include('components.adminNavBarComponent')
@include('components.adminSideBarComponent')

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
    </div>

    @if(Auth::user()->role === 'admin')
        <!-- Event Modal -->
        <div id="eventModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
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
                            <input type="text" id="event-title"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>

                        <div>
                            <label for="event-start" class="block text-sm font-medium text-gray-700">Start Date/Time</label>
                            <input type="datetime-local" id="event-start"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>

                        <div>
                            <label for="event-end" class="block text-sm font-medium text-gray-700">End Date/Time
                                (Optional)</label>
                            <input type="datetime-local" id="event-end"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
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


    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Marcellus+SC&family=Manrope:wght@200;300;400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&family=Marcellus+SC&family=Manrope:wght@200;300;400;500;600;700;800&display=swap">
    <style>
        /* Calendar styles */
        .fc {
            background-color: white !important;
    border-radius: 24px !important;
    overflow: hidden !important;
    font-family: 'Manrope', sans-serif;
    border-radius: 24px !important;  /* Increased from 16px */
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    position: relative;
    z-index: 5;
    display: block !important;
}

#calendar-container {
    position: relative; 
    z-index: 5; 
    min-height: 600px;
    border-radius: 24px !important;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    background-color: #fff;
    width: 98% !important
}
.fc-header-toolbar {
  background: transparent !important;
  margin: 0 !important;
  padding: 0 !important;
  border: none !important;
  overflow: visible !important;
  min-height: 0px
}

/* Also round the table inside */
.fc-scrollgrid {
    border-radius: 24px !important;
    overflow: hidden;
    border: none !important;
}

/* Round the top of the header */
.fc-theme-standard .fc-scrollgrid {
    border: none !important;
}

/* Remove borders between cells for cleaner look */
.fc-theme-standard td, .fc-theme-standard th {
    border-color: #e5e5e5 !important;
}

/* Ensure day header row doesn't have overlapping issues */
.fc-theme-standard .fc-scrollgrid-section-header > * {
    border: none !important;
}
/* Add subtle white background to enhance the card feel */
.fc-daygrid-day {
    background-color: #ffffff;
}
.fc-header-toolbar.fc-toolbar.fc-toolbar-ltr {
    background: transparent !important;
    margin: 0 !important;
    padding: 15px 20px !important; /* Add consistent padding */
    border: none !important;
    box-shadow: none !important; /* Remove any shadow causing the white effect */
    width: 100% !important; /* Ensure full width */
    overflow: visible !important;
    position: relative !important;
}



        /* Toolbar layout */
        .fc .fc-toolbar {
            display: flex !important;
            justify-content: flex-start !important;
            align-items: center !important;
            position: relative !important;
            padding: 15px 20px !important;
            margin-bottom: 0 !important;
        }

        /* Center section with month and arrows */
        .fc-toolbar-chunk:nth-child(2) {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
        }

        /* Month title styling */
        .fc-toolbar-title {
            color: #000 !important;
            text-align: center !important;
            font-family: 'Lexend', sans-serif !important;
            font-size: 24px !important;
            font-style: normal !important;
            font-weight: 500 !important;
            line-height: normal !important;
            margin: 0 5px !important;
        }
        /* Navigation arrows styling */
        .fc-prev-button,
        .fc-next-button {
            background: none !important;
            border: none !important;
            width: 30px !important;
            height: 30px !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
            flex-shrink: 0 !important;
            position: relative !important;
        }

        .fc-icon-chevron-left:before,
        .fc-icon-chevron-right:before {
            content: "" !important;
        }

        .fc-prev-button .fc-icon,
        .fc-next-button .fc-icon {
            background-repeat: no-repeat !important;
            background-position: center !important;
            width: 30px !important;
            height: 30px !important;
            display: block !important;
        }

        .fc-prev-button .fc-icon {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30' fill='none'%3E%3Cpath d='M18.75 7.5L11.25 15L18.75 22.5' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") !important;
        }

        .fc-next-button .fc-icon {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30' fill='none'%3E%3Cpath d='M11.25 7.5L18.75 15L11.25 22.5' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") !important;
        }

        .fc-prev-button {
            margin-right: 15px !important;
        }

        .fc-next-button {
            margin-left: 15px !important;
        }

        /* Today button styling */
        .fc-today-button {
            background-color: #f1c40f !important;
    border: none !important;
    color: #FFF !important; /* Keeping black text on yellow background for readability */
    text-transform: none !important;
    font-family: 'Manrope', sans-serif !important;
    font-size: 16px !important;
    font-weight: 800 !important;
    font-style: normal !important;
    line-height: normal !important;
    border-radius: 20px !important;
    padding: 6px 16px !important;
    margin-right: 10px !important;
        }

        .fc-today-button:hover {
            background-color: #e2b60d !important;
        }

        /* Position the buttons on the right */
        .fc-toolbar-chunk:last-child {
            position: absolute !important;
            right: 0 !important;
            display: flex !important;
            align-items: center !important;
        }

        /* Custom create event button */
        .custom-create-event {
            background-color: #7A1212;
            
            border: none;
            border-radius: 20px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            font-size: 14px;
            cursor: pointer;
        }
        /* Custom create event button text styling */
.custom-create-event-text {
    color: #FFF;
    font-family: 'Manrope', sans-serif;
    font-size: 16px;
    font-style: normal;
    font-weight: 800;
    line-height: normal;
    
}
.custom-create-event-icon {
    color: #FFF;
    stroke: #FFF;
    stroke-width: 2.5;
    font-weight: 800;
}

        .custom-create-event:hover {
            background-color: #8A2222;
        }

        /* Match the header styling */
        .fc-col-header {
            background-color: #7A1212;
        }

        .fc-col-header-cell {
            background-color: #7A1212 !important;
            color: white !important;
            text-transform: uppercase !important;
            padding: 10px 0 !important;
            font-weight: 400 !important;
        }

        .fc-daygrid-day-number {
            font-size: 1em !important;
            font-weight: bold !important;
            padding: 8px !important;
        }

        /* Style events */
        .fc-event {
            border: none !important;
            border-radius: 3px !important;
            margin: 1px 2px !important;
            padding: 2px 4px !important;
            font-size: 0.85em !important;
            line-height: 1.3 !important;
            white-space: normal !important;
            overflow: visible !important;
        }

        /* Make day cells larger */
        .fc-daygrid-day {
            min-height: 100px !important;
        }

        .fc-daygrid-day-frame {
            min-height: 100px !important;
        }

        /* Fix for event containers */
        .fc-daygrid-event-harness {
            margin: 1px 0 !important;
        }

        /* Special styling for days outside current month */
        .fc-day-other .fc-daygrid-day-number {
            opacity: 0.5;
            background-color: #f5f5f5 !important;
        }
        .year-dropdown {
    position: relative;
    display: inline-block;
    margin-left: 10px;
    font-family: 'Lexend', sans-serif;
}

.year-dropdown-toggle {
    background: none;
    border: none;
    font-size: 24px;
    font-weight: 500;
    color: #000;
    cursor: pointer;
    padding: 0 5px;
    display: flex;
    align-items: center;
}

.year-dropdown-toggle::after {
    content: "";
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 5px solid #000;
    margin-left: 5px;
}

.year-dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    padding: 8px 0;
    min-width: 100px;
    z-index: 1000;
    display: none;
    max-height: 300px;
    overflow-y: auto;
}

.year-dropdown-menu.show {
    display: block;
}

.year-dropdown-item {
    padding: 8px 16px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.year-dropdown-item:hover {
    background-color: #f5f5f5;
}

.year-dropdown-item.selected {
    font-weight: 500;
}

.year-dropdown-item .checkmark {
    visibility: hidden;
}

.year-dropdown-item.selected .checkmark {
    visibility: visible;
}
    </style>


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        let calendarObj; // Make calendar variable accessible globally

        // Wait for everything to load
        window.addEventListener('load', function () {
            console.log('Window loaded, initializing calendar...');
            const calendarEl = document.getElementById('calendar');

            if (!calendarEl) {
                console.error('Calendar element not found!');
                return;
            }

            calendarObj = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                initialDate: new Date(), // Use current date
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
                fixedWeekCount: false, // Allows the calendar to show the exact number of weeks in a month
                datesSet: function() {
                    // This runs whenever the calendar date changes
                    checkIfCurrentMonth();
                },
                // Date click handler
                dateClick: function (info) {
                    @if(Auth::user()->role === 'admin')
                    openEventModal(info.dateStr);
                    @endif
                },
                eventDisplay: 'block', // Display events as blocks
                eventMaxStack: 3, // Show maximum 3 events per day before showing "+more"
                events: [
                    {
                        title: 'Campus Clean-up',
                        start: '2025-04-08',
                        backgroundColor: '#3498db',
                        textColor: '#ffffff'
                    },
                    {
                        title: 'Freshmen Welcome',
                        start: '2025-04-19',
                        backgroundColor: '#f1c40f',
                        textColor: '#000000'
                    },
                    {
                        title: 'TechTalk 2025',
                        start: '2025-04-19',
                        backgroundColor: '#e74c3c',
                        textColor: '#ffffff'
                    },
                    {
                        title: 'Outreach Program',
                        start: '2025-04-19',
                        backgroundColor: '#e67e22',
                        textColor: '#000000'
                    },
                    {
                        title: 'Clean & Green',
                        start: '2025-04-23',
                        backgroundColor: '#2ecc71',
                        textColor: '#000000'
                    },
                    {
                        title: 'Leadership Workshop',
                        start: '2025-04-27',
                        backgroundColor: '#3498db',
                        textColor: '#ffffff'
                    },
                    {
                        title: 'Business Planning',
                        start: '2025-04-27',
                        backgroundColor: '#f39c12',
                        textColor: '#000000'
                    },
                    {
                        title: 'Reel',
                        start: '2025-04-27',
                        backgroundColor: '#e74c3c',
                        textColor: '#ffffff'
                    }
                ],
                eventDidMount: function (info) {
                    // Ensure event text isn't truncated
                    info.el.style.whiteSpace = 'normal';
                    info.el.style.overflow = 'visible';

                    // Truncate the text if it's too long (add ellipsis)
                    const originalTitle = info.event.title;
                    if (originalTitle.length > 15) {
                        const truncatedTitle = originalTitle.substring(0, 12) + '...';
                        const eventTitleEl = info.el.querySelector('.fc-event-title');
                        if (eventTitleEl) {
                            eventTitleEl.textContent = truncatedTitle;
                            // Add tooltip with full title
                            info.el.title = originalTitle;
                        }
                    }
                }
            });

            console.log('Rendering calendar...');
            calendarObj.render();
            console.log('Calendar rendered!');

            // Add the custom create event button outside the calendar
            const headerRight = document.querySelector('.fc-toolbar-chunk:last-child');
            @if(Auth::user()->role === 'admin')
                const createEventBtn = document.createElement('button');
                createEventBtn.className = 'custom-create-event';
                createEventBtn.innerHTML = '<svg class="custom-create-event-icon" style="width: 1em; height: 1em; margin-right: 4px;" fill="none" stroke="#FFF" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg> <span class="custom-create-event-text">Create new event</span>';
                createEventBtn.addEventListener('click', openEventModal);

                if (headerRight) {
                    headerRight.appendChild(createEventBtn);
                }
            @endif

            // Check if we're on current month
            checkIfCurrentMonth();
        });

        // Function to check if the current calendar view is showing the current month
        function checkIfCurrentMonth() {
            if (!calendarObj) return;
            
            // Get the current date from the system
            const now = new Date();
            const currentMonth = now.getMonth();
            const currentYear = now.getFullYear();

            // Get the date currently displayed in the calendar
            const calendarDate = calendarObj.getDate();
            const calendarMonth = calendarDate.getMonth();
            const calendarYear = calendarDate.getFullYear();

            // Get the today button element
            const todayButton = document.querySelector('.fc-today-button');
            if (!todayButton) return;

            // Hide or show the today button based on whether we're viewing the current month
            if (currentMonth === calendarMonth && currentYear === calendarYear) {
                todayButton.style.display = 'none';
            } else {
                todayButton.style.display = '';
            }
        }

        // Event handlers for today, prev, and next buttons
        document.addEventListener('click', function(e) {
            if (e.target && (e.target.matches('.fc-today-button') || e.target.closest('.fc-today-button') || 
                             e.target.matches('.fc-prev-button') || e.target.closest('.fc-prev-button') || 
                             e.target.matches('.fc-next-button') || e.target.closest('.fc-next-button'))) {
                setTimeout(function() {
                    checkIfCurrentMonth();
                }, 100);
            }
        });

        // Modal functions
        function openEventModal(dateStr = null) {
            const modal = document.getElementById('eventModal');
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

                modal.classList.remove('hidden');
            }
        }

        function closeEventModal() {
            const modal = document.getElementById('eventModal');
            if (modal) {
                modal.classList.add('hidden');
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

            console.log('Creating event with:', { title, start: startStr, end: endStr, color });

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

@endsection