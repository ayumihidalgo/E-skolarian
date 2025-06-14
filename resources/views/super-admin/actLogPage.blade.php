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
        <div class="bg-white rounded-[25px] shadow-lg overflow-hidden mb-12 relative" style="width: 100%; height: 725px; flex-shrink:0;">
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
                    <button id="generatePDFBtn"
                        class="bg-[#4D0F0F] px-2 py-1 rounded-[8px] text-white font-[Lexend] hover:bg-red-800 transition duration-200 flex items-center cursor-pointer">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Generate Report
                    </button>
                </div>
            </div>

            <!-- Table Content -->
            <div class="overflow-x-auto" style="height: calc(100% - 110px);">
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
                                <td class="w-[25%] px-6 py-2 text-l text-gray-900 font-[Lexend]">
                                    <div class="max-w-xs truncate" title="{{ $activity->description }}">
                                        {{ $activity->description }}
                                    </div>
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

            <!-- Pagination - Fixed at bottom with proper positioning -->
            <div class="absolute bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 py-3 rounded-b-[25px]">
                <div class="flex justify-center">
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
                                       class="px-3 py-1 rounded-lg text-black hover:bg-gray-100">
                                        <
                                    </a>
                                @endif
                            </li>

                            <!-- Page Numbers -->
                            @for ($i = 1; $i <= $activities->lastPage(); $i++)
                                <li>
                                    <a href="{{ $activities->url($i) }}"
                                        class="px-3 py-1 rounded-lg {{ $activities->currentPage() == $i ? 'bg-[#4D0F0F] text-white' : 'text-black hover:bg-gray-100' }}">
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
                                       class="px-3 py-1 rounded-lg text-black hover:bg-gray-100">
                                        >
                                    </a>
                                @endif
                            </li>
                        </ul>
                    </nav>
                </div>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all required elements
    const filterModal = document.getElementById('filterModal');
    const openFilterModalBtn = document.getElementById('openFilterModal');
    const applyFiltersBtn = document.getElementById('applyFilters');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const tableBody = document.getElementById('activityTableBody');
    const noResultsRow = document.getElementById('noResultsRow');
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const quickFilterBtns = filterModal.querySelectorAll('[data-filter]');

    // Store all activities data and pagination
    let allActivities = [];
    let filteredActivities = [];
    let currentPage = 1;
    const itemsPerPage = 8;

    // Track current filters
    let currentFilterType = null;
    let currentStartDate = null;
    let currentEndDate = null;
    let currentSearchTerm = '';

    // Fetch all activities from server on page load
    async function fetchAllActivities() {
        try {
            const response = await fetch('/super-admin/activity-logs/all');
            allActivities = await response.json();
            filteredActivities = [...allActivities]; // Copy all activities initially
            return true;
        } catch (error) {
            console.error('Error fetching activities:', error);
            return false;
        }
    }

    // Render activities in table with pagination
    function renderActivities(activities, searchTerm = '') {
        tableBody.innerHTML = ''; // Clear current content

        if (activities.length === 0) {
            noResultsRow.classList.remove('hidden');
            updatePagination(0);
            return;
        }

        // Calculate pagination
        const totalPages = Math.ceil(activities.length / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const currentPageActivities = activities.slice(startIndex, endIndex);

        // Render current page activities
        currentPageActivities.forEach(activity => {
            const row = createActivityRow(activity);
            
            // Apply search highlighting if search term exists
            if (searchTerm) {
                highlightSearchTerm(row, searchTerm);
            }
            
            tableBody.appendChild(row);
        });

        // Add no results row to DOM
        tableBody.appendChild(noResultsRow);
        noResultsRow.classList.add('hidden');

        // Update pagination controls
        updatePagination(totalPages);
    }

    // Update pagination controls
    function updatePagination(totalPages) {
        const paginationContainer = document.querySelector('nav ul');
        if (!paginationContainer) return;

        paginationContainer.innerHTML = '';

        if (totalPages <= 1) {
            paginationContainer.style.display = 'none';
            return;
        }

        paginationContainer.style.display = 'flex';

        // Calculate the range of pages to show (5 pages max)
        const maxVisiblePages = 5;
        let startPage, endPage;

        if (totalPages <= maxVisiblePages) {
            // Show all pages if total is 5 or less
            startPage = 1;
            endPage = totalPages;
        } else {
            // Calculate start and end based on current page
            const halfVisible = Math.floor(maxVisiblePages / 2);
            
            if (currentPage <= halfVisible + 1) {
                // Near the beginning
                startPage = 1;
                endPage = maxVisiblePages;
            } else if (currentPage >= totalPages - halfVisible) {
                // Near the end
                startPage = totalPages - maxVisiblePages + 1;
                endPage = totalPages;
            } else {
                // In the middle
                startPage = currentPage - halfVisible;
                endPage = currentPage + halfVisible;
            }
        }

        // Previous button
        const prevLi = document.createElement('li');
        if (currentPage === 1) {
            prevLi.innerHTML = '<span class="px-3 py-1 rounded-lg text-gray-400 cursor-not-allowed"><</span>';
        } else {
            prevLi.innerHTML = `<button class="px-3 py-1 rounded-lg text-black hover:bg-gray-100" onclick="changePage(${currentPage - 1})"><</button>`;
        }
        paginationContainer.appendChild(prevLi);

        // First page + ellipsis (if needed)
        if (startPage > 1) {
            const firstLi = document.createElement('li');
            firstLi.innerHTML = `<button class="px-3 py-1 rounded-lg text-black hover:bg-gray-100" onclick="changePage(1)">1</button>`;
            paginationContainer.appendChild(firstLi);

            if (startPage > 2) {
                const ellipsisLi = document.createElement('li');
                ellipsisLi.innerHTML = '<span class="px-3 py-1 text-gray-400">...</span>';
                paginationContainer.appendChild(ellipsisLi);
            }
        }

        // Page numbers in range
        for (let i = startPage; i <= endPage; i++) {
            const pageLi = document.createElement('li');
            const isActive = i === currentPage;
            pageLi.innerHTML = `
                <button class="px-3 py-1 rounded-lg ${isActive ? 'bg-[#4D0F0F] text-white' : 'text-black hover:bg-gray-100'}" 
                        onclick="changePage(${i})">${i}</button>
            `;
            paginationContainer.appendChild(pageLi);
        }

        // Last page + ellipsis (if needed)
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const ellipsisLi = document.createElement('li');
                ellipsisLi.innerHTML = '<span class="px-3 py-1 text-gray-400">...</span>';
                paginationContainer.appendChild(ellipsisLi);
            }

            const lastLi = document.createElement('li');
            lastLi.innerHTML = `<button class="px-3 py-1 rounded-lg text-black hover:bg-gray-100" onclick="changePage(${totalPages})">${totalPages}</button>`;
            paginationContainer.appendChild(lastLi);
        }

        // Next button
        const nextLi = document.createElement('li');
        if (currentPage === totalPages) {
            nextLi.innerHTML = '<span class="px-3 py-1 rounded-lg text-gray-400 cursor-not-allowed">></span>';
        } else {
            nextLi.innerHTML = `<button class="px-3 py-1 rounded-lg text-black hover:bg-gray-100" onclick="changePage(${currentPage + 1})">></button>`;
        }
        paginationContainer.appendChild(nextLi);
    }

    // Change page function (make it global)
    window.changePage = function(page) {
        currentPage = page;
        const searchTerm = searchInput.value.trim();
        let displayActivities = filteredActivities;
        
        if (searchTerm) {
            displayActivities = searchActivities(filteredActivities, searchTerm);
        }
        
        renderActivities(displayActivities, searchTerm);
    };

    // Create activity row element
    function createActivityRow(activity) {
        const row = document.createElement('tr');
        row.className = 'h-16';
        
        const dateObj = new Date(activity.created_at);
        const formattedDate = dateObj.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        const formattedTime = dateObj.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        });

        // Determine display name and role
        let displayName = 'Unknown User';
        let roleName = '';
        let profilePic = '{{ asset("images/dprofile.svg") }}';

        if (activity.user) {
            if (['Admin', 'Superadmin'].includes(activity.user.role_name)) {
                displayName = activity.user.role_name;
            } else {
                displayName = activity.user.username;
            }
            roleName = activity.user.role_name;
            
            if (activity.user.profile_pic) {
                profilePic = `{{ asset('storage/') }}/${activity.user.profile_pic}`;
            }
        }

        row.innerHTML = `
            <td class="w-[10%] px-13 py-2 whitespace-nowrap text-l text-[#000000] font-[Lexend]">
                <div>${formattedDate}</div>
                <div class="text-m text-gray-500">${formattedTime}</div>
            </td>
            <td class="w-[25%] px-6 py-2 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="h-8 w-8 flex-shrink-0">
                        <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-200">
                            <img src="${profilePic}" alt="Profile" class="w-full h-full object-cover">
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="text-l font-semibold text-gray-900 font-[Lexend] max-w-[450px] truncate">
                            <span class="font-semibold text-[18px] text-black text[Lexend]">
                                ${displayName}
                            </span>
                        </div>
                        <div class="text-l text-gray-500 font-[Lexend] max-w-[450px] truncate">
                            ${roleName}
                        </div>
                    </div>
                </div>
            </td>
            <td class="w-[10%] px-6 py-1 whitespace-nowrap">
                <span class="inline-flex px-3 py-1 text-l font-semibold bg-gray-100 text-gray-700 rounded-full font-[Lexend]">
                    ${activity.action ? activity.action.toUpperCase() : ''}
                </span>
            </td>
            <td class="w-[10%] px-6 py-2 whitespace-nowrap">
                <span class="inline-flex px-2 py-1 text-l font-medium rounded bg-gray-100 text-gray-700 font-[Lexend]">
                    ${activity.target || ''}
                </span>
            </td>
            <td class="w-[25%] px-6 py-2 text-l text-gray-900 font-[Lexend]">
                <div class="max-w-xs truncate" title="${activity.description || ''}">
                    ${activity.description || ''}
                </div>
            </td>
        `;

        return row;
    }

    // Reset to first page when applying filters or searching
    function resetToFirstPage() {
        currentPage = 1;
    }

    // Filter activities based on criteria
    function filterActivities(activities, filterType, startDate = null, endDate = null) {
        const now = new Date();
        
        return activities.filter(activity => {
            const activityDate = new Date(activity.created_at);
            
            switch(filterType) {
                case 'today':
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const actToday = new Date(activityDate);
                    actToday.setHours(0, 0, 0, 0);
                    return actToday.getTime() === today.getTime();
                    
                case 'this-week':
                    const weekStart = new Date(now);
                    weekStart.setDate(now.getDate() - now.getDay());
                    weekStart.setHours(0, 0, 0, 0);
                    const weekEnd = new Date(weekStart);
                    weekEnd.setDate(weekStart.getDate() + 6);
                    weekEnd.setHours(23, 59, 59, 999);
                    return activityDate >= weekStart && activityDate <= weekEnd;
                    
                case 'this-month':
                    return activityDate.getMonth() === now.getMonth() && 
                           activityDate.getFullYear() === now.getFullYear();
                           
                case 'custom':
                    if (startDate && endDate) {
                        const start = new Date(startDate);
                        start.setHours(0, 0, 0, 0);
                        const end = new Date(endDate);
                        end.setHours(23, 59, 59, 999);
                        return activityDate >= start && activityDate <= end;
                    } else if (startDate) {
                        const start = new Date(startDate);
                        start.setHours(0, 0, 0, 0);
                        return activityDate >= start;
                    } else if (endDate) {
                        const end = new Date(endDate);
                        end.setHours(23, 59, 59, 999);
                        return activityDate <= end;
                    }
                    return true;
                    
                default:
                    return true;
            }
        });
    }

    // Sort activities
    function sortActivities(activities, sortType) {
        const sorted = [...activities];
        
        if (sortType === 'newest') {
            return sorted.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        } else if (sortType === 'oldest') {
            return sorted.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        }
        
        return sorted;
    }

    // Search activities
    function searchActivities(activities, searchTerm) {
        if (!searchTerm) return activities;
        
        const term = searchTerm.toLowerCase();
        return activities.filter(activity => {
            const dateStr = new Date(activity.created_at).toLocaleDateString();
            const name = activity.user ? 
                ((['Admin', 'Superadmin'].includes(activity.user.role_name)) ? 
                 activity.user.role_name : activity.user.username) : 'Unknown User';
            const action = activity.action || '';
            const target = activity.target || '';
            const description = activity.description || '';
            const roleName = activity.user ? activity.user.role_name : '';
            
            return dateStr.toLowerCase().includes(term) ||
                   name.toLowerCase().includes(term) ||
                   action.toLowerCase().includes(term) ||
                   target.toLowerCase().includes(term) ||
                   description.toLowerCase().includes(term) ||
                   roleName.toLowerCase().includes(term);
        });
    }

    // Highlight search terms
    function highlightSearchTerm(row, searchTerm) {
        if (!searchTerm) return;
        
        const cells = row.querySelectorAll('td');
        cells.forEach(cell => {
            const text = cell.textContent;
            if (text.toLowerCase().includes(searchTerm.toLowerCase())) {
                const regex = new RegExp(`(${searchTerm})`, 'gi');
                cell.innerHTML = cell.innerHTML.replace(regex, '<mark style="background-color: yellow;">$1</mark>');
            }
        });
    }

    // Initialize page
    async function initializePage() {
        const success = await fetchAllActivities();
        if (!success) {
            console.error('Failed to load activity data');
            return;
        }
        renderActivities(filteredActivities);
    }

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

    // Quick filter buttons
    quickFilterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filterType = this.dataset.filter;
            
            // Store current filter state - ADD THIS
            currentFilterType = filterType;
            if (filterType !== 'custom') {
                currentStartDate = null;
                currentEndDate = null;
            }
            
            // Reset to first page
            resetToFirstPage();
            
            // Reset custom range inputs
            startDateInput.value = '';
            endDateInput.value = '';

            // Remove active state from all buttons
            quickFilterBtns.forEach(b => b.classList.remove('bg-[#F5E6E6]'));
            // Add active state to clicked button
            this.classList.add('bg-[#F5E6E6]');

            // Apply filter
            if (filterType === 'newest' || filterType === 'oldest') {
                filteredActivities = sortActivities(allActivities, filterType);
            } else {
                filteredActivities = filterActivities(allActivities, filterType);
            }

            // Apply current search term if exists
            const searchTerm = searchInput.value.trim();
            currentSearchTerm = searchTerm; // ADD THIS
            if (searchTerm) {
                filteredActivities = searchActivities(filteredActivities, searchTerm);
            }

            renderActivities(filteredActivities, searchTerm);
            filterModal.classList.add('hidden');
        });
    });

    // Apply custom range filter
    applyFiltersBtn.addEventListener('click', function() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        
        // Validate date range
        if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
            alert('Start date cannot be after end date');
            return;
        }

        // Store current filter state - ADD THIS
        currentFilterType = 'custom';
        currentStartDate = startDate ? new Date(startDate) : null;
        currentEndDate = endDate ? new Date(endDate) : null;

        // Reset to first page
        resetToFirstPage();

        // Remove active state from quick filter buttons
        quickFilterBtns.forEach(btn => btn.classList.remove('bg-[#F5E6E6]'));

        // Apply custom filter
        filteredActivities = filterActivities(allActivities, 'custom', startDate, endDate);

        // Apply current search term if exists
        const searchTerm = searchInput.value.trim();
        currentSearchTerm = searchTerm; // ADD THIS
        if (searchTerm) {
            filteredActivities = searchActivities(filteredActivities, searchTerm);
        }

        renderActivities(filteredActivities, searchTerm);
        filterModal.classList.add('hidden');
    });

    // Clear filters
    clearFiltersBtn.addEventListener('click', function() {
        clearAllFiltersAndSearch();
    });

    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        currentSearchTerm = searchTerm; // ADD THIS
        
        // Reset to first page when searching
        resetToFirstPage();
        
        // Start with filtered activities (respecting any date filters)
        let searchResults = [...filteredActivities];
        
        // Apply search
        if (searchTerm) {
            searchResults = searchActivities(filteredActivities, searchTerm);
        }

        renderActivities(searchResults, searchTerm);
    });

    // Clear all filters and search
    function clearAllFiltersAndSearch() {
        // Clear all input values
        searchInput.value = '';
        startDateInput.value = '';
        endDateInput.value = '';

        // Reset filter state - ADD THIS
        currentFilterType = null;
        currentStartDate = null;
        currentEndDate = null;
        currentSearchTerm = '';

        // Reset to first page
        resetToFirstPage();

        // Remove active state from quick filter buttons
        quickFilterBtns.forEach(btn => btn.classList.remove('bg-[#F5E6E6]'));

        // Reset to show all activities
        filteredActivities = [...allActivities];
        renderActivities(filteredActivities);

        // Hide filter modal
        filterModal.classList.add('hidden');
    }

    // Clear search button
    clearSearchBtn.addEventListener('click', clearAllFiltersAndSearch);

    // Initialize the page
    initializePage();

    // PDF Generation functionality (inline solution)
    const generatePDFBtn = document.getElementById('generatePDFBtn');

    if (generatePDFBtn) {
        generatePDFBtn.addEventListener('click', async function() {
            const button = this;
            const originalContent = button.innerHTML;

            button.innerHTML = `
                <svg class="animate-spin h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Generating PDF...
            `;
            button.disabled = true;

            // Use existing data if available, otherwise fetch
            let activities = [];
            if (allActivities.length > 0) {
                activities = [...allActivities];
            } else {
                try {
                    const response = await fetch('/super-admin/activity-logs/all');
                    activities = await response.json();
                } catch (e) {
                    alert('Failed to fetch all activity logs.');
                    button.innerHTML = originalContent;
                    button.disabled = false;
                    return;
                }
            }

            if (!activities.length) {
                alert('No activity logs found.');
                button.innerHTML = originalContent;
                button.disabled = false;
                return;
            }

            // Apply current filters to get the data that should be in the PDF - MODIFIED
            const filteredData = applyCurrentFilters(activities);

            if (!filteredData.length) {
                alert('No activity logs found matching the current filter.');
                button.innerHTML = originalContent;
                button.disabled = false;
                return;
            }

            // Get filter description - ADDED
            const { description, recordCount } = getFilterDescription();

            // Create a container for PDF content
            const pdfContainer = document.createElement('div');
            pdfContainer.style.cssText = `
                padding: 20px;
                background-color: white;
                font-family: Arial, sans-serif;
                color: black;
                width: 100%;
            `;

            // Add this style to prevent row breaks
            const style = document.createElement('style');
            style.textContent = `
                table, tr, td, th, tbody, thead, tfoot {
                    page-break-inside: avoid !important;
                    break-inside: avoid !important;
                }
            `;
            pdfContainer.appendChild(style);

            // Add header with filter information - OPTIMIZED FOR SPACE
            const header = document.createElement('div');
            header.innerHTML = `
                <div style="text-align: center; margin-bottom: 15px;">
                    <h1 style="color: #4D0F0F; font-size: 20px; margin-bottom: 5px; font-family: Arial, sans-serif;">Activity Logs Report</h1>
                    <p style="color: #666; font-size: 11px; font-family: Arial, sans-serif; margin: 2px 0;">Generated on ${new Date().toLocaleString()}</p>
                    <p style="color: #4D0F0F; font-size: 12px; font-weight: bold; margin: 3px 0; font-family: Arial, sans-serif;">${description}</p>
                    <p style="color: #666; font-size: 10px; font-family: Arial, sans-serif; margin: 2px 0;">${recordCount}</p>
                    <hr style="border: 0.5px solid #4D0F0F; margin: 10px 0;">
                </div>
            `;
            pdfContainer.appendChild(header);

            // Create a clean table for PDF
            const cleanTable = document.createElement('table');
            cleanTable.style.cssText = `
                width: 100%;
                border-collapse: collapse;
                font-family: Arial, sans-serif;
                margin-top: 20px;
            `;

            // Create table header - OPTIMIZED
            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');
            const headers = ['Timestamp', 'Name', 'Action', 'Target', 'Description'];
            headers.forEach(headerText => {
                const th = document.createElement('th');
                th.style.cssText = `
                    background-color: #4D0F0F;
                    color: white;
                    padding: 8px 6px;
                    border: 1px solid #ddd;
                    text-align: left;
                    font-size: 12px;
                    font-weight: bold;
                    font-family: Arial, sans-serif;
                `;
                th.textContent = headerText;
                headerRow.appendChild(th);
            });
            thead.appendChild(headerRow);
            cleanTable.appendChild(thead);

            // Create table body with filtered data - MODIFIED to use filteredData
            const tbody = document.createElement('tbody');
            filteredData.forEach((activity, index) => {
                const newRow = document.createElement('tr');
                newRow.style.cssText = `
                    background-color: ${index % 2 === 0 ? 'white' : '#f9f9f9'};
                `;

                // Timestamp cell - OPTIMIZED
                const timestampCell = document.createElement('td');
                const dateObj = new Date(activity.created_at);
                timestampCell.style.cssText = `
                    padding: 6px;
                    border: 1px solid #ddd;
                    font-size: 10px;
                    vertical-align: top;
                    font-family: Arial, sans-serif;
                `;
                timestampCell.innerHTML = `
                    <div style="font-weight: bold;">${dateObj.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' })}</div>
                    <div style="color: #666; font-size: 10px;">${dateObj.toLocaleTimeString()}</div>
                `;
                newRow.appendChild(timestampCell);

                // Name cell - OPTIMIZED
                const nameCell = document.createElement('td');
                let name = 'Unknown User';
                let roleName = '';
                if (activity.user) {
                    if (['Admin', 'Superadmin'].includes(activity.user.role_name)) {
                        name = activity.user.role_name;
                    } else {
                        name = activity.user.username;
                    }
                    roleName = activity.user.role_name;
                }
                nameCell.style.cssText = `
                    padding: 6px;
                    border: 1px solid #ddd;
                    font-size: 10px;
                    vertical-align: top;
                    font-family: Arial, sans-serif;
                `;
                nameCell.innerHTML = `
                    <div style="font-weight: bold;">${name}</div>
                    <div style="color: #666; font-size: 10px;">${roleName}</div>
                `;
                newRow.appendChild(nameCell);

                // Action cell - OPTIMIZED
                const actionCell = document.createElement('td');
                actionCell.style.cssText = `
                    padding: 6px;
                    border: 1px solid #ddd;
                    font-size: 10px;
                    vertical-align: top;
                    font-family: Arial, sans-serif;
                `;
                actionCell.innerHTML = `
                    <span style="background-color: #f3f4f6; padding: 4px 8px; border-radius: 12px; font-size: 10px;">
                        ${activity.action ? activity.action.toUpperCase() : ''}
                    </span>
                `;
                newRow.appendChild(actionCell);

                // Target cell - OPTIMIZED
                const targetCell = document.createElement('td');
                targetCell.style.cssText = `
                    padding: 6px;
                    border: 1px solid #ddd;
                    font-size: 10px;
                    vertical-align: top;
                    font-family: Arial, sans-serif;
                `;
                targetCell.innerHTML = `
                    <span style="background-color: #f3f4f6; padding: 4px 8px; border-radius: 4px; font-size: 10px;">
                        ${activity.target || ''}
                    </span>
                `;
                newRow.appendChild(targetCell);

                // Description cell - OPTIMIZED
                const descCell = document.createElement('td');
                descCell.style.cssText = `
                    padding: 6px;
                    border: 1px solid #ddd;
                    font-size: 10px;
                    vertical-align: top;
                    word-wrap: break-word;
                    max-width: 200px;
                    font-family: Arial, sans-serif;
                `;
                descCell.textContent = activity.description || '';
                newRow.appendChild(descCell);

                tbody.appendChild(newRow);
            });

            cleanTable.appendChild(tbody);
            pdfContainer.appendChild(cleanTable);

            // Generate filename based on current filters - MODIFIED
            let filename = 'activity-logs';
            if (currentFilterType) {
                switch(currentFilterType) {
                    case 'today':
                        filename += '-today';
                        break;
                    case 'this-week':
                        filename += '-this-week';
                        break;
                    case 'this-month':
                        filename += '-this-month';
                        break;
                    case 'custom':
                        filename += '-custom-range';
                        break;
                    case 'newest':
                        filename += '-newest-first';
                        break;
                    case 'oldest':
                        filename += '-oldest-first';
                        break;
                }
            }
            if (currentSearchTerm) {
                filename += '-search';
            }
            filename += `-${new Date().toISOString().split('T')[0]}.pdf`;

            // PDF generation options - OPTIMIZED
            const opt = {
                margin: [8, 8, 8, 8], // Reduced margins
                filename: filename,
                image: { 
                    type: 'jpeg', 
                    quality: 0.98 
                },
                html2canvas: { 
                    scale: 1.3, // Slightly reduced scale
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    removeContainer: true
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: 'a4', 
                    orientation: 'landscape' 
                }
            };

            // Generate and download PDF
            html2pdf().set(opt).from(pdfContainer).save().then(() => {
                button.innerHTML = originalContent;
                button.disabled = false;
            }).catch(error => {
                console.error('PDF generation failed:', error);
                alert('Failed to generate PDF. Please try again.');
                button.innerHTML = originalContent;
                button.disabled = false;
            });
        });
    }

    // Helper function to apply current filters to activities
    function applyCurrentFilters(activities) {
        let result = [...activities];
        
        // Apply date/sort filters
        if (currentFilterType) {
            if (currentFilterType === 'newest' || currentFilterType === 'oldest') {
                result = sortActivities(result, currentFilterType);
            } else if (currentFilterType === 'custom') {
                result = filterActivities(result, 'custom', currentStartDate, currentEndDate);
            } else {
                result = filterActivities(result, currentFilterType);
            }
        }
        
        // Apply search filter
        if (currentSearchTerm) {
            result = searchActivities(result, currentSearchTerm);
        }
        
        return result;
    }

    // Generate filter description for PDF header
    function getFilterDescription() {
        if (!currentFilterType && !currentSearchTerm) {
            return {
                description: 'All Activity Logs',
                recordCount: `Total Records: ${allActivities.length}`
            };
        }
        
        let filterParts = [];
        
        if (currentFilterType) {
            switch(currentFilterType) {
                case 'today':
                    filterParts.push("Today's Activities");
                    break;
                case 'this-week':
                    filterParts.push("This Week's Activities");
                    break;
                case 'this-month':
                    filterParts.push("This Month's Activities");
                    break;
                case 'newest':
                    filterParts.push('All Activities (Newest First)');
                    break;
                case 'oldest':
                    filterParts.push('All Activities (Oldest First)');
                    break;
                case 'custom':
                    let customDesc = 'Custom Date Range';
                    if (currentStartDate && currentEndDate) {
                        customDesc += ` (${currentStartDate.toLocaleDateString()} - ${currentEndDate.toLocaleDateString()})`;
                    } else if (currentStartDate) {
                        customDesc += ` (From ${currentStartDate.toLocaleDateString()})`;
                    } else if (currentEndDate) {
                        customDesc += ` (Until ${currentEndDate.toLocaleDateString()})`;
                    }
                    filterParts.push(customDesc);
                    break;
            }
        }
        
        if (currentSearchTerm) {
            filterParts.push(`Search: "${currentSearchTerm}"`);
        }
        
        const filteredData = applyCurrentFilters(allActivities);
        return {
            description: filterParts.join(' | '),
            recordCount: `Filtered Records: ${filteredData.length} of ${allActivities.length}`
        };
    }
});
</script>
@endsection
