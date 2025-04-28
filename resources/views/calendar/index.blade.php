@extends('app')

@section('content')
    <div class="flex min-h-screen">
        <!-- Sidebar with dark red background - exact match to Image 2 -->
        <div class="fixed left-0 top-0 w-64 h-screen overflow-y-auto z-50 bg-[#7A1212] text-white">
            <!-- Logo area -->
            <div class="p-4 pb-6">
                <div class="flex items-center gap-2">
                    <img src="/img/star-logo.png" alt="Star Logo" class="h-10">
                    <div>
                        <div class="text-lg font-semibold">E-SKOLARI*N</div>
                        <div class="text-xs opacity-80">DOCUMENT MANAGEMENT</div>
                    </div>
                </div>
                <div class="absolute right-4 top-4">
                    <button class="rounded-full bg-[#efb72b] w-6 h-6 flex items-center justify-center text-black">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Navigation links -->
            <nav class="mt-2">
                <a href="#" class="flex items-center px-6 py-3 text-white hover:bg-[#8a2222]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Home</span>
                </a>
                <a href="#" class="flex items-center px-6 py-3 text-white hover:bg-[#8a2222]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Review</span>
                </a>
                <a href="#" class="flex items-center px-6 py-3 text-white hover:bg-[#8a2222]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    <span>Archive</span>
                </a>
                <a href="#" class="flex items-center px-6 py-3 bg-[#8a2222] text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Calendar</span>
                </a>
                <a href="#" class="flex items-center px-6 py-3 text-white hover:bg-[#8a2222]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Settings</span>
                </a>
            </nav>

            <!-- Logout at bottom -->
            <div class="absolute bottom-8 left-0 w-full px-6">
                <a href="#" class="flex items-center text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Log Out</span>
                </a>
            </div>
        </div>

        <!-- Main content area -->
        <div class="ml-64 w-full bg-white min-h-screen">
            <!-- Top header bar -->
            <div class="w-full bg-[#7A1212] text-white py-3 px-6 flex justify-between items-center">
                <div></div> <!-- Empty div for spacing -->
                <div class="flex items-center gap-6">
                    <a href="#" class="text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </a>
                    <div class="flex items-center gap-2">
                        <div class="h-8 w-8 rounded-full bg-gray-200 overflow-hidden">
                            <img src="/img/admin-avatar.jpg" alt="Admin" class="h-full w-full object-cover">
                        </div>
                        <span>Admin</span>
                    </div>
                </div>
            </div>

            <!-- Calendar content section -->
            <div class="py-8 px-10">
                <!-- Calendar header with title only (no create button) -->
                <div class="mb-8 flex justify-between items-center">
                    <h1
                        style="color: #000; font-family: Manrope, sans-serif; font-size: 32px; font-weight: 800; line-height: normal;">
                        Calendar of Activities</h1>
                </div>

                <!-- Calendar container with proper rounded corners -->
                <div>
                    <div id="calendar-container" class="bg-white rounded-lg overflow-hidden shadow-md">
                        <div id="calendar"></div>
                    </div>
                </div>
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
@endsection

