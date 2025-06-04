@extends('base')

@section('content')
@include('components.adminSidebarComponent')
<div id="main-content" class="flex flex-col min-h-screen ml-[20%] transition-all duration-300 bg-[#F2F4F7]">
    @include('components.adminNavBarComponent')
    <div class="flex-grow mb-10">
        <div class="w-full px-6 py-8 flex flex-col">
            <!-- Header section with title and archive page link -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-extrabold">Document History Table</h2>

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
                            <option class="bg-white text-black" value="Organization" disabled selected>Organization
                            </option>
                            <option class="bg-white text-black" value="All">All Organizations</option>
                            <!-- Organization options -->
                            <option class="bg-white text-black" value="ACAP">ACAP</option>
                            <option class="bg-white text-black" value="AECES">AECES</option>
                            <option class="bg-white text-black" value="ELITE">ELITE</option>
                            <option class="bg-white text-black" value="GIVE">GIVE</option>
                            <option class="bg-white text-black" value="JEHRA">JEHRA</option>
                            <option class="bg-white text-black" value="JMAP">JMAP</option>
                            <option class="bg-white text-black" value="JPIA">JPIA</option>
                            <option class="bg-white text-black" value="PIIE">PIIE</option>
                            <option class="bg-white text-black" value="AGDS">AGDS</option>
                            <option class="bg-white text-black" value="Chorale">Chorale</option>
                            <option class="bg-white text-black" value="SIGMA">SIGMA</option>
                            <option class="bg-white text-black" value="TAPNOTCH">TAPNOTCH</option>
                            <option class="bg-white text-black" value="OSC">OSC</option>
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
                            <!-- Document type options -->
                            <option class="bg-white text-black truncate" value="Event Proposal">Event Proposal</option>
                            <option class="bg-white text-black truncate" value="General Plan of Activities">General Plan
                                of
                                Activities</option>
                            <option class="bg-white text-black truncate" value="Calendar of Activities">Calendar of
                                Activities
                            </option>
                            <option class="bg-white text-black truncate" value="Accomplishment Report">Accomplishment
                                Report
                            </option>
                            <option class="bg-white text-black truncate" value="Contribution and By-Laws">Contribution
                                and
                                By-Laws</option>
                            <option class="bg-white text-black truncate" value="Request Letter">Request Letter</option>
                            <option class="bg-white text-black truncate" value="Off-Campus">Off-Campus</option>
                            <option class="bg-white text-black truncate" value="Petition and Concern">Petition and
                                Concern
                            </option>
                        </select>
                        <!-- Custom dropdown arrow -->
                        <img src="{{ asset('images/dropdownIcon.svg') }}" alt="Dropdown Icon"
                            class="absolute top-1/2 right-3 -translate-y-1/2 w-4 h-4 pointer-events-none" />
                    </div>

                    <!-- Status dropdown filter -->
                    <div class="relative w-40">
                        <select id="statusFilter"
                            class="appearance-none border px-4 py-2 rounded-full bg-[#7A1212] text-white w-full pr-8 hover:bg-[#DAA520] hover:text-white transition-colors duration-200">
                            <option class="bg-white text-black" value="Status" disabled selected>Status</option>
                            <option class="bg-white text-black" value="All">All Status</option>
                            <option class="bg-white text-black" value="Approved">Approved</option>
                            <option class="bg-white text-black" value="Rejected">Rejected</option>
                        </select>
                        <!-- Custom dropdown arrow -->
                        <img src="{{ asset('images/dropdownIcon.svg') }}" alt="Dropdown Icon"
                            class="absolute top-1/2 right-3 -translate-y-1/2 w-4 h-4 pointer-events-none" />
                    </div>
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
            'IT' => 'text-orange-500',
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
                                    <!-- Select all checkbox column -->
                                    <th class="px-4 py-2 whitespace-nowrap">
                                        <input type="checkbox" id="selectAll" class="w-4 h-4 cursor-pointer">
                                    </th>
                                    <!-- Column headers with sort functionality -->
                                    @php $headers = ['Tag', 'Organization', 'Title', 'Date Submitted', 'Type', 'Status']; @endphp
                                    @foreach ($headers as $i => $header)
                                    <th class="px-4 py-2 whitespace-nowrap max-w-[160px] truncate">
                                        @if ($header !== 'Status')
                                        <button onclick="sortTable({{ $i + 1 }})"
                                            class="flex items-center gap-1 group">
                                            <span>{{ $header }}</span>
                                            <img src="{{ asset('images/sortIcon.svg') }}" alt="Sort"
                                                class="w-3 h-3 text-gray-500 group-hover:text-black transition">
                                        </button>
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
                                $orgName = isset($orgMap[$acronym]) ? $orgMap[$acronym] : $acronym;

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
                                $tagColor = isset($tagColors[$colorKey])
                                ? $tagColors[$colorKey]
                                : 'text-gray-500';

                                // Format date for consistent display
                                $createdDate = \Carbon\Carbon::parse($document->created_at)->format(
                                'm/d/Y',
                                );
                                @endphp
                                <!-- Document row with data attributes for filtering -->
                                <tr class="border-b border-gray-300 hover:bg-gray-100"
                                    data-org-acronym="{{ $acronym }}" data-status="{{ $document->status }}"
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
                                    <!-- Organization name with tooltip for full name -->
                                    <td class="px-4 py-2 truncate max-w-[160px] cursor-pointer"
                                        onclick="viewDocument({{ $document->id }})" title="{{ $orgName }}">
                                        {{ $orgName }}
                                    </td>
                                    <!-- Document subject with tooltip for full text -->
                                    <td class="px-4 py-2 truncate max-w-[160px] cursor-pointer"
                                        onclick="viewDocument({{ $document->id }})"
                                        title="{{ $document->subject }}">
                                        {{ $document->subject }}
                                    </td>
                                    <!-- Date submitted -->
                                    <td class="px-4 py-2 truncate max-w-[120px] cursor-pointer"
                                        onclick="viewDocument({{ $document->id }})">
                                        {{ $createdDate }}
                                    </td>
                                    <!-- Document type with tooltip -->
                                    <td class="px-4 py-2 truncate max-w-[160px] cursor-pointer"
                                        onclick="viewDocument({{ $document->id }})"
                                        title="{{ $document->type }}">
                                        {{ $document->type }}
                                    </td>
                                    <!-- Status with color-coded badge -->
                                    <td class="px-4 py-2 cursor-pointer"
                                        onclick="viewDocument({{ $document->id }})">
                                        <span
                                            class="px-4 py-1 rounded-full text-white inline-block min-w-[100px] text-center 
                                              {{ $document->status === 'Approved'
                                                  ? 'bg-[#10B981]'
                                                  : ($document->status === 'Rejected'
                                                      ? 'bg-[#EF4444]'
                                                      : 'bg-[#F59E0B]') }}">
                                            {{ $document->status }}
                                        </span>
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

            <!-- Pagination controls -->
            @if (count($documents) > 0)
            <div class="mt-4 flex justify-center" id="paginationContainer">
                <nav>
                    <ul class="inline-flex items-center space-x-2">
                        <!-- Previous page button -->
                        <li>
                            <a href="{{ $documents->previousPageUrl() }}"
                                class="pagination-btn-prev px-3 py-1 rounded-lg {{ $documents->currentPage() == 1 ? 'cursor-not-allowed opacity-50' : '' }}">
                                < </a>
                        </li>

                        <!-- Page numbers -->
                        @for ($i = 1; $i <= $documents->lastPage(); $i++)
                            <li>
                                <a href="{{ $documents->url($i) }}"
                                    class="pagination-btn px-3 py-1 rounded-lg {{ $documents->currentPage() == $i ? 'bg-[#7A1212] text-white' : '' }}">
                                    {{ $i }}
                                </a>
                            </li>
                            @endfor

                            <!-- Next page button -->
                            <li>
                                <a href="{{ $documents->nextPageUrl() }}"
                                    class="pagination-btn-next px-3 py-1 rounded-lg {{ $documents->currentPage() == $documents->lastPage() ? 'cursor-not-allowed opacity-50' : '' }}">
                                    >
                                </a>
                            </li>
                    </ul>
                </nav>
            </div>
            @endif
        </div>

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
        <!-- Generate Reports button -->
        <div class="w-full px-6 flex justify-end mb-8">
            <button id="floatingExportBtn"
                class="px-4 py-2 bg-[#7A1212] text-white rounded-full shadow-lg hover:bg-[#DAA520] transition-colors duration-200 flex items-center gap-2"
                onclick="exportDocumentHistory()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Generate Reports
            </button>
        </div>


        <script>
            // Configuration constants
            let selectedItems = new Set(); // Set to track selected document IDs

            // Updates the selected document count and button state
            function updateSelectedCount() {
                const count = selectedItems.size;
                document.getElementById('selectedCount').textContent = count;
                document.getElementById('archiveSelectedBtn').disabled = count === 0;
            }

            //Archives the selected documents - ONLY called from the confirmation modal

            function processArchiving() {
                if (selectedItems.size === 0) return;

                const documentIds = Array.from(selectedItems);

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
                            // Reload the page to show updated list
                            window.location.reload();
                        } else {
                            alert(data.message || 'Failed to archive documents.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while archiving documents.');
                    });
            }

            // Track sort direction for each column
            let sortDirection = [true, true, true, true, true, true];

            //view document preview
            function viewDocument(id) {
                window.location.href = "{{ route('admin.documentPreview', ['id' => ':id']) }}".replace(':id', id);
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

                const columnName = columnMap[columnIndex - 1];

                if (!columnName) return;

                sortDirection[columnIndex] = !sortDirection[columnIndex];
                const direction = sortDirection[columnIndex] ? 'asc' : 'desc';

                const tableContainer = document.querySelector('#tableContainer');
                tableContainer.classList.add('opacity-50');

                const statusFilter = document.getElementById("statusFilter").value;
                const orgFilter = document.getElementById("organizationFilter").value;
                const typeFilter = document.getElementById("typeFilter").value;
                const searchInput = document.querySelector('input[placeholder="Search..."]').value;

                const params = new URLSearchParams(window.location.search);

                params.set('sort_by', columnName);
                params.set('sort_dir', direction);

                if (statusFilter !== 'Status') {
                    params.set('status', statusFilter);
                }

                if (orgFilter !== 'Organization') {
                    params.set('organization', orgFilter);
                }

                if (typeFilter !== 'Type') {
                    params.set('type', typeFilter);
                }

                if (searchInput.trim() !== '') {
                    params.set('search', searchInput);
                }

                const url = `${window.location.pathname}?${params.toString()}`;

                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        const newTableBody = doc.querySelector('#documentTable tbody');
                        if (newTableBody) {
                            document.querySelector('#documentTable tbody').innerHTML = newTableBody.innerHTML;
                        }

                        const newPagination = doc.querySelector('#paginationContainer');
                        const currentPagination = document.querySelector('#paginationContainer');

                        if (newPagination && currentPagination) {
                            currentPagination.outerHTML = newPagination.outerHTML;
                        }

                        window.history.pushState({}, '', url);

                        updateSortIndicator(columnIndex);

                        tableContainer.classList.remove('opacity-50');

                        // Reset selections after sort
                        selectedItems.clear();
                        updateSelectedCount();

                        // Uncheck "select all" checkbox
                        const selectAllCheckbox = document.getElementById('selectAll');
                        if (selectAllCheckbox) {
                            selectAllCheckbox.checked = false;
                        }
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
            });

            // Close button functionality for the modal
            document.getElementById("closeArchiveModalBtn").addEventListener("click", function() {
                document.getElementById("archiveConfirmationModal").classList.add("hidden");
            });

            document.getElementById("cancelArchiveBtn").addEventListener("click", function() {
                document.getElementById("archiveConfirmationModal").classList.add("hidden");
            });

            // Show the modal when the archive button is clicked
            function showArchiveConfirmation() {
                if (selectedItems.size > 0) {
                    document.getElementById("archiveConfirmationModal").classList.remove("hidden");
                }
            }

            // ONLY THIS BUTTON SHOULD TRIGGER THE ACTUAL ARCHIVING
            document.getElementById("confirmArchiveBtn").addEventListener("click", function() {
                processArchiving(); // Call the function that actually does the archiving
                document.getElementById("archiveConfirmationModal").classList.add("hidden");
            });

            document.addEventListener('DOMContentLoaded', function() {
                // Initial setup
                const initialVisibleRows = document.querySelectorAll("#documentTable tbody tr[data-id]").length;
                const selectAllCheckbox = document.getElementById('selectAll');
                if (selectAllCheckbox) {
                    selectAllCheckbox.disabled = initialVisibleRows === 0;
                }

                // Filter form elements
                const statusFilter = document.getElementById("statusFilter");
                const organizationFilter = document.getElementById("organizationFilter");
                const typeFilter = document.getElementById("typeFilter");
                const searchInput = document.querySelector('input[placeholder="Search..."]');

                // Handle filter changes
                function handleFilterChange() {
                    applyServerSideFilters();
                }

                // Add event listeners to filters
                statusFilter.addEventListener("change", handleFilterChange);
                organizationFilter.addEventListener("change", handleFilterChange);
                typeFilter.addEventListener("change", handleFilterChange);

                // For search, use debouncing
                let searchTimeout;
                searchInput.addEventListener("input", function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(handleFilterChange, 500);
                });

                // Function to apply server-side filters
                function applyServerSideFilters() {
                    // Show loading state
                    const tableContainer = document.querySelector('#tableContainer');
                    tableContainer.classList.add('opacity-50');

                    // Reset selections
                    selectedItems.clear();
                    updateSelectedCount();

                    // Build the query parameters
                    const params = new URLSearchParams();

                    if (statusFilter.value !== 'Status') {
                        params.append('status', statusFilter.value);
                    }

                    if (organizationFilter.value !== 'Organization') {
                        params.append('organization', organizationFilter.value);
                    }

                    if (typeFilter.value !== 'Type') {
                        params.append('type', typeFilter.value);
                    }

                    if (searchInput.value.trim() !== '') {
                        params.append('search', searchInput.value);
                    }

                    // Create the URL
                    const url = `${window.location.pathname}?${params.toString()}`;

                    // Fetch filtered results
                    fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            // Parse the HTML response
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');

                            // Update the table body
                            const newTableBody = doc.querySelector('#documentTable tbody');
                            if (newTableBody) {
                                document.querySelector('#documentTable tbody').innerHTML = newTableBody.innerHTML;
                            }

                            // Update pagination
                            const newPagination = doc.querySelector('#paginationContainer');
                            const currentPagination = document.querySelector('#paginationContainer');

                            if (newPagination) {
                                if (currentPagination) {
                                    currentPagination.outerHTML = newPagination.outerHTML;
                                }
                            } else if (currentPagination) {
                                // If no new pagination but we had one before, hide it
                                currentPagination.style.display = 'none';
                            }

                            // Update URL without reload for bookmarking
                            window.history.pushState({}, '', url);

                            // Uncheck "select all" checkbox
                            if (selectAllCheckbox) {
                                selectAllCheckbox.checked = false;

                                // Disable checkbox if no results
                                const visibleRows = document.querySelectorAll("#documentTable tbody tr[data-id]").length;
                                selectAllCheckbox.disabled = visibleRows === 0;
                            }

                            // Remove loading state
                            tableContainer.classList.remove('opacity-50');
                        })
                        .catch(error => {
                            console.error('Error applying filters:', error);
                            tableContainer.classList.remove('opacity-50');
                        });
                }

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
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.text())
                            .then(html => {
                                // Create a temporary element to parse the HTML
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');

                                // Extract the table body content
                                const newTableBody = doc.querySelector('#documentTable tbody').innerHTML;
                                document.querySelector('#documentTable tbody').innerHTML = newTableBody;

                                // Update pagination
                                const newPagination = doc.querySelector('#paginationContainer');
                                if (newPagination) {
                                    const currentPagination = document.querySelector(
                                        '#paginationContainer');
                                    if (currentPagination) {
                                        currentPagination.outerHTML = newPagination.outerHTML;
                                    }
                                }

                                // Update URL without reload
                                window.history.pushState({}, '', url);

                                // Reset selections
                                selectedItems.clear();
                                updateSelectedCount();

                                // If "select all" checkbox is checked, uncheck it
                                if (selectAllCheckbox) {
                                    selectAllCheckbox.checked = false;
                                }

                                // Remove loading state
                                tableContainer.classList.remove('opacity-50');
                            })
                            .catch(error => {
                                console.error('Error loading page:', error);
                                tableContainer.classList.remove('opacity-50');
                            });
                    }
                });
            });

            function exportDocumentHistory() {
                // Get current filter values
                const status = document.getElementById("statusFilter").value !== "Status" ?
                    document.getElementById("statusFilter").value : '';
                const organization = document.getElementById("organizationFilter").value !== "Organization" ?
                    document.getElementById("organizationFilter").value : '';
                const type = document.getElementById("typeFilter").value !== "Type" ?
                    document.getElementById("typeFilter").value : '';
                const search = document.querySelector('input[placeholder="Search..."]').value;

                // Build export URL with current filters
                let exportUrl = "{{ route('admin.document.export') }}";
                const params = new URLSearchParams();

                if (status && status !== 'All') params.append('status', status);
                if (organization && organization !== 'All') params.append('organization', organization);
                if (type && type !== 'All') params.append('type', type);
                if (search.trim() !== '') params.append('search', search);

                // Get only buttons that exist
                const floatingExportBtn = document.getElementById('floatingExportBtn');

                // Store original HTML
                const originalContent = floatingExportBtn.innerHTML;

                // Set loading state
                floatingExportBtn.innerHTML = '<span class="animate-pulse">Generating...</span>';
                floatingExportBtn.disabled = true;

                // Append parameters to URL
                if (params.toString()) {
                    exportUrl += '?' + params.toString();
                }

                // Create a hidden iframe to handle the download
                const hiddenIframe = document.createElement('iframe');
                hiddenIframe.style.display = 'none';
                document.body.appendChild(hiddenIframe);

                // Set a timeout to restore the button state if the download takes too long
                setTimeout(() => {
                    floatingExportBtn.innerHTML = originalContent;
                    floatingExportBtn.disabled = false;
                }, 5000); // 5 seconds timeout

                // Navigate iframe to the export URL to trigger download
                hiddenIframe.src = exportUrl;

                // Remove the iframe after download likely started
                setTimeout(() => {
                    if (hiddenIframe.parentNode) {
                        hiddenIframe.parentNode.removeChild(hiddenIframe);
                    }
                    // Restore button state
                    floatingExportBtn.innerHTML = originalContent;
                    floatingExportBtn.disabled = false;
                }, 2000);
            }
        </script>
    </div>
    @include('components.footer')
</div>
@endsection