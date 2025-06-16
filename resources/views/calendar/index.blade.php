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


<div id="main-content" class="transition-all duration-300 ml-[20%]">
    <!-- Calendar content section -->
    <div class="py-8 px-10 lg:py-8 lg:px-10 md:py-4 md:px-4 sm:py-2 sm:px-2">
        <!-- Calendar header with title -->
        <!-- Calendar header with title and navigation in one line -->
        <div class="mb-8 lg:mb-8 md:mb-4 sm:mb-2 grid lg:grid-cols-3 md:grid-cols-1 sm:grid-cols-1">
    <!-- Left: Calendar Title and Show Past Toggle -->
    <div>
        <h1 class="text-black font-manrope text-xl lg:text-3xl md:text-2xl sm:text-xl font-extrabold leading-normal mb-2 text-center lg:text-left">
            Calendar
        </h1>
        <!-- Show Past Deadlines Toggle - Moved here -->
        <div class="flex items-center lg:justify-start justify-center mb-3">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" id="show-past-toggle" class="sr-only">
                <div class="relative">
                    <div class="block bg-gray-600 w-12 h-6 rounded-full"></div>
                    <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition"></div>
                </div>
                <div class="ml-2 text-gray-700 font-medium text-sm">
                    Show Past Deadlines
                </div>
            </label>
        </div>
    </div>
   
    <!-- Middle: Month & Year Dropdowns with Navigation Arrows (centered) -->
     <div class="flex items-center justify-center mb-4 md:mb-3 sm:mb-2 -mt-8">
        <div class="flex items-center gap-2 md:gap-3 lg:gap-6">


                    <!-- Left Arrow (Previous Month) -->
                    <button id="prev-month" class="flex-shrink-0 focus:outline-none hover:opacity-80 transition-opacity p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 30 30" fill="none">
                            <path d="M18.75 7.5L11.25 15L18.75 22.5" stroke="black" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                     <!-- MOBILE VERSION: Month & Year (simplified on mobile) -->
            <div class="flex items-center md:hidden">
                <div id="mobile-month-trigger" class="text-black text-center font-lexend text-lg lg:text-2xl font-medium mr-1 min-h-[44px] flex items-center">
                    <span id="mobile-selected-month"class="font-bold">January</span>
                </div>
                <div id="mobile-year-trigger" class="text-black text-center font-lexend text-lg lg:text-2xl font-medium min-h-[44px] flex items-center">
                    <span id="mobile-selected-year"class="font-bold">2023</span>
                </div>
            </div>

                   
                    <!-- Custom Month Dropdown -->
                    <div class="relative mx-2 hidden md:block">
                        <!-- Custom dropdown trigger -->
                        <div id="month-dropdown-trigger" class="appearance-none bg-transparent border-b border-transparent pr-6 py-1 cursor-pointer text-black text-center font-lexend text-2xl font-medium leading-normal hover:border-blue-500 focus:border-blue-500 transition-colors flex items-center">
                            <span id="selected-month"class="font-bold">January</span>
                            <div class="absolute inset-y-0 right-0 flex items-center px-1 text-gray-700 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" class="flex-shrink-0">
                                    <path d="M1 1.5L6 6.5L11 1.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                       
                        <!-- Custom dropdown menu -->
                        <div id="month-dropdown-menu" class="absolute mt-1 bg-white border border-gray-200 rounded shadow-lg z-10 w-48 hidden">
                            <div class="py-1 max-h-60 overflow-y-auto">
                                <div class="flex px-2 py-2 items-center gap-2 hover:bg-gray-100 cursor-pointer" data-value="0">
                                    <div class="w-5 h-5 checkmark-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 21" fill="none">
                                            <path d="M16.6668 5.5L7.50016 14.6667L3.3335 10.5" stroke="#161616" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>January</span>
                                </div>
                                <div class="flex px-2 py-2 items-center gap-2 hover:bg-gray-100 cursor-pointer" data-value="1">
                                    <div class="w-5 h-5 checkmark-icon invisible">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 21" fill="none">
                                            <path d="M16.6668 5.5L7.50016 14.6667L3.3335 10.5" stroke="#161616" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>February</span>
                                </div>
                                <div class="flex px-2 py-2 items-center gap-2 hover:bg-gray-100 cursor-pointer" data-value="2">
                                    <div class="w-5 h-5 checkmark-icon invisible">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 21" fill="none">
                                            <path d="M16.6668 5.5L7.50016 14.6667L3.3335 10.5" stroke="#161616" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>March</span>
                                </div>
                                <div class="flex px-2 py-2 items-center gap-2 hover:bg-gray-100 cursor-pointer" data-value="3">
                                    <div class="w-5 h-5 checkmark-icon invisible">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 21" fill="none">
                                            <path d="M16.6668 5.5L7.50016 14.6667L3.3335 10.5" stroke="#161616" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>April</span>
                                </div>
                                <div class="flex px-2 py-2 items-center gap-2 hover:bg-gray-100 cursor-pointer" data-value="4">
                                    <div class="w-5 h-5 checkmark-icon invisible">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 21" fill="none">
                                            <path d="M16.6668 5.5L7.50016 14.6667L3.3335 10.5" stroke="#161616" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>May</span>
                                </div>
                                <div class="flex px-2 py-2 items-center gap-2 hover:bg-gray-100 cursor-pointer" data-value="5">
                                    <div class="w-5 h-5 checkmark-icon invisible">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 21" fill="none">
                                            <path d="M16.6668 5.5L7.50016 14.6667L3.3335 10.5" stroke="#161616" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>June</span>
                                </div>
                                <div class="flex px-2 py-2 items-center gap-2 hover:bg-gray-100 cursor-pointer" data-value="6">
                                    <div class="w-5 h-5 checkmark-icon invisible">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 21" fill="none">
                                            <path d="M16.6668 5.5L7.50016 14.6667L3.3335 10.5" stroke="#161616" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>July</span>
                                </div>
                                <div class="flex px-2 py-2 items-center gap-2 hover:bg-gray-100 cursor-pointer" data-value="7">
                                    <div class="w-5 h-5 checkmark-icon invisible">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 21" fill="none">
                                            <path d="M16.6668 5.5L7.50016 14.6667L3.3335 10.5" stroke="#161616" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>August</span>
                                </div>
                                <div class="flex px-2 py-2 items-center gap-2 hover:bg-gray-100 cursor-pointer" data-value="8">
                                    <div class="w-5 h-5 checkmark-icon invisible">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 21" fill="none">
                                            <path d="M16.6668 5.5L7.50016 14.6667L3.3335 10.5" stroke="#161616" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>September</span>
                                </div>
                                <div class="flex px-2 py-2 items-center gap-2 hover:bg-gray-100 cursor-pointer" data-value="9">
                                    <div class="w-5 h-5 checkmark-icon invisible">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 21" fill="none">
                                            <path d="M16.6668 5.5L7.50016 14.6667L3.3335 10.5" stroke="#161616" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>October</span>
                                </div>
                                <div class="flex px-2 py-2 items-center gap-2 hover:bg-gray-100 cursor-pointer" data-value="10">
                                    <div class="w-5 h-5 checkmark-icon invisible">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 21" fill="none">
                                            <path d="M16.6668 5.5L7.50016 14.6667L3.3335 10.5" stroke="#161616" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>November</span>
                                </div>
                                <div class="flex px-2 py-2 items-center gap-2 hover:bg-gray-100 cursor-pointer" data-value="11">
                                    <div class="w-5 h-5 checkmark-icon invisible">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 21" fill="none">
                                            <path d="M16.6668 5.5L7.50016 14.6667L3.3335 10.5" stroke="#161616" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>December</span>
                                </div>
                            </div>
                        </div>
                       
                        <!-- Hidden native select (for functionality) -->
                        <select id="month-dropdown" class="hidden">
                            <option value="0">January</option>
                            <option value="1">February</option>
                            <option value="2">March</option>
                            <option value="3">April</option>
                            <option value="4">May</option>
                            <option value="5">June</option>
                            <option value="6">July</option>
                            <option value="7">August</option>
                            <option value="8">September</option>
                            <option value="9">October</option>
                            <option value="10">November</option>
                            <option value="11">December</option>
                        </select>
                    </div>
                   
                    <!-- Year Dropdown (Custom) -->
                    <div class="relative mx-2 hidden md:block">
                        <!-- Custom dropdown trigger -->
                        <div id="year-dropdown-trigger" class="appearance-none bg-transparent border-b border-transparent pr-6 py-1 cursor-pointer text-black text-center font-lexend text-2xl font-medium leading-normal hover:border-blue-500 focus:border-blue-500 transition-colors flex items-center">
                            <span id="selected-year"class="font-bold">2023</span>
                            <div class="absolute inset-y-0 right-0 flex items-center px-1 text-gray-700 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" class="flex-shrink-0">
                                    <path d="M1 1.5L6 6.5L11 1.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                       
                        <!-- Custom dropdown menu -->
                        <div id="year-dropdown-menu" class="absolute mt-1 bg-white border border-gray-200 rounded shadow-lg z-10 w-48 hidden">
                            <div id="year-options-container" class="py-1 max-h-60 overflow-y-auto">
                                <!-- Year options will be populated by JavaScript -->
                            </div>
                        </div>
                       
                        <!-- Hidden native select (for functionality) -->
                        <select id="year-dropdown" class="hidden">
                            <!-- Years will be populated by JavaScript -->
                        </select>
                    </div>


                <!-- Right Arrow -->
                <button id="next-month" class="flex-shrink-0 focus:outline-none hover:opacity-80 transition-opacity p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 30 30" fill="none">
                        <path d="M11.25 22.5L18.75 15L11.25 7.5" stroke="black" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>
           
    <!-- Today Button -->
    <div class="flex justify-center lg:justify-end items-center lg:pr-8 -mt-8">
        <button id="today-btn" class="flex justify-center items-center w-[85px] h-[40px] p-[10px] gap-[10px] rounded-[22px] bg-[#DAA520] transition-colors invisible hover:bg-[#c99418]">
            <span class="text-white font-manrope text-[14px] lg:text-[16px] font-extrabold leading-normal underline decoration-solid">Today</span>
        </button>
    </div>
