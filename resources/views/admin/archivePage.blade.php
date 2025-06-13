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
                        <!-- Standard document type options -->
                        <option class="bg-white text-black truncate" value="Event Proposal">Event Proposal</option>
                        <option class="bg-white text-black truncate" value="General Plan of Activities">General Plan of
                            Activities</option>
                        <option class="bg-white text-black truncate" value="Reports of Proceedings">Reports of Proceedings
                        </option>
                        <option class="bg-white text-black truncate" value="Constitution and By-Laws">Constitution and
                            By-Laws</option>
                        <option class="bg-white text-black truncate" value="Fundraising Activities">Fundraising Activities
                        </option>
                        <option class="bg-white text-black truncate" value="Request Letter">Request Letter</option>
                        <option class="bg-white text-black truncate" value="Petition and Concern">Petition and Concern
                        </option>
                        <option class="bg-white text-black truncate" value="Memorandum of Agreement">Memorandum of
                            Agreement</option>
                        <option class="bg-white text-black truncate" value="Off Campus Activities">Off Campus Activities
                        </option>
                        <!-- Others option for non-standard types -->
                        <option class="bg-white text-black truncate" value="Others">Others</option>
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
                                    <th class="px-4 py-2 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" id="selectAll" class="w-4 h-4 cursor-pointer">
                                            <button id="selectAllLink" 
                                                class="text-[#7A1212] underline hover:text-[#DAA520] text-sm font-medium cursor-pointer">
                                                Select All
                                            </button>
                                        </div>
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
                                $archivedDate = \Carbon\Carbon::parse($document->archived_at)->format('m/d/Y g:i A');
                                
                                // Determine display type - show 'Others' for non-standard types
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
                                $displayType = in_array($document->type, $standardTypes) ? $document->type : 'Others';
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
                                    <!-- Date archived -->
                                    <td class="px-4 py-2 truncate max-w-[120px] cursor-pointer"
                                        onclick="viewDocument({{ $document->id }})">
                                        {{ $archivedDate }}
                                    </td>
                                    <!-- Document type - show 'Others' for non-standard types -->
                                    <td class="px-4 py-2 truncate max-w-[160px] cursor-pointer"
                                        onclick="viewDocument({{ $document->id }})"
                                        title="{{ $displayType }}">
                                        {{ $displayType }}
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
    // Configuration constants
    let selectedItems = new Set();
    let isSelectAllActive = false; // Track if server-side select all is active
    let allFilteredDocumentIds = []; // Store all document IDs from server-side select all

    // Remember sort direction for each column
    let sortDirection = [true, true, true, true, true, true];

    // Update the counter showing how many documents are selected
    function updateSelectedCount() {
        const count = selectedItems.size;
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('restoreSelectedBtn').disabled = count === 0;
        
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

    // Server-side select all function - selects ALL archived documents across ALL pages
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

        // Make AJAX request to get all archived document IDs
        fetch("{{ route('admin.selectAllArchivedDocuments') }}", {
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
                    `Selected ${data.total_count} archived documents across all pages.`,
                    true
                );

                console.log(`Server-side select all: ${data.total_count} archived documents selected`);
            } else {
                showActionToast(
                    'Selection Failed',
                    'Failed to select all archived documents.',
                    false
                );
            }
        })
        .catch(error => {
            console.error('Error selecting all archived documents:', error);
            showActionToast(
                'Selection Error',
                'An error occurred while selecting archived documents.',
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
        console.log('All archived documents deselected');
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
            actionIcon.src = "{{ asset('images/error.svg') }}";
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

    // Actually restore the selected documents - UPDATED WITH DYNAMIC PAGINATION FIX
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

                    // Reset selection state
                    deselectAllDocuments();

                    // Wait for animations to complete, then refresh pagination
                    setTimeout(() => {
                        // Check if there are any remaining documents on current page
                        const remainingRows = document.querySelectorAll("#documentTable tbody tr[data-id]").length;
                        
                        if (remainingRows === 0) {
                            // If no documents left on current page, refresh to update pagination
                            refreshCurrentPage();
                        } else {
                            // If documents still exist, just update pagination numbers
                            updatePaginationAfterRestore();
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

    // NEW FUNCTION: Refresh current page to update pagination
    function refreshCurrentPage() {
        const currentUrl = window.location.href;
        const tableContainer = document.querySelector('#tableContainer');
        
        // Show loading indicator
        tableContainer.classList.add('opacity-50');
        
        fetch(currentUrl, {
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
            
            // Update pagination - this will remove empty pages
            const newPagination = doc.querySelector('#paginationContainer');
            const currentPagination = document.querySelector('#paginationContainer');
            
            if (newPagination) {
                if (currentPagination) {
                    currentPagination.outerHTML = newPagination.outerHTML;
                }
            } else {
                // If no pagination returned, hide the pagination container
                if (currentPagination) {
                    currentPagination.style.display = 'none';
                }
            }
            
            // Remove loading state
            tableContainer.classList.remove('opacity-50');
            
            // Reset checkbox states
            const selectAllCheckbox = document.getElementById('selectAll');
            const hasDocuments = document.querySelectorAll("#documentTable tbody tr[data-id]").length > 0;
            
            if (selectAllCheckbox) {
                selectAllCheckbox.disabled = !hasDocuments;
                selectAllCheckbox.checked = false;
            }
            
            // Update select all link
            updateSelectAllLinkText();
            
        })
        .catch(error => {
            console.error('Error refreshing page:', error);
            tableContainer.classList.remove('opacity-50');
            // Fallback to full page reload if AJAX fails
            window.location.reload();
        });
    }

    // NEW FUNCTION: Update pagination without full refresh
    function updatePaginationAfterRestore() {
        const currentUrl = window.location.href;
        
        // Just fetch pagination info
        fetch(currentUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Only update pagination, keep existing table content
            const newPagination = doc.querySelector('#paginationContainer');
            const currentPagination = document.querySelector('#paginationContainer');
            
            if (newPagination && currentPagination) {
                currentPagination.outerHTML = newPagination.outerHTML;
            } else if (!newPagination && currentPagination) {
                // Hide pagination if no longer needed
                currentPagination.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error updating pagination:', error);
        });
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

    // Updated applyServerSideFilters function
    function applyServerSideFilters() {
        // Show loading effect
        const tableContainer = document.querySelector('#tableContainer');
        tableContainer.classList.add('opacity-50');
        
        // Reset server-side selection when filters change
        if (isSelectAllActive) {
            deselectAllDocuments();
        }
        
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
        if (organizationFilter !== 'Organization' && organizationFilter !== 'All') {
            params.append('organization', organizationFilter);
        }
        
        if (typeFilter !== 'Type' && typeFilter !== 'All') {
            params.append('type', typeFilter);
        }
        
        if (searchTerm) {
            params.append('search', searchTerm);
        }
        
        // Always start from page 1 when applying filters
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
            
            // Update the page numbers - CRITICAL FOR PAGINATION FIX
            const newPagination = doc.querySelector('#paginationContainer');
            const currentPagination = document.querySelector('#paginationContainer');
            
            if (newPagination) {
                if (currentPagination) {
                    currentPagination.outerHTML = newPagination.outerHTML;
                }
            } else {
                // Hide pagination if no results need pagination
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
            
            // Update select all link text
            updateSelectAllLinkText();
            
            // Remove the loading effect
            tableContainer.classList.remove('opacity-50');
        })
        .catch(error => {
            console.error('Error applying filters:', error);
            tableContainer.classList.remove('opacity-50');
        });
    }

    // Setup when page loads
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

                        // Restore selection state for visible documents
                        restoreSelectionStateOnPage();

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

        // Add this at the beginning to check initial state
        const hasDocuments = document.querySelectorAll("#documentTable tbody tr[data-id]").length > 0;
        if (selectAllCheckbox) {
            selectAllCheckbox.disabled = !hasDocuments;
        }

        // Initialize selected count
        updateSelectedCount();
    });
</script>
@endsection