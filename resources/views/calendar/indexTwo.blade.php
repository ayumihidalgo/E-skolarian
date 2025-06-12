@extends('base')

@push('styles')
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" as="style">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js" as="script">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css">
    <link rel="stylesheet" href="{{ asset('calendar.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
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

<div id="main-content" class="transition-all duration-300 ml-[20%] lg:ml-[20%] md:ml-0 sm:ml-0">
    <!-- Calendar content section -->
    <div class="py-8 px-10 lg:py-8 lg:px-10 md:py-4 md:px-4 sm:py-2 sm:px-2">
        <!-- Calendar header with title -->
        <div class="mb-8 lg:mb-8 md:mb-4 sm:mb-4">
            <h1 class="text-black font-manrope text-2xl lg:text-3xl md:text-2xl sm:text-xl font-extrabold leading-normal text-center lg:text-left">
                Calendar 
            </h1>
        </div>

        <!-- Calendar container with responsive dimensions -->
        <div id="calendar-container" class="bg-white rounded-lg overflow-hidden shadow-md relative z-[5] min-h-[600px] lg:min-h-[600px] md:min-h-[500px] sm:min-h-[400px]">
            <div id="calendar" class="min-h-[600px] lg:min-h-[600px] md:min-h-[500px] sm:min-h-[400px] w-full"></div>
        </div>
    </div>

    <!-- Event Details Modal (Responsive) -->
    <div id="eventDetailsModal" class="fixed inset-0 modal-backdrop z-50 flex items-center justify-center hidden p-4">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md lg:max-w-md md:max-w-sm sm:max-w-[95vw] modal-container modal-hidden">
            <div class="p-6 lg:p-6 md:p-4 sm:p-4">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg lg:text-lg md:text-base sm:text-base font-semibold flex-1 mr-4" id="event-details-title">Event Details</h3>
                    <button onclick="closeEventDetailsModal()" class="text-gray-500 hover:text-gray-700 flex-shrink-0 p-1">
                        <svg class="w-5 h-5 lg:w-5 lg:h-5 md:w-4 md:h-4 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div id="event-details-content">
                        <div class="mb-4">
                            <div id="event-color-indicator" class="w-4 h-4 rounded-full inline-block mr-2"></div>
                            <strong id="detail-title" class="text-gray-900 text-sm lg:text-base break-words"></strong>
                        </div>
                        <div class="mb-4">
                            <span class="text-sm text-gray-600">Date & Time:</span>
                            <div id="detail-date" class="text-gray-800 text-sm lg:text-base break-words"></div>
                        </div>
                        <div class="mb-4">
                            <div id="event-action-buttons" class="text-sm text-gray-600">
                                <!-- Event info will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>
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
    
    // Mobile detection
    function isMobileDevice() {
        return window.innerWidth <= 768;
    }
    
    // Adjust calendar settings for mobile
    function getCalendarConfig() {
        const isMobile = isMobileDevice();
        
        return {
            initialView: 'dayGridMonth',
            initialDate: new Date(),
            height: 'auto',
            aspectRatio: isMobile ? 1.2 : 1.5,
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
            selectable: false,
            editable: false,
            
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                meridiem: 'short'  
            },
            
            // Responsive event settings
            eventMaxStack: isMobile ? 2 : 3,
        };
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Check if FullCalendar is loaded
        if (typeof FullCalendar === 'undefined') {
            // If not loaded yet, wait a bit and try loading the calendar
            const calendarScript = document.createElement('script');
            calendarScript.src = 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js';
            calendarScript.onload = function() {
                initializeCalendarWhenReady();
            };
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
            const config = getCalendarConfig();
            calendarObj = new FullCalendar.Calendar(calendarEl, {
                ...config,
                
                // Complete events function
                events: function(fetchInfo, successCallback, failureCallback) {
                    console.log('=== Fetching announcements only ===');
                    
                    fetch('/calendar/announcements')
                        .then(response => {
                            console.log('Announcements response:', response.status);
                            if (!response.ok) {
                                console.warn('Announcements failed with status', response.status);
                                return [];
                            }
                            return response.json();
                        })
                        .then(announcements => {
                            console.log('Announcements:', announcements);
                            successCallback(announcements || []);
                        })
                        .catch(error => {
                            console.error('Error fetching announcements:', error);
                            successCallback([]);
                        });
                },
                
                // Handle date changes
                datesSet: function() {
                    checkIfCurrentMonth();
                },
                
                // Handle event clicks (read-only)
                eventClick: function(info) {
                    console.log('Event clicked:', info.event);
                    info.jsEvent.preventDefault();
                    openAnnouncementDetailsModal(info.event);
                },
                
                // Display settings
                eventDisplay: 'block',
                eventMaxStack: config.eventMaxStack,
                
                // Handle event styling
                eventDidMount: function(info) {
                    // Add announcement styling
                    if (info.event.extendedProps.source === 'announcement') {
                        info.el.style.borderLeft = '4px solid #FF6347';
                        info.el.style.backgroundColor = '#FF6347';
                        info.el.setAttribute('title', 'Announcement: ' + info.event.title);
                    } else if (info.event.extendedProps.source === 'proposal') {
                        info.el.style.borderLeft = '4px solid #0085FF';
                        info.el.setAttribute('title', 'Approved Proposal: ' + info.event.title);
                    }
                    
                    // Handle long titles
                    const titleEl = info.el.querySelector('.fc-event-title');
                    if (titleEl) {
                        const fullTitle = info.event.title;
                        titleEl.setAttribute('data-full-title', fullTitle);
                        info.el.setAttribute('title', fullTitle);
                    }
                }
            });
            
            // Render calendar immediately
            calendarObj.render();


            
            
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

    function debugCalendarData() {
        console.log('=== DEBUG: Testing announcement fetch ===');
        
        fetch('/calendar/announcements')
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Announcement data:', data);
                console.log('Number of announcements:', data.length);
            })
            .catch(error => {
                console.error('Error fetching announcements:', error);
            });
    }

    // Call this function after calendar initialization
    setTimeout(debugCalendarData, 2000);

    function openAnnouncementDetailsModal(event) {
        console.log("Opening announcement details modal for:", event.title);
        
        const modal = document.getElementById('eventDetailsModal');
        const modalContent = modal.querySelector('.modal-container');
        
        if (!modal) {
            console.error('Event details modal not found');
            return;
        }
        
        // Populate the modal with announcement details
        const titleElement = document.getElementById('detail-title');
        const dateElement = document.getElementById('detail-date');
        const colorIndicator = document.getElementById('event-color-indicator');
        const actionContainer = document.getElementById('event-action-buttons');
        
        if (titleElement) {
            // Use full title and apply word wrapping styles for announcements only
            const fullTitle = event.extendedProps.full_title || event.title.replace('📢 ', '');
            titleElement.textContent = fullTitle;
            
            // Apply word wrapping styles specifically for announcements
            titleElement.style.wordWrap = 'break-word';
            titleElement.style.overflowWrap = 'break-word';
            titleElement.style.whiteSpace = 'normal';
            titleElement.style.maxWidth = '100%';
            titleElement.style.lineHeight = '1.4';
        }
        
        // Format and display the date
        if (dateElement) {
            let dateStr = '';
            const startDate = event.start ? new Date(event.start) : null;
            const endDate = event.end ? new Date(event.end) : null;
            
            if (startDate) {
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                if (event.allDay) {
                    dateStr = startDate.toLocaleDateString('en-US', options);
                    if (endDate && endDate.getTime() !== startDate.getTime()) {
                        dateStr += ' - ' + endDate.toLocaleDateString('en-US', options);
                    }
                } else {
                    const timeOptions = { ...options, hour: 'numeric', minute: '2-digit', hour12: true };
                    dateStr = startDate.toLocaleDateString('en-US', timeOptions);
                    if (endDate) {
                        dateStr += ' - ' + endDate.toLocaleDateString('en-US', timeOptions);
                    }
                }
            }
            dateElement.textContent = dateStr;
        }
        
        // Set event color indicator
        if (colorIndicator) {
            colorIndicator.style.backgroundColor = '#FF6347';
        }
        
        // Show announcement-specific content
        if (actionContainer) {
            actionContainer.innerHTML = `
                <div class="text-sm text-gray-600 mb-2" style="word-wrap: break-word; overflow-wrap: break-word;">
                    <strong>Posted by:</strong> ${event.extendedProps.poster || 'Unknown'}<br>
                    <strong>Deadline:</strong> ${event.extendedProps.deadline_text || ''}
                </div>
                <p class="text-xs text-orange-600 font-medium">📢 Deadline Announcement.</p>
            `;
        }
        
        // Show modal with animation
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('modal-hidden');
            modalContent.classList.add('modal-visible');
        }, 10);
    }

function closeEventDetailsModal() {
    console.log("Closing event details modal");
    
    const modal = document.getElementById('eventDetailsModal');
    const modalContent = modal.querySelector('.modal-container');
    
    if (modal && modalContent) {
        // Force hide the modal content first
        modalContent.style.display = 'none';
        modalContent.classList.remove('modal-visible');
        modalContent.classList.add('modal-hidden');
        
        // Then hide the modal backdrop
        modal.style.display = 'none';
        modal.classList.add('hidden');
        
        // Reset modal content after hiding
        setTimeout(() => {
            const titleEl = document.getElementById('detail-title');
            const dateEl = document.getElementById('detail-date');
            if (titleEl) titleEl.textContent = '';
            if (dateEl) dateEl.textContent = '';
            
            // Remove inline styles
            modalContent.style.display = '';
            modal.style.display = '';
            
            console.log("Modal closed");
        }, 50);
    }
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
        yearSelector.style.width = '220px';
        
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
    
    // Add window resize handler for responsive updates
    window.addEventListener('resize', function() {
        if (calendarObj) {
            calendarObj.updateSize();
        }
    });
</script>
@endpush

@endsection