</div>


        <!-- Calendar container with responsive dimensions -->
        <div id="calendar-container" class="bg-white rounded-lg overflow-hidden shadow-md relative z-[5] min-h-[600px] lg:min-h-[600px] md:min-h-[500px] sm:min-h-[400px]">
            <div id="calendar" class="min-h-[600px] lg:min-h-[600px] md:min-h-[500px] sm:min-h-[400px] w-full"></div>
        </div>
    </div>


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


    <!-- Add footer here with proper spacing -->
    <div class="mt-auto">
        @include('components.footer')
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
        initialView: isMobile ? 'listMonth' : 'dayGridMonth', // Use list view on mobile
        initialDate: new Date(),
        height: 'auto',
        aspectRatio: isMobile ? 1.1 : 1.5,
        expandRows: !isMobile,  // Don't expand rows on mobile
        headerToolbar: false,   // We're using custom header
        dayHeaderFormat: { weekday: 'short' },
        fixedWeekCount: false,
        selectable: false,
        editable: false,
        contentHeight: 'auto',
        nextDayThreshold: '24:00:00',
        
        // These settings help with mobile
        dayMaxEventRows: isMobile ? 2 : 3,
        eventMaxStack: isMobile ? 2 : 3,
        
        // Better time formatting
        eventTimeFormat: {
            hour: 'numeric',
            minute: '2-digit',
            meridiem: 'short'
        }
    };
}

