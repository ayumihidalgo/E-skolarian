@extends('base')

@section('content')
@include('components.adminNavBarComponent')
@include('components.adminSidebarComponent')

<div id="main-content" class="transition-all duration-300 ml-[20%]">
    <div class="w-full min-h-screen bg-[#f2f4f7] px-6 py-8 flex flex-col">
        <!-- Header section with title and history page link -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-extrabold">Document Archive Table</h2>

            <!-- Link back to document history page -->
            <a href="{{ route('admin.documentHistory') }}" class="text-[#7A1212] underline font-medium hover:text-[#DAA520] transition-colors duration-200">
                Return to History
            </a>
        </div>

        <!-- Search and filter controls section -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
            <!-- Search input field with magnifier icon -->
            <div class="flex-1 min-w-[200px] relative">
                <input type="text" placeholder="Search..."
                    class="border border-[#9099A5] px-4 py-2 pr-10 rounded-full w-full bg-white">
                <img src="{{ asset('images/Magnifier.svg') }}" alt="Search"
                    class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 pointer-events-none" />
            </div>

            <!-- Restore selected documents button -->
            <button id="restoreSelectedBtn"
                class="px-4 py-2 bg-[#7A1212] text-white rounded-full hover:bg-[#DAA520] transition-colors duration-200 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                disabled>
                Restore (<span id="selectedCount">0</span>)
            </button>

            <!-- Organization filter dropdown -->
            <div class="relative w-40">
                <select id="organizationFilter"
                    class="appearance-none border px-4 py-2 rounded-full bg-[#7A1212] text-white w-full pr-8 hover:bg-[#DAA520] hover:text-white transition-colors duration-200">
                    <option class="bg-white text-black" value="Organization" disabled selected>Organization</option>
                    <option class="bg-white text-black" value="All">All Organizations</option>
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
                <!-- Custom dropdown arrow icon -->
                <img src="{{ asset('images/dropdownIcon.svg') }}" alt="Dropdown Icon"
                    class="absolute top-1/2 right-3 -translate-y-1/2 w-4 h-4 pointer-events-none" />
            </div>

            <!-- Document type filter dropdown -->
            <div class="relative w-40">
                <select id="typeFilter"
                    class="appearance-none border px-4 py-2 rounded-full bg-[#7A1212] text-white w-full pr-8 hover:bg-[#DAA520] hover:text-white transition-colors duration-200 truncate">
                    <option class="bg-white text-black truncate" value="Type" disabled selected>Type</option>
                    <option class="bg-white text-black truncate" value="All">All Types</option>
                    <option class="bg-white text-black truncate" value="Event Proposal">Event Proposal</option>
                    <option class="bg-white text-black truncate" value="General Plan of Activities">General Plan of Activities</option>
                    <option class="bg-white text-black truncate" value="Calendar of Activities">Calendar of Activities</option>
                    <option class="bg-white text-black truncate" value="Accomplishment Report">Accomplishment Report</option>
                    <option class="bg-white text-black truncate" value="Constitution and By-Laws">Contribution and By-Laws</option>
                    <option class="bg-white text-black truncate" value="Request Letter">Request Letter</option>
                    <option class="bg-white text-black truncate" value="Off-Campus">Off-Campus</option>
                    <option class="bg-white text-black truncate" value="Petition and Concern">Petition and Concern</option>
                </select>
                <!-- Custom dropdown arrow icon -->
                <img src="{{ asset('images/dropdownIcon.svg') }}" alt="Dropdown Icon"
                    class="absolute top-1/2 right-3 -translate-y-1/2 w-4 h-4 pointer-events-none" />
            </div>
        </div>

        <!-- Main table container for displaying archived documents -->
        <div id="tableContainer" class="bg-white rounded-[24px] shadow-md overflow-hidden p-6">
            <div class="h-auto">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto" id="documentTable">
                        <!-- Table header with sortable columns -->
                        <thead class="bg-white text-left">
                            <tr>
                                <!-- Select all checkbox column -->
                                <th class="px-4 py-2 whitespace-nowrap">
                                    <input type="checkbox" id="selectAll" class="w-4 h-4 cursor-pointer">
                                </th>
                                <!-- Define column headers array -->
                                @php $headers = ['Tag', 'Organization', 'Title', 'Date Archived', 'Type']; @endphp
                                @foreach ($headers as $i => $header)
                                <th class="px-4 py-2 whitespace-nowrap max-w-[160px] truncate">
                                    <!-- Sortable column headers with icons -->
                                    <button onclick="sortTable({{ $i + 1 }})"
                                        class="flex items-center gap-1 group">
                                        <span>{{ $header }}</span>
                                        <img src="{{ asset('images/sortIcon.svg') }}" alt="Sort"
                                            class="w-3 h-3 text-gray-500 group-hover:text-black transition">
                                    </button>
                                </th>
                                @endforeach
                                <!-- Remove the Action buttons column header -->
                            </tr>
                        </thead>
                        <!-- Table body - empty state shown when no archived documents exist -->
                        <tbody>
                            @forelse ($documents as $document)
                            @php
                            // Extract organization acronym from control tag
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

                            // Format archive date for display
                            $archivedDate = \Carbon\Carbon::parse($document->archived_at)->format('m/d/Y');
                            @endphp
                            <!-- Document row with data attributes for filtering -->
                            <tr class="border-b border-gray-300 hover:bg-gray-100"
                                data-org-acronym="{{ $acronym }}"
                                data-type="{{ $document->type }}"
                                data-id="{{ $document->id }}">
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
                                <!-- Date archived -->
                                <td class="px-4 py-2 truncate max-w-[120px] cursor-pointer"
                                    onclick="viewDocument({{ $document->id }})">
                                    {{ $archivedDate }}
                                </td>
                                <!-- Document type with tooltip -->
                                <td class="px-4 py-2 truncate max-w-[160px] cursor-pointer"
                                    onclick="viewDocument({{ $document->id }})"
                                    title="{{ $document->type }}">
                                    {{ $document->type }}
                                </td>
                            </tr>
                            @empty
                            <!-- Empty state when no documents are found -->
                            <tr>
                                <td colspan="{{ count($headers) + 1 }}" class="px-4 py-8 text-center text-gray-500">
                                    No archived documents found.
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

