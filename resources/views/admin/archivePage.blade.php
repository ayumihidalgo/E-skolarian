@extends('base')

@section('content')
@include('components.adminSidebarComponent')

<div id="main-content" class="flex flex-col min-h-screen ml-[20%] transition-all duration-300 bg-[#F2F4F7]">
    @include('components.adminNavBarComponent')
    <div class="w-full">
        <div class="w-full min-h-screen bg-[#f2f4f7] px-6 py-8 flex flex-col">
            <!-- Header section with title and history page link -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="md:text-2xl text-xl font-extrabold">Document Archive Table</h2>

                <!-- Link back to document history page -->
                <a href="{{ route('admin.documentHistory') }}"
                    class="text-[#7A1212] underline font-medium hover:text-[#DAA520] transition-colors duration-200">
                    Return to Repository
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
                        @if(isset($availableOrganizations))
                            @foreach($availableOrganizations as $org)
                                <option class="bg-white text-black" value="{{ $org }}">{{ $org }}</option>
                            @endforeach
                        @endif
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
                                    <!-- Change the table header -->
                                    @php $headers = ['Tag', 'Organization', 'Title', 'Date Archived', 'Type', 'Submitted to']; @endphp
                                    @foreach ($headers as $i => $header)
                                    <th class="px-4 py-2 whitespace-nowrap max-w-[160px] truncate">
                                        @if ($header === 'Organization' || $header === 'Type' || $header === 'Submitted to')
                                        <button onclick="sortTable({{ $i + 1 }})" class="group">
                                            <span>{{ $header }}</span>
                                        </button>
                                    @else
                                        <!-- Other headers have normal sort icon -->
                                        <button onclick="sortTable({{ $i + 1 }})"
                                            class="flex items-center gap-1 group">
                                            <span>{{ $header }}</span>
                                            <img src="{{ asset('images/sortIcon.svg') }}" alt="Sort"
                                                class="w-3 h-3 text-gray-500 group-hover:text-black transition">
                                        </button>
                                    @endif
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
                                    data-role="{{ $document->role_name }}"
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
                                    <!-- Role column -->
                                    <td class="px-4 py-2 truncate cursor-pointer"
                                        onclick="viewDocument({{ $document->id }})">
                                        {{ $document->role_name ?? 'N/A' }}
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
    let isSelectAllActive = false;
    let allFilteredDocumentIds = [];

    // Remember sort direction for each column
    let sortDirection = [true, true, true, true, true, true];

    document.addEventListener('DOMContentLoaded', function() {
        console.log('Archive page loaded');
        updateSelectedCount();
        
        // Initialize filter state from URL parameters if they exist
        initializeFiltersFromURL();

        // Handle regular checkbox select all (only current page)
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    selectCurrentPageDocuments();
                } else {
                    deselectAllDocuments();
                }
            });
        }

        // Handle "Select All" link
        const selectAllLink = document.getElementById('selectAllLink');
        if (selectAllLink) {
            selectAllLink.addEventListener('click', function(e) {
                e.preventDefault();
                if (isSelectAllActive) {
                    deselectAllDocuments();
                } else {
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

        // Filter form elements
        const organizationFilter = document.getElementById("organizationFilter");
        const typeFilter = document.getElementById("typeFilter");
        const searchInput = document.querySelector('input[placeholder="Search..."]');

        // Handle filter changes with debugging
        function handleFilterChange() {
            const orgValue = organizationFilter ? organizationFilter.value : '';
            const typeValue = typeFilter ? typeFilter.value : '';
            const searchValue = searchInput ? searchInput.value : '';
            
            console.log('Archive filter change detected:', {
                organization: orgValue,
                type: typeValue,
                search: searchValue
            });
            
            applyServerSideFilters();
        }

        // Add event listeners to filters
        if (organizationFilter) {
            organizationFilter.addEventListener("change", function() {
                console.log('Organization filter changed to:', this.value);
                handleFilterChange();
            });
        }

        if (typeFilter) {
            typeFilter.addEventListener("change", function() {
                console.log('Type filter changed to:', this.value);
                handleFilterChange();
            });
        }

        // For search, use debouncing
        let searchTimeout;
        if (searchInput) {
            searchInput.addEventListener("input", function() {
                console.log('Search input changed to:', this.value);
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(handleFilterChange, 500);
            });
        }

        // Handle pagination links with AJAX
        document.addEventListener('click', function(e) {
            const paginationLink = e.target.closest('a.pagination-btn, a.pagination-btn-prev, a.pagination-btn-next');

            if (paginationLink && !paginationLink.classList.contains('cursor-not-allowed')) {
                e.preventDefault();
                const url = paginationLink.getAttribute('href');

                const tableContainer = document.querySelector('#tableContainer');
                if (tableContainer) {
                    tableContainer.classList.add('opacity-50');
                }

                fetch(url, {
                        headers:
                        {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(data.html, 'text/html');

                        const newTableBody = doc.querySelector('#documentTable tbody');
                        const currentTableBody = document.querySelector('#documentTable tbody');
                        
                        if (newTableBody && currentTableBody) {
                            currentTableBody.innerHTML = newTableBody.innerHTML;
                        }

                        const newPagination = doc.querySelector('#paginationContainer');
                        if (newPagination) {
                            const currentPagination = document.querySelector('#paginationContainer');
                            if (currentPagination) {
                                currentPagination.outerHTML = newPagination.outerHTML;
                            }
                        }

                        restoreSelectionStateOnPage();
                        window.history.pushState({}, '', url);
                        updateSelectedCount();
                        
                        if (tableContainer) {
                            tableContainer.classList.remove('opacity-50');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading page:', error);
                        if (tableContainer) {
                            tableContainer.classList.remove('opacity-50');
                        }
                    });
            }
        });

        // Add this to your DOMContentLoaded event listener
        document.getElementById('restoreSelectedBtn').addEventListener('click', function() {
            if (selectedItems.size === 0) return;
            
            // Show confirmation modal
            document.getElementById('restoreConfirmationModal').classList.remove('hidden');
        });

        document.getElementById('confirmRestoreBtn').addEventListener('click', function() {
            const documentIds = isSelectAllActive ? allFilteredDocumentIds : Array.from(selectedItems);
            const count = documentIds.length;
            
            fetch('{{ route("admin.restoreDocuments") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    document_ids: documentIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showActionToast('Restore Successful', `Successfully restored ${count} document${count > 1 ? 's' : ''}.`, true);
                    
                    // Add smooth fade-out animation for restored rows (same as archive animation)
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

                    // Wait for animations to complete, then reset selections and reload data
                    setTimeout(() => {
                        deselectAllDocuments();
                        // Instead of full page reload, refresh the table data
                        resetFiltersAndReloadData();
                    }, 600);
                    
                } else {
                    showActionToast('Restore Failed', data.message || 'Failed to restore documents.', false);
                }
            })
            .catch(error => {
                console.error('Error restoring documents:', error);
                showActionToast('Restore Error', 'An error occurred while restoring documents.', false);
            });
            
            // Hide modal
            document.getElementById('restoreConfirmationModal').classList.add('hidden');
        });

        // Add the resetFiltersAndReloadData function (similar to documentHistory.blade.php)
        function resetFiltersAndReloadData() {
            // Reset all filter dropdowns to their default values
            const organizationFilter = document.getElementById("organizationFilter");
            const typeFilter = document.getElementById("typeFilter");
            const searchInput = document.querySelector('input[placeholder="Search..."]');

            // Reset filter values
            if (organizationFilter) organizationFilter.value = "Organization";
            if (typeFilter) typeFilter.value = "Type";
            if (searchInput) searchInput.value = "";

            // Show loading state
            const tableContainer = document.querySelector('#tableContainer');
            if (tableContainer) {
                tableContainer.classList.add('opacity-50');
            }

            // Clear selections
            selectedItems.clear();
            updateSelectedCount();

            // Fetch all documents (no filters)
            const baseUrl = window.location.pathname;

            fetch(baseUrl, {
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
                const currentTableBody = document.querySelector('#documentTable tbody');
                
                if (newTableBody && currentTableBody) {
                    currentTableBody.innerHTML = newTableBody.innerHTML;
                }

                // Update pagination
                const newPagination = doc.querySelector('#paginationContainer');
                const currentPagination = document.querySelector('#paginationContainer');

                if (newPagination) {
                    if (currentPagination) {
                        currentPagination.outerHTML = newPagination.outerHTML;
                        currentPagination.style.display = 'block';
                    }
                } else if (currentPagination) {
                    currentPagination.style.display = 'none';
                }

                // Update filter dropdowns with new available options
                updateFilterDropdowns(data.availableOrganizations, data.availableTypes);

                // Update URL to base path (remove all query parameters)
                window.history.pushState({}, '', baseUrl);

                // Update select all checkbox state
                const selectAllCheckbox = document.getElementById('selectAll');
                if (selectAllCheckbox) {
                    const visibleRows = document.querySelectorAll("#documentTable tbody tr[data-id]").length;
                    selectAllCheckbox.disabled = visibleRows === 0;
                    selectAllCheckbox.checked = false;
                }

                // Remove loading state
                if (tableContainer) {
                    tableContainer.classList.remove('opacity-50');
                }
            })
            .catch(error => {
                console.error('Error resetting filters:', error);
                if (tableContainer) {
                    tableContainer.classList.remove('opacity-50');
                }

                // Fallback: if AJAX fails, do a page reload to base URL
                window.location.href = window.location.pathname;
            });
        }
    });

    // New function to initialize filters from URL parameters
    function initializeFiltersFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        
        const orgFilter = document.getElementById("organizationFilter");
        const typeFilter = document.getElementById("typeFilter");
        const searchInput = document.querySelector('input[placeholder="Search..."]');
        
        if (urlParams.has('organization') && orgFilter) {
            orgFilter.value = urlParams.get('organization');
        }
        
        if (urlParams.has('type') && typeFilter) {
            typeFilter.value = urlParams.get('type');
        }
        
        if (urlParams.has('search') && searchInput) {
            searchInput.value = urlParams.get('search');
        }
    }

    // Updated applyServerSideFilters function
    function applyServerSideFilters() {
        const tableContainer = document.querySelector('#tableContainer');
        if (tableContainer) {
            tableContainer.classList.add('opacity-50');
        }
        
        if (isSelectAllActive) {
            deselectAllDocuments();
        }
        
        selectedItems.clear();
        updateSelectedCount();
        
        const params = new URLSearchParams();
        
        const searchTerm = document.querySelector('input[placeholder="Search..."]');
        const organizationFilter = document.getElementById("organizationFilter");
        const typeFilter = document.getElementById("typeFilter");
        
        const searchValue = searchTerm ? searchTerm.value.trim() : '';
        const orgValue = organizationFilter ? organizationFilter.value : '';
        const typeValue = typeFilter ? typeFilter.value : '';
        
        console.log('Building archive request with filters:', {
            search: searchValue,
            organization: orgValue,
            type: typeValue
        });
        
        // Only add parameters if they have actual filter values
        if (orgValue && orgValue !== 'Organization' && orgValue !== 'All') {
            params.append('organization', orgValue);
            console.log('Added organization filter:', orgValue);
        }
        
        if (typeValue && typeValue !== 'Type' && typeValue !== 'All') {
            params.append('type', typeValue);
            console.log('Added type filter:', typeValue);
        }
        
        if (searchValue) {
            params.append('search', searchValue);
            console.log('Added search filter:', searchValue);
        }
        
        const url = `${window.location.pathname}?${params.toString()}`;
        console.log('Making archive request to URL:', url);
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Archive response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Archive filter response received:', {
                hasHtml: !!data.html,
                availableOrgs: data.availableOrganizations,
                availableTypes: data.availableTypes,
                debug: data.debug
            });
            
            const parser = new DOMParser();
            const doc = parser.parseFromString(data.html, 'text/html');
            
            const newTableBody = doc.querySelector('#documentTable tbody');
            const currentTableBody = document.querySelector('#documentTable tbody');
            
            if (newTableBody && currentTableBody) {
                currentTableBody.innerHTML = newTableBody.innerHTML;
                console.log('Archive table updated successfully');
            } else {
                console.error('No table body found in archive response');
            }
            
            const newPagination = doc.querySelector('#paginationContainer');
            const currentPagination = document.querySelector('#paginationContainer');
            
            if (newPagination) {
                if (currentPagination) {
                    currentPagination.outerHTML = newPagination.outerHTML;
                }
            } else {
                if (currentPagination) {
                    currentPagination.style.display = 'none';
                }
            }

            // Update filter dropdowns with available options
            updateFilterDropdowns(data.availableOrganizations, data.availableTypes);
            
            window.history.pushState({}, '', url);
            
            const hasDocuments = document.querySelectorAll("#documentTable tbody tr[data-id]").length > 0;
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.disabled = !hasDocuments;
                if (!hasDocuments) {
                    selectAllCheckbox.checked = false;
                }
            }
            
            updateSelectAllLinkText();
            
            if (tableContainer) {
                tableContainer.classList.remove('opacity-50');
            }
        })
        .catch(error => {
            console.error('Error applying archive filters:', error);
            if (tableContainer) {
                tableContainer.classList.remove('opacity-50');
            }
            
            // Show error message to user
            showActionToast('Filter Error', 'An error occurred while applying filters.', false);
        });
    }

    // Improved updateFilterDropdowns function
    function updateFilterDropdowns(availableOrganizations, availableTypes) {
        console.log('Updating archive filter dropdowns:', {
            orgs: availableOrganizations,
            types: availableTypes
        });
        
        const orgSelect = document.getElementById('organizationFilter');
        const typeSelect = document.getElementById('typeFilter');
        
        if (!orgSelect || !typeSelect) {
            console.error('Archive filter dropdowns not found');
            return;
        }
        
        const currentOrgValue = orgSelect.value;
        const currentTypeValue = typeSelect.value;
        
        // Update organization dropdown
        const orgOptions = orgSelect.querySelectorAll('option');
        orgOptions.forEach(option => {
            const value = option.value;
            if (value === 'Organization' || value === 'All') {
                option.style.display = 'block';
            } else {
                const shouldShow = availableOrganizations && availableOrganizations.includes(value);
                option.style.display = shouldShow ? 'block' : 'none';
            }
        });
        
        // Update type dropdown
        const typeOptions = typeSelect.querySelectorAll('option');
        typeOptions.forEach(option => {
            const value = option.value;
            if (value === 'Type' || value === 'All') {
                option.style.display = 'block';
            } else {
                const shouldShow = availableTypes && availableTypes.includes(value);
                option.style.display = shouldShow ? 'block' : 'none';
            }
        });
        
        // Reset to default if current selection is no longer available
        if (currentOrgValue !== 'Organization' && currentOrgValue !== 'All') {
            if (!availableOrganizations || !availableOrganizations.includes(currentOrgValue)) {
                orgSelect.value = 'Organization';
            }
        }
        
        if (currentTypeValue !== 'Type' && currentTypeValue !== 'All') {
            if (!availableTypes || !availableTypes.includes(currentTypeValue)) {
                typeSelect.value = 'Type';
            }
        }
    }

    // Rest of your existing functions (keep them unchanged)...
    function updateSelectedCount() {
        const count = selectedItems.size;
        const selectedCountElement = document.getElementById('selectedCount');
        const restoreBtn = document.getElementById('restoreSelectedBtn');
        
        if (selectedCountElement) {
            selectedCountElement.textContent = count;
        }
        
        if (restoreBtn) {
            restoreBtn.disabled = count === 0;
        }
        
        // Update the regular checkbox state based on visible selections
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox && !isSelectAllActive) {
            const visibleCheckboxes = document.querySelectorAll('.row-checkbox');
            const checkedVisibleCount = Array.from(visibleCheckboxes).filter(cb => cb.checked).length;
            
            selectAllCheckbox.checked = visibleCheckboxes.length > 0 && checkedVisibleCount === visibleCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedVisibleCount > 0 && checkedVisibleCount < visibleCheckboxes.length;
        }
    }

    // Add the other functions that are referenced but missing...
    function selectCurrentPageDocuments() {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
            selectedItems.add(checkbox.getAttribute('data-id'));
        });
        updateSelectedCount();
    }

    function selectAllDocumentsServerSide() {
        console.log('Starting server-side select all for archived documents');
        
        // Build the current filter parameters
        const params = new URLSearchParams();
        
        const searchTerm = document.querySelector('input[placeholder="Search..."]');
        const organizationFilter = document.getElementById("organizationFilter");
        const typeFilter = document.getElementById("typeFilter");
        
        const searchValue = searchTerm ? searchTerm.value.trim() : '';
        const orgValue = organizationFilter ? organizationFilter.value : '';
        const typeValue = typeFilter ? typeFilter.value : '';
        
        if (orgValue && orgValue !== 'Organization' && orgValue !== 'All') {
            params.append('organization', orgValue);
        }
        
        if (typeValue && typeValue !== 'Type' && typeValue !== 'All') {
            params.append('type', typeValue);
        }
        
        if (searchValue) {
            params.append('search', searchValue);
        }
        
        // Make request to get all filtered document IDs
        fetch(`{{ route('admin.selectAllArchivedDocuments') }}?${params.toString()}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log(`Selected ${data.total_count} documents across all pages`);
                
                // Clear existing selections
                selectedItems.clear();
                
                // Add all document IDs to selection
                data.document_ids.forEach(id => {
                    selectedItems.add(id.toString());
                });
                
                // Mark as server-side select all active
                isSelectAllActive = true;
                allFilteredDocumentIds = data.document_ids.map(id => id.toString());
                
                // Update UI
                const checkboxes = document.querySelectorAll('.row-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
                
                const selectAllCheckbox = document.getElementById('selectAll');
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                }
                
                updateSelectedCount();
                updateSelectAllLinkText();
                
                // Show confirmation
                showActionToast('Selection Complete', `Selected ${data.total_count} documents across all pages`, true);
            } else {
                console.error('Failed to select all documents:', data.message);
                showActionToast('Selection Failed', 'Failed to select all documents', false);
            }
        })
        .catch(error => {
            console.error('Error selecting all documents:', error);
            showActionToast('Selection Error', 'An error occurred while selecting documents', false);
        });
    }

    function deselectAllDocuments() {
        selectedItems.clear();
        isSelectAllActive = false;
        
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
        
        updateSelectedCount();
        updateSelectAllLinkText();
    }

    function updateSelectAllLinkText() {
        const selectAllLink = document.getElementById('selectAllLink');
        if (selectAllLink) {
            if (isSelectAllActive) {
                selectAllLink.textContent = 'Deselect All';
            } else {
                selectAllLink.textContent = 'Select All';
            }
        }
    }

    function handleIndividualCheckboxChange(checkbox) {
        const documentId = checkbox.getAttribute('data-id');
        if (checkbox.checked) {
            selectedItems.add(documentId);
        } else {
            selectedItems.delete(documentId);
            if (isSelectAllActive) {
                isSelectAllActive = false;
                updateSelectAllLinkText();
            }
        }
        updateSelectedCount();
    }

    function restoreSelectionStateOnPage() {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(checkbox => {
            const documentId = checkbox.getAttribute('data-id');
            checkbox.checked = selectedItems.has(documentId) || isSelectAllActive;
        });
    }

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
    function viewDocument(id) {
    window.location.href = "{{ route('admin.documentPreview', ['id' => ':id']) }}".replace(':id', id);
}

    // Handle close button ("X")
    document.getElementById('closeRestoreModalBtn').addEventListener('click', function() {
        document.getElementById('restoreConfirmationModal').classList.add('hidden');
    });

    // Handle cancel button
    document.getElementById('cancelRestoreBtn').addEventListener('click', function() {
        document.getElementById('restoreConfirmationModal').classList.add('hidden');
    });
</script>
@endsection