// Add this mobile detection function
function isMobileDevice() {
    return window.innerWidth <= 768;
}
// Initialize mobile-optimized month/year selection
function setupMobileMonthYearSelection() {
    const mobileTriggers = {
        month: document.getElementById('mobile-month-trigger'),
        year: document.getElementById('mobile-year-trigger')
    };
    
    if (!mobileTriggers.month || !mobileTriggers.year) return;
    
    // Initialize displays
    updateMobileSelectors();
    
    // Setup click handlers for mobile
    mobileTriggers.month.addEventListener('click', function() {
        openMobileMonthSelector();
    });
    
    mobileTriggers.year.addEventListener('click', function() {
        openMobileYearSelector();
    });
}

// Create a mobile month selector popup
function openMobileMonthSelector() {
    // Remove any existing popup first
    removeMobileSelectors();
    
    // Create the month selector
    const popup = document.createElement('div');
    popup.className = 'month-popup fixed bottom-0 left-0 right-0 bg-white shadow-lg z-50 p-4';
    popup.style.maxHeight = '60vh';
    popup.style.overflowY = 'auto';
    popup.style.borderRadius = '12px 12px 0 0';
    
    // Create header
    const header = document.createElement('div');
    header.className = 'flex justify-between items-center mb-4 pb-2 border-b';
    
    const title = document.createElement('h3');
    title.className = 'text-lg font-medium';
    title.textContent = 'Select Month';
    
    const closeBtn = document.createElement('button');
    closeBtn.className = 'text-gray-500 p-2';
    closeBtn.innerHTML = '&times;';
    closeBtn.onclick = removeMobileSelectors;
    
    header.appendChild(title);
    header.appendChild(closeBtn);
    popup.appendChild(header);
    
    // Create month grid
    const monthsGrid = document.createElement('div');
    monthsGrid.className = 'grid grid-cols-3 gap-2';
    
    const months = [
        'January', 'February', 'March', 
        'April', 'May', 'June', 
        'July', 'August', 'September', 
        'October', 'November', 'December'
    ];
    
    const currentMonth = calendarObj.getDate().getMonth();
    
    months.forEach((month, index) => {
        const monthBtn = document.createElement('button');
        monthBtn.className = 'p-3 text-center rounded-lg ' + 
                            (currentMonth === index ? 
                                'bg-[#DAA520] text-white' : 
                                'bg-gray-100 text-gray-800 hover:bg-gray-200');
        monthBtn.textContent = month;
        
        monthBtn.addEventListener('click', function() {
            // Update calendar month
            const currentDate = calendarObj.getDate();
            const newDate = new Date(currentDate.getFullYear(), index, 1);
            calendarObj.gotoDate(newDate);
            
            // Update UI
            document.getElementById('selected-month').textContent = month;
            document.getElementById('mobile-selected-month').textContent = month;
            
            // Close popup
            removeMobileSelectors();
        });
        
        monthsGrid.appendChild(monthBtn);
    });
    
    popup.appendChild(monthsGrid);
    document.body.appendChild(popup);
    
    // Animate in
    setTimeout(() => {
        popup.classList.add('active');
    }, 10);
    
    // Add backdrop and close on backdrop click
    const backdrop = document.createElement('div');
    backdrop.className = 'fixed inset-0 bg-black bg-opacity-50 z-40';
    backdrop.addEventListener('click', removeMobileSelectors);
    document.body.appendChild(backdrop);
}

