@extends('base')

@section('content')
    @include('components.studentSidebarComponent')
    <div id="main-content" class="flex flex-col min-h-screen ml-[20%] transition-all duration-300 bg-[#F2F4F7]">
        @include('components.studentNavBarComponent')
        <div class="flex-grow mb-10">
            <div class="w-full px-6 py-8 flex flex-col">
                <h2 class="text-2xl font-extrabold mb-4">Document History Table</h2>

                <!-- Search and filter controls section -->
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <!-- Search input with magnifier icon -->
                    <div class="flex-1 min-w-[200px] relative">
                        <input type="text" placeholder="Search..."
                            class="border border-[#9099A5] px-4 py-2 pr-10 rounded-full w-full bg-white">
                        <img src="{{ asset('images/Magnifier.svg') }}" alt="Search"
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 pointer-events-none" />
                    </div>

                    <!-- Filter dropdowns -->
                    <div class="flex flex-wrap items-center gap-4 justify-end">
                        <!-- Document type dropdown filter -->
                        <div class="relative w-40">
                            <select id="typeFilter"
                                class="appearance-none border px-4 py-2 rounded-full bg-[#7A1212] text-white w-full pr-8 hover:bg-[#DAA520] hover:text-white transition-colors duration-200 truncate">
                                <option class="bg-white text-black truncate" value="Type" disabled selected>Type</option>
                                <option class="bg-white text-black truncate" value="All">All Types</option>
                                <option class="bg-white text-black truncate" value="Event Proposal">Event Proposal</option>
                                <option class="bg-white text-black truncate" value="General Plan of Activities">General Plan
                                    of
                                    Activities</option>
                                <option class="bg-white text-black truncate" value="Calendar of Activities">Calendar of
                                    Activities</option>
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
                    // Assuming we get these values from auth/session - using ELITE as user's organization
$userOrganization = 'ELITE'; // This should come from authenticated user's data

                    // Organization mapping - only include user's organization
$orgMap = [
    'ELITE' => 'Eligible League of Information Technology Enthusiasts',
];

// Color coding for different organization tags
$tagColors = [
    'IT' => 'text-orange-500',
];