<!-- Restore document confirmation modal -->
<div id="restoreConfirmationModal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background-color: rgba(0,0,0,0.3);">
    <div class="bg-white w-[30rem] rounded-2xl shadow-xl p-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-semibold text-gray-800">Restore Document Confirmation</h3>
            <button id="closeRestoreModalBtn" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <p class="text-sm text-gray-600 mb-6">
            Are you sure you want to restore this document? Once restored, it will reappear in your history list and be accessible alongside your active documents.
        </p>

        <div class="flex justify-end space-x-3">
            <button id="cancelRestoreBtn" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                Cancel
            </button>
            <button id="confirmRestoreBtn" class="px-4 py-2 bg-[#7A1212] text-white rounded-md hover:bg-[#DAA520] cursor-pointer">
                Restore
            </button>
        </div>
    </div>
</div>

<script>
    // Configuration constants
    let selectedItems = new Set(); // Set to track selected document IDs

    /**
     * Updates the selected document count and button state
     */
    function updateSelectedCount() {
        const count = selectedItems.size;
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('restoreSelectedBtn').disabled = count === 0;
    }

    /**
     * Apply filters to the document table
     */
    function applyFilters() {
        const searchTerm = document.querySelector('input[placeholder="Search..."]').value.toLowerCase();
        const organizationFilter = document.getElementById("organizationFilter").value;
        const typeFilter = document.getElementById("typeFilter").value;

        const rows = document.querySelectorAll("#documentTable tbody tr");

        rows.forEach(row => {
            // Skip the "No documents found" row
            if (!row.getAttribute('data-id')) return;

            const orgAcronym = row.getAttribute('data-org-acronym');
            const docType = row.getAttribute('data-type');
            const rowText = row.textContent.toLowerCase();

            const matchesSearch = searchTerm === '' || rowText.includes(searchTerm);
            const matchesOrg = organizationFilter === 'All' || organizationFilter === 'Organization' || orgAcronym === organizationFilter;
            const matchesType = typeFilter === 'All' || typeFilter === 'Type' || docType === typeFilter;

            row.style.display = (matchesSearch && matchesOrg && matchesType) ? '' : 'none';
        });
    }

    /**
     * Sort table by column index
     */
    function sortTable(columnIndex) {
        const table = document.getElementById("documentTable");
        const tbody = table.querySelector("tbody");
        const rows = Array.from(tbody.querySelectorAll("tr[data-id]"));

        // Toggle sort direction for this column
        sortDirection[columnIndex] = !sortDirection[columnIndex];
        const direction = sortDirection[columnIndex] ? 1 : -1;

        // Sort the rows
        rows.sort((a, b) => {
            const cellA = a.querySelectorAll("td")[columnIndex].textContent.trim();
            const cellB = b.querySelectorAll("td")[columnIndex].textContent.trim();

            if (isNaN(cellA) || isNaN(cellB)) {
                return direction * cellA.localeCompare(cellB);
            } else {
                return direction * (parseFloat(cellA) - parseFloat(cellB));
            }
        });

        // Remove all rows
        rows.forEach(row => row.remove());

        // Add sorted rows back to table
        rows.forEach(row => tbody.appendChild(row));
    }

    /**
     * Navigate to document preview page
     */
    function viewDocument(id) {
        window.location.href = "{{ route('admin.documentPreview', ['id' => ':id']) }}".replace(':id', id);
    }

    /**
     * Restores selected documents from archive - ONLY called from the confirmation modal
     */
    function processRestore() {
        if (selectedItems.size === 0) return;

        const documentIds = Array.from(selectedItems);

        fetch("{{ route('admin.restoreDocuments') }}", {
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
                    alert(data.message || 'Failed to restore documents.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while restoring documents.');
            });
    }

    /**
     * Show the restore confirmation modal
     */
    function showRestoreConfirmation() {
        if (selectedItems.size > 0) {
            document.getElementById("restoreConfirmationModal").classList.remove("hidden");
        }
    }

    // Track sort direction for each column
    let sortDirection = [true, true, true, true, true, true];

    // Event delegation for document interactions
    document.addEventListener('click', function(e) {
        // Handle checkboxes through delegation instead of direct binding
        if (e.target.id === 'selectAll') {
            const checkboxes = document.querySelectorAll('.row-checkbox');
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
        } else if (e.target.classList.contains('row-checkbox')) {
            const id = e.target.getAttribute('data-id');
            if (e.target.checked) {
                selectedItems.add(id);
            } else {
                selectedItems.delete(id);
            }
            updateSelectedCount();
        }
    });

    // Execute when the DOM is fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners for restore button
        document.getElementById('restoreSelectedBtn').addEventListener('click', showRestoreConfirmation);

        // Add event listeners for filters
        document.getElementById("organizationFilter").addEventListener("change", applyFilters);
        document.getElementById("typeFilter").addEventListener("change", applyFilters);

        // For search, use debouncing to avoid too many requests
        const searchInput = document.querySelector('input[placeholder="Search..."]');
        let searchTimeout;
        searchInput.addEventListener("input", function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 500);
        });

        // Modal control event listeners
        document.getElementById("closeRestoreModalBtn").addEventListener("click", function() {
            document.getElementById("restoreConfirmationModal").classList.add("hidden");
        });

        document.getElementById("cancelRestoreBtn").addEventListener("click", function() {
            document.getElementById("restoreConfirmationModal").classList.add("hidden");
        });

        // ONLY THIS BUTTON SHOULD TRIGGER THE ACTUAL RESTORATION
        document.getElementById("confirmRestoreBtn").addEventListener("click", function() {
            processRestore(); // Call the function that actually does the restoring
            document.getElementById("restoreConfirmationModal").classList.add("hidden");
        });

        // Initialize selected count
        updateSelectedCount();
    });
</script>
@endsection