// Create a mobile year selector popup
function openMobileYearSelector() {
    // Remove any existing popup first
    removeMobileSelectors();
    
    // Create the year selector
    const popup = document.createElement('div');
    popup.className = 'year-popup fixed bottom-0 left-0 right-0 bg-white shadow-lg z-50 p-4';
    popup.style.maxHeight = '60vh';
    popup.style.overflowY = 'auto';
    popup.style.borderRadius = '12px 12px 0 0';
    
    // Create header
    const header = document.createElement('div');
    header.className = 'flex justify-between items-center mb-4 pb-2 border-b';
    
    const title = document.createElement('h3');
    title.className = 'text-lg font-medium';
    title.textContent = 'Select Year';
    
    const closeBtn = document.createElement('button');
    closeBtn.className = 'text-gray-500 p-2';
    closeBtn.innerHTML = '&times;';
    closeBtn.onclick = removeMobileSelectors;
    
    header.appendChild(title);
    header.appendChild(closeBtn);
    popup.appendChild(header);
    
    // Create year list
    const yearsContainer = document.createElement('div');
    yearsContainer.className = 'grid grid-cols-3 gap-2';
    
    const currentYear = calendarObj.getDate().getFullYear();
    const startYear = currentYear - 10;
    const endYear = currentYear + 10;
    
    for (let year = startYear; year <= endYear; year++) {
        const yearBtn = document.createElement('button');
        yearBtn.className = 'p-3 text-center rounded-lg ' + 
                           (year === currentYear ? 
                              'bg-[#DAA520] text-white' : 
                              'bg-gray-100 text-gray-800 hover:bg-gray-200');
        yearBtn.textContent = year;
        
        yearBtn.addEventListener('click', function() {
            // Update calendar year
            const currentDate = calendarObj.getDate();
            const newDate = new Date(year, currentDate.getMonth(), 1);
            calendarObj.gotoDate(newDate);
            
            // Update UI
            document.getElementById('selected-year').textContent = year;
            document.getElementById('mobile-selected-year').textContent = year;
            
            // Close popup
            removeMobileSelectors();
        });
        
        yearsContainer.appendChild(yearBtn);
    }
    
    popup.appendChild(yearsContainer);
    document.body.appendChild(popup);
    
    // Animate in
    setTimeout(() => {
        popup.classList.add('active');
    }, 10);
    
    // Add backdrop and close on backdrop click
    const backdrop = document.createElement('div');
    backdrop.className = 'fixed inset-0 bg-black bg-opacity-50 z-40';
    backdrop.addEventListener('click', removeMobileSelectors);
    document.body.appendChild(backdrop);
}

// Keep mobile selectors in sync with calendar
function updateMobileSelectors() {
    if (!calendarObj) return;
    
    const mobileMonthTrigger = document.getElementById('mobile-month-trigger');
    const mobileYearTrigger = document.getElementById('mobile-year-trigger');
    
    if (!mobileMonthTrigger || !mobileYearTrigger) return;
    
    const date = calendarObj.getDate();
    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    
    // Update displays - either find span inside or update directly
    const mobileMonthText = mobileMonthTrigger.querySelector('span') || mobileMonthTrigger;
    const mobileYearText = mobileYearTrigger.querySelector('span') || mobileYearTrigger;
    
    mobileMonthText.textContent = monthNames[date.getMonth()];
    mobileYearText.textContent = date.getFullYear();
}

