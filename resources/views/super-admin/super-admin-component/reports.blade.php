@extends('base')<!-- Extend the base component -->
@section('content')<!-- Content section -->
<!-- This is the main content area for the super admin dashboard -->
@include('components.superAdminNavigation')

<div class="max-h-screen bg-[#F2F4F7] bg-opacity-30 px-10 py-8">
            <div class="flex justify-between items-center mb-4">
                <!-- Back to Dashboard Button -->
                <a href="{{ route('super-admin.dashboard') }}"
                    class="bg-[#F2F4F7] hover:text-red-800 text-[#7A1212] px-4 py-2 rounded-[16px] font-sm font-[Lexend] inline-flex items-center self-start mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>
    <div class="bg-white rounded-[25px] shadow-lg overflow-hidden" style= "width: 100%; height: 725px; flex-shrink:0;">
    <!-- Header with title and filters -->
        <div class="px-8 py-4 flex justify-between items-center">
            <h2 class="text-[30px] font-bold text-[#161616] font-[Lexend]">REPORTS</h2>
            
            <!-- Header Actions -->
            <div class="flex items-center space-x-3">
                <!-- Search Box -->
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Search reports..."
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
                    class="bg-white border border-gray-300 hover:bg-[#F5E6E6] text-gray-700 px-2 py-1 rounded-lg font-[Lexend] inline-flex items-center cursor-pointer transition duration-200" 
                    id="openFilterModal">
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
        <!-- Reports Table Container with vertical scrollbar -->
        <div class="overflow-x-auto rounded-md mx-auto">
            <!-- Reports Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white">
                        <tr>
                            <th class="w-[10%] px-12 py-4 text-left text-xl font-semibold text-[#000000] font-[Lexend]">
                                <div class="flex items-center">
                                    <span class="whitespace-nowrap text-black">Timestamp</span>
                                    <div class="flex flex-col ml-2">
                                       
                                    </div>
                                </div>
                            </th>
                            <th class="w-[15%] px-6 py-4 text-left text-xl font-semibold text-[#000000] font-[Lexend]">
                                <div class="flex items-center">
                                    <span class="whitespace-nowrap text-black">Report ID</span>
                                    <div class="flex flex-col ml-2">
                                      
                                    </div>
                                </div>
                            </th>
                            <th class="w-[25%] px-6 py-4 text-left text-xl font-semibold text-[#000000] font-[Lexend]">
                                <div class="flex items-center">
                                    <span class="whitespace-nowrap">Email</span>
                                    <div class="flex flex-col ml-2">
                                     
                                    </div>
                                </div>
                            </th>
                            <th class="w-[45%] px-6 py-4 text-left text-xl font-semibold text-[#000000] font-[Lexend]">
                                <span class="whitespace-nowrap">Problem Description</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#D9D9D9]/70">
                    <!-- No Results Row - Initially Hidden -->
                    <tr id="noResultsRow" class="hidden">
                        <td colspan="4" class="text-center py-12 text-gray-500 font-[Lexend]">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>
                                <span class="text-lg font-semibold mb-2">No reports found</span>
                                <span class="text-l text-gray-400">Try adjusting your search or filter to find what you're looking for.</span>
                                <button id="clearSearchBtn"
                                    class="mt-6 px-4 py-2 bg-[#7A1212] text-white rounded-lg font-[Lexend] hover:bg-red-800 transition cursor-pointer focus:outline-none focus:ring-0">
                                    Clear Search
                                </button>
                            </div>
                        </td>
                    </tr>
    
                    <!-- Data Rows -->
                    @forelse($reports as $report)
                        <tr class="cursor-pointer hover:bg-gray-50" 
                                onclick="openReportModal({{ 
 
                                    json_encode([
                                        'id' => $report->id,
                                        'created_at' => $report->created_at,
                                        'email' => $report->email,
                                        'description' => $report->description,
                                       'attachment' => $report->file_path ? asset('storage/' . $report->file_path) : null
                                    ]) 
                            }})">
                            <td class="w-[10%] px-13 py-2 whitespace-nowrap text-l text-[#000000] font-[Lexend]">
                                {{ $report->created_at->format('F j, Y') }}<br>
                                <span class="text-m text-gray-500">{{ $report->created_at->format('h:i A') }}</span>
                            </td>
                            <td class="w-[15%] px-6 py-1 whitespace-nowrap">
                                RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="w-[25%] px-6 py-2 whitespace-nowrap">
                                {{ $report->email }}
                            </td>
                            <td class="w-[45%] px-6 py-2 text-l text-gray-900 font-[Lexend] max-w-l truncate">
                                <div class="max-w-full overflow-hidden text-ellipsis">
                                    {{ Str::limit($report->description, 85) }}
                                </div>
                            </td>
                        </tr>
                        @empty
                        @endforelse
                        </tbody>
                    </table>
                </div>
              <!-- Pagination always visible -->
                <div class="mt-4 flex justify-center mb-1 absolute bottom-10 left-0 w-full p-2 text-center">
                    <nav>
                        <ul class="inline-flex items-center space-x-2">
                            <!-- First/Previous Page -->
                            <li>
                                @if ($reports->currentPage() == 1)
                                    <span class="px-3 py-1 rounded-lg text-gray-400 cursor-not-allowed">
                                        <
                                    </span>
                                @else
                                    <a href="{{ $reports->url(1) }}" 
                                       class="px-3 py-1 rounded-lg text-black">
                                        <
                                    </a>
                                @endif
                            </li>

                            <!-- Page Numbers -->
                            @for ($i = 1; $i <= $reports->lastPage(); $i++)
                                <li>
                                    <a href="{{ $reports->url($i) }}"
                                        class="px-3 py-1 rounded-lg {{ $reports->currentPage() == $i ? 'bg-[#4D0F0F] text-white' : 'text-black' }}">
                                        {{ $i }}
                                    </a>
                                </li>
                            @endfor

                            <!-- Next/Last Page -->
                            <li>
                                @if ($reports->currentPage() == $reports->lastPage())
                                    <span class="px-3 py-1 rounded-lg text-gray-400 cursor-not-allowed">
                                        >
                                    </span>
                                @else
                                    <a href="{{ $reports->url($reports->lastPage()) }}" 
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
</div>


