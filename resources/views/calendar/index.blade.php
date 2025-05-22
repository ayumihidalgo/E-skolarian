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

    <!-- Event Details Modal -->
    <div id="eventDetailsModal" class="fixed inset-0 modal-backdrop z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md modal-container modal-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold" id="event-details-title">Event Details</h3>
                    <button onclick="closeEventDetailsModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div id="event-details-content">
                        <div class="mb-4">
                            <h4 class="text-gray-600 text-sm font-medium">Event Title</h4>
                            <p id="detail-title" class="text-gray-800 font-semibold"></p>
                        </div>
                        <div class="mb-4">
                            <h4 class="text-gray-600 text-sm font-medium">Date/Time</h4>
                            <p id="detail-date" class="text-gray-800"></p>
                        </div>
                        <div class="mb-4">
                            <div id="event-color-indicator" class="w-full h-2 rounded-full mb-1"></div>
                        </div>
                    </div>
                    @if(Auth::user()->role === 'admin')
                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" id="delete-event-btn" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                            Delete Event
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- CONFIRMATION AND DISCARD MODALS -->
 <!-- Confirm Save Changes Modal -->
<div id="confirmSaveModal" class="fixed inset-0 modal-backdrop z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md modal-container modal-hidden">
        <div class="p-6">
            <div class="text-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirm Changes</h3>
                <p class="text-gray-600">Are you sure you want to save these changes?</p>
            </div>
            <div class="flex justify-center space-x-4">
                <button type="button" onclick="closeConfirmSaveModal(false)" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <button type="button" onclick="closeConfirmSaveModal(true)" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Discard Changes Modal -->
<div id="discardChangesModal" class="fixed inset-0 modal-backdrop z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md modal-container modal-hidden">
        <div class="p-6">
            <div class="text-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Discard Changes</h3>
                <p class="text-gray-600">Are you sure you want to leave without saving changes?</p>
            </div>
            <div class="flex justify-center space-x-4">
                <button type="button" onclick="closeDiscardModal(false)" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <button type="button" onclick="closeDiscardModal(true)" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Global calendar object
    let calendarObj;
    
    // Global tracking of current month/year for the Today button
    const now = new Date();
    const currentMonth = now.getMonth();
    const currentYear = now.getFullYear();
    
    document.addEventListener('DOMContentLoaded', function() {
    // Check if FullCalendar is loaded
    if (typeof FullCalendar === 'undefined') {
        // If not loaded yet, wait a bit and try loading the calendar
        const calendarScript = document.createElement('script');
        calendarScript.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js';
        calendarScript.onload = function() {
            initializeCalendarWhenReady();
            setupEmojiValidation();
        };
        document.head.appendChild(calendarScript);
    } else {
        // FullCalendar is already loaded
        initializeCalendarWhenReady();
        setupEmojiValidation();
    }
    
});