@push('styles')
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css">
    <style>
        /* Global styles */
        body {
            overflow-x: hidden;
            font-family: 'Arial', sans-serif;
        }

        /* Calendar styles to precisely match Image 2 */
        .fc {
            font-family: 'Arial', sans-serif;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Improve the toolbar layout to match the image */
        .fc .fc-toolbar {
            display: flex !important;
            justify-content: flex-start !important;
            /* Changed from center to flex-start */
            align-items: center !important;
            position: relative !important;
            padding: 10px 0 10px 20px !important;
            /* Added left padding */
            margin-bottom: 0 !important;
        }

        /* Center section with month and arrows - adjust spacing */
        .fc-toolbar-chunk:nth-child(2) {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            /* Changed from center to flex-start */
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

        /* Update navigation arrows to use SVG */
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

        /* Remove the default content */
        .fc-icon-chevron-left:before,
        .fc-icon-chevron-right:before {
            content: "" !important;
        }


        /* Create SVG arrows using pseudo-elements */
        .fc-prev-button .fc-icon,
        .fc-next-button .fc-icon {
            background-repeat: no-repeat !important;
            background-position: center !important;
            width: 30px !important;
            height: 30px !important;
            display: block !important;
        }

        /* Left arrow SVG as background */
        .fc-prev-button .fc-icon {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30' fill='none'%3E%3Cpath d='M18.75 7.5L11.25 15L18.75 22.5' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") !important;
        }

        /* Right arrow SVG (flipped version of left) */
        .fc-next-button .fc-icon {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30' fill='none'%3E%3Cpath d='M11.25 7.5L18.75 15L11.25 22.5' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") !important;
        }

        /* Hover states */
        .fc-prev-button:hover .fc-icon,
        .fc-next-button:hover .fc-icon {
            opacity: 0.8;
        }

        /* Adjusted spacing between arrows and month */
        .fc-prev-button {
            margin-right: 15px !important; /* Reduced from 120px */
        }

        .fc-next-button {
            margin-left: 15px !important; /* Reduced from 120px */
        }


        .fc-prev-button:hover,
        .fc-next-button:hover {
            background: none !important;
            color: #7A1212 !important;
        }

        /* Today button styling to match the gold button in the image */
        .fc-today-button {
            background-color: #f1c40f !important;
            border: none !important;
            color: black !important;
            text-transform: none !important;
            font-weight: normal !important;
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
            color: white;
            border: none;
            border-radius: 20px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            font-size: 14px;
            cursor: pointer;
        }

        .custom-create-event:hover {
            background-color: #8A2222;
        }

        /* Match the header styling in Image 2 */
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

        /* Style events to match Image 2 */
        .fc-event {
            border: none !important;
            border-radius: 3px !important;
            margin: 1px 2px !important;
            padding: 2px 4px !important;
            font-size: 0.85em !important;
            line-height: 1.3 !important;
            white-space: normal !important;
            /* Allow text to wrap */
            overflow: visible !important;
        }

        /* Day 18 special styling (highlighted in yellow) */
        .fc-day-sat.fc-day-18 .fc-daygrid-day-frame {
            background-color: #f1c40f !important;
        }

        /* Make day cells larger with proper height */
        .fc-daygrid-day {
            min-height: 100px !important;
        }

        /* Date cell styling */
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
            color: #888;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        let calendarObj; // Make calendar variable accessible globally

        // Wait for everything to load
        window.addEventListener('load', function () {
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
                // Special day rendering
                dayCellDidMount: function (info) {
                    // Highlight day 18 (as shown in the image)
                    if (info.date.getDate() === 18 && info.date.getMonth() === 3) { // April = 3
                        info.el.style.backgroundColor = '#f1c40f';
                    }
                },
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

            calendarObj.render();

            // Add the custom create event button outside the calendar
            const headerRight = document.querySelector('.fc-toolbar-chunk:last-child');
            const createEventBtn = document.createElement('button');
            createEventBtn.className = 'custom-create-event';
            createEventBtn.innerHTML = '<svg class="w-4 h-4 mr-1" style="width: 1em; height: 1em; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg> Create new event';
            createEventBtn.addEventListener('click', openEventModal);

            if (headerRight) {
                headerRight.appendChild(createEventBtn);
            }

            // Additional styling tweaks after rendering
            document.querySelectorAll('.fc-daygrid-day-frame').forEach(el => {
                el.style.minHeight = '100px';
            });

            // Check if we're on current month
            checkIfCurrentMonth();
        });

        // Function to check if the current calendar view is showing the current month
        function checkIfCurrentMonth() {
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


        // Modify the click handler for the today button
        document.addEventListener('click', function (e) {
            if (e.target && (e.target.matches('.fc-today-button') || e.target.closest('.fc-today-button'))) {
                // After clicking Today button, it will go to current month
                setTimeout(function () {
                    checkIfCurrentMonth();
                }, 100); // Small delay to ensure the calendar has updated
            }
        });
        // Also add a listener for prev/next buttons to ensure Today button appears when navigating away
        document.addEventListener('click', function (e) {
            if (e.target && (e.target.matches('.fc-prev-button') || e.target.closest('.fc-prev-button') ||
                e.target.matches('.fc-next-button') || e.target.closest('.fc-next-button'))) {
                // When clicking prev/next, check if we should show/hide Today button
                setTimeout(function () {
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
@endpush