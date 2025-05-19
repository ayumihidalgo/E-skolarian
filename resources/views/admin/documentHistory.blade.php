@extends('base')

@section('content')
@include('components.adminNavBarComponent')
@include('components.adminSidebarComponent')

<div id="main-content" class="transition-all duration-300 ml-[20%]">
    <div class="w-full min-h-screen bg-[#f2f4f7] px-6 py-8 flex flex-col">
        <!-- Header section with title and archive page link -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-extrabold">Document History Table</h2>

            <!-- Link to archive page for viewing archived documents -->
            <a href="{{ route('admin.archivePage') }}" class="text-[#7A1212] underline font-medium hover:text-[#DAA520] transition-colors duration-200">
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
                        <option class="bg-white text-black" value="Organization" disabled selected>Organization</option>
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
                        <option class="bg-white text-black truncate" value="General Plan of Activities">General Plan of
                            Activities</option>
                        <option class="bg-white text-black truncate" value="Calendar of Activities">Calendar of
                            Activities
                        </option>
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
                                @php $headers = ['Tag', 'Organization', 'Subject', 'Date Submitted', 'Type', 'Status']; @endphp
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
                            $tagColor = isset($tagColors[$colorKey]) ? $tagColors[$colorKey] : 'text-gray-500';

                            // Format date for consistent display
                            $createdDate = \Carbon\Carbon::parse($document->created_at)->format('m/d/Y');
                            @endphp
                            <!-- Document row with data attributes for filtering -->
                            <tr class="border-b border-gray-300 hover:bg-gray-100"
                                data-org-acronym="{{ $acronym }}" data-status="{{ $document->status }}"
                                data-type="{{ $document->type }}" data-id="{{ $document->id }}">
                                <!-- Checkbox for row selection -->
                                <td class="px-4 py-2">
                                    <input type="checkbox" class="row-checkbox w-4 h-4 cursor-pointer" data-id="{{ $document->id }}">
                                </td>
                                <!-- Document tag with color coding -->
                                <td class="px-4 py-2 font-semibold truncate max-w-[120px] cursor-pointer"
                                    onclick="viewDocument({{ $document->id }})">
                                    <span class="{{ $tagColor }}">{{ $document->control_tag }}</span>
                                </td>
                                <!-- Organization name with tooltip for full name -->
                                <td class="px-4 py-2 truncate max-w-[160px] cursor-pointer"
                                    onclick="viewDocument({{ $document->id }})"
                                    title="{{ $orgName }}">
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
                                <td colspan="{{ count($headers) + 1 }}" class="px-4 py-8 text-center text-gray-500">
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

    <!-- Archive Documents Confirmation Modal -->
    <div id="archiveConfirmationModal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background-color: rgba(0,0,0,0.3);">
        <div class="bg-white w-[30rem] rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-semibold text-gray-800">Archive Document Confirmation</h3>
                <button id="closeArchiveModalBtn" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <p class="text-sm text-gray-600 mb-6">
                Are you sure you want to archive this document? Once archived, it will be removed from your history list and will no longer be visible there.
            </p>

            <div class="flex justify-end space-x-3">
                <button id="cancelArchiveBtn" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                    Cancel
                </button>
                <button id="confirmArchiveBtn" class="px-4 py-2 bg-[#7A1212] text-white rounded-md hover:bg-[#DAA520] cursor-pointer">
                     Archive
                </button>
            </div>
        </div>
    </div>

    <script>
        // Configuration constants
        let selectedItems = new Set(); // Set to track selected document IDs

        /**
         * Applies all active filters to the table data in real-time without page reload
         */
        function applyFilters() {
            // Get filter values
            const statusFilter = document.getElementById("statusFilter").value;
            const organizationFilter = document.getElementById("organizationFilter").value;
            const typeFilter = document.getElementById("typeFilter").value;
            const searchTerm = document.querySelector('input[placeholder="Search..."]').value.toLowerCase();

            // Get all rows in the table
            const rows = document.querySelectorAll("#documentTable tbody tr[data-id]");
            
            // Loop through each row and apply filters
            rows.forEach(row => {
                const orgAcronym = row.getAttribute('data-org-acronym');
                const docType = row.getAttribute('data-type');
                const docStatus = row.getAttribute('data-status');
                const rowText = row.textContent.toLowerCase();
                
                // Check if row matches all filters
                const matchesSearch = searchTerm === '' || rowText.includes(searchTerm);
                const matchesOrg = organizationFilter === 'Organization' || organizationFilter === 'All' || orgAcronym === organizationFilter;
                const matchesType = typeFilter === 'Type' || typeFilter === 'All' || docType === typeFilter;
                const matchesStatus = statusFilter === 'Status' || statusFilter === 'All' || docStatus === statusFilter;
                
                // Show or hide the row based on filter matches
                row.style.display = (matchesSearch && matchesOrg && matchesType && matchesStatus) ? '' : 'none';
            });
            
            // Update counts and UI
            updateSelectedCount();
            
            // Hide the "No documents found" row if needed
            const noDocRow = document.querySelector("#documentTable tbody tr:not([data-id])");
            if (noDocRow) {
                const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
                noDocRow.style.display = visibleRows.length === 0 ? '' : 'none';
            }
        }

        // Add delayed event listeners for filter controls to avoid immediate reloading
        document.getElementById("statusFilter").addEventListener("change", applyFilters);
        document.getElementById("organizationFilter").addEventListener("change", applyFilters);
        document.getElementById("typeFilter").addEventListener("change", applyFilters);

        // For search, use debouncing to avoid too many requests
        const searchInput = document.querySelector('input[placeholder="Search..."]');
        let searchTimeout;
        searchInput.addEventListener("input", function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 500);
        });

        /**
         * Updates the selected document count and button state
         */
        function updateSelectedCount() {
            const count = selectedItems.size;
            document.getElementById('selectedCount').textContent = count;
            document.getElementById('archiveSelectedBtn').disabled = count === 0;
        }

        /**
         * Archives the selected documents - ONLY called from the confirmation modal
         */
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
            .then(data => {
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

        /**
         * Navigate to document preview page for a specific document
         */
        function viewDocument(id) {
            window.location.href = "{{ route('admin.documentPreview', ['id' => ':id']) }}".replace(':id', id);
        }

        // Event delegation for checkbox and button clicks
        document.addEventListener('click', function(e) {
            // Handle "Select All" checkbox
            if (e.target.id === 'selectAll') {
                const checkboxes = document.querySelectorAll('.row-checkbox');

                // Check/uncheck all checkboxes
                checkboxes.forEach(checkbox => {
                    checkbox.checked = e.target.checked;

                    const id = checkbox.getAttribute('data-id');
                    if (e.target.checked) {
                        selectedItems.add(id);
                    } else {
                        selectedItems.delete(id);
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
        document.getElementById("archiveSelectedBtn").addEventListener("click", function() {
            if (selectedItems.size > 0) {
                document.getElementById("archiveConfirmationModal").classList.remove("hidden");
            }
        });

        // ONLY THIS BUTTON SHOULD TRIGGER THE ACTUAL ARCHIVING
        document.getElementById("confirmArchiveBtn").addEventListener("click", function() {
            processArchiving(); // Call the function that actually does the archiving
            document.getElementById("archiveConfirmationModal").classList.add("hidden");
        });
    </script>
</div>
@endsection