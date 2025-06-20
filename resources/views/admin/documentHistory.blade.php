@extends('base')

@section('content')
@include('components.adminSidebarComponent')
<div id="main-content" class="flex flex-col min-h-screen ml-[20%] transition-all duration-300 bg-[#F2F4F7]">
    @include('components.adminNavBarComponent')
    <div class="flex-grow mb-10">
        <div class="w-full px-6 py-8 flex flex-col">
            <!-- Header section with title and archive page link -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="md:text-2xl text-xl font-extrabold">Document Repository</h2>

                <!-- Link to archive page for viewing archived documents -->
                <a href="{{ route('admin.archivePage') }}"
                    class="text-[#7A1212] underline font-medium hover:text-[#DAA520] transition-colors duration-200">
                    Go to Archive Page
                </a>
            </div>

            <!-- Search and filter controls section -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <!-- Search input with magnifier icon -->
                <div class="flex-1 min-w-[200px] relative">
                    <input type="text" placeholder="Search..."
                        class="border border-[#9099A5] px-4 py-2 pr-10 rounded-full w-full bg-white">
                    <img src="{{ asset('images/Magnifier.svg') }}" alt="Search"
                        class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 pointer-events-none" />
                </div>

                <!-- Action buttons and filter dropdowns -->
                <div class="flex flex-wrap items-center gap-4 justify-end">
                    <!-- Button for batch archiving selected documents -->
                    <button id="archiveSelectedBtn"
                        class="px-4 py-2 bg-[#7A1212] text-white rounded-full hover:bg-[#DAA520] transition-colors duration-200 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                        Archive Selected (<span id="selectedCount">0</span>)
                    </button>

                    <!-- Organization dropdown filter -->
                    <div class="relative w-40">
                        <select id="organizationFilter"
                            class="appearance-none border px-4 py-2 rounded-full bg-[#7A1212] text-white w-full pr-8 hover:bg-[#DAA520] hover:text-white transition-colors duration-200">
                            <option class="bg-white text-black truncate" value="Organization" disabled selected>Organization
                            </option>
                            <option class="bg-white text-black truncate" value="All">All Organizations</option>
                            <!-- Dynamic organization options from database -->
                            @if(isset($availableOrganizations))
                                @foreach($availableOrganizations as $org)
                                    <option class="bg-white text-black truncate" value="{{ $org }}">{{ $org }}</option>
                                @endforeach
                            @endif
                        </select>
                        <!-- Custom dropdown arrow -->
                        <img src="{{ asset('images/dropdownIcon.svg') }}" alt="Dropdown Icon"
                            class="absolute top-1/2 right-3 -translate-y-1/2 w-4 h-4 pointer-events-none" />
                    </div>

                    <!-- Document type dropdown filter -->
                    <div class="relative w-40">
                        <select id="typeFilter"
                            class="appearance-none border px-4 py-2 rounded-full bg-[#7A1212] text-white w-full pr-8 hover:bg-[#DAA520] hover:text-white transition-colors duration-200 truncate">
                            <option class="bg-white text-black truncate" value="Type" disabled selected>Type</option>
                            <option class="bg-white text-black truncate" value="All">All Types</option>
                            @if(isset($availableTypes))
                                @foreach($availableTypes as $type)
                                    <option class="bg-white text-black truncate" value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            @endif
                        </select>
                        <!-- Custom dropdown arrow -->
                        <img src="{{ asset('images/dropdownIcon.svg') }}" alt="Dropdown Icon"
                            class="absolute top-1/2 right-3 -translate-y-1/2 w-4 h-4 pointer-events-none" />
                    </div>

                    <!-- Submitted to filter dropdown -->
                    <div class="relative w-40">
                        <select id="submittedToFilter"
                            class="appearance-none border px-4 py-2 rounded-full bg-[#7A1212] text-white w-full pr-8 hover:bg-[#DAA520] hover:text-white transition-colors duration-200 truncate">
                            <option class="bg-white text-black truncate" value="Submitted" disabled selected>Submitted to
                            </option>
                            <option class="bg-white text-black truncate" value="All">All Recipients</option>
                            @if(isset($availableRoles))
                                @foreach($availableRoles as $role)
                                    <option class="bg-white text-black truncate" value="{{ $role }}">{{ $role }}</option>
                                @endforeach
                            @endif
                        </select>
                        <!-- Custom dropdown arrow -->
                        <img src="{{ asset('images/dropdownIcon.svg') }}" alt="Dropdown Icon"
                            class="absolute top-1/2 right-3 -translate-y-1/2 w-4 h-4 pointer-events-none" />
                    </div>

                    <!-- Date filter button (replacing status dropdown) -->
                    <button id="dateFilterBtn"
                        class="px-4 py-2 rounded-full bg-[#7A1212] text-white w-40 hover:bg-[#DAA520] hover:text-white transition-colors duration-200 flex items-center justify-between">
                        <span id="dateFilterText">Date Range</span>
                        <img src="{{ asset('images/dropdownIcon.svg') }}" alt="Calendar Icon" class="w-4 h-4" />
                    </button>
                </div>
            </div>

            @php
            // Organization mapping data - maps acronyms to full organization names
            $orgMap = [
            'ACAP' => 'Association of Competent and Aspiring Psychologists',
            'AECES' => 'Association of Electronics and Communications Engineering Students',
            'ELITE' => 'Eligible League of Information Technology Enthusiasts',
            'GIVE' => 'Guild of Imporous and Valuable Educators',
            'JEHRA' => 'Junior Executive of Human Resource Association',
            'JMAP' => 'Junior Marketing Association of the Philippines',
            'JPIA' => 'Junior Philippine Institute of Accountants',
            'PIIE' => 'Philippine Institute of Industrial Engineers',
            'AGDS' => 'Artist Guild Dance Squad',
            'Chorale' => 'PUP SRC Chorale',
            'SIGMA' => 'Supreme Innovators Guild for Mathematics Advancements',
            'TAPNOTCH' =>
            'Transformation Advocates through Purpose-driven and Noble Objectives Toward Community Holism',
            'OSC' => 'Office of the Student Council',
            ];
            $orgKeys = array_keys($orgMap);

            // Color coding for different organization tags in the table
            $tagColors = [
            'OSC' => 'text-blue-500',
            'ECE' => 'text-red-500',
            'PSY' => 'text-purple-500',
            'ELITE' => 'text-orange-500',
            'HR' => 'text-pink-400',
            'ACC' => 'text-pink-400',
            'EDU' => 'text-blue-500',
            'MAR' => 'text-yellow-500',
            'IE' => 'text-green-500',
            'TAP' => 'text-green-500',
            'SIGMA' => 'text-yellow-900',
            'AGDS' => 'text-yellow-900',
            'CHO' => 'text-blue-500',
            ];
            @endphp

            <!-- Main table container -->
            <div id="tableContainer" class="bg-white rounded-[24px] shadow-md overflow-hidden p-6">
                <div class="h-auto">
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto" id="documentTable">
                            <!-- Table header -->
                            <thead class="bg-white text-left">
                                <tr>
                                    <!-- Select all checkbox column with separate "Select All" link -->
                                    <th class="px-4 py-2 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" id="selectAll" class="w-4 h-4 cursor-pointer">
                                            <button id="selectAllLink" 
                                                class="text-[#7A1212] underline hover:text-[#DAA520] text-sm font-medium cursor-pointer">
                                                Select All
                                            </button>
                                        </div>
                                    </th>
                                    <!-- Replace this section in documentHistory.blade.php -->
                                    @php $headers = ['Tag', 'Organization', 'Title', 'Date Submitted', 'Type', 'Submitted to']; @endphp
                                    @foreach ($headers as $i => $header)
                                    <th class="px-4 py-2 whitespace-nowrap {{ $header === 'Date Submitted' ? 'max-w-[140px]' : 'max-w-[160px]' }} truncate">
                                    @if ($header !== 'Status')
                                        @if ($header === 'Organization' || $header === 'Type' || $header === 'Submitted to')
                                            <!-- These headers have no icon but keep sorting functionality -->
                                            <button onclick="sortTable({{ $i + 1 }})" class="group">
                                                <span>{{ $header }}</span>
                                            </button>
                                        @else
                                            <!-- Other headers have normal sort icon -->
                                            <button onclick="sortTable({{ $i + 1 }})" class="flex items-center gap-1 group">
                                                <span>{{ $header }}</span>
                                                <img src="{{ asset('images/sortIcon.svg') }}" alt="Sort"
                                                    class="w-3 h-3 text-gray-500 group-hover:text-black transition">
                                            </button>
                                        @endif
                                    @else
                                        <span>{{ $header }}</span>
                                    @endif
                                    </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <!-- Table body - dynamically generated rows from database -->
                            <tbody>
                                @forelse ($documents as $document)
                                @php
                                // Extract organization acronym from control tag (e.g., "ACAP_001")
                                $parts = explode('_', $document->control_tag);
                                $acronym = count($parts) > 0 ? $parts[0] : '';
                                
                                // Map the acronym to a color key for consistent color coding
                                $colorKey = match ($acronym) {
                                'ACAP' => 'PSY',
                                'AECES' => 'ECE',
                                'ELITE' => 'IT',
                                'GIVE' => 'EDU',
                                'JEHRA' => 'HR',
                                'JMAP' => 'MAR',
                                'JPIA' => 'ACC',
                                'PIIE' => 'IE',
                                'AGDS' => 'AGDS',
                                'Chorale' => 'CHO',
                                'SIGMA' => 'SIGMA',
                                'TAPNOTCH' => 'TAP',
                                'OSC' => 'OSC',
                                default => 'text-gray-500',
                                };
                                $tagColor = isset($tagColors[$colorKey]) ? $tagColors[$colorKey] : 'text-gray-500';

                                // Format date AND TIME for consistent display
                                $createdDateTime = \Carbon\Carbon::parse($document->created_at)->format('m/d/Y g:i A');
                                
                                // Determine display type - show 'Others' for non-standard types
                                $displayType = in_array($document->type, $standardTypes ?? []) ? $document->type : 'Others';
                                @endphp

                                <!-- Document row with data attributes for filtering -->
                                <tr class="border-b border-gray-300 hover:bg-gray-100"
                                    data-org-acronym="{{ $acronym }}" data-role="{{ $document->role_name }}"
                                    data-type="{{ $document->type }}" data-id="{{ $document->id }}">
                                    <!-- Checkbox for row selection -->
                                    <td class="px-4 py-2">
                                        <input type="checkbox" class="row-checkbox w-4 h-4 cursor-pointer"
                                            data-id="{{ $document->id }}">
                                    </td>
                                    <!-- Document tag with color coding -->
                                    <td class="px-4 py-2 font-semibold truncate max-w-[120px] cursor-pointer"
                                        onclick="viewDocument({{ $document->id }})">
                                        <span class="{{ $tagColor }}">{{ $document->control_tag }}</span>
                                    </td>
                                    <!-- USERNAME instead of organization name -->
                                    <td class="px-4 py-2 truncate max-w-[160px] cursor-pointer"
                                        onclick="viewDocument({{ $document->id }})" 
                                        title="{{ $document->username ?? 'N/A' }}">
                                        {{ $document->username ?? 'N/A' }}
                                    </td>
                                    <!-- Document subject with tooltip for full text -->
                                    <td class="px-4 py-2 truncate max-w-[160px] cursor-pointer"
                                        onclick="viewDocument({{ $document->id }})"
                                        title="{{ $document->subject }}">
                                        {{ $document->subject }}
                                    </td>
                                    <!-- Date AND TIME submitted -->
                                    <td class="px-4 py-2 truncate max-w-[140px] cursor-pointer"
                                        onclick="viewDocument({{ $document->id }})" 
                                        title="{{ $createdDateTime }}">
                                        {{ $createdDateTime }}
                                    </td>
                                    <!-- Document type - show 'Others' for non-standard types -->
                                    <td class="px-4 py-2 truncate max-w-[160px] cursor-pointer"
                                        onclick="viewDocument({{ $document->id }})"
                                        title="{{ $displayType }}">
                                        {{ $displayType }}
                                    </td>
                                    <!-- Role column -->
                                    <td class="px-4 py-2 truncate cursor-pointer"
                                        onclick="viewDocument({{ $document->id }})">
                                        {{ $document->role_name ?? 'N/A' }}
                                    </td>
                                </tr>
                                @empty
                                <!-- Empty state when no documents are found -->
                                <tr>
                                    <td colspan="{{ count($headers) }}"
                                        class="px-4 py-8 text-center text-gray-500 align-middle">
                                        <div class="flex flex-col items-center justify-center min-h-[300px] pl-36">
                                            <img src="{{ asset('images/viewNoFileFound.svg') }}"
                                                alt="No documents found" class="mb-4 w-40 h-40" />
                                            <span>No documents found.</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination controls with ellipsis -->
            @if (count($documents) > 0)
            <div class="mt-4 flex justify-center" id="paginationContainer">
                <nav>
                    <ul class="inline-flex items-center space-x-2">
                        <!-- Previous page button -->
                        <li>
                            <a href="{{ $documents->previousPageUrl() }}"
                                class="pagination-btn-prev px-3 py-1 rounded-lg {{ $documents->currentPage() == 1 ? 'cursor-not-allowed opacity-50' : '' }}">
                                &lt;
                            </a>
                        </li>

                        @php
                            $current = $documents->currentPage();
                            $last = $documents->lastPage();
                            $start = max(1, $current - 2);
                            $end = min($last, $current + 2);
                        @endphp

                        <!-- First page -->
                        @if($start > 1)
                            <li>
                                <a href="{{ $documents->url(1) }}"
                                    class="pagination-btn px-3 py-1 rounded-lg {{ $current == 1 ? 'bg-[#7A1212] text-white' : '' }}">
                                    1
                                </a>
                            </li>
                            @if($start > 2)
                                <li><span class="px-3 py-1">...</span></li>
                            @endif
                        @endif

                        <!-- Page numbers around current page -->
                        @for ($i = $start; $i <= $end; $i++)
                            <li>
                                <a href="{{ $documents->url($i) }}"
                                    class="pagination-btn px-3 py-1 rounded-lg {{ $current == $i ? 'bg-[#7A1212] text-white' : '' }}">
                                    {{ $i }}
                                </a>
                            </li>
                        @endfor

                        <!-- Last page -->
                        @if($end < $last)
                            @if($end < $last - 1)
                                <li><span class="px-3 py-1">...</span></li>
                            @endif
                            <li>
                                <a href="{{ $documents->url($last) }}"
                                    class="pagination-btn px-3 py-1 rounded-lg {{ $current == $last ? 'bg-[#7A1212] text-white' : '' }}">
                                    {{ $last }}
                                </a>
                            </li>
                        @endif

                        <!-- Next page button -->
                        <li>
                            <a href="{{ $documents->nextPageUrl() }}"
                                class="pagination-btn-next px-3 py-1 rounded-lg {{ $current == $last ? 'cursor-not-allowed opacity-50' : '' }}">
                                &gt;
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            @endif

            <!-- Archive Documents Confirmation Modal -->
            <div id="archiveConfirmationModal" class="hidden fixed inset-0 z-50 flex items-center justify-center"
                style="background-color: rgba(0,0,0,0.3);">
                <div class="bg-white w-[30rem] rounded-2xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-semibold text-gray-800">Archive Document Confirmation</h3>
                        <button id="closeArchiveModalBtn" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <p class="text-sm text-gray-600 mb-6">
                        Are you sure you want to archive this document? Once archived, it will be removed from your history
                        list
                        and will no longer be visible there.
                    </p>

                    <div class="flex justify-end space-x-3">
                        <button id="cancelArchiveBtn"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                            Cancel
                        </button>
                        <button id="confirmArchiveBtn"
                            class="px-4 py-2 bg-[#7A1212] text-white rounded-md hover:bg-[#DAA520] cursor-pointer">
                            Archive
                        </button>
                    </div>
                </div>
            </div>

            <!-- Date Filter Modal -->
            <div id="dateFilterModal" class="hidden fixed inset-0 z-50 flex items-center justify-center"
                style="background-color: rgba(0,0,0,0.3);">
                <div class="bg-white w-[25rem] rounded-2xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Filter by Date Range</h3>
                        <button id="closeDateModalBtn" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="startDate" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" id="startDate"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212] focus:border-transparent">
                        </div>
                        <div>
                            <label for="endDate" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" id="endDate"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212] focus:border-transparent">
                        </div>
                        <div id="dateError" class="text-red-500 text-sm hidden">
                            End date cannot be earlier than start date.
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button id="clearDateFilterBtn"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                            Clear Filter
                        </button>
                        <button id="applyDateFilterBtn"
                            class="px-4 py-2 bg-[#7A1212] text-white rounded-md hover:bg-[#DAA520] cursor-pointer">
                            Apply Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Document Action Toast -->
            <div id="documentActionToast" class="hidden fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white border-l-4 border-gray-400 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50">
                <div>
                    <img
                        src="{{ asset('images/successful.svg') }}"
                        alt="Action Icon"
                        id="actionIcon"
                        class="">
                </div>
                <div class="flex-1">
                    <p class="font-semibold" id="actionTitle">Document Action</p>
                    <p id="actionMessage" class="text-sm">Action performed on document.</p>
                </div>
                <button type="button" onclick="hideActionToast()" class="text-gray-500 hover:text-gray-700 text-2xl leading-none cursor-pointer self-center">&times;</button>
            </div>

            <!-- Generate Reports button with dropdown -->
            <div class="w-full px-6 flex justify-end mb-8 mt-4">
                <div class="relative inline-block">
                    <button id="floatingExportBtn"
                        class="px-4 py-2 bg-[#7A1212] text-white rounded-full shadow-lg hover:bg-[#DAA520] transition-colors duration-200 flex items-center gap-2 {{ count($documents) === 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                        onclick="toggleExportDropdown()" {{ count($documents) === 0 ? 'disabled' : '' }}>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Generate Reports
                        <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    
                    <!-- Dropdown menu -->
                    <div id="exportDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                        <div class="py-1">
                            <button onclick="exportDocumentHistory('excel')" 
                                class="flex items-center gap-2 w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Download as Excel
                            </button>
                            <button onclick="exportDocumentHistory('pdf')" 
                                class="flex items-center gap-2 w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Download as PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('components.footer')
