@extends('base')

@section('content')
@include('components.studentSidebarComponent')
<div id="main-content" class="flex flex-col min-h-screen ml-[20%] transition-all duration-300 bg-[#F2F4F7]">
    @include('components.studentNavBarComponent')
    <div class="flex-grow mb-10">
        <div class="w-full px-6 py-8 flex flex-col">
            <!-- Header section with title and archive page link -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-extrabold">Document Repository</h2>

                <!-- Link to archive page for viewing archived documents -->
                <a href="{{ route('student.archivePage') }}"
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

                <!-- Filter dropdowns -->
                <div class="flex flex-wrap items-center gap-4 justify-end">
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
                        <!-- Custom dropdown arrow icon -->
                        <img src="{{ asset('images/dropdownIcon.svg') }}" alt="Dropdown Icon"
                            class="absolute top-1/2 right-3 -translate-y-1/2 w-4 h-4 pointer-events-none" />
                    </div>
                </div>
            </div>

            @php
            // Standard document types - matching AdminDocumentController
            $standardTypes = [
                'Event Proposal',
                'General Plan of Activities',
                'Reports of Proceedings',
                'Constitution and By-Laws',
                'Fundraising Activities',
                'Request Letter',
                'Petition and Concern',
                'Memorandum of Agreement',
                'Off Campus Activities'
            ];

            // Assuming we get these values from auth/session - using ELITE as user's organization
            $userOrganization = 'ELITE'; // This should come from authenticated user's data

            // Organization mapping - only include user's organization
            $orgMap = [
                'ELITE' => 'Eligible League of Information Technology Enthusiasts',
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

                                // Format date for consistent display
                                $createdDate = \Carbon\Carbon::parse($document->created_at)->format('m/d/Y');
                                
                                // Determine display type - show 'Others' for non-standard types
                                $displayType = in_array($document->type, $standardTypes) ? $document->type : 'Others';
                                @endphp
                                <!-- Document row with data attributes for filtering -->
                                <tr class="border-b border-gray-300 hover:bg-gray-100 cursor-pointer"
                                    onclick="viewDocument({{ $document->id }})"
                                    data-type="{{ $document->type }}">
                                    <!-- Document tag with color coding -->
                                     <td class="px-4 py-2 font-semibold truncate max-w-[120px]">
                                        {{ $document->control_tag }}
                                    </td>
                                    <!-- Document title with tooltip for full text -->
                                    <td class="px-4 py-2 truncate max-w-[160px]" title="{{ $document->subject }}">
                                        {{ $document->subject }}
                                    </td>
                                    <!-- Date submitted -->
                                    <td class="px-4 py-2 truncate max-w-[120px]">
                                        {{ $createdDate }}
                                    </td>
                                    <!-- Document type - show 'Others' for non-standard types -->
                                    <td class="px-4 py-2 truncate max-w-[160px]" title="{{ $displayType }}">
                                        {{ $displayType }}
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
        </div>
    </div>
    @include('components.footer')
    
    <!-- Keep the existing JavaScript exactly the same -->
    <script>
        // Track sort direction for each column and current sort column
        let sortDirection = [true, true, true, true, true];
        let currentSortColumn = null;

        // Column mapping: index to database column name
        const columnMap = ['control_tag', 'subject', 'created_at', 'type', 'status'];

        document.addEventListener('DOMContentLoaded', function() {
            // Filter form elements
            const typeFilter = document.getElementById("typeFilter");
            const searchInput = document.querySelector('input[placeholder="Search..."]');

            // Handle filter changes - immediately apply server-side filtering
            function handleFilterChange() {
                applyServerSideFilters();
            }

            // Add event listeners to filters
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

                    fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(data.html, 'text/html');

                            const newTableBody = doc.querySelector('#documentTable tbody');
                            if (newTableBody) {
                                document.querySelector('#documentTable tbody').innerHTML = newTableBody.innerHTML;
                            }

                            const newPagination = doc.querySelector('#paginationContainer');
                            if (newPagination) {
                                document.querySelector('#paginationContainer').outerHTML = newPagination.outerHTML;
                            }

                            window.history.pushState({}, '', url);
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
         * Sort the table by the given column index
         * @param {number} columnIndex - The index of the column to sort by
         */
        function sortTable(columnIndex) {
            // Toggle sort direction for this column
            sortDirection[columnIndex] = !sortDirection[columnIndex];
            currentSortColumn = columnIndex;
            
            // Update sort indicators visually
            updateSortIndicator(columnIndex);
            
            // Apply filters with sorting
            applyServerSideFilters();
        }

        /**
         * Update the sort indicator visuals
         * @param {number} columnIndex - The column index being sorted
         */
        function updateSortIndicator(columnIndex) {
            // Get all sort icons in table headers
            const sortIcons = document.querySelectorAll('#documentTable thead button img');
            
            sortIcons.forEach((icon, i) => {
                if (i === columnIndex) {
                    // Update active sort icon
                    icon.style.transform = sortDirection[i] ? 'rotate(0deg)' : 'rotate(180deg)';
                    icon.style.opacity = '1';
                } else {
                    // Reset other icons
                    icon.style.transform = 'rotate(0deg)';
                    icon.style.opacity = '0.5';
                }
            });
        }

        /**
         * Apply server-side filters via AJAX
         */
        function applyServerSideFilters() {
            const tableContainer = document.querySelector('#tableContainer');
            tableContainer.classList.add('opacity-50');

            const params = new URLSearchParams();
            const typeFilter = document.getElementById("typeFilter").value;
            const searchInput = document.querySelector('input[placeholder="Search..."]').value;

            if (typeFilter !== 'Type' && typeFilter !== 'All') {
                params.append('type', typeFilter);
            }

            if (searchInput.trim() !== '') {
                params.append('search', searchInput);
            }

            // Add sorting parameters if a column is selected for sorting
            if (currentSortColumn !== null && columnMap[currentSortColumn]) {
                params.append('sort_by', columnMap[currentSortColumn]);
                params.append('sort_dir', sortDirection[currentSortColumn] ? 'asc' : 'desc');
            }

            const url = `${window.location.pathname}?${params.toString()}`;

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Parse the HTML response
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(data.html, 'text/html');

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
                        currentPagination.style.display = 'none';
                    }
                    
                    // Update URL without reload for bookmarking
                    window.history.pushState({}, '', url);
                    tableContainer.classList.remove('opacity-50');
                })
                .catch(error => {
                    console.error('Error applying filters:', error);
                    tableContainer.classList.remove('opacity-50');
                });
        }

        /**
         * Navigate to document preview page when a row is clicked
         * @param {number} id - Document ID to preview
         */
        function viewDocument(id) {
            // Use the correct route for document preview
            window.location.href = "{{ route('student.documentPreview', ['id' => ':id']) }}".replace(':id', id);
        }

        // Function to update type filter options
        function updateTypeFilterOptions(availableTypes) {
            const typeSelect = document.getElementById('typeFilter');
            if (!typeSelect) return;
            
            const currentValue = typeSelect.value;
            
            // Get all type options except the first two (Type and All)
            const typeOptions = typeSelect.querySelectorAll('option');
            typeOptions.forEach((option, index) => {
                const value = option.value;
                // Keep "Type" and "All" options always visible
                if (index < 2) {
                    option.style.display = 'block';
                } else {
                    // Show/hide other options based on availability
                    const shouldShow = availableTypes && availableTypes.includes(value);
                    option.style.display = shouldShow ? 'block' : 'none';
                }
            });
            
            // Reset to default if current selection is no longer available
            if (currentValue !== 'Type' && currentValue !== 'All') {
                if (!availableTypes || !availableTypes.includes(currentValue)) {
                    typeSelect.value = 'Type';
                }
            }
        }
    </script>
@endsection