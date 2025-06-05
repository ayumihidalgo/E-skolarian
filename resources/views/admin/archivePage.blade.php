@extends('base')

@section('content')
@include('components.adminSidebarComponent')

<div id="main-content" class="flex flex-col min-h-screen ml-[20%] transition-all duration-300 bg-[#F2F4F7]">
    @include('components.adminNavBarComponent')
    <div class="w-full">
        <div class="w-full min-h-screen bg-[#f2f4f7] px-6 py-8 flex flex-col">
            <!-- Header section with title and history page link -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-extrabold">Document Archive Table</h2>

                <!-- Link back to document history page -->
                <a href="{{ route('admin.documentHistory') }}"
                    class="text-[#7A1212] underline font-medium hover:text-[#DAA520] transition-colors duration-200">
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
                        <option class="bg-white text-black truncate" value="General Plan of Activities">General Plan of
                            Activities</option>
                        <option class="bg-white text-black truncate" value="Calendar of Activities">Calendar of
                            Activities</option>
                        <option class="bg-white text-black truncate" value="Accomplishment Report">Accomplishment Report
                        </option>
                        <option class="bg-white text-black truncate" value="Constitution and By-Laws">Contribution and
                            By-Laws</option>
                        <option class="bg-white text-black truncate" value="Request Letter">Request Letter</option>
                        <option class="bg-white text-black truncate" value="Off-Campus">Off-Campus</option>
                        <option class="bg-white text-black truncate" value="Petition and Concern">Petition and Concern
                        </option>
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
                                    @php $headers = ['Tag', 'Organization', 'Title', 'Date Archived', 'Type', 'Status']; @endphp
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
                                $tagColor = isset($tagColors[$colorKey])
                                ? $tagColors[$colorKey]
                                : 'text-gray-500';

                                // Format archive date for display
                                $archivedDate = \Carbon\Carbon::parse($document->archived_at)->format('m/d/Y');
                                @endphp
                                <!-- Document row with data attributes for filtering -->
                                <tr class="border-b border-gray-300 hover:bg-gray-100"
                                    data-org-acronym="{{ $acronym }}"
                                    data-type="{{ $document->type }}"
                                    data-status="{{ $document->status }}"
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
                                    <td colspan="{{ count($headers) }}" class="px-4 py-8 text-center text-gray-500 align-middle">
                                        <div class="flex flex-col items-center justify-center min-h-[300px] pl-36">
                                            <img src="{{ asset('images/viewNoFileFound.svg') }}" alt="No documents found" class="mb-4 w-40 h-40" />
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
    </div>
    @include('components.footer')
</div>
<!-- Restore document confirmation modal -->
<div id="restoreConfirmationModal" class="hidden fixed inset-0 z-50 flex items-center justify-center"
    style="background-color: rgba(0,0,0,0.3);">
    <div class="bg-white w-[30rem] rounded-2xl shadow-xl p-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-semibold text-gray-800">Restore Document Confirmation</h3>
            <button id="closeRestoreModalBtn" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <p class="text-sm text-gray-600 mb-6">
            Are you sure you want to restore this document? Once restored, it will reappear in your history list and be
            accessible alongside your active documents.
        </p>

        <div class="flex justify-end space-x-3">
            <button id="cancelRestoreBtn"
                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                Cancel
            </button>
            <button id="confirmRestoreBtn"
                class="px-4 py-2 bg-[#7A1212] text-white rounded-md hover:bg-[#DAA520] cursor-pointer">
                Restore
            </button>
        </div>
    </div>
</div>

<!-- Document Action Toast -->
<div id="documentActionToast" class="hidden fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white border-l-4 border-gray-400 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50">
    <div>
        <img src="{{ asset('images/successful.svg') }}" alt="Action Icon" id="actionIcon" class="">
    </div>
    <div class="flex-1">
        <p class="font-semibold" id="actionTitle">Document Action</p>
        <p id="actionMessage" class="text-sm">Action performed on document.</p>
    </div>
    <button type="button" onclick="hideActionToast()" class="text-gray-500 hover:text-gray-700 text-2xl leading-none cursor-pointer self-center">&times;</button>
</div>

