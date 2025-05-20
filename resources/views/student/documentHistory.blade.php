@extends('base')

@section('content')
    @include('components.studentNavBarComponent')
    @include('components.studentSidebarComponent')

    <div id="main-content" class="transition-all duration-300 ml-[20%]">
        <div class="w-full min-h-screen bg-[#f2f4f7] px-6 py-8 flex flex-col">
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
                            <option class="bg-white text-black truncate" value="General Plan of Activities">General Plan of
                                Activities</option>
                            <option class="bg-white text-black truncate" value="Calendar of Activities">Calendar of
                                Activities</option>
                            <option class="bg-white text-black truncate" value="Accomplishment Report">Accomplishment Report
                            </option>
                            <option class="bg-white text-black truncate" value="Contribution and By-Laws">Contribution and
                                By-Laws</option>
                            <option class="bg-white text-black truncate" value="Request Letter">Request Letter</option>
                            <option class="bg-white text-black truncate" value="Off-Campus">Off-Campus</option>
                            <option class="bg-white text-black truncate" value="Petition and Concern">Petition and Concern
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
                                        $createdDate = \Carbon\Carbon::parse($document->created_at)->format('m/d/Y');
                                    @endphp
                                    <!-- Document row with data attributes for filtering -->
                                    <tr class="border-b border-gray-300 hover:bg-gray-100 cursor-pointer"
                                        onclick="viewDocument({{ $document->id }})"
                                        data-status="{{ $document->status }}" 
                                        data-type="{{ $document->type }}">
                                        <!-- Document tag with color coding -->
                                        <td class="px-4 py-2 font-semibold truncate max-w-[120px]">
                                            <span class="{{ $tagColor }}">{{ $document->control_tag }}</span>
                                        </td>
                                        <!-- Document title with tooltip for full text -->
                                        <td class="px-4 py-2 truncate max-w-[160px]" 
                                            title="{{ $document->subject }}">
                                            {{ $document->subject }}
                                        </td>
                                        <!-- Date submitted -->
                                        <td class="px-4 py-2 truncate max-w-[120px]">
                                            {{ $createdDate }}
                                        </td>
                                        <!-- Document type with tooltip -->
                                        <td class="px-4 py-2 truncate max-w-[160px]" 
                                            title="{{ $document->type }}">
                                            {{ $document->type }}
                                        </td>
                                        <!-- Status with color-coded badge -->
                                        <td class="px-4 py-2">
                                            <span class="px-4 py-1 rounded-full text-white inline-block min-w-[100px] text-center 
                                                    {{ $document->status === 'Approved' ? 'bg-[#10B981]' : 
                                                    ($document->status === 'Rejected' ? 'bg-[#EF4444]' : 'bg-[#F59E0B]') }}">
                                                {{ $document->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <!-- Empty state when no documents are found -->
                                    <tr>
                                        <td colspan="{{ count($headers) }}" class="px-4 py-8 text-center text-gray-500">
                                            No documents found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination controls -->
            <div class="mt-4 flex justify-center">
                <nav>
                    <ul class="inline-flex items-center space-x-2">
                        <li>
                            <a href="{{ $documents->url(1) }}"
                                class="pagination-btn-first px-3 py-1 rounded-lg {{ $documents->currentPage() == 1 ? 'cursor-not-allowed opacity-50' : '' }}">
                                First
                            </a>
                        </li>

                        @for ($i = 1; $i <= $documents->lastPage(); $i++)
                            <li>
                                <a href="{{ $documents->url($i) }}"
                                    class="pagination-btn px-3 py-1 rounded-lg {{ $documents->currentPage() == $i ? 'bg-[#7A1212] text-white' : '' }}">
                                    {{ $i }}
                                </a>
                            </li>
                        @endfor

                        <li>
                            <a href="{{ $documents->url($documents->lastPage()) }}"
                                class="pagination-btn-last px-3 py-1 rounded-lg {{ $documents->currentPage() == $documents->lastPage() ? 'cursor-not-allowed opacity-50' : '' }}">
                                Last
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <script>
        /**
         * Applies all active filters to the table data in real-time without page reload
         */
        function applyFilters() {
            // Get filter values
            const statusFilter = document.getElementById("statusFilter").value;
            const typeFilter = document.getElementById("typeFilter").value;
            const searchTerm = document.querySelector('input[placeholder="Search..."]').value.toLowerCase();

            // Get all rows in the table
            const rows = document.querySelectorAll("#documentTable tbody tr[data-type]");
            
            // Loop through each row and apply filters
            rows.forEach(row => {
                const docType = row.getAttribute('data-type');
                const docStatus = row.getAttribute('data-status');
                const rowText = row.textContent.toLowerCase();
                
                // Check if row matches all filters
                const matchesSearch = searchTerm === '' || rowText.includes(searchTerm);
                const matchesType = typeFilter === 'Type' || typeFilter === 'All' || docType === typeFilter;
                const matchesStatus = statusFilter === 'Status' || statusFilter === 'All' || docStatus === statusFilter;
                
                // Show or hide the row based on filter matches
                row.style.display = (matchesSearch && matchesType && matchesStatus) ? '' : 'none';
            });
            
            // Hide the "No documents found" row if needed
            const noDocRow = document.querySelector("#documentTable tbody tr:not([data-type])");
            if (noDocRow) {
                const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
                noDocRow.style.display = visibleRows.length === 0 ? '' : 'none';
            }
        }

        // Add event listeners for filter controls
        document.getElementById("statusFilter").addEventListener("change", applyFilters);
        document.getElementById("typeFilter").addEventListener("change", applyFilters);

        // For search, use debouncing to avoid too many filtering operations
        const searchInput = document.querySelector('input[placeholder="Search..."]');
        let searchTimeout;
        searchInput.addEventListener("input", function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 300);
        });

        // Track sort direction for each column
        let sortDirection = [true, true, true, true, true];

        /**
         * Sorts the table based on the selected column
         */
        function sortTable(columnIndex) {
            const table = document.getElementById("documentTable");
            const tbody = table.tBodies[0];
            const rows = Array.from(tbody.rows).filter(row => row.hasAttribute('data-type'));

            // Don't sort if there are no data rows
            if (rows.length <= 1) return;

            rows.sort((a, b) => {
                let valA = a.cells[columnIndex].textContent.trim().toLowerCase();
                let valB = b.cells[columnIndex].textContent.trim().toLowerCase();

                // Special handling for date column (index 2)
                if (columnIndex === 2) {
                    // Convert date strings to Date objects for proper comparison
                    const dateA = new Date(valA);
                    const dateB = new Date(valB);
                    return sortDirection[columnIndex] ? dateA - dateB : dateB - dateA;
                }

                // Normal string comparison for other columns
                if (sortDirection[columnIndex]) {
                    return valA.localeCompare(valB);
                } else {
                    return valB.localeCompare(valA);
                }
            });

            // Toggle sort direction for next click
            sortDirection[columnIndex] = !sortDirection[columnIndex];
            
            // Reattach sorted rows to table
            rows.forEach(row => tbody.appendChild(row));
            
            // Keep the "no documents" row at the bottom if it exists
            const noDocRow = Array.from(table.tBodies[0].rows).find(row => !row.hasAttribute('data-type'));
            if (noDocRow) tbody.appendChild(noDocRow);
        }

        /**
         * Navigate to document preview page for a specific document
         */
        function viewDocument(id) {
            window.location.href = "{{ route('student.documentPreview', ['id' => ':id']) }}".replace(':id', id);
        }

        // Initialize filters on page load
        document.addEventListener("DOMContentLoaded", applyFilters);
    </script>
@endsection