// Function to set up emoji and special character validation
function setupEmojiValidation() {
    const titleInput = document.getElementById('event-title');
    if (titleInput) {
        // Add maxlength attribute to limit input
        titleInput.setAttribute('maxlength', '80');
        
        // Create character counter element
        const counterEl = document.createElement('div');
        counterEl.id = 'char-counter';
        counterEl.className = 'text-sm mt-1';
        titleInput.parentNode.appendChild(counterEl);
        
        titleInput.addEventListener('input', function(e) {
            const value = this.value;
            
            // Enforce 80 character limit
            if (value.length > 80) {
                this.value = value.substring(0, 80);
            }
            
            let hasInvalidChars = false;
            let warningMessage = '';
            let cleanedValue = this.value;
            
            // Check for emoji
            if (containsEmoji(cleanedValue)) {
                cleanedValue = cleanedValue.replace(/[\u{1F000}-\u{1FFFF}|\u{2600}-\u{27BF}|\u{2B50}|\u{1F004}|\u{1F0CF}|\u{1F170}-\u{1F251}|\u{1F300}-\u{1F8FF}]/gu, '');
                hasInvalidChars = true;
                warningMessage = 'Emoji are not allowed in event titles';
            }
            
            // Check for special characters (allowing only letters, numbers, spaces, commas, periods, and basic punctuation)
            if (containsSpecialChars(cleanedValue)) {
                cleanedValue = cleanedValue.replace(/[^\w\s.,;:()'"-]/g, '');
                hasInvalidChars = true;
                warningMessage = warningMessage ? 'Special characters and emoji are not allowed' : 'Special characters are not allowed';
            }
            
            // Apply changes if invalid characters were found
            if (hasInvalidChars) {
                // Update the value without invalid characters
                this.value = cleanedValue;
                
                // Show a warning
                const warningEl = document.getElementById('char-warning') || document.createElement('div');
                warningEl.id = 'char-warning';
                warningEl.className = 'text-red-600 text-sm mt-1';
                warningEl.textContent = warningMessage;
                
                if (!document.getElementById('char-warning')) {
                    this.parentNode.appendChild(warningEl);
                    
                    // Remove the warning after 3 seconds
                    setTimeout(() => {
                        warningEl.remove();
                    }, 3000);
                }
            }
            
            // Check character length and update counter
            const charLength = this.value.length;
            const counterEl = document.getElementById('char-counter');
            
            if (counterEl) {
                // Set counter message and color based on length
                if (charLength < 6) {
                    counterEl.textContent = `${charLength}/80 characters (minimum 6 required)`;
                    counterEl.className = 'text-red-600 text-sm mt-1';
                } else if (charLength > 80) {
                    counterEl.textContent = `${charLength}/80 characters (maximum exceeded)`;
                    counterEl.className = 'text-red-600 text-sm mt-1';
                } else {
                    counterEl.textContent = `${charLength}/80 characters`;
                    counterEl.className = 'text-gray-600 text-sm mt-1';
                }
            }
        });
        
        // Trigger input event to initialize counter on page load
        titleInput.dispatchEvent(new Event('input'));
    }
}

// Function to detect emoji characters
function containsEmoji(text) {
    // Regex for common emoji ranges
    const emojiRegex = /[\u{1F000}-\u{1FFFF}|\u{2600}-\u{27BF}|\u{2B50}|\u{1F004}|\u{1F0CF}|\u{1F170}-\u{1F251}|\u{1F300}-\u{1F8FF}]/u;
    return emojiRegex.test(text);
}
// Function to detect special characters
function containsSpecialChars(text) {
    // Allow letters, numbers, spaces, and basic punctuation (periods, commas, semicolons, colons, parentheses, quotes, hyphens)
    // Block everything else
    const specialCharsRegex = /[^\w\s.,;:()'"-]/;
    return specialCharsRegex.test(text);
}
    
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
                eventClick: function(info) {
                    openEventDetailsModal(info.event);
                    
                    // Prevent browser from following the link
                    info.jsEvent.preventDefault();
                },
                // Handle date clicks
                // function dateClick 
                dateClick: function(info) {
                    // Check if the clicked date is in the past

                    
                    @if(Auth::user()->role === 'admin')
                    openEventModal(info.dateStr);
                    @endif
                },
                // Display settings
                eventDisplay: 'block',
                eventMaxStack: 3,
                // Handle long event titles
                eventDidMount: function(info) {
                    // Get the title element
                    const titleEl = info.el.querySelector('.fc-event-title');
                    if (!titleEl) return;
                    
                    // Store full title for tooltip
                    const fullTitle = info.event.title;
                    titleEl.setAttribute('data-full-title', fullTitle);
                    
                    // Handle different title lengths
                    const titleLength = fullTitle.length;
                    
                    // Very long titles (60+): Use multi-line
                    if (titleLength > 60) {
                        info.el.classList.add('multi-line');
                    }
                    
                    // Add title attribute for native browser tooltip
                    info.el.setAttribute('title', fullTitle);
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
            // Setup year dropdown functionality
            setupYearDropdown();
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
// Modal functions for event creation
function openEventModal(dateStr = null) {
    const modal = document.getElementById('eventModal');
    const modalContent = modal.querySelector('.modal-container');
    
    if (modal) {
        // Reset form fields
        const eventForm = document.getElementById('eventForm');
        if (eventForm) {
            eventForm.reset();
            
            // Also clear any validation messages or character counter
            const charCounter = document.getElementById('char-counter');
            if (charCounter) {
                charCounter.textContent = '0/80 characters';
                charCounter.className = 'text-gray-600 text-sm mt-1';
            }
            
            const charWarning = document.getElementById('char-warning');
            if (charWarning) {
                charWarning.remove();
            }
        }
        
        // If a date was clicked, set that date in the form (without time)
        if (dateStr) {
            const startInput = document.getElementById('event-start');
            if (startInput) {
                // Change input type to date
                startInput.setAttribute('type', 'date');
                startInput.value = dateStr;
            }
            
            const endInput = document.getElementById('event-end');
            if (endInput) {
                // Change input type to date
                endInput.setAttribute('type', 'date');
                endInput.value = dateStr;
            }
        } else {
            // If no date was clicked, still change the input types
            const startInput = document.getElementById('event-start');
            if (startInput) {
                startInput.setAttribute('type', 'date');
            }
            
            const endInput = document.getElementById('event-end');
            if (endInput) {
                endInput.setAttribute('type', 'date');
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
    // Check if form has changes
    const titleEl = document.getElementById('event-title');
    const hasChanges = titleEl && titleEl.value.trim() !== '';
    
    if (hasChanges) {
        // If there are changes, show discard confirmation
        showDiscardChangesModal(function() {
            // This runs when user confirms discard
            const modal = document.getElementById('eventModal');
            const modalContent = modal.querySelector('.modal-container');
            
            modalContent.classList.remove('modal-visible');
            modalContent.classList.add('modal-hidden');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                // Reset form
                if (document.getElementById('eventForm')) {
                    document.getElementById('eventForm').reset();
                }
            }, 300);
        });
    } else {
        // No changes, close directly
        const modal = document.getElementById('eventModal');
        const modalContent = modal.querySelector('.modal-container');
        
        modalContent.classList.remove('modal-visible');
        modalContent.classList.add('modal-hidden');
        
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
    
    if (!titleEl || !startEl) {
        console.error('Form elements not found!');
        alert('Error: Form elements not found.');
        return;
    }

    const title = titleEl.value;
    const trimmedTitle = title.trim();
// Alternative combined check
    if (title.startsWith(' ') || !trimmedTitle) {
        alert('Event title cannot be empty or start with spaces.');
        return;
    }
    const startStr = startEl.value;
    const endStr = endEl && endEl.value ? endEl.value : null;
    const defaultColor = '#7A1212'; // Default maroon color for all events
    
    // Validate form first
    if (!title || !startStr) {
        alert('Please fill in required fields');
        return;
    }
    
    // THEN check if the event date is in the past

    // Rest of your validations
    // Check for emoji or special characters in title
    if (containsEmoji(title) || containsSpecialChars(title)) {
        alert('Your event title contains invalid characters. Please use only letters, numbers, spaces, and basic punctuation.');
        return;
    }
    
    // Check character limit (6-80 range)
    const titleLength = title.length;
    if (titleLength < 6) {
        alert('Event title is too short. Please use at least 6 characters.');
        return;
    }
    if (titleLength > 80) {
        alert('Event title is too long. Please keep it under 80 characters.');
        return; 
    }

    // Check for duplicate event titles
    const existingEvents = calendarObj.getEvents();
    const duplicateEvent = existingEvents.find(event => 
        event.title.toLowerCase() === title.toLowerCase()
    );
    
    if (duplicateEvent) {
        alert('An event with this title already exists. Please use a different title.');
        return;
    }
    

    // Check if this event should be all-day
    const hasTimeComponent = startStr.includes('T') || (endStr && endStr.includes('T'));
    
    
    
 showConfirmSaveModal(function() {
   // Add event to calendar with properly formatted dates
    try {
        calendarObj.addEvent({
            title: title,
            start: startStr,
            end: endStr,
            allDay: !hasTimeComponent,
            backgroundColor: defaultColor,
            textColor: '#ffffff'
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
})
}
    function containsEmoji(text) {
    // Regex for common emoji ranges
    const emojiRegex = /[\u{1F000}-\u{1FFFF}|\u{2600}-\u{27BF}|\u{2B50}|\u{1F004}|\u{1F0CF}|\u{1F170}-\u{1F251}|\u{1F300}-\u{1F8FF}]/u;
    return emojiRegex.test(text);
}
// Functions to handle the event details modal
function openEventDetailsModal(event) {
    const modal = document.getElementById('eventDetailsModal');
    const modalContent = modal.querySelector('.modal-container');
    
    if (!modal) return;
    
    // Populate the modal with event details
    document.getElementById('detail-title').textContent = event.title;
    
    // Format and display the date
    let dateStr = '';
    const startDate = event.start ? new Date(event.start) : null;
    const endDate = event.end ? new Date(event.end) : null;
    
    if (startDate) {
        // Format the date nicely
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        if (event.allDay) {
            dateStr = startDate.toLocaleDateString(undefined, options);
            if (endDate) {
                const endStr = endDate.toLocaleDateString(undefined, options);
                dateStr += ' to ' + endStr;
            }
        } else {
            options.hour = 'numeric';
            options.minute = 'numeric';
            dateStr = startDate.toLocaleString(undefined, options);
            if (endDate) {
                const endStr = endDate.toLocaleString(undefined, options);
                dateStr += ' to ' + endStr;
            }
        }
    }
    
    document.getElementById('detail-date').textContent = dateStr;
    
    // Set event color indicator
    const colorIndicator = document.getElementById('event-color-indicator');
    if (colorIndicator) {
        colorIndicator.style.backgroundColor = event.backgroundColor || '#7A1212';
    }
    
    @if(Auth::user()->role === 'admin')
    // Set up delete button handler
    const deleteBtn = document.getElementById('delete-event-btn');
    if (deleteBtn) {
        // Remove any existing event listeners
        const newDeleteBtn = deleteBtn.cloneNode(true);
        deleteBtn.parentNode.replaceChild(newDeleteBtn, deleteBtn);
        
        // Add event listener for deleting the event
        newDeleteBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this event?')) {
                event.remove();
                closeEventDetailsModal();
            }
        });
    }
    @endif
    
    // Show modal with animation
    modal.classList.remove('hidden');
    
    // Trigger animation after a small delay
    setTimeout(() => {
        modalContent.classList.remove('modal-hidden');
        modalContent.classList.add('modal-visible');
    }, 10);
}

function closeEventDetailsModal() {
    const modal = document.getElementById('eventDetailsModal');
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
// Improved Year Dropdown implementation
function setupYearDropdown() {
    // Find the title element and set up a click handler
    const titleElement = document.querySelector('.fc-toolbar-title');
    if (!titleElement) return;
    
    // Make the title element clickable
    titleElement.style.cursor = 'pointer';
    titleElement.setAttribute('title', 'Click to change year');
    
    titleElement.addEventListener('click', function() {
        // Extract the current year
        const match = this.textContent.match(/\d{4}/);
        if (!match) return;
        
        const currentYear = parseInt(match[0]);
        showYearSelector(currentYear, this);
    });
}

// Improved Year Selector with better spacing
function showYearSelector(currentYear, titleElement) {
    // Remove existing year selector
    const existingSelector = document.getElementById('year-selector');
    if (existingSelector) {
        existingSelector.remove();
        return;
    }
    
    // Create a custom inline year selector
    const yearSelector = document.createElement('div');
    yearSelector.id = 'year-selector';
    yearSelector.className = 'flex items-center bg-white border border-gray-300 rounded-md';
    yearSelector.style.position = 'absolute';
    yearSelector.style.zIndex = '100';
    
    // Position it over the title
    const rect = titleElement.getBoundingClientRect();
    yearSelector.style.top = (rect.top + window.scrollY) + 'px';
    yearSelector.style.left = (rect.left + window.scrollX + rect.width/2 - 100) + 'px';
    yearSelector.style.width = '220px'; // Increased width
    
    // Add a select dropdown
    const selectContainer = document.createElement('div');
    selectContainer.className = 'relative flex-grow';
    
    const select = document.createElement('select');
    select.className = 'block w-full px-3 py-2 text-base font-medium text-gray-900 focus:outline-none';
    select.style.border = 'none';
    select.style.backgroundColor = 'transparent';
    select.style.appearance = 'none';
    select.style.paddingRight = '1.5rem';
    
    // Add years (20 years back, 20 years forward)
    const startYear = currentYear - 20;
    const endYear = currentYear + 20;
    
    for (let year = startYear; year <= endYear; year++) {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        if (year === currentYear) {
            option.selected = true;
        }
        select.appendChild(option);
    }
    
    // Add change handler
    select.addEventListener('change', function() {
        const selectedYear = parseInt(this.value);
        const currentDate = calendarObj.getDate();
        const newDate = new Date(selectedYear, currentDate.getMonth(), 1);
        calendarObj.gotoDate(newDate);
        yearSelector.remove();
    });
    
    // Add custom arrow inside the select container
    const arrow = document.createElement('div');
    arrow.className = 'pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700';
    arrow.innerHTML = '<svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>';
    
    selectContainer.appendChild(select);
    selectContainer.appendChild(arrow);
    
    // Add a cancel button with more spacing
    const cancelBtn = document.createElement('button');
    cancelBtn.className = 'px-3 py-2 text-gray-700 hover:text-gray-900 hover:bg-gray-100 border-l';
    cancelBtn.innerHTML = '×';
    cancelBtn.style.fontSize = '1.2rem';
    cancelBtn.style.fontWeight = 'bold';
    cancelBtn.style.borderLeft = '1px solid #e2e8f0';
    cancelBtn.setAttribute('title', 'Close');
    cancelBtn.addEventListener('click', function() {
        yearSelector.remove();
    });
    
    // Append everything
    yearSelector.appendChild(selectContainer);
    yearSelector.appendChild(cancelBtn);
    document.body.appendChild(yearSelector);
    
    // Auto-focus the select
    select.focus();
    
    // Close when clicking outside
    document.addEventListener('click', function closeSelector(e) {
        if (!yearSelector.contains(e.target) && e.target !== titleElement) {
            yearSelector.remove();
            document.removeEventListener('click', closeSelector);
        }
    });
}
// Variables to track callbacks
let confirmSaveCallback = null;
// Confirm Save Modal functions
function showConfirmSaveModal(callback) {
    const modal = document.getElementById('confirmSaveModal');
    const modalContent = modal.querySelector('.modal-container');
    
    // Store the callback function
    confirmSaveCallback = callback;
    
    // Show modal with animation
    modal.classList.remove('hidden');
    
    // Trigger animation after a small delay
    setTimeout(() => {
        modalContent.classList.remove('modal-hidden');
        modalContent.classList.add('modal-visible');
    }, 10);
}

function closeConfirmSaveModal(confirmed) {
    const modal = document.getElementById('confirmSaveModal');
    const modalContent = modal.querySelector('.modal-container');
    
    // Hide with animation
    modalContent.classList.remove('modal-visible');
    modalContent.classList.add('modal-hidden');
    
    // Completely hide after animation completes
    setTimeout(() => {
        modal.classList.add('hidden');
        
        // Call the callback if it exists
        if (confirmed && typeof confirmSaveCallback === 'function') {
            confirmSaveCallback();
        }
        
        // Reset the callback
        confirmSaveCallback = null;
    }, 300);
}
let discardChangesCallback = null;



// Discard Changes Modal functions
function showDiscardChangesModal(callback) {
    const modal = document.getElementById('discardChangesModal');
    const modalContent = modal.querySelector('.modal-container');
    
    // Store the callback function
    discardChangesCallback = callback;
    
    // Show modal with animation
    modal.classList.remove('hidden');
    
    // Trigger animation after a small delay
    setTimeout(() => {
        modalContent.classList.remove('modal-hidden');
        modalContent.classList.add('modal-visible');
    }, 10);
}

function closeDiscardModal(confirmed) {
    const modal = document.getElementById('discardChangesModal');
    const modalContent = modal.querySelector('.modal-container');
    
    // Hide with animation
    modalContent.classList.remove('modal-visible');
    modalContent.classList.add('modal-hidden');
    
    // Completely hide after animation completes
    setTimeout(() => {
        modal.classList.add('hidden');
        
        // Call the callback if it exists
        if (confirmed && typeof discardChangesCallback === 'function') {
            discardChangesCallback();
        }
        
        // Reset the callback
        discardChangesCallback = null;
    }, 300);
}
</script>
@endpush

@endsection