<div id="reportModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white w-full h-full relative flex flex-col">
        <!-- Fixed navigation header at top -->
        <div class="sticky top-0 z-20 bg-white">
            @include('components.superAdminNavigation')
        </div>
        <!-- Main content area -->
        <div class="flex-1 bg-gray-100 px-15 py-8 overflow-y-auto">
            <!-- SUPER ADMIN Header -->
            <div class="px-6 py-4 flex justify-between items-center">
                <h1 class="text-[40px] font-bold text-[#161616] font-[Lexend]">REPORT</h1>
            </div>
                <!-- Report Card Container -->
            <div class="overflow-hidden rounded-[25px] shadow bg-[#FFFFFFA6] mt-8" style="width: 100%; height: 450px; flex-shrink:0;">
                <div class="overflow-hidden rounded-[25px] shadow bg-[#FFFFFFA6]" style="width: 100%; height: 450px; flex-shrink:0;">

                    <!-- Report Header with close button -->
                    <div class="bg-white px-6 py-4 border-b border-gray-300 flex justify-center items-center relative">
                        <h2 id="modalReportId" class="text-[30px] font-semibold text-black font-[Lexend]">REPORT-001</h2>
                        <button onclick="closeReportModal()" class="absolute right-6 w-6 h-6 bg-[#7A1212] text-white rounded flex items-center justify-center hover:bg-red-700 transition-colors cursor-pointer ">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Report Content -->
                    <div class="px-12 py-6 space-y-4">
                        <!-- Timestamp -->
                        <div class="text-m text-gray-800 font-[Lexend]">
                            <span id="modalTimestamp"></span>
                        </div>
                        <!-- Email -->
                        <div>
                            <span class="text-xl font-semibold text-black font-[Lexend]">Email: </span>
                            <span id="modalEmail" class="text-l text-black font-[Lexend]"></span>
                        </div>
                        <!-- Problem Description -->
                        <div>
                            <div class="text-xl font-semibold text-black font-[Lexend]">Problem Description:</div>
                            <div id="modalDescription" class="text-l text-black font-[Lexend] leading-relaxed whitespace-pre-wrap" style="word-wrap: break-word; max-width: 100%;"></div>
                        </div>
                        <!-- Attachment -->
                        <div>
                            <div class="text-xl font-semibold text-black font-[Lexend] pb-2">Attachment:</div>
                            <div id="modalAttachment" class="inline-block">
                                <!-- Content will be dynamically inserted by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Email Response Button -->
                <div class="text-center mt-6">
                    <button onclick="emailResponse()" class="bg-green-600 text-white px-6 py-2 rounded-[10px] shadow font-[Lexend] text-xl font-semibold hover:bg-[#28B309] transition-colors curosr-pointer">
                        Email Response
                    </button>
                </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div id="filterModal" class="hidden absolute right-[250px] top-[245px] w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
    <div class="p-4">
        <h3 class="text-sm font-semibold text-gray-900 mb-3 font-[Lexend]">Filter Reports</h3>
        
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
    const monthFilter = document.getElementById('monthFilter');
    const yearFilter = document.getElementById('yearFilter');

    // Set current values from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('month')) {
        monthFilter.value = urlParams.get('month');
    }
    if (urlParams.get('year')) {
        yearFilter.value = urlParams.get('year');
    }

    // Handle filter changes
    function applyFilters() {
        const month = monthFilter.value;
        const year = yearFilter.value;

        const url = new URL(window.location);
        url.searchParams.delete('page'); // Reset pagination

        if (month) {
            url.searchParams.set('month', month);
        } else {
            url.searchParams.delete('month');
        }

        if (year) {
            url.searchParams.set('year', year);
        } else {
            url.searchParams.delete('year');
        }

        window.location.href = url.toString();
    }

    monthFilter.addEventListener('change', applyFilters);
    yearFilter.addEventListener('change', applyFilters);
});