// Document types array
$types = [
    'Event Proposal',
    'General Plan of Activities',
    'Calendar of Activities',
    'Accomplishment Report',
    'Contribution and By-Laws',
    'Request Letter',
    'Off-Campus',
    'Petition and Concern',
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
                                        @php $headers = ['Tag', 'Title', 'Date Submitted', 'Type', 'Status']; @endphp
                                        @foreach ($headers as $i => $header)
                                            <th class="px-4 py-2 whitespace-nowrap max-w-[160px] truncate">
                                                @if ($header !== 'Status')
                                                    <button onclick="sortTable({{ $i }})"
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
                                <!-- Table body -->
                                <tbody>
                                    @forelse ($documents as $document)
                                        @php
                                            // Extract organization acronym from control tag (e.g., "ELITE_001")
                                            $parts = explode('_', $document->control_tag);
                                            $acronym = count($parts) > 0 ? $parts[0] : '';
                                            $tagColor = isset($tagColors['IT']) ? $tagColors['IT'] : 'text-gray-500';

                                            // Format date for consistent display
                                            $createdDate = \Carbon\Carbon::parse($document->created_at)->format(
                                                'm/d/Y',
                                            );
                                        @endphp
                                        <!-- Document row with data attributes for filtering -->
                                        <tr class="border-b border-gray-300 hover:bg-gray-100 cursor-pointer"
                                            onclick="viewDocument({{ $document->id }})"
                                            data-status="{{ $document->status }}" data-type="{{ $document->type }}">
                                            <!-- Document tag with color coding -->
                                            <td class="px-4 py-2 font-semibold truncate max-w-[120px]">
                                                <span class="{{ $tagColor }}">{{ $document->control_tag }}</span>
                                            </td>
                                            <!-- Document title with tooltip for full text -->
                                            <td class="px-4 py-2 truncate max-w-[160px]" title="{{ $document->subject }}">
                                                {{ $document->subject }}
                                            </td>
                                            <!-- Date submitted -->
                                            <td class="px-4 py-2 truncate max-w-[120px]">
                                                {{ $createdDate }}
                                            </td>
                                            <!-- Document type with tooltip -->
                                            <td class="px-4 py-2 truncate max-w-[160px]" title="{{ $document->type }}">
                                                {{ $document->type }}
                                            </td>
                                            <!-- Status with color-coded badge -->
                                            <td class="px-4 py-2">
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
                                                class="px-4 py-8 text-center text-gray-500">
                                                <div class="flex flex-col items-center justify-center w-full h-full">
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
        @if(count($documents) > 0)
        <div class="mt-4 flex justify-center" id="paginationContainer">
            <nav>
                <ul class="inline-flex items-center space-x-2">
                    <!-- Previous page button -->
                    <li>
                        <a href="{{ $documents->previousPageUrl() }}"
                            class="pagination-btn-prev px-3 py-1 rounded-lg {{ $documents->currentPage() == 1 ? 'cursor-not-allowed opacity-50' : '' }}">
                            <
                                </a>
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
</div>

<script>
    // Track sort direction for each column
    let sortDirection = [true, true, true, true, true];
    
    document.addEventListener('DOMContentLoaded', function() {
        // Filter form elements
        const statusFilter = document.getElementById("statusFilter");
        const typeFilter = document.getElementById("typeFilter");
        const searchInput = document.querySelector('input[placeholder="Search..."]');
        
        // Handle filter changes - immediately apply server-side filtering
        function handleFilterChange() {
            applyServerSideFilters();
        }
        
        // Add event listeners to filters
        statusFilter.addEventListener("change", handleFilterChange);
        typeFilter.addEventListener("change", handleFilterChange);
        
        // For search, use debouncing
        let searchTimeout;
        searchInput.addEventListener("input", function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(handleFilterChange, 500); // Wait for 500ms before applying filter
        });
        
        // Handle pagination links (AJAX navigation)
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
                        const currentPagination = document.querySelector('#paginationContainer');
                        if (currentPagination) {
                            currentPagination.outerHTML = newPagination.outerHTML;
                        }
                    }
                    
                    // Update URL without reload
                    window.history.pushState({}, '', url);
                    
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

    /**
     * Apply server-side filters via AJAX
     */
    function applyServerSideFilters() {
        // Show loading state
        const tableContainer = document.querySelector('#tableContainer');
        tableContainer.classList.add('opacity-50');
        
        // Build the query parameters
        const params = new URLSearchParams();
        
        const statusFilter = document.getElementById("statusFilter").value;
        const typeFilter = document.getElementById("typeFilter").value;
        const searchInput = document.querySelector('input[placeholder="Search..."]').value;
        
        if (statusFilter !== 'Status') {
            params.append('status', statusFilter);
        }
        
        if (typeFilter !== 'Type') {
            params.append('type', typeFilter);
        }
        
        if (searchInput.trim() !== '') {
            params.append('search', searchInput);
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
            
            // Remove loading state
            tableContainer.classList.remove('opacity-50');
        })
        .catch(error => {
            console.error('Error applying filters:', error);
            tableContainer.classList.remove('opacity-50');
        });
    }

        /**
         * Navigate to document preview page for a specific document
         */
        function viewDocument(id) {
            window.location.href = "{{ route('student.documentPreview', ['id' => ':id']) }}".replace(':id', id);
        }

    // Server-side sorting functionality
    function sortTable(columnIndex) {
        const columnMap = [
            'control_tag',   // Tag - index 0
            'subject',       // Title - index 1
            'created_at',    // Date Submitted - index 2
            'type'           // Type - index 3
        ];

        const columnName = columnMap[columnIndex];
        if (!columnName) return;

        // Toggle sort direction
        sortDirection[columnIndex] = !sortDirection[columnIndex];
        const direction = sortDirection[columnIndex] ? 'asc' : 'desc';

        // Show loading state
        const tableContainer = document.querySelector('#tableContainer');
        tableContainer.classList.add('opacity-50');

        // Get current filter values to preserve them
        const statusFilter = document.getElementById("statusFilter").value;
        const typeFilter = document.getElementById("typeFilter").value;
        const searchInput = document.querySelector('input[placeholder="Search..."]').value;

        // Build query parameters
        const params = new URLSearchParams(window.location.search);

        // Add sorting parameters
        params.set('sort_by', columnName);
        params.set('sort_dir', direction);

        // Keep filter parameters
        if (statusFilter !== 'Status') {
            params.set('status', statusFilter);
        }

        if (typeFilter !== 'Type') {
            params.set('type', typeFilter);
        }

        if (searchInput.trim() !== '') {
            params.set('search', searchInput);
        }

        const url = `${window.location.pathname}?${params.toString()}`;

        // Fetch sorted results from server
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Update table content
            const newTableBody = doc.querySelector('#documentTable tbody');
            if (newTableBody) {
                document.querySelector('#documentTable tbody').innerHTML = newTableBody.innerHTML;
            }

            // Update pagination if needed
            const newPagination = doc.querySelector('#paginationContainer');
            const currentPagination = document.querySelector('#paginationContainer');

            if (newPagination && currentPagination) {
                currentPagination.outerHTML = newPagination.outerHTML;
            }

            // Update URL without page reload
            window.history.pushState({}, '', url);

            // Update sort indicator
            updateSortIndicator(columnIndex);

            // Remove loading state
            tableContainer.classList.remove('opacity-50');
        })
        .catch(error => {
            console.error('Error sorting table:', error);
            tableContainer.classList.remove('opacity-50');
        });
    }

    // Update the sort direction indicator
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
</script>
@endsection