</div>

<script>
    // Configuration constants
    let selectedItems = new Set();
    let isSelectAllActive = false; // Track if server-side select all is active
    let allFilteredDocumentIds = []; // Store all document IDs from server-side select all
    let currentDateFilter = {
        start: '',
        end: ''
    }; // Track current date filter

    // Track sort direction for each column 
    let sortDirection = [true, true, true, true, true, true];

    document.addEventListener('DOMContentLoaded', function() {
        // Handle regular checkbox select all (only current page)
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    // Only select visible documents on current page
                    selectCurrentPageDocuments();
                } else {
                    // Deselect all
                    deselectAllDocuments();
                }
            });
        }

        // Handle "Select All" link (server-side select all across all pages)
        const selectAllLink = document.getElementById('selectAllLink');
        if (selectAllLink) {
            selectAllLink.addEventListener('click', function(e) {
                e.preventDefault();
                if (isSelectAllActive) {
                    // If already active, deselect all
                    deselectAllDocuments();
                } else {
                    // Server-side select all across all pages
                    selectAllDocumentsServerSide();
                }
            });
        }

        // Handle individual checkbox changes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('row-checkbox')) {
                handleIndividualCheckboxChange(e.target);
            }
        });

        // Initialize export button state
        updateExportButtonState();

        // Filter form elements
        const organizationFilter = document.getElementById("organizationFilter");
        const typeFilter = document.getElementById("typeFilter");
        const searchInput = document.querySelector('input[placeholder="Search..."]');

        // Handle filter changes
        function handleFilterChange() {
            applyServerSideFilters();
        }

        // Make handleFilterChange globally available
        window.handleFilterChange = handleFilterChange;

        // Add event listeners to filters
        organizationFilter.addEventListener("change", handleFilterChange);
        typeFilter.addEventListener("change", handleFilterChange);

        // For search, use debouncing
        let searchTimeout;
        searchInput.addEventListener("input", function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(handleFilterChange, 500);
        });

        // Handle pagination links with AJAX
        document.addEventListener('click', function(e) {
            const paginationLink = e.target.closest('a.pagination-btn, a.pagination-btn-prev, a.pagination-btn-next');

            if (paginationLink && !paginationLink.classList.contains('cursor-not-allowed')) {
                e.preventDefault();
                const url = paginationLink.getAttribute('href');

                // Show loading indicator
                const tableContainer = document.querySelector('#tableContainer');
                tableContainer.classList.add('opacity-50');

                // Fetch the new page content
                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'  // Request JSON response
                        }
                    })
                    .then(response => response.json())  // Parse as JSON instead of text
                    .then(data => {
                        // Parse the HTML response
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(data.html, 'text/html');

                        // Extract the table body content
                        const newTableBody = doc.querySelector('#documentTable tbody');
                        if (newTableBody) {
                            document.querySelector('#documentTable tbody').innerHTML = newTableBody.innerHTML;
                        }

                        // Update pagination
                        const newPagination = doc.querySelector('#paginationContainer');
                        if (newPagination) {
                            const currentPagination = document.querySelector('#paginationContainer');
                            if (currentPagination) {
                                currentPagination.outerHTML = newPagination.outerHTML;
                            }
                        }

                        // Restore selection state for visible documents
                        restoreSelectionStateOnPage();

                        // Update URL without reload
                        window.history.pushState({}, '', url);

                        // Update export button state
                        updateExportButtonState();

                        // Remove loading state
                        tableContainer.classList.remove('opacity-50');
                    })
                    .catch(error => {
                        console.error('Error loading page:', error);
                        tableContainer.classList.remove('opacity-50');
                    });
            }
        });

        // Initialize date filter display
        updateDateFilterDisplay(); // This will set it to "Date Range"

        // Handle filter changes
        const submittedToFilter = document.getElementById("submittedToFilter");
        submittedToFilter.addEventListener("change", handleFilterChange);
    });

    // Select only documents visible on current page
    function selectCurrentPageDocuments() {
        const checkboxes = document.querySelectorAll('.row-checkbox:not(:disabled)');
        
        checkboxes.forEach(checkbox => {
            if (checkbox.closest('tr').style.display !== 'none') {
                checkbox.checked = true;
                const id = checkbox.getAttribute('data-id');
                selectedItems.add(id);
            }
        });

        updateSelectedCount();
        updateSelectAllLinkText();
    }

    // Server-side select all function - selects ALL approved documents across ALL pages
    function selectAllDocumentsServerSide() {
        // Show loading indicator
        const selectAllLink = document.getElementById('selectAllLink');
        const originalText = selectAllLink.textContent;
        selectAllLink.textContent = 'Loading...';
        selectAllLink.disabled = true;

        // Get current filter values
        const organizationFilter = document.getElementById("organizationFilter").value;
        const typeFilter = document.getElementById("typeFilter").value;
        const searchTerm = document.querySelector('input[placeholder="Search..."]').value.trim();

        // Build filter parameters
        const params = new URLSearchParams();
        
        if (organizationFilter !== 'Organization' && organizationFilter !== 'All') {
            params.append('organization', organizationFilter);
        }
        
        if (typeFilter !== 'Type' && typeFilter !== 'All') {
            params.append('type', typeFilter);
        }
        
        if (searchTerm) {
            params.append('search', searchTerm);
        }

        // Add date filters
        if (currentDateFilter.start) {
            params.append('start_date', currentDateFilter.start);
        }
        
        if (currentDateFilter.end) {
            params.append('end_date', currentDateFilter.end);
        }

        if (document.getElementById("submittedToFilter").value !== 'Submitted' && 
            document.getElementById("submittedToFilter").value !== 'All') {
            params.append('submitted_to', document.getElementById("submittedToFilter").value);
        }

        // Make AJAX request to get all document IDs
        fetch("{{ route('admin.selectAllDocuments') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: params.toString()
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Store all document IDs from server
                allFilteredDocumentIds = data.document_ids;
                isSelectAllActive = true;

                // Add all IDs to selectedItems
                data.document_ids.forEach(id => {
                    selectedItems.add(id.toString());
                });

                // Check all visible checkboxes on current page
                document.querySelectorAll('.row-checkbox').forEach(checkbox => {
                    checkbox.checked = true;
                });

                // Check the main checkbox too
                const selectAllCheckbox = document.getElementById('selectAll');
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = true;
                }

                // Update UI
                updateSelectedCount();
                updateSelectAllLinkText();
                
                // Show success message
                showActionToast(
                    'Selection Complete',
                    `Selected ${data.total_count} approved documents across all pages.`,
                    true
                );

                console.log(`Server-side select all: ${data.total_count} documents selected`);
            } else {
                showActionToast(
                    'Selection Failed',
                    'Failed to select all documents.',
                    false
                );
            }
        })
        .catch(error => {
            console.error('Error selecting all documents:', error);
            showActionToast(
                'Selection Error',
                'An error occurred while selecting documents.',
                false
            );
        })
        .finally(() => {
            // Restore link state
            selectAllLink.disabled = false;
            updateSelectAllLinkText();
        });
    }

    // Deselect all documents
    function deselectAllDocuments() {
        isSelectAllActive = false;
        allFilteredDocumentIds = [];
        selectedItems.clear();

        // Uncheck all visible checkboxes
        document.querySelectorAll('.row-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });

        // Uncheck select all checkbox
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = false;
        }

        updateSelectedCount();
        updateSelectAllLinkText();
        console.log('All documents deselected');
    }

    // Update the "Select All" link text based on current state
    function updateSelectAllLinkText() {
        const selectAllLink = document.getElementById('selectAllLink');
        if (selectAllLink && !selectAllLink.disabled) {
            if (isSelectAllActive) {
                selectAllLink.textContent = 'Deselect All';
                selectAllLink.classList.remove('text-[#7A1212]');
                selectAllLink.classList.add('text-red-600');
            } else {
                selectAllLink.textContent = 'Select All';
                selectAllLink.classList.remove('text-red-600');
                selectAllLink.classList.add('text-[#7A1212]');
            }
        }
    }

    // Handle filter changes
    function handleFilterChange() {
        applyServerSideFilters();
    }

    // Add this function globally
    window.handleFilterChange = handleFilterChange;

    // Handle individual checkbox changes
    function handleIndividualCheckboxChange(checkbox) {
        const documentId = checkbox.getAttribute('data-id');
        
        if (checkbox.checked) {
            selectedItems.add(documentId);
        } else {
            selectedItems.delete(documentId);
            
            // If user unchecks any item, disable server-side select all
            if (isSelectAllActive) {
                isSelectAllActive = false;
                const selectAllCheckbox = document.getElementById('selectAll');
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = false;
                }
                updateSelectAllLinkText();
            }
        }
        
        updateSelectedCount();
    }

    // Restore selection state when navigating between pages
    function restoreSelectionStateOnPage() {
        // Check select all checkbox if server-side select all is active
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox && isSelectAllActive) {
            selectAllCheckbox.checked = true;
        }

        // Check individual checkboxes based on selectedItems
        document.querySelectorAll('.row-checkbox').forEach(checkbox => {
            const documentId = checkbox.getAttribute('data-id');
            if (selectedItems.has(documentId)) {
                checkbox.checked = true;
            }
        });

        updateSelectedCount();
        updateSelectAllLinkText();
    }

    // Updated function to maintain selection across filter changes
    function applyServerSideFilters() {
        console.log('Starting filter application...');
        
        // Show loading state
        const tableContainer = document.querySelector('#tableContainer');
        tableContainer.classList.add('opacity-50');

        // Reset server-side selection when filters change
        if (isSelectAllActive) {
            deselectAllDocuments();
        }

        // Reset selections
        selectedItems.clear();
        updateSelectedCount();

        // Build the query parameters
        const params = new URLSearchParams();

        const organizationFilter = document.getElementById("organizationFilter");
        const typeFilter = document.getElementById("typeFilter");
        const searchInput = document.querySelector('input[placeholder="Search..."]');
        const submittedToFilter = document.getElementById("submittedToFilter");

        // Log current filter values
        console.log('Current filter values:', {
            organization: organizationFilter ? organizationFilter.value : 'null',
            type: typeFilter ? typeFilter.value : 'null',
            search: searchInput ? searchInput.value : 'null',
            dateStart: currentDateFilter.start,
            dateEnd: currentDateFilter.end
        });

        if (organizationFilter && organizationFilter.value !== 'Organization' && organizationFilter.value !== 'All') {
            params.append('organization', organizationFilter.value);
            console.log('Added organization filter:', organizationFilter.value);
        }

        if (typeFilter && typeFilter.value !== 'Type' && typeFilter.value !== 'All') {
            params.append('type', typeFilter.value);
            console.log('Added type filter:', typeFilter.value);
        }

        if (searchInput && searchInput.value.trim() !== '') {
            params.append('search', searchInput.value.trim());
            console.log('Added search filter:', searchInput.value.trim());
        }

        // Add date filter parameters
        if (currentDateFilter.start) {
            params.append('start_date', currentDateFilter.start);
            console.log('Added start date filter:', currentDateFilter.start);
        }
        if (currentDateFilter.end) {
            params.append('end_date', currentDateFilter.end);
            console.log('Added end date filter:', currentDateFilter.end);
        }

        // Add submitted_to parameter if not default or All
        if (submittedToFilter && submittedToFilter.value !== 'Submitted' && submittedToFilter.value !== 'All') {
            params.append('submitted_to', submittedToFilter.value);
            console.log('Added submitted_to filter:', submittedToFilter.value);
        }
        
        // Create the URL
        const url = `${window.location.pathname}?${params.toString()}`;
        console.log('Making request to:', url);

        // Fetch filtered results
        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Filter response received:', {
                    hasHtml: !!data.html,
                    availableOrgs: data.availableOrganizations ? data.availableOrganizations.length : 0,
                    availableTypes: data.availableTypes ? data.availableTypes.length : 0
                });
                
                // Parse the HTML response
                const parser = new DOMParser();
                const doc = parser.parseFromString(data.html, 'text/html');

                // Directly replace the entire table body content
                const newTableBody = doc.querySelector('#documentTable tbody');
                const currentTableBody = document.querySelector('#documentTable tbody');
                
                if (newTableBody && currentTableBody) {
                    // IMPORTANT: This line is the key fix - replacing innerHTML instead of the element itself
                    currentTableBody.innerHTML = newTableBody.innerHTML;
                    console.log('Table body updated successfully');
                } else {
                    console.error('Table body not found', {
                        newTableBodyFound: !!newTableBody,
                        currentTableBodyFound: !!currentTableBody
                    });
                }

                // Update pagination
                const newPagination = doc.querySelector('#paginationContainer');
                const currentPagination = document.querySelector('#paginationContainer');

                // Get document count to determine if pagination should be visible
                const documentRows = doc.querySelectorAll('#documentTable tbody tr[data-id]');
                const hasDocuments = documentRows.length > 0;

                if (newPagination && hasDocuments) {
                    // Show and update pagination if we have documents and pagination HTML
                    if (currentPagination) {
                        currentPagination.outerHTML = newPagination.outerHTML;
                        console.log('Pagination updated');
                    }
                } else {
                    // Hide pagination when no documents or no pagination HTML
                    if (currentPagination) {
                        currentPagination.classList.add('hidden');
                        console.log('No documents or pagination in response, hiding pagination');
                    }
                }

                // Update URL without reload for bookmarking
                window.history.pushState({}, '', url);

                // Uncheck "select all" checkbox
                const selectAllCheckbox = document.getElementById('selectAll');
                if (selectAllCheckbox) {
                    const visibleRows = document.querySelectorAll("#documentTable tbody tr[data-id]").length;
                    selectAllCheckbox.disabled = visibleRows === 0;
                    selectAllCheckbox.checked = false;
                    console.log('Updated select all checkbox, visible rows:', visibleRows);
                }

                // Update export button state
                updateExportButtonState();

                // Update select all link text
                updateSelectAllLinkText();

                // Remove loading state
                tableContainer.classList.remove('opacity-50');
            })
            .catch(error => {
                console.error('Error applying filters:', error);
                tableContainer.classList.remove('opacity-50');
                
                // Show error to user
                showActionToast('Filter Error', 'An error occurred while applying filters.', false);
            });
    }

    // View document preview - THIS WAS MISSING
    function viewDocument(id) {
        window.location.href = "{{ route('admin.documentPreview', ['id' => ':id']) }}".replace(':id', id);
    }

    // Toast functionality
    function showActionToast(title, message, isSuccess = true) {
        const toast = document.getElementById('documentActionToast');
        const actionIcon = document.getElementById('actionIcon');
        const actionTitle = document.getElementById('actionTitle');
        const actionMessage = document.getElementById('actionMessage');

        // Set the appropriate icon and styling based on success/failure
        if (isSuccess) {
            actionIcon.src = "{{ asset('images/successful.svg') }}";
            toast.className = toast.className.replace('border-red-400', 'border-green-400');
            if (!toast.className.includes('border-green-400')) {
                toast.className = toast.className.replace('border-gray-400', 'border-green-400');
            }
        } else {
            actionIcon.src = "{{ asset('images/error.svg') }}"; // Assuming you have an error icon
            toast.className = toast.className.replace('border-green-400', 'border-red-400');
            if (!toast.className.includes('border-red-400')) {
                toast.className = toast.className.replace('border-gray-400', 'border-red-400');
            }
        }

        actionTitle.textContent = title;
        actionMessage.textContent = message;

        // Show the toast
        toast.classList.remove('hidden');

        // Auto-hide after 5 seconds
        setTimeout(() => {
            hideActionToast();
        }, 5000);
    }

    function hideActionToast() {
        const toast = document.getElementById('documentActionToast');
        toast.classList.add('hidden');
    }

    // Archives the selected documents - ONLY called from the confirmation modal
    function processArchiving() {
        if (selectedItems.size === 0) return;

        const documentIds = Array.from(selectedItems);
        const count = documentIds.length;

        fetch("{{ route('admin.archiveDocuments') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    document_ids: documentIds
                })
            })
            .then(response => response.json())
            .then((data) => {
                if (data.success) {
                    // Show success toast
                    showActionToast(
                        'Archive Successful',
                        `Successfully archived ${count} document${count > 1 ? 's' : ''}.`,
                        true
                    );

                    // Remove archived rows with smooth animation
                    documentIds.forEach(id => {
                        const row = document.querySelector(`tr[data-id="${id}"]`);
                        if (row) {
                            // Add fade-out animation
                            row.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(-20px)';

                            // Remove the row after animation completes
                            setTimeout(() => {
                                row.remove();
                            }, 500);
                        }
                    });

                    // Wait for animations to complete, then reset filters and reload data
                    setTimeout(() => {
                        resetFiltersAndReloadData();
                    }, 600);

                } else {
                    // Show error toast
                    showActionToast(
                        'Archive Failed',
                        data.message || 'Failed to archive documents.',
                        false
                    );
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Show error toast
                showActionToast(
                    'Archive Error',
                    'An error occurred while archiving documents.',
                    false
                );
            });
    }

    // Date filter functionality - UPDATED TO KEEP CONSTANT TEXT
    function updateDateFilterDisplay() {
        // Keep the button text constant as "Date Range"
        const dateFilterText = document.getElementById('dateFilterText');
        dateFilterText.textContent = 'Date Range';
    }

    function validateDateRange() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const errorDiv = document.getElementById('dateError');

        if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
            errorDiv.classList.remove('hidden');
            return false;
        } else {
            errorDiv.classList.add('hidden');
            return true;
        }
    }

    // Function to update the export button state based on document count
    function updateExportButtonState() {
        const floatingExportBtn = document.getElementById('floatingExportBtn');
        const visibleRows = document.querySelectorAll("#documentTable tbody tr[data-id]").length;

        if (visibleRows === 0) {
            floatingExportBtn.disabled = true;
            floatingExportBtn.classList.add('opacity-50', 'cursor-not-allowed');
            floatingExportBtn.classList.remove('hover:bg-[#DAA520]');
        } else {
            floatingExportBtn.disabled = false;
            floatingExportBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            floatingExportBtn.classList.add('hover:bg-[#DAA520]');
        }
    }

    // Function to reset all filters and reload all documents
    function resetFiltersAndReloadData() {
        // Reset all filter dropdowns to their default values
        const organizationFilter = document.getElementById("organizationFilter");
        const typeFilter = document.getElementById("typeFilter");
        const submittedToFilter = document.getElementById("submittedToFilter");
        const searchInput = document.querySelector('input[placeholder="Search..."]');

        // Reset filter values
        organizationFilter.value = "Organization";
        typeFilter.value = "Type";
        submittedToFilter.value = "Submitted";
        searchInput.value = "";

        // Reset date filter
        currentDateFilter = {
            start: '',
            end: ''
        };
        updateDateFilterDisplay();

        // Show loading state
        const tableContainer = document.querySelector('#tableContainer');
        tableContainer.classList.add('opacity-50');

        // Clear selections
        selectedItems.clear();
        updateSelectedCount();

        // Fetch all documents (no filters)
        const baseUrl = window.location.pathname;

        // Improved fetch with better error handling
        fetch(baseUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Server error: ${response.status}`);
            }
            
            // Check content type to handle both JSON and HTML appropriately
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json().then(data => {
                    // If JSON contains HTML property, use that
                    return { html: data.html, data };
                });
            } else {
                // If HTML response, wrap it in an object
                return response.text().then(text => ({ html: text }));
            }
        })
        .then(result => {
            // Parse the HTML response
            const parser = new DOMParser();
            const doc = parser.parseFromString(result.html, 'text/html');

            // Update the table body
            const newTableBody = doc.querySelector('#documentTable tbody');
            if (newTableBody) {
                document.querySelector('#documentTable tbody').innerHTML = newTableBody.innerHTML;
            }

            // Update pagination
            const newPagination = doc.querySelector('#paginationContainer');
            const currentPagination = document.querySelector('#paginationContainer');
            
            if (newPagination && currentPagination) {
                currentPagination.innerHTML = newPagination.innerHTML;
            }

            // Update URL without query parameters
            window.history.pushState({}, '', baseUrl);

            // Update export button state
            updateExportButtonState();
            
            // Remove loading state
            tableContainer.classList.remove('opacity-50');
            
            // If we have additional data from JSON response, update filters
            if (result.data) {
                if (result.data.availableOrganizations) {
                    updateOrganizationFilterOptions(result.data.availableOrganizations);
                }
                if (result.data.availableTypes) {
                    updateTypeFilterOptions(result.data.availableTypes);
                }
            }
            
            // Show success toast for archive operation
            showActionToast('Success', 'Documents archived successfully!', true);
        })
        .catch(error => {
            console.error('Error refreshing document list:', error);
            tableContainer.classList.remove('opacity-50');
            
            // Don't show error toast - instead show a refresh button
            const refreshButton = document.createElement('button');
            refreshButton.className = 'px-4 py-2 bg-[#7A1212] text-white rounded hover:bg-[#DAA520] mt-4';
            refreshButton.textContent = 'Refresh List';
            refreshButton.onclick = () => window.location.reload();
            
            const tableContainer = document.querySelector('#tableContainer');
            tableContainer.appendChild(refreshButton);
        });
    }

    // Sorting functionality
    function sortTable(columnIndex) {
        const columnMap = [
            'control_tag', // Tag - index 1
            'organization', // Organization - index 2
            'subject', // Subject - index 3
            'created_at', // Date Submitted - index 4
            'type' // Type - index 5
        ];

       // Get the database column name based on index (adjust for checkbox column)
    const columnName = columnMap[columnIndex - 1]; 
    if (!columnName) return;

    // Toggle sort direction for this column
    sortDirection[columnIndex] = !sortDirection[columnIndex];
    const direction = sortDirection[columnIndex] ? 'asc' : 'desc';
    
    // Show loading indicator
    const tableContainer = document.querySelector('#tableContainer');
    tableContainer.classList.add('opacity-50');

    // Build the query parameters - preserve ALL existing filters
    const params = new URLSearchParams(window.location.search);
    
    // Update/add sort parameters
    params.set('sort_by', columnName);
    params.set('sort_dir', direction);
    
    // Create the URL with all parameters
    const url = `${window.location.pathname}?${params.toString()}`;

    // Fetch the sorted data
    fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Parse the HTML response
            const parser = new DOMParser();
            const doc = parser.parseFromString(data.html, 'text/html');

            // Update the table body
            const newTableBody = doc.querySelector('#documentTable tbody');
            if (newTableBody) {
                const currentTableBody = document.querySelector('#documentTable tbody');
                if (currentTableBody) {
                    currentTableBody.innerHTML = newTableBody.innerHTML;
                }
            }

            // Check if there are any document rows to determine if pagination should be shown
            const documentRows = document.querySelectorAll('#documentTable tbody tr[data-id]');
            const documentCount = documentRows.length;
            const paginationContainer = document.querySelector('#paginationContainer');
            
            // Update pagination visibility based on document count
            if (documentCount > 0) {
                // If we have documents, show and update pagination
                const newPagination = doc.querySelector('#paginationContainer');
                if (newPagination && paginationContainer) {
                    paginationContainer.innerHTML = newPagination.innerHTML;
                    paginationContainer.classList.remove('hidden');
                }
            } else {
                // If no documents, hide pagination
                if (paginationContainer) {
                    paginationContainer.classList.add('hidden');
                }
            }

            // Update URL without reload
            window.history.pushState({}, '', url);

            // Update sort indicator visually
            updateSortIndicator(columnIndex);

            // Reset selected items since we got new data
            if (isSelectAllActive) {
                deselectAllDocuments();
            }
            selectedItems.clear();
            updateSelectedCount();

            // Update export button state
            updateExportButtonState();

            // Remove loading state
            tableContainer.classList.remove('opacity-50');
        })
        .catch(error => {
            console.error('Error sorting table:', error);
            tableContainer.classList.remove('opacity-50');
        });
}

    function updateSortIndicator(columnIndex) {
        const sortIcons = document.querySelectorAll('thead button img');
        sortIcons.forEach(icon => {
            icon.classList.remove('rotate-180');
        });

        const clickedIcon = document.querySelector(`thead th:nth-child(${columnIndex + 1}) button img`);
        if (clickedIcon && !sortDirection[columnIndex]) {
            clickedIcon.classList.add('rotate-180');
        }
    }

    // Show the modal when the archive button is clicked
    function showArchiveConfirmation() {
        if (selectedItems.size > 0) {
            document.getElementById("archiveConfirmationModal").classList.remove("hidden");
        }
    }

    // Event delegation for checkbox and button clicks
    document.addEventListener('click', function(e) {
        // Handle "Select All" checkbox
        if (e.target.id === 'selectAll') {
            const checkboxes = document.querySelectorAll('.row-checkbox:not(:disabled)');

            // Check/uncheck all visible checkboxes
            checkboxes.forEach(checkbox => {
                if (checkbox.closest('tr').style.display !== 'none') {
                    checkbox.checked = e.target.checked;

                    const id = checkbox.getAttribute('data-id');
                    if (e.target.checked) {
                        selectedItems.add(id);
                    } else {
                        selectedItems.delete(id);
                    }
                }
            });

            updateSelectedCount();
        }
        // Handle individual row checkbox
        else if (e.target.classList.contains('row-checkbox')) {
            const id = e.target.getAttribute('data-id');

            if (e.target.checked) {
                selectedItems.add(id);
            } else {
                selectedItems.delete(id);
            }

            updateSelectedCount();
        }
        // Handle archive button - ONLY SHOW MODAL, NO ARCHIVING
        else if (e.target.id === 'archiveSelectedBtn') {
            showArchiveConfirmation();
        }
        // Handle date filter button
        else if (e.target.id === 'dateFilterBtn' || e.target.closest('#dateFilterBtn')) {
            document.getElementById('dateFilterModal').classList.remove('hidden');
        }
    });

    // Close button functionality for the archive modal
    document.getElementById("closeArchiveModalBtn").addEventListener("click", function() {
        document.getElementById("archiveConfirmationModal").classList.add("hidden");
    });

    document.getElementById("cancelArchiveBtn").addEventListener("click", function() {
        document.getElementById("archiveConfirmationModal").classList.add("hidden");
    });

    // ONLY THIS BUTTON SHOULD TRIGGER THE ACTUAL ARCHIVING
    document.getElementById("confirmArchiveBtn").addEventListener("click", function() {
        processArchiving(); // Call the function that actually does the archiving
        document.getElementById("archiveConfirmationModal").classList.add("hidden");
    });

    // Date filter modal event listeners
    document.getElementById("closeDateModalBtn").addEventListener("click", function() {
        document.getElementById("dateFilterModal").classList.add("hidden");
    });

    document.getElementById("startDate").addEventListener("change", function() {
        validateDateRange();
        // Update the minimum date for end date
        const startDate = this.value;
        if (startDate) {
            document.getElementById('endDate').min = startDate;
        }
    });

    document.getElementById("endDate").addEventListener("change", validateDateRange);

    document.getElementById("clearDateFilterBtn").addEventListener("click", function() {
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('endDate').removeAttribute('min');
        currentDateFilter = {
            start: '',
            end: ''
        };
        updateDateFilterDisplay(); // This will reset to "Date Range"
        document.getElementById("dateFilterModal").classList.add("hidden");
        // Apply the cleared filter using global function
        if (typeof handleFilterChange === 'function') {
            handleFilterChange();
        }
    });

    document.getElementById("applyDateFilterBtn").addEventListener("click", function() {
        if (validateDateRange()) {
            currentDateFilter.start = document.getElementById('startDate').value;
            currentDateFilter.end = document.getElementById('endDate').value;
            updateDateFilterDisplay(); // This will keep it as "Date Range"
            document.getElementById("dateFilterModal").classList.add("hidden");
            // Apply the date filter using global function
            if (typeof handleFilterChange === 'function') {
                handleFilterChange();
            }
        }
    });

    // Update selected count display
    function updateSelectedCount() {
        const count = selectedItems.size;
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('archiveSelectedBtn').disabled = count === 0;
        
        // Update the regular checkbox state based on visible selections
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox && !isSelectAllActive) {
            const visibleCheckboxes = document.querySelectorAll('.row-checkbox');
            const checkedVisibleCheckboxes = document.querySelectorAll('.row-checkbox:checked');
            
            if (visibleCheckboxes.length > 0 && checkedVisibleCheckboxes.length === visibleCheckboxes.length) {
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.checked = false;
            }
        }
    }

    // Function to export document history
    function exportDocumentHistory(format = 'excel') {
        // Close dropdown
        document.getElementById('exportDropdown').classList.add('hidden');
        
        // Check if button is disabled
        const floatingExportBtn = document.getElementById('floatingExportBtn');
        if (floatingExportBtn.disabled) {
            return; // Don't proceed if button is disabled
        }

        // Get current filter values - ALWAYS get these regardless of selection
        const organization = document.getElementById("organizationFilter").value !== "Organization" ?
            document.getElementById("organizationFilter").value : '';
        const type = document.getElementById("typeFilter").value !== "Type" ?
            document.getElementById("typeFilter").value : '';
        const search = document.querySelector('input[placeholder="Search..."]').value;

        // Build export URL with current filters
        let exportUrl = format === 'pdf' 
            ? "{{ route('admin.document.export.pdf') }}" 
            : "{{ route('admin.document.export') }}";
        const params = new URLSearchParams();

        // Store original HTML
        const originalContent = floatingExportBtn.innerHTML;

        // ALWAYS send filter parameters (whether documents are selected or not)
        if (organization && organization !== 'All') params.append('organization', organization);
        if (type && type !== 'All') params.append('type', type);
        if (search.trim() !== '') params.append('search', search);

        // Add date filter parameters with display-friendly versions
        if (currentDateFilter.start) {
            params.append('start_date', currentDateFilter.start);
            params.append('start_date_display', new Date(currentDateFilter.start).toLocaleDateString());
        }
        if (currentDateFilter.end) {
            params.append('end_date', currentDateFilter.end);
            params.append('end_date_display', new Date(currentDateFilter.end).toLocaleDateString());
        }

        // Check if there are selected documents and add them
        if (selectedItems.size > 0) {
            // Export only selected documents BUT with current filter information
            const selectedArray = Array.from(selectedItems);
            selectedArray.forEach(id => {
                params.append('selected_documents[]', id);
            });
            
            // Set loading state for selected export
            floatingExportBtn.innerHTML = `<span class="animate-pulse">Exporting ${selectedItems.size} selected as ${format.toUpperCase()}...</span>`;
        } else {
            // Set loading state for full export
            floatingExportBtn.innerHTML = `<span class="animate-pulse">Generating ${format.toUpperCase()}...</span>`;
        }

        floatingExportBtn.disabled = true;

        // Append parameters to URL
        if (params.toString()) {
            exportUrl += '?' + params.toString();
        }

        // Create a hidden iframe to handle the download
        const hiddenIframe = document.createElement('iframe');
        hiddenIframe.style.display = 'none';
        document.body.appendChild(hiddenIframe);

        // Set a timeout to restore the button state
        setTimeout(() => {
            floatingExportBtn.innerHTML = originalContent;
            updateExportButtonState(); // Use the function to properly restore state
            
            // Show success message
            if (selectedItems.size > 0) {
                showActionToast(
                    'Export Successful',
                    `Successfully exported ${selectedItems.size} selected documents as ${format.toUpperCase()}.`,
                    true
                );
            } else {
                showActionToast(
                    'Export Successful',
                    `Document history exported successfully as ${format.toUpperCase()}.`,
                    true
                );
            }
        }, 3000); // 3 seconds timeout

        // Navigate iframe to the export URL to trigger download
        hiddenIframe.src = exportUrl;

        // Remove the iframe after download likely started
        setTimeout(() => {
            if (hiddenIframe.parentNode) {
                hiddenIframe.parentNode.removeChild(hiddenIframe);
            }
        }, 5000);
    }

    function toggleExportDropdown() {
        const floatingExportBtn = document.getElementById('floatingExportBtn');
        // Only toggle dropdown if button is not disabled
        if (!floatingExportBtn.disabled) {
            const dropdown = document.getElementById('exportDropdown');
            dropdown.classList.toggle('hidden');
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('exportDropdown');
        const button = document.getElementById('floatingExportBtn');
        
        if (!button.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('a.pagination-btn, a.pagination-btn-prev, a.pagination-btn-next');

        if (paginationLink && !paginationLink.classList.contains('cursor-not-allowed')) {
            e.preventDefault();
            const url = paginationLink.getAttribute('href');

            // Show loading indicator
            const tableContainer = document.querySelector('#tableContainer');
            tableContainer.classList.add('opacity-50');

            // Fetch the new page content
            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'  // Request JSON response
                    }
                })
                .then(response => response.json())  // Parse as JSON instead of text
                .then(data => {
                    // Parse the HTML response
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(data.html, 'text/html');

                    // Extract the table body content
                    const newTableBody = doc.querySelector('#documentTable tbody');
                    if (newTableBody) {
                        document.querySelector('#documentTable tbody').innerHTML = newTableBody.innerHTML;
                    }

                    // Update pagination
                    const newPagination = doc.querySelector('#paginationContainer');
                    if (newPagination) {
                        const currentPagination = document.querySelector('#paginationContainer');
                        if (currentPagination) {
                            currentPagination.outerHTML = newPagination.outerHTML;
                        }
                    }

                    // Restore selection state for visible documents
                    restoreSelectionStateOnPage();

                    // Update URL without reload
                    window.history.pushState({}, '', url);

                    // Update export button state
                    updateExportButtonState();

                    // Remove loading state
                    tableContainer.classList.remove('opacity-50');
                })
                .catch(error => {
                    console.error('Error loading page:', error);
                    tableContainer.classList.remove('opacity-50');
                });
        }
    });
</script>
@endsection