// Remove mobile selector popups
function removeMobileSelectors() {
    const popups = document.querySelectorAll('.month-popup, .year-popup');
    const backdrops = document.querySelectorAll('.fixed.inset-0.bg-black.bg-opacity-50');
    
    popups.forEach(popup => popup.remove());
    backdrops.forEach(backdrop => backdrop.remove());
}
function addMobileViewToggle() {
    if (!isMobileDevice()) return;
    
    // Check if it already exists
    if (document.getElementById('mobile-view-toggle')) return;
    
    const toggleBtn = document.createElement('button');
    toggleBtn.id = 'mobile-view-toggle';
    toggleBtn.className = 'fixed bottom-6 right-6 z-50 bg-[#DAA520] text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg';
    toggleBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>';
    
    toggleBtn.addEventListener('click', function() {
        const currentView = calendarObj.view.type;
        calendarObj.changeView(
            currentView === 'dayGridMonth' ? 'listMonth' : 'dayGridMonth'
        );
        
        // Save preference
        localStorage.setItem('calendarViewMode', 
            currentView === 'dayGridMonth' ? 'listMonth' : 'dayGridMonth');
    });
    
    document.body.appendChild(toggleBtn);
    
    // Check if we have a saved preference
    const savedView = localStorage.getItem('calendarViewMode');
    if (savedView && calendarObj) {
        calendarObj.changeView(savedView);
    }
}
function setupMobileLayout() {
    const isMobile = window.innerWidth <= 768;
    const mainContent = document.getElementById('main-content');
    
    // Adjust main content spacing on mobile
    if (isMobile && mainContent) {
        // For mobile, use full width
        mainContent.classList.remove('ml-[20%]');
        mainContent.classList.add('ml-0', 'w-full');
    } else if (mainContent) {
        // For desktop, restore sidebar space
        mainContent.classList.remove('ml-0', 'w-full');
        mainContent.classList.add('ml-[20%]');
    }
}
   
    document.addEventListener('DOMContentLoaded', function() {
        const toggleElement = document.getElementById('show-past-toggle');
    if (toggleElement) {
        toggleElement.addEventListener('change', function() {
            console.log('Show past toggle changed to:', this.checked);
            if (calendarObj) {
                console.log('Refetching events...');
                calendarObj.refetchEvents();
            }
        });
    }
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
        setupMobileLayout();
        initCalendar();
        setupCalendarResizeObserver();
        setupMobileMonthYearSelection();
        setupMobileToggle();
        
        // Handle orientation changes
        window.addEventListener('orientationchange', function() {
            setTimeout(() => {
                setupMobileLayout();
                updateMobileSelectors();
                if (calendarObj) {
                    calendarObj.updateSize();
                }
            }, 200);
        });
        
        // Handle resize for dynamic updates
        window.addEventListener('resize', function() {
            clearTimeout(window.mobileResizeTimeout);
            window.mobileResizeTimeout = setTimeout(() => {
                setupMobileLayout();
                updateMobileSelectors();
            }, 200);
        });
        
        // Add mobile view toggle
        setTimeout(addMobileViewToggle, 1000);
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
                    
                    // Check if show past toggle is enabled
                    const showPast = document.getElementById('show-past-toggle')?.checked || false;
                    console.log('Show past toggle is:', showPast);
                    
                    // Build the URL with the correct parameter
                    const url = showPast ? '/calendar/announcements?show_past=true' : '/calendar/announcements';
                    console.log('Fetching from URL:', url);
                    
                    fetch(url)
                        .then(response => {
                            console.log('Announcements response:', response.status);
                            if (!response.ok) {
                                console.warn('Announcements failed with status', response.status);
                                return [];
                            }
                            return response.json();
                        })
                        .then(announcements => {
                            console.log('Received announcements:', announcements);
                            console.log('Number of announcements:', announcements.length);
                            
                            // Log each announcement for debugging
                            announcements.forEach((ann, index) => {
                                console.log(`Announcement ${index + 1}:`, {
                                    title: ann.title,
                                    start: ann.start,
                                    is_expired: ann.is_expired,
                                    backgroundColor: ann.backgroundColor
                                });
                            });
                            
                            successCallback(announcements || []);
                        })
                        .catch(error => {
                            console.error('Error fetching announcements:', error);
                            successCallback([]);
                        });
                },
               
                // Handle date changes
                datesSet: function() {
                    updateCustomControls();
                    checkIfCurrentMonth();
                    adjustCalendarHeight();
                    updateMobileSelectors();
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
                    info.el.style.overflow = 'hidden';
                    info.el.style.textOverflow = 'ellipsis';
                    info.el.style.whiteSpace = 'nowrap';
                    
                    // Add announcement styling
                    if (info.event.extendedProps.source === 'announcement') {
                        // Check if announcement is expired/past deadline
                        if (info.event.extendedProps.is_expired) {
                            // Gray styling for past deadlines
                            info.el.style.borderLeft = '4px solid #9CA3AF'; // Gray border
                            info.el.style.backgroundColor = '#9CA3AF'; // Gray background
                            info.el.setAttribute('title', '[Past] Announcement: ' + info.event.title);
                        } else {
                            // Red styling for active deadlines
                            info.el.style.borderLeft = '4px solid #FF6347';  
                            info.el.style.backgroundColor = '#FF6347';
                            info.el.setAttribute('title', 'Announcement: ' + info.event.title);
                        }
                        
                        // Force single day display for announcements
                        info.el.style.position = 'relative';
                        info.el.style.zIndex = '1';
                        info.el.style.width = 'auto';
                        info.el.style.maxWidth = '100%';
                        info.el.classList.add('fc-event-single-day');
                        
                        // Add pointer cursor only to announcement events
                        info.el.style.cursor = 'pointer';
                        
                        // Add data attribute for CSS targeting
                        info.el.setAttribute('data-source', 'announcement');
                        
                        // Add expired data attribute if applicable
                        if (info.event.extendedProps.is_expired) {
                            info.el.setAttribute('data-expired', 'true');
                        }
                    } else if (info.event.extendedProps.source === 'proposal') {
                        info.el.style.borderLeft = '4px solid #0085FF';
                        info.el.setAttribute('title', 'Approved Proposal: ' + info.event.title);
                        
                        // Add pointer cursor to proposal events too
                        info.el.style.cursor = 'pointer';
                        
                        // Add data attribute for CSS targeting
                        info.el.setAttribute('data-source', 'proposal');
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
            setupCustomNavigation();
           
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

    function setupMobileToggle() {
    const toggleCheckbox = document.getElementById('show-past-toggle');
    const toggleDot = document.querySelector('.dot');
    
    if (!toggleCheckbox || !toggleDot) return;
    
    // Make sure the dot moves properly
    toggleCheckbox.addEventListener('change', function() {
        if (this.checked) {
            toggleDot.style.transform = 'translateX(calc(100% + 2px))';
            toggleDot.style.backgroundColor = '#DAA520';
        } else {
            toggleDot.style.transform = 'translateX(0)';
            toggleDot.style.backgroundColor = 'white';
        }
        
        // Refetch events as in your original code
        if (calendarObj) {
            calendarObj.refetchEvents();
        }
    });
}
   
    function setupCalendarResizeObserver() {
    const mainContent = document.getElementById('main-content');
    const calendarContainer = document.getElementById('calendar-container');
   
    if (!mainContent || !calendarContainer) return;
   
    // Create a ResizeObserver to watch for main content size changes
    const resizeObserver = new ResizeObserver(entries => {
        for (let entry of entries) {
            // Debounce the calendar update to avoid too many calls
            clearTimeout(window.calendarResizeTimeout);
            window.calendarResizeTimeout = setTimeout(() => {
                if (calendarObj) {
                    console.log('Main content resized, updating calendar');
                    calendarObj.updateSize();
                   
                    // Force a re-render after a short delay to ensure proper sizing
                    setTimeout(() => {
                        calendarObj.render();
                    }, 50);
                }
            }, 100);
        }
    });
   
    // Observe the main content for size changes
    resizeObserver.observe(mainContent);
   
    // Also observe the calendar container itself
    resizeObserver.observe(calendarContainer);
   
    // Additionally, listen for transition end events on the main content
    mainContent.addEventListener('transitionend', (e) => {
        if (e.target === mainContent && e.propertyName === 'margin-left') {
            console.log('Sidebar transition completed, updating calendar');
            setTimeout(() => {
                if (calendarObj) {
                    calendarObj.updateSize();
                    calendarObj.render();
                }
            }, 50);
        }
    });
}


    // Month dropdown functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Setup custom month dropdown
        const monthDropdown = document.getElementById('month-dropdown');
        const monthDropdownTrigger = document.getElementById('month-dropdown-trigger');
        const monthDropdownMenu = document.getElementById('month-dropdown-menu');
        const selectedMonthText = document.getElementById('selected-month');
        const monthOptions = document.querySelectorAll('#month-dropdown-menu [data-value]');
       
        // Set initial display based on current month
        function updateSelectedMonth() {
            const monthValue = parseInt(monthDropdown.value);
            selectedMonthText.textContent = monthDropdown.options[monthValue].text;
           
            // Update checkmarks
            document.querySelectorAll('.checkmark-icon').forEach(icon => {
                icon.classList.add('invisible');
            });
           
            // Show checkmark for selected month
            const selectedOption = document.querySelector(`#month-dropdown-menu [data-value="${monthValue}"]`);
            if (selectedOption) {
                const checkmark = selectedOption.querySelector('.checkmark-icon');
                if (checkmark) checkmark.classList.remove('invisible');
            }
        }
       
        // Toggle dropdown on click
        monthDropdownTrigger.addEventListener('click', () => {
            monthDropdownMenu.classList.toggle('hidden');
        });
       
        // Handle option clicks
        monthOptions.forEach(option => {
            option.addEventListener('click', () => {
                const value = option.getAttribute('data-value');
                monthDropdown.value = value;
                monthDropdownMenu.classList.add('hidden');
               
                // Update display
                updateSelectedMonth();
               
                // Trigger change event on the hidden select
                const event = new Event('change');
                monthDropdown.dispatchEvent(event);
            });
        });
       
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!monthDropdownTrigger.contains(e.target) && !monthDropdownMenu.contains(e.target)) {
                monthDropdownMenu.classList.add('hidden');
            }
        });
       
        // Update custom dropdown when the real one changes
        monthDropdown.addEventListener('change', updateSelectedMonth);
       
        // Initialize to current month
        updateSelectedMonth();


        // Year dropdown functionality
        const yearDropdown = document.getElementById('year-dropdown');
        const yearDropdownTrigger = document.getElementById('year-dropdown-trigger');
        const yearDropdownMenu = document.getElementById('year-dropdown-menu');
        const selectedYearText = document.getElementById('selected-year');
        const yearOptionsContainer = document.getElementById('year-options-container');
       
        // Populate years (from current year - 10 to current year + 10)
        function populateYears() {
            const currentYear = new Date().getFullYear();
            const startYear = currentYear - 10;
            const endYear = currentYear + 10;
           
            // Clear existing options
            yearOptionsContainer.innerHTML = '';
            yearDropdown.innerHTML = '';
           
            // Add years
            for (let year = startYear; year <= endYear; year++) {
                // Add to hidden select
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                yearDropdown.appendChild(option);
               
                // Add to custom dropdown
                const customOption = document.createElement('div');
                customOption.className = 'flex px-2 py-2 items-center gap-2 hover:bg-gray-100 cursor-pointer';
                customOption.setAttribute('data-value', year);
               
                // Add checkmark
                const checkmark = document.createElement('div');
                checkmark.className = 'w-5 h-5 checkmark-icon-year' + (year === currentYear ? '' : ' invisible');
                checkmark.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 21" fill="none">
                    <path d="M16.6668 5.5L7.50016 14.6667L3.3335 10.5" stroke="#161616" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>`;
               
                const text = document.createElement('span');
                text.textContent = year;
               
                customOption.appendChild(checkmark);
                customOption.appendChild(text);
               
                // Handle click on year option
                customOption.addEventListener('click', () => {
                    yearDropdown.value = year;
                    yearDropdownMenu.classList.add('hidden');
                    updateSelectedYear();
                   
                    // Trigger change event on the hidden select
                    const event = new Event('change');
                    yearDropdown.dispatchEvent(event);
                });
               
                yearOptionsContainer.appendChild(customOption);
            }
           
            // Set default to current year
            yearDropdown.value = currentYear;
            selectedYearText.textContent = currentYear;
        }


       
        // Update selected year display
        function updateSelectedYear() {
            const yearValue = yearDropdown.value;
            selectedYearText.textContent = yearValue;
           
            // Update checkmarks
            document.querySelectorAll('.checkmark-icon-year').forEach(icon => {
                icon.classList.add('invisible');
            });
           
            // Show checkmark for selected year
            const selectedOption = document.querySelector(`#year-options-container [data-value="${yearValue}"]`);
            if (selectedOption) {
                const checkmark = selectedOption.querySelector('.checkmark-icon-year');
                if (checkmark) checkmark.classList.remove('invisible');
            }
        }
       
        // Toggle dropdown on click
        yearDropdownTrigger.addEventListener('click', () => {
            yearDropdownMenu.classList.toggle('hidden');
        });
       
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!yearDropdownTrigger.contains(e.target) && !yearDropdownMenu.contains(e.target)) {
                yearDropdownMenu.classList.add('hidden');
            }
        });
       
        // Update custom dropdown when the real one changes
        yearDropdown.addEventListener('change', updateSelectedYear);
       
        // Initialize years
        populateYears();
        updateSelectedYear();
    });


    function adjustCalendarHeight() {
        if (!calendarObj) return;
       
        const date = calendarObj.getDate();
        const currentView = calendarObj.view;
        const calendarContainer = document.getElementById('calendar-container');
        const calendar = document.getElementById('calendar');
       
        if (!calendarContainer || !calendar) return;
       
        // Calculate number of weeks in the current month view
        const start = currentView.currentStart;
        const end = currentView.currentEnd;
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        const weeksCount = Math.ceil(diffDays / 7);
       
        // Base height per week (adjust as needed)
        const baseHeightPerWeek = 120; // pixels
        const minHeight = 400; // minimum height
       
        // Calculate new height based on weeks
        let newHeight = Math.max(weeksCount * baseHeightPerWeek, minHeight);
       
        // Apply the new height
        calendarContainer.style.minHeight = `${newHeight}px`;
        calendar.style.minHeight = `${newHeight}px`;
       
        // Force calendar to update its size
        calendarObj.updateSize();
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
    console.log("Opening student announcement details modal for:", event.title);
   
    const modal = document.getElementById('eventDetailsModal');
    const modalContent = modal.querySelector('.modal-container');
   
    if (!modal) {
        console.error('Event details modal not found');
        return;
    }
   
    // Populate the modal with simplified student announcement details
    const titleElement = document.getElementById('detail-title');
    const dateElement = document.getElementById('detail-date');
    const colorIndicator = document.getElementById('event-color-indicator');
    const actionContainer = document.getElementById('event-action-buttons');
   
    if (titleElement) {
        const fullTitle = event.extendedProps.full_title || event.title.replace(/^[📢⏰] /, '');
        titleElement.textContent = fullTitle;
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
       
        if (startDate) {
            dateStr = startDate.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        }
       
        dateElement.textContent = dateStr;
    }
   
    // Set event color indicator based on status
    if (colorIndicator) {
        colorIndicator.style.backgroundColor = event.backgroundColor || '#FF6347';
    }
   
    // Show announcement content with simplified status
    if (actionContainer) {
        let deadlineInfo = '';
        if (event.extendedProps.deadline_text) {
            deadlineInfo = `<strong>Deadline:</strong> ${event.extendedProps.deadline_text}<br>`;
        }
        
        let statusInfo = '';
        if (event.extendedProps.is_expired) {
            statusInfo = `<strong class="text-gray-600">Status:</strong> <span class="text-gray-600">Past Deadline</span><br>`;
        }
       
        let contentInfo = '';
        if (event.extendedProps.content) {
            contentInfo = `<div class="text-sm text-gray-700 mt-2">${event.extendedProps.content}</div>`;
        }
        
        const iconText = event.extendedProps.is_expired ? 
            '⏰ Past Deadline' : 
            '📢 Important Deadline Announcement';
       
        actionContainer.innerHTML = `
            <div class="text-sm text-gray-600 mb-2" style="word-wrap: break-word; overflow-wrap: break-word;">
                ${deadlineInfo}
                ${statusInfo}
            </div>
            ${contentInfo}
            <p class="text-xs font-medium mt-2 ${event.extendedProps.is_expired ? 'text-gray-500' : 'text-orange-600'}">${iconText}</p>
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


    // Setup custom navigation
    function setupCustomNavigation() {
        const prevBtn = document.getElementById('prev-month');
        const nextBtn = document.getElementById('next-month');
        const monthDropdown = document.getElementById('month-dropdown');
        const yearDropdown = document.getElementById('year-dropdown');
        const todayBtn = document.getElementById('today-btn');
       
        // Populate year dropdown
        populateYearDropdown();
       
        // Set initial values
        updateCustomControls();
       
        // Handle previous month button click
        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                calendarObj.prev();
            });
        }
       
        // Handle next month button click
        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                calendarObj.next();
            });
        }
       
        // Handle month dropdown change
        if (monthDropdown) {
            monthDropdown.addEventListener('change', function() {
                const selectedMonth = parseInt(this.value);
                const currentDate = calendarObj.getDate();
                const newDate = new Date(currentDate.getFullYear(), selectedMonth, 1);
                calendarObj.gotoDate(newDate);
            });
        }
       
        // Handle year dropdown change
        if (yearDropdown) {
            yearDropdown.addEventListener('change', function() {
                const selectedYear = parseInt(this.value);
                const currentDate = calendarObj.getDate();
                const newDate = new Date(selectedYear, currentDate.getMonth(), 1);
                calendarObj.gotoDate(newDate);
            });
        }
       
        // Handle today button click
        if (todayBtn) {
            todayBtn.addEventListener('click', function() {
                calendarObj.today();
            });
        }
    }


    // Populate year dropdown with range of years
    function populateYearDropdown() {
        const yearDropdown = document.getElementById('year-dropdown');
        if (!yearDropdown) return;
       
        const currentYear = new Date().getFullYear();
        const startYear = currentYear - 10;
        const endYear = currentYear + 10;
       
        yearDropdown.innerHTML = '';
       
        for (let year = startYear; year <= endYear; year++) {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            if (year === currentYear) {
                option.selected = true;
            }
            yearDropdown.appendChild(option);
        }
    }


    // Update custom controls based on calendar date
// Update custom controls based on calendar date
function updateCustomControls() {
    if (!calendarObj) return;
   
    const calendarDate = calendarObj.getDate();
    const monthDropdown = document.getElementById('month-dropdown');
    const yearDropdown = document.getElementById('year-dropdown');
    const selectedMonthText = document.getElementById('selected-month');
    const selectedYearText = document.getElementById('selected-year');
    const mobileMonthTrigger = document.getElementById('mobile-month-trigger');
    const mobileYearTrigger = document.getElementById('mobile-year-trigger');
    const todayBtn = document.getElementById('today-btn');
    
    // Get month name for display
    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    const newMonth = calendarDate.getMonth();
    const newYear = calendarDate.getFullYear();
    const monthName = monthNames[newMonth];
   
    // Update month dropdown and its display (desktop)
    if (monthDropdown) {
        monthDropdown.value = newMonth;
        
        // Update visible month text display
        if (selectedMonthText) {
            selectedMonthText.textContent = monthName;
           
            // Update month checkmarks
            document.querySelectorAll('.checkmark-icon').forEach(icon => {
                icon.classList.add('invisible');
            });
           
            // Show checkmark for selected month
            const selectedOption = document.querySelector(`#month-dropdown-menu [data-value="${newMonth}"]`);
            if (selectedOption) {
                const checkmark = selectedOption.querySelector('.checkmark-icon');
                if (checkmark) checkmark.classList.remove('invisible');
            }
        }
    }
   
    // Update year dropdown and its display (desktop)
    if (yearDropdown) {
        yearDropdown.value = newYear;
       
        // Update visible year text display
        if (selectedYearText) {
            selectedYearText.textContent = newYear;
            
            // Update year checkmarks
            document.querySelectorAll('.checkmark-icon-year').forEach(icon => {
                icon.classList.add('invisible');
            });
           
            // Show checkmark for selected year
            const selectedOption = document.querySelector(`#year-options-container [data-value="${newYear}"]`);
            if (selectedOption) {
                const checkmark = selectedOption.querySelector('.checkmark-icon-year');
                if (checkmark) checkmark.classList.remove('invisible');
            }
        }
    }
    
    // Update mobile month/year display
    if (mobileMonthTrigger) {
        const mobileMonthText = mobileMonthTrigger.querySelector('span') || mobileMonthTrigger;
        mobileMonthText.textContent = monthName;
    }
    
    if (mobileYearTrigger) {
        const mobileYearText = mobileYearTrigger.querySelector('span') || mobileYearTrigger;
        mobileYearText.textContent = newYear;
    }
   
    // Show/hide today button based on current month
    if (todayBtn) {
        const isCurrentMonth =
            currentMonth === calendarDate.getMonth() &&
            currentYear === calendarDate.getFullYear();
       
        // Use visibility instead of display to maintain layout
        todayBtn.style.visibility = isCurrentMonth ? 'hidden' : 'visible';
    }
}


    // Improved Year Selector
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
    clearTimeout(window.globalResizeTimeout);
    window.globalResizeTimeout = setTimeout(() => {
        if (calendarObj) {
            console.log('Window resized, updating calendar');
            calendarObj.updateSize();
            calendarObj.render();
        }
    }, 150);
});
</script>
@endpush


@endsection

