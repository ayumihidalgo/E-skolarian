@extends('base')

@section('content')
    @include('components.superAdminNavigation')

    <div class="max-h-screen bg-[#F2F4F7] bg-opacity-30 px-10 py-8">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <a href="{{ route('super-admin.dashboard') }}"
                    class="bg-[#F2F4F7] hover:text-red-800 text-[#7A1212] px-4 py-2 rounded-[16px] font-xl font-[Lexend] inline-flex items-center mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Activity Table -->
        <div class="bg-white rounded-[25px] shadow-lg overflow-hidden" style= "width: 100%; height: 725px; flex-shrink:0;">
            <!-- Table Header -->
            <div class="px-8 py-4 flex justify-between items-center">
                <h2 class="text-[30px] font-bold text-[#161616] font-[Lexend]">ACTIVITY LOG</h2>
                <!-- Header Actions -->
            <div class="flex items-center space-x-3">
                <!-- Search Box -->
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Search activities..."
                        class="w-64 px-4 py-2 pl-10 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7A1212] focus:border-transparent font-[Lexend]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Filter Button -->
                <button
                    class="bg-white border border-gray-300 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded-lg font-[Lexend] inline-flex items-center cursor-pointer" id="openFilterModal">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>

                <!-- Export Button -->
                <button
                    class="bg-[#4D0F0F] px-2 py-1 rounded-[8px] text-white font-[Lexend] hover:bg-red-800 transition duration-200 flex items-center cursor-pointer">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Generate Report
                </button>
            </div>
                <!-- <span class="text-sm text-gray-500 font-[Lexend]">
                    Last updated:
                    @if ($activities->count())
                        {{ \Carbon\Carbon::parse($activities->first()->created_at)->format('F j, Y') }}
                    @else
                        N/A
                    @endif
                </span> -->
            </div>

            <!-- Table Content -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white">
                        <tr>
                            <th
                                class="w-[10%] px-12 py-4 text-left text-xl font-semibold text-[#000000] font-[Lexend]">
                                Timestamp</th>
                            <th
                                class="w-[25%] px-6 py-4 text-left text-xl font-semibold text-[#000000] font-[Lexend]">
                                Name</th>
                            <th
                                class="w-[10%] px-6 py-4 text-left text-xl font-semibold text-[#000000] font-[Lexend]">
                                Action</th>
                            <th
                                class="w-[10%] px-6 py-4 text-left text-xl font-semibold text-[#000000] font-[Lexend]">
                                Target</th>
                            <th
                                class="w-[25%] px-6 py-4 text-left text-xl font-semibold text-[#000000] font-[Lexend]">
                                Description</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="activityTableBody">
                        @foreach ($activities as $activity)
                            <tr class="h-16">
                                <td class="w-[10%] px-13 py-2 whitespace-nowrap text-l text-[#000000] font-[Lexend]">
                                    <div>{{ \Carbon\Carbon::parse($activity->created_at)->format('F j, Y') }}</div>
                                    <div class="text-m text-gray-500">
                                        {{ \Carbon\Carbon::parse($activity->created_at)->format('h:i:s A') }}</div>
                                </td>
                                <td class="w-[25%] px-6 py-2 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 flex-shrink-0">
                                            <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-200">
                                                @if ($activity->user && $activity->user->profile_pic)
                                                    <img src="{{ asset('storage/' . $activity->user->profile_pic) }}"
                                                        alt="Profile" class="w-full h-full object-cover">
                                                @else
                                                    <img src="{{ asset('images/dprofile.svg') }}" alt="Default Profile"
                                                        class="w-full h-full object-cover">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-l font-semibold text-gray-900 font-[Lexend] max-w-[450px] truncate">
                                                <span class="font-semibold text-[18px] text-black text[Lexend]">
                                                    @if($activity->user)
                                                        @if(in_array($activity->user_role_name, ['Admin', 'Superadmin']))
                                                            {{ $activity->user->role_name }}
                                                        @else
                                                            {{ $activity->user->username }}
                                                        @endif
                                                    @else
                                                        Unknown User
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="text-l text-gray-500 font-[Lexend] max-w-[450px] truncate">
                                                {{ $activity->user_role_name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="w-[10%] px-6 py-1 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-3 py-1 text-l font-semibold bg-gray-100 text-gray-700 rounded-full font-[Lexend]">
                                        {{ strtoupper($activity->action) }}
                                    </span>
                                </td>
                                <td class="w-[10%] px-6 py-2 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-2 py-1 text-l font-medium rounded bg-gray-100 text-gray-700 font-[Lexend]">
                                        {{ $activity->target }}
                                    </span>
                                </td>
                                <td class="w-[25%] px-6 py-2 text-l text-gray-900 font-[Lexend] max-w-l truncate">
                                    {{ $activity->description }}
                                </td>
                            </tr>
                        @endforeach
                        <tr id="noResultsRow" class="hidden">
                            <td colspan="5" class="text-center py-12 text-gray-500 font-[Lexend]">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>
                                    <span class="text-lg font-semibold mb-2">No activities found</span>
                                    <span class="text-l text-gray-400">Try adjusting your search or filter to find what
                                        you're looking for.</span>
                                    <button id="clearSearchBtn"
                                        class="mt-6 px-4 py-2 bg-[#7A1212] text-white rounded-lg font-[Lexend] hover:bg-red-800 transition cursor-pointer focus:outline-none focus:ring-0">
                                        Clear Search
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex justify-center mb-1 absolute bottom-10 left-0 w-full p-2 text-center">
                <nav>
                    <ul class="inline-flex items-center space-x-2">
                        <!-- First/Previous Page -->
                        <li>
                            @if ($activities->currentPage() == 1)
                                <span class="px-3 py-1 rounded-lg text-gray-400 cursor-not-allowed">
                                    <
                                </span>
                            @else
                                <a href="{{ $activities->url(1) }}" 
                                   class="px-3 py-1 rounded-lg text-black">
                                    <
                                </a>
                            @endif
                        </li>

                        <!-- Page Numbers -->
                        @for ($i = 1; $i <= $activities->lastPage(); $i++)
                            <li>
                                <a href="{{ $activities->url($i) }}"

                                    class="px-3 py-1 rounded-lg {{ $activities->currentPage() == $i ? 'bg-[#4D0F0F] text-white' : 'text-black' }}">
                                    {{ $i }}
                                </a>
                            </li>
                        @endfor

                        <!-- Next/Last Page -->
                        <li>
                            @if ($activities->currentPage() == $activities->lastPage())
                                <span class="px-3 py-1 rounded-lg text-gray-400 cursor-not-allowed">
                                    >
                                </span>
                            @else
                                <a href="{{ $activities->url($activities->lastPage()) }}" 
                                   class="px-3 py-1 rounded-lg text-black">
                                    >
                                </a>
                            @endif
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
<div id="filterModal" class="hidden absolute right-[250px] top-[245px] w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
    <div class="p-4">
        <h3 class="text-sm font-semibold text-gray-900 mb-3 font-[Lexend]">Filter Activities</h3>
        
        <!-- Quick Filters -->
        <div class="mb-4">
            <button data-filter="newest" class="w-full text-left px-3 py-2 text-sm font-[Lexend] hover:bg-[#F5E6E6] rounded mb-1">Newest</button>
            <button data-filter="oldest" class="w-full text-left px-3 py-2 text-sm font-[Lexend] hover:bg-[#F5E6E6] rounded mb-1">Oldest</button>
            <button data-filter="today" class="w-full text-left px-3 py-2 text-sm font-[Lexend] hover:bg-[#F5E6E6] rounded mb-1">Today</button>
            <button data-filter="this-week" class="w-full text-left px-3 py-2 text-sm font-[Lexend] hover:bg-[#F5E6E6] rounded mb-1">This Week</button>
            <button data-filter="this-month" class="w-full text-left px-3 py-2 text-sm font-[Lexend] hover:bg-[#F5E6E6] rounded mb-1">This Month</button>
        </div>

        <!-- Custom Range -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1 font-[Lexend]">Custom Range</label>
            <div class="space-y-2">
                <div>
                    <label class="text-xs text-gray-500 mb-1 block font-[Lexend]">Start Date</label>
                    <input type="date" id="startDate" class="w-full p-2 text-sm border rounded font-[Lexend]">
                </div>
                <div>
                    <label class="text-xs text-gray-500 mb-1 block font-[Lexend]">End Date</label>
                    <input type="date" id="endDate" class="w-full p-2 text-sm border rounded font-[Lexend]">
                </div>
            </div>
        </div>
        
        <!-- Filter Buttons -->
        <div class="flex justify-end gap-2">
            <button id="clearFilters" class="px-3 py-1 text-sm bg-gray-200 text-gray-800 rounded font-[Lexend] hover:bg-gray-300">
                Clear
            </button>
            <button id="applyFilters" class="px-3 py-1 text-sm bg-[#4D0F0F] text-white rounded font-[Lexend] hover:bg-red-800">
                Apply
            </button>
        </div>
    </div>
</div>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all required elements
    const filterModal = document.getElementById('filterModal');
    const openFilterModalBtn = document.getElementById('openFilterModal');
    const applyFiltersBtn = document.getElementById('applyFilters');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const dateFilterInput = document.getElementById('dateFilter');
    const nameFilterInput = document.getElementById('nameFilter');
    const tableBody = document.getElementById('activityTableBody');
    const noResultsRow = document.getElementById('noResultsRow');
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');

    // Toggle filter modal
    openFilterModalBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        filterModal.classList.toggle('hidden');
    });

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        if (!filterModal.contains(e.target) && e.target !== openFilterModalBtn) {
            filterModal.classList.add('hidden');
        }
    });

    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const quickFilterBtns = filterModal.querySelectorAll('[data-filter]');

    // Quick filter buttons
    quickFilterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filterType = this.dataset.filter;
            const now = new Date();
            
            // Reset custom range inputs
            startDateInput.value = '';
            endDateInput.value = '';

            // Remove active state from all buttons
            quickFilterBtns.forEach(b => b.classList.remove('bg-[#F5E6E6]'));
            // Add active state to clicked button
            this.classList.add('bg-[#F5E6E6]');

            const rows = Array.from(tableBody.querySelectorAll('tr:not(#noResultsRow)'));
            let visibleCount = 0;

            rows.forEach(row => {
                const dateCell = row.querySelector('td:first-child');
                const dateText = dateCell.querySelector('div:first-child').textContent;
                const rowDate = new Date(dateText);
                let showRow = false;

                switch(filterType) {
                    case 'newest':
                        showRow = true;
                        rows.sort((a, b) => {
                            const dateA = new Date(a.querySelector('td:first-child div:first-child').textContent);
                            const dateB = new Date(b.querySelector('td:first-child div:first-child').textContent);
                            return dateB - dateA;
                        });
                        break;
                    case 'oldest':
                        showRow = true;
                        rows.sort((a, b) => {
                            const dateA = new Date(a.querySelector('td:first-child div:first-child').textContent);
                            const dateB = new Date(b.querySelector('td:first-child div:first-child').textContent);
                            return dateA - dateB;
                        });
                        break;
                    case 'today':
                        showRow = rowDate.toDateString() === now.toDateString();
                        break;
                    case 'this-week':
                        const weekStart = new Date(now);
                        weekStart.setDate(now.getDate() - now.getDay());
                        const weekEnd = new Date(weekStart);
                        weekEnd.setDate(weekStart.getDate() + 6);
                        showRow = rowDate >= weekStart && rowDate <= weekEnd;
                        break;
                    case 'this-month':
                        showRow = rowDate.getMonth() === now.getMonth() && 
                                rowDate.getFullYear() === now.getFullYear();
                        break;
                }

                row.style.display = showRow ? '' : 'none';
                if (showRow) visibleCount++;
            });

            // Reorder table if sorting was applied
            if (filterType === 'newest' || filterType === 'oldest') {
                const tbody = tableBody;
                rows.forEach(row => tbody.appendChild(row));
            }

            noResultsRow.classList.toggle('hidden', visibleCount > 0);
        });
    });

    // Apply custom range filter
    applyFiltersBtn.addEventListener('click', function() {
        const startDate = startDateInput.value ? new Date(startDateInput.value) : null;
        const endDate = endDateInput.value ? new Date(endDateInput.value) : null;
        
        // Set start date to beginning of day (00:00:00)
        if (startDate) {
            startDate.setHours(0, 0, 0, 0);
        }
        
        // Set end date to end of day (23:59:59)
        if (endDate) {
            endDate.setHours(23, 59, 59, 999);
        }

        // Remove active state from quick filter buttons
        quickFilterBtns.forEach(btn => btn.classList.remove('bg-[#F5E6E6]'));

        const rows = Array.from(tableBody.querySelectorAll('tr:not(#noResultsRow)'));
        let visibleCount = 0;

        rows.forEach(row => {
            const dateCell = row.querySelector('td:first-child');
            const dateText = dateCell.querySelector('div:first-child').textContent;
            const rowDate = new Date(dateText);
            rowDate.setHours(0, 0, 0, 0); // Normalize row date to start of day
            
            let showRow = true;
            if (startDate && endDate) {
                // Include both start and end dates in the range
                showRow = rowDate >= startDate && rowDate <= endDate;
            } else if (startDate) {
                showRow = rowDate >= startDate;
            } else if (endDate) {
                showRow = rowDate <= endDate;
            }

            row.style.display = showRow ? '' : 'none';
            if (showRow) visibleCount++;
        });

        noResultsRow.classList.toggle('hidden', visibleCount > 0);
        filterModal.classList.add('hidden');
    });

    // Update clear filters function
    clearFiltersBtn.addEventListener('click', function() {
        startDateInput.value = '';
        endDateInput.value = '';
        quickFilterBtns.forEach(btn => btn.classList.remove('bg-[#F5E6E6]'));
        
        const rows = Array.from(tableBody.querySelectorAll('tr:not(#noResultsRow)'));
        rows.forEach(row => row.style.display = '');
        
        noResultsRow.classList.add('hidden');
        filterModal.classList.add('hidden');
    });

    // Add search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = Array.from(tableBody.querySelectorAll('tr:not(#noResultsRow)'));
        let visibleCount = 0;

        rows.forEach(row => {
            const timestamp = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const action = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const target = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const description = row.querySelector('td:nth-child(5)').textContent.toLowerCase();

            const matchesSearch = timestamp.includes(searchTerm) ||
                                name.includes(searchTerm) ||
                                action.includes(searchTerm) ||
                                target.includes(searchTerm) ||
                                description.includes(searchTerm);

            row.style.display = matchesSearch ? '' : 'none';
            if (matchesSearch) visibleCount++;
        });

        // Changed from style.display to classList.toggle
        noResultsRow.classList.toggle('hidden', visibleCount > 0);
    });

    // Function to clear all filters and search
    function clearAllFiltersAndSearch() {
        // Clear all input values
        searchInput.value = '';
        startDateInput.value = '';
        endDateInput.value = '';
        if (dateFilterInput) dateFilterInput.value = '';
        if (nameFilterInput) nameFilterInput.value = '';

        // Remove active state from quick filter buttons
        quickFilterBtns.forEach(btn => btn.classList.remove('bg-[#F5E6E6]'));

        // Reset table sorting and show all rows
        const rows = Array.from(tableBody.querySelectorAll('tr:not(#noResultsRow)'));
        rows.forEach(row => {
            row.style.display = '';
            row.style.order = ''; // Reset any custom ordering
        });

        // Hide no results message
        noResultsRow.classList.add('hidden');

        // Hide filter modal
        filterModal.classList.add('hidden');

        // Reset any stored filter states
        filterType = null;
    }

    // Update both clear button event listeners to use the same function
    clearFiltersBtn.addEventListener('click', clearAllFiltersAndSearch);
    clearSearchBtn.addEventListener('click', clearAllFiltersAndSearch);
});
</script>
@endsection