// Modal functions
function openReportModal(report) {
    // Mark report as viewed via AJAX
    fetch(`/reports/${report.id}/mark-as-viewed`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    }).then(response => response.json())
      .then(data => {
          // Refresh the notification count
          const notificationBadge = document.querySelector('.absolute.bg-red-500.rounded-full');
          if (notificationBadge) {
              const newCount = parseInt(notificationBadge.textContent) - 1;
              if (newCount <= 0) {
                  notificationBadge.remove();
              } else {
                  notificationBadge.textContent = newCount;
              }
          }
      });

    document.getElementById('modalReportId').textContent = 'RPT-' + String(report.id).padStart(3, '0');

    const date = new Date(report.created_at);
    document.getElementById('modalTimestamp').textContent = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();

    document.getElementById('modalEmail').textContent = report.email;

    // Format description with approximately 50 words per line
    const description = report.description;
    const words = description.split(' ');
    const linesOfWords = [];
    let currentLine = [];

    for (let word of words) {
        currentLine.push(word);
        if (currentLine.length >= 50) {
            linesOfWords.push(currentLine.join(' '));
            currentLine = [];
        }
    }
    if (currentLine.length > 0) {
        linesOfWords.push(currentLine.join(' '));
    }

    document.getElementById('modalDescription').textContent = linesOfWords.join('\n');

    // Handle attachment
    const attachmentDiv = document.getElementById('modalAttachment');
    if (report.attachment) {
        attachmentDiv.innerHTML = `
            <a href="${report.attachment}" 
               target="_blank" 
               class="inline-flex items-center bg-yellow-500 px-4 py-2 rounded text-black font-bold hover:bg-yellow-600 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                    </path>
                </svg>
                <span class="whitespace-nowrap">View Attachment</span>
            </a>`;
    } else {
        attachmentDiv.innerHTML = `
        <span class="inline-block bg-yellow-500 px-4 py-2 rounded text-black font-bold">
            No attachment provided
        </span>`;
    }

    // Show modal
    const modal = document.getElementById('reportModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeReportModal() {
    const modal = document.getElementById('reportModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function emailResponse() {
    const email = document.getElementById('modalEmail').textContent;
    const reportId = document.getElementById('modalReportId').textContent;

    // Create mailto link
    const subject = encodeURIComponent('Response to ' + reportId);
    const mailtoLink = 'mailto:' + email + '?subject=' + subject;

    window.location.href = mailtoLink;
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('reportModal');
    if (event.target === modal) {
        closeReportModal();
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all required elements
    const filterModal = document.getElementById('filterModal');
    const openFilterModalBtn = document.getElementById('openFilterModal');
    const applyFiltersBtn = document.getElementById('applyFilters');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const tableBody = document.querySelector('tbody');
    const noResultsRow = document.getElementById('noResultsRow');
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const quickFilterBtns = document.querySelectorAll('[data-filter]');

    // Toggle filter modal
    openFilterModalBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        const buttonRect = this.getBoundingClientRect();
        filterModal.style.top = `${buttonRect.bottom + 5}px`;
        filterModal.style.right = `${window.innerWidth - buttonRect.right}px`;
        filterModal.classList.toggle('hidden');
    });

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        if (!filterModal.contains(e.target) && e.target !== openFilterModalBtn) {
            filterModal.classList.add('hidden');
        }
    });

    // Parse date string to Date object
    function parseDate(dateStr) {
        const parts = dateStr.split(',')[0].trim().split(' ');
        const month = new Date(Date.parse(parts[0] + " 1, 2012")).getMonth();
        const day = parseInt(parts[1]);
        const year = parseInt(parts[2] || new Date().getFullYear());
        return new Date(year, month, day);
    }

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
                if (!dateCell) return;
                
                const dateText = dateCell.childNodes[0].textContent.trim();
                const rowDate = parseDate(dateText);
                let showRow = false;

                switch(filterType) {
                    case 'newest':
                        showRow = true;
                        rows.sort((a, b) => {
                            const dateA = parseDate(a.querySelector('td:first-child').childNodes[0].textContent.trim());
                            const dateB = parseDate(b.querySelector('td:first-child').childNodes[0].textContent.trim());
                            return dateB - dateA;
                        });
                        break;
                    case 'oldest':
                        showRow = true;
                        rows.sort((a, b) => {
                            const dateA = parseDate(a.querySelector('td:first-child').childNodes[0].textContent.trim());
                            const dateB = parseDate(b.querySelector('td:first-child').childNodes[0].textContent.trim());
                            return dateA - dateB;
                        });
                        break;
                    case 'today':
                        showRow = rowDate.toDateString() === now.toDateString();
                        break;
                    case 'this-week':
                        const weekStart = new Date(now);
                        weekStart.setDate(now.getDate() - now.getDay());
                        weekStart.setHours(0, 0, 0, 0);
                        const weekEnd = new Date(weekStart);
                        weekEnd.setDate(weekStart.getDate() + 6);
                        weekEnd.setHours(23, 59, 59, 999);
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

            if (filterType === 'newest' || filterType === 'oldest') {
                rows.forEach(row => tableBody.appendChild(row));
            }

            noResultsRow.classList.toggle('hidden', visibleCount > 0);
        });
    });

    // Apply custom range filter
    applyFiltersBtn.addEventListener('click', function() {
        const startDate = startDateInput.value ? new Date(startDateInput.value) : null;
        const endDate = endDateInput.value ? new Date(endDateInput.value) : null;
        
        if (startDate) startDate.setHours(0, 0, 0, 0);
        if (endDate) endDate.setHours(23, 59, 59, 999);

        quickFilterBtns.forEach(btn => btn.classList.remove('bg-[#F5E6E6]'));

        const rows = Array.from(tableBody.querySelectorAll('tr:not(#noResultsRow)'));
        let visibleCount = 0;

        rows.forEach(row => {
            const dateCell = row.querySelector('td:first-child');
            if (!dateCell) return;

            const dateText = dateCell.childNodes[0].textContent.trim();
            const rowDate = parseDate(dateText);
            
            let showRow = true;
            if (startDate && endDate) {
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

    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = Array.from(tableBody.querySelectorAll('tr:not(#noResultsRow)'));
        let visibleCount = 0;

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const matchesSearch = Array.from(cells).some(cell => 
                cell.textContent.toLowerCase().includes(searchTerm)
            );

            row.style.display = matchesSearch ? '' : 'none';
            if (matchesSearch) visibleCount++;
        });

        noResultsRow.classList.toggle('hidden', visibleCount > 0);
    });

    // Clear all filters and search
    function clearAllFiltersAndSearch() {
        searchInput.value = '';
        startDateInput.value = '';
        endDateInput.value = '';
        
        quickFilterBtns.forEach(btn => btn.classList.remove('bg-[#F5E6E6]'));

        const rows = Array.from(tableBody.querySelectorAll('tr:not(#noResultsRow)'));
        rows.forEach(row => {
            row.style.display = '';
            row.style.order = '';
        });

        noResultsRow.classList.add('hidden');
        filterModal.classList.add('hidden');
    }

    clearFiltersBtn.addEventListener('click', clearAllFiltersAndSearch);
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', clearAllFiltersAndSearch);
    }

    // PDF Generation functionality for reports
    const generatePDFBtn = document.getElementById('generatePDFBtn');

    if (generatePDFBtn) {
        generatePDFBtn.addEventListener('click', function() {
            const button = this;
            const originalContent = button.innerHTML;
            
            // Show loading state
            button.innerHTML = `
                <svg class="animate-spin h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Generating PDF...
            `;
            button.disabled = true;

            // Check if html2pdf is available
            if (typeof html2pdf === 'undefined') {
                alert('PDF library not loaded. Please refresh the page and try again.');
                button.innerHTML = originalContent;
                button.disabled = false;
                return;
            }

            // Create a container for PDF content
            const pdfContainer = document.createElement('div');
            pdfContainer.style.cssText = `
                padding: 20px;
                background-color: white;
                font-family: Arial, sans-serif;
                color: black;
                width: 100%;
            `;

            // Add header
            const header = document.createElement('div');
            header.innerHTML = `
                <div style="text-align: center; margin-bottom: 30px;">
                    <h1 style="color: #4D0F0F; font-size: 24px; margin-bottom: 10px; font-family: Arial, sans-serif;">Problem Reports Summary</h1>
                    <p style="color: #666; font-size: 14px; font-family: Arial, sans-serif;">Generated on ${new Date().toLocaleString()}</p>
                    <hr style="border: 1px solid #4D0F0F; margin-top: 20px;">
                </div>
            `;
            pdfContainer.appendChild(header);

            // Get visible table rows only (excluding no results row)
            const tableBody = document.querySelector('tbody');
            const visibleRows = Array.from(tableBody.querySelectorAll('tr:not(#noResultsRow)'))
                .filter(row => row.style.display !== 'none');

            if (visibleRows.length === 0) {
                alert('No data to export. Please adjust your filters.');
                button.innerHTML = originalContent;
                button.disabled = false;
                return;
            }

            // Create a clean table for PDF
            const cleanTable = document.createElement('table');
            cleanTable.style.cssText = `
                width: 100%;
                border-collapse: collapse;
                font-family: Arial, sans-serif;
                margin-top: 20px;
            `;

            // Create table header
            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');
            const headers = ['Timestamp', 'Report ID', 'Email', 'Problem Description'];
            
            headers.forEach(headerText => {
                const th = document.createElement('th');
                th.style.cssText = `
                    background-color: #4D0F0F;
                    color: white;
                    padding: 12px 8px;
                    border: 1px solid #ddd;
                    text-align: left;
                    font-size: 14px;
                    font-weight: bold;
                    font-family: Arial, sans-serif;
                `;
                th.textContent = headerText;
                headerRow.appendChild(th);
            });
            thead.appendChild(headerRow);
            cleanTable.appendChild(thead);

            // Create table body
            const tbody = document.createElement('tbody');
            
            visibleRows.forEach((row, index) => {
                const newRow = document.createElement('tr');
                newRow.style.cssText = `
                    background-color: ${index % 2 === 0 ? 'white' : '#f9f9f9'};
                `;

                // Extract and clean cell data
                const cells = row.querySelectorAll('td');
                
                // Timestamp cell
                const timestampCell = document.createElement('td');
                const timestampText = cells[0].textContent.trim();
                const [date, time] = timestampText.split('\n');
                timestampCell.style.cssText = `
                    padding: 8px;
                    border: 1px solid #ddd;
                    font-size: 11px;
                    vertical-align: top;
                    font-family: Arial, sans-serif;
                `;
                timestampCell.innerHTML = `
                    <div style="font-weight: bold;">${date || ''}</div>
                    <div style="color: #666; font-size: 10px;">${time || ''}</div>
                `;
                newRow.appendChild(timestampCell);

                // Report ID cell
                const reportIdCell = document.createElement('td');
                reportIdCell.style.cssText = `
                    padding: 8px;
                    border: 1px solid #ddd;
                    font-size: 11px;
                    vertical-align: top;
                    font-family: Arial, sans-serif;
                    font-weight: bold;
                `;
                reportIdCell.textContent = cells[1] ? cells[1].textContent.trim() : '';
                newRow.appendChild(reportIdCell);

                // Email cell
                const emailCell = document.createElement('td');
                emailCell.style.cssText = `
                    padding: 8px;
                    border: 1px solid #ddd;
                    font-size: 11px;
                    vertical-align: top;
                    font-family: Arial, sans-serif;
                `;
                emailCell.textContent = cells[2] ? cells[2].textContent.trim() : '';
                newRow.appendChild(emailCell);

                // Description cell
                const descCell = document.createElement('td');
                descCell.style.cssText = `
                    padding: 8px;
                    border: 1px solid #ddd;
                    font-size: 11px;
                    vertical-align: top;
                    word-wrap: break-word;
                    max-width: 300px;
                    font-family: Arial, sans-serif;
                `;
                descCell.textContent = cells[3] ? cells[3].textContent.trim() : '';
                newRow.appendChild(descCell);

                tbody.appendChild(newRow);
            });
            
            cleanTable.appendChild(tbody);
            pdfContainer.appendChild(cleanTable);

            // Add summary statistics
            const summaryDiv = document.createElement('div');
            summaryDiv.style.cssText = `
                margin-top: 30px;
                padding: 15px;
                background-color: #f9f9f9;
                border-radius: 8px;
                font-family: Arial, sans-serif;
            `;
            summaryDiv.innerHTML = `
                <h3 style="color: #4D0F0F; margin-bottom: 10px;">Summary</h3>
                <p style="margin: 5px 0;"><strong>Total Report(s):</strong> ${visibleRows.length}</p>
                <p style="margin: 5px 0;"><strong>Report Period:</strong> ${getReportPeriod(visibleRows)}</p>
            `;
            pdfContainer.appendChild(summaryDiv);

            // PDF generation options
            const opt = {
                margin: [10, 10, 10, 10],
                filename: `problem-reports-${new Date().toISOString().split('T')[0]}.pdf`,
                image: { 
                    type: 'jpeg', 
                    quality: 0.98 
                },
                html2canvas: { 
                    scale: 1.5,
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
                // Reset button state
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

    // Helper function to get report period
    function getReportPeriod(rows) {
        if (rows.length === 0) return 'No data';
        
        const dates = rows.map(row => {
            const dateText = row.querySelector('td:first-child').textContent.trim().split('\n')[0];
            return new Date(dateText);
        }).filter(date => !isNaN(date));
        
        if (dates.length === 0) return 'Invalid dates';
        
        const minDate = new Date(Math.min(...dates));
        const maxDate = new Date(Math.max(...dates));
        
        if (minDate.toDateString() === maxDate.toDateString()) {
            return minDate.toLocaleDateString();
        } else {
            return `${minDate.toLocaleDateString()} - ${maxDate.toLocaleDateString()}`;
        }
    }
});
</script>
@endsection