<script>
    // Keep track of which documents are checked
    let selectedItems = new Set();

    // Update the counter showing how many documents are selected
    function updateSelectedCount() {
        const count = selectedItems.size;
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('restoreSelectedBtn').disabled = count === 0;
    }

    // Toast functionality - ADDED FROM documentHistory.blade.php
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

    // Actually restore the selected documents - UPDATED WITH TOAST
    function processRestore() {
        if (selectedItems.size === 0) return;

        const documentIds = Array.from(selectedItems);
        const count = documentIds.length;

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
                    // Show success toast
                    showActionToast(
                        'Restore Successful',
                        `Successfully restored ${count} document${count > 1 ? 's' : ''}.`,
                        true
                    );

                    // Remove restored rows with smooth animation
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

                    // Clear selections and update count
                    selectedItems.clear();
                    updateSelectedCount();

                    // Wait for animations to complete, then refresh if needed
                    setTimeout(() => {
                        // Check if there are any remaining documents
                        const remainingRows = document.querySelectorAll("#documentTable tbody tr[data-id]").length;
                        if (remainingRows === 0) {
                            // If no documents left, reload the page to show empty state properly
                            window.location.reload();
                        }
                    }, 600);

                } else {
                    // Show error toast
                    showActionToast(
                        'Restore Failed',
                        data.message || 'Failed to restore documents.',
                        false
                    );
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Show error toast
                showActionToast(
                    'Restore Error',
                    'An error occurred while restoring documents.',
                    false
                );
            });
    }

    // Get filtered results from the server
    function applyServerSideFilters() {
        // Show loading effect
        const tableContainer = document.querySelector('#tableContainer');
        tableContainer.classList.add('opacity-50');
        
        // Clear any previous selections
        selectedItems.clear();
        updateSelectedCount();
        
        // Build the filter query
        const params = new URLSearchParams();
        
        // Get what the user has entered in the filters
        const searchTerm = document.querySelector('input[placeholder="Search..."]').value.trim();
        const organizationFilter = document.getElementById("organizationFilter").value;
        const typeFilter = document.getElementById("typeFilter").value;
        
        // Only include filters that are actually set
        if (organizationFilter !== 'Organization') {
            params.append('organization', organizationFilter);
        }
        
        if (typeFilter !== 'Type') {
            params.append('type', typeFilter);
        }
        
        if (searchTerm) {
            params.append('search', searchTerm);
        }
        
        const url = `${window.location.pathname}?${params.toString()}`;
        
        // Ask the server for filtered results
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Process the server's response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Update the table with new data
            const newTableBody = doc.querySelector('#documentTable tbody');
            if (newTableBody) {
                document.querySelector('#documentTable tbody').innerHTML = newTableBody.innerHTML;
            }
            
            // Update the page numbers
            const newPagination = doc.querySelector('#paginationContainer');
            const currentPagination = document.querySelector('#paginationContainer');
            
            if (newPagination) {
                if (currentPagination) {
                    currentPagination.outerHTML = newPagination.outerHTML;
                }
            } else {
                const currentPagination = document.querySelector('#paginationContainer');
                if (currentPagination) {
                    currentPagination.style.display = 'none';
                }
            }
            
            // Update the URL so bookmarks work
            window.history.pushState({}, '', url);
            
            // Handle the "select all" checkbox state
            const hasDocuments = document.querySelectorAll("#documentTable tbody tr[data-id]").length > 0;
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.disabled = !hasDocuments;
                if (!hasDocuments) {
                    selectAllCheckbox.checked = false;
                }
            }
            
            // Remove the loading effect
            tableContainer.classList.remove('opacity-50');
        })
        .catch(error => {
            console.error('Error applying filters:', error);
            tableContainer.classList.remove('opacity-50');
        });
    }

    // Sort the table when a column header is clicked
    function sortTable(columnIndex) {
        const columnMap = [
            'control_tag', // Tag column
            'organization', // Organization column
            'subject', // Title column
            'archived_at', // Date column
            'type', // Document type column
            'status' // Status column
        ];

        const columnName = columnMap[columnIndex - 1];
        if (!columnName) return;

        // Toggle sort direction
        sortDirection[columnIndex] = !sortDirection[columnIndex];
        const direction = sortDirection[columnIndex] ? 'asc' : 'desc';

        // Show loading effect
        const tableContainer = document.querySelector('#tableContainer');
        tableContainer.classList.add('opacity-50');

        // Keep any existing filters
        const orgFilter = document.getElementById("organizationFilter").value;
        const typeFilter = document.getElementById("typeFilter").value;
        const searchInput = document.querySelector('input[placeholder="Search..."]').value;

        const params = new URLSearchParams(window.location.search);

        // Add sorting parameters
        params.set('sort_by', columnName);
        params.set('sort_dir', direction);

        // Keep filter parameters
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

        // Ask the server for sorted results
        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Update the table with sorted data
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

                // Update URL
                window.history.pushState({}, '', url);

                // Update sort arrow icon
                updateSortIndicator(columnIndex);

                // Remove loading effect
                tableContainer.classList.remove('opacity-50');

                // Reset selections
                selectedItems.clear();
                updateSelectedCount();

                // Uncheck "select all" box
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

    // Update the arrow icon next to the sorted column
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

    // Open document details page
    function viewDocument(id) {
        window.location.href = "{{ route('admin.documentPreview', ['id' => ':id']) }}".replace(':id', id);
    }

    // Display the confirmation dialog before restoring
    function showRestoreConfirmation() {
        if (selectedItems.size > 0) {
            document.getElementById("restoreConfirmationModal").classList.remove("hidden");
        }
    }

    // Remember sort direction for each column
    let sortDirection = [true, true, true, true, true, true];

    // Handle clicks on checkboxes and other elements
    document.addEventListener('click', function(e) {
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

    // Setup when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Setup restore button
        document.getElementById('restoreSelectedBtn').addEventListener('click', showRestoreConfirmation);

        // Setup filter dropdowns
        document.getElementById("organizationFilter").addEventListener("change", applyServerSideFilters);
        document.getElementById("typeFilter").addEventListener("change", applyServerSideFilters);

        // Setup search with typing delay
        const searchInput = document.querySelector('input[placeholder="Search..."]');
        let searchTimeout;
        searchInput.addEventListener("input", function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyServerSideFilters, 500);
        });

        // Setup modal buttons
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

        // Add event delegation for pagination links
        document.addEventListener('click', function(e) {
            // Find closest anchor that has pagination-btn class or variations
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
                            document.querySelector('#paginationContainer').outerHTML = newPagination.outerHTML;
                        }

                        // Update URL without reload
                        window.history.pushState({}, '', url);

                        // Reset filters and selections
                        selectedItems.clear();
                        updateSelectedCount();

                        // Remove loading state
                        tableContainer.classList.remove('opacity-50');
                    })
                    .catch(error => {
                        console.error('Error loading page:', error);
                        tableContainer.classList.remove('opacity-50');
                    });
            }
        });

        // Add this at the beginning to check initial state
        const hasDocuments = document.querySelectorAll("#documentTable tbody tr[data-id]").length > 0;
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.disabled = !hasDocuments;
        }

        // Initialize selected count
        updateSelectedCount();
    });
</script>
@endsection