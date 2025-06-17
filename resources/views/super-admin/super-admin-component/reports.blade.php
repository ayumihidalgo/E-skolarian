@extends('base')<!-- Extend the base component -->
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Content section -->
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
    
    <div class="bg-white rounded-[25px] shadow-lg overflow-hidden mb-12 relative w-full max-w-full" style="height: 725px; flex-shrink:0;">
        <!-- Header with title and filters -->
        <div class="px-4 md:px-8 py-4 flex flex-col md:flex-row md:justify-between md:items-center gap-4 md:gap-0">
            <h2 class="text-2xl md:text-[30px] font-bold text-[#161616] font-[Lexend]">REPORTS</h2>
            
            <!-- Header Actions -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-3 w-full md:w-auto">
                <!-- Search Box -->
                <div class="relative w-full sm:w-64">
                    <input type="text" id="searchInput" placeholder="Search reports..."
                        class="w-full px-4 py-2 pl-10 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7A1212] focus:border-transparent font-[Lexend] transition">
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
                    <span class="sm:inline">Filter</span>
                </button>

                <!-- Export Button -->
                <button id="generatePDFBtn"
                    class="bg-[#4D0F0F] px-2 py-1 rounded-[8px] text-white font-[Lexend] hover:bg-red-800 transition duration-200 flex items-center cursor-pointer">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="sm:inline">Generate Report</span>
                </button>
            </div>
        </div>
        
        <!-- Reports Table Container -->
        <div class="overflow-x-auto rounded-md mx-auto" style="height: calc(100% - 105px);">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white">
                        <tr>
                            <th class="w-[10%] px-12 py-4 text-left text-xl font-semibold text-[#000000] font-[Lexend]">
                                <div class="flex items-center">
                                    <span class="whitespace-nowrap text-black">Timestamp</span>
                                </div>
                            </th>
                            <th class="w-[15%] px-6 py-4 text-left text-xl font-semibold text-[#000000] font-[Lexend]">
                                <div class="flex items-center">
                                    <span class="whitespace-nowrap text-black">Report ID</span>
                                </div>
                            </th>
                            <th class="w-[25%] px-6 py-4 text-left text-xl font-semibold text-[#000000] font-[Lexend]">
                                <div class="flex items-center">
                                    <span class="whitespace-nowrap">Email</span>
                                </div>
                            </th>
                            <th class="w-[45%] px-6 py-4 text-left text-xl font-semibold text-[#000000] font-[Lexend]">
                                <span class="whitespace-nowrap">Problem Description</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#D9D9D9]/70" id="reportsTableBody">
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
                            <tr class="cursor-pointer hover:bg-gray-50 {{ !$report->viewed ? 'bg-red-50 border-l-4 border-red-500' : '' }}" 
                                    onclick="openReportModal({{ 
                                        json_encode([
                                            'id' => $report->id,
                                            'created_at' => $report->created_at,
                                            'email' => $report->email,
                                            'description' => $report->description,
                                           'attachment' => $report->file_path ? asset('storage/' . $report->file_path) : null
                                        ]) 
                                }})">
                                <td class="w-[10%] px-13 py-2 whitespace-nowrap text-l {{ !$report->viewed ? 'text-red-700 font-semibold' : 'text-[#000000]' }} font-[Lexend]">
                                    {{ $report->created_at->format('F j, Y') }}<br>
                                    <span class="text-m {{ !$report->viewed ? 'text-red-500' : 'text-gray-500' }}">{{ $report->created_at->format('h:i A') }}</span>
                                </td>
                                <td class="w-[15%] px-6 py-1 whitespace-nowrap {{ !$report->viewed ? 'text-red-700 font-bold' : '' }}">
                                    @if(!$report->viewed)
                                        <div class="flex items-center">
                                            <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse mr-2"></div>
                                            RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}
                                        </div>
                                    @else
                                        RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}
                                    @endif
                                </td>
                                <td class="w-[25%] px-6 py-2 whitespace-nowrap {{ !$report->viewed ? 'text-red-700 font-semibold' : '' }}">
                                    {{ $report->email }}
                                </td>
                                <td class="w-[45%] px-6 py-2 text-l {{ !$report->viewed ? 'text-red-700 font-semibold' : 'text-gray-900' }} font-[Lexend] max-w-l truncate">
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
        </div>
        
        <!-- Pagination - Fixed at bottom with proper positioning -->
        <div class="absolute bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 py-3 rounded-b-[25px]">
            <div class="flex justify-center">
                <nav>
                    <ul class="inline-flex items-center space-x-2">
                        <!-- Pagination will be dynamically updated by JavaScript -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Report Modal (Keep existing modal code) -->
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
                        <button onclick="window.closeReportModal()" class="absolute right-6 w-6 h-6 bg-[#7A1212] text-white rounded flex items-center justify-center hover:bg-red-700 transition-colors cursor-pointer ">
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

<!-- Document Viewer Modal (PDF/Image Preview) -->
<div id="documentViewerModal" class="hidden fixed inset-0 bg-black z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white w-11/12 h-5/6 rounded-lg flex flex-col">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 id="documentTitle" class="font-semibold text-lg truncate">Document Preview</h3>
            <div class="flex items-center space-x-4">
                <!-- Tabs for Preview and Download -->
                <div class="flex items-center bg-gray-100 rounded-lg p-1">
                    <button id="previewTab" class="py-1 px-4 rounded-lg bg-blue-500 text-white cursor-pointer">Preview</button>
                    <button id="downloadTab" class="py-1 px-4 rounded-lg text-gray-700 cursor-pointer">Download</button>
                </div>
                <!-- Close Button -->
                <button onclick="closeDocumentViewer()" class="text-gray-500 hover:text-gray-700 cursor-pointer">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="flex-1 overflow-hidden">
            <!-- PDF Viewer -->
            <div id="pdfViewer" class="w-full h-full overflow-auto"></div>
            <!-- Image Viewer -->
            <div id="imageViewer" class="hidden h-full flex items-center justify-center bg-gray-100"></div>
            <!-- Download View -->
            <div id="downloadView" class="hidden h-full flex items-center justify-center bg-gray-100 flex-col p-8">
                <h3 id="downloadFileName" class="text-xl font-semibold mb-4">filename.pdf</h3>
                <p class="text-gray-600 mb-8 text-center">Click the button below to download this document</p>
                <a id="downloadButton" href="#" download class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-lg flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Document
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all required elements
    const filterModal = document.getElementById('filterModal');
    const openFilterModalBtn = document.getElementById('openFilterModal');
    const applyFiltersBtn = document.getElementById('applyFilters');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const tableBody = document.getElementById('reportsTableBody');
    const noResultsRow = document.getElementById('noResultsRow');
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const quickFilterBtns = filterModal.querySelectorAll('[data-filter]');

    // Store all reports data and pagination
    let allReports = [];
    let filteredReports = [];
    let currentPage = 1;
    const itemsPerPage = 8;

    // Track current filters
    let currentFilterType = null;
    let currentStartDate = null;
    let currentEndDate = null;
    let currentSearchTerm = '';

    // Fetch all reports from server on page load
    async function fetchAllReports() {
        try {
            const response = await fetch('{{ url('/super-admin/reports/all') }}');
            allReports = await response.json();
            filteredReports = [...allReports];
            return true;
        } catch (error) {
            console.error('Error fetching reports:', error);
            return false;
        }
    }

    // Render reports in table with pagination
    function renderReports(reports, searchTerm = '') {
        tableBody.innerHTML = '';

        if (reports.length === 0) {
            noResultsRow.classList.remove('hidden');
            updatePagination(0);
            return;
        }

        // Calculate pagination
        const totalPages = Math.ceil(reports.length / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const currentPageReports = reports.slice(startIndex, endIndex);

        // Render current page reports
        currentPageReports.forEach(report => {
            const row = createReportRow(report);
            
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

    // Create report row element
    function createReportRow(report) {
        const row = document.createElement('tr');
        
        // Add highlighting classes for unread reports
        const isUnread = !report.viewed;
        row.className = `cursor-pointer hover:bg-gray-50 ${isUnread ? 'bg-red-50 border-l-4 border-red-500' : ''}`;
        
        const dateObj = new Date(report.created_at);
        const formattedDate = dateObj.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        const formattedTime = dateObj.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });

        row.innerHTML = `
            <td class="w-[10%] px-13 py-2 whitespace-nowrap text-l ${isUnread ? 'text-red-700 font-semibold' : 'text-[#000000]'} font-[Lexend]">
                ${formattedDate}<br>
                <span class="text-m ${isUnread ? 'text-red-500' : 'text-gray-500'}">${formattedTime}</span>
            </td>
            <td class="w-[15%] px-6 py-1 whitespace-nowrap ${isUnread ? 'text-red-700 font-bold' : ''}">
                ${isUnread ? `
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse mr-2"></div>
                        RPT-${String(report.id).padStart(3, '0')}
                    </div>
                ` : `RPT-${String(report.id).padStart(3, '0')}`}
            </td>
            <td class="w-[25%] px-6 py-2 whitespace-nowrap ${isUnread ? 'text-red-700 font-semibold' : ''}">
                ${report.email}
            </td>
            <td class="w-[45%] px-6 py-2 text-l ${isUnread ? 'text-red-700 font-semibold' : 'text-gray-900'} font-[Lexend] max-w-l truncate">
                <div class="max-w-full overflow-hidden text-ellipsis">
                    ${report.description.length > 85 ? report.description.substring(0, 85) + '...' : report.description}
                </div>
            </td>
        `;

        // Add click event to open modal
        row.addEventListener('click', function() {
            openReportModal({
                id: report.id,
                created_at: report.created_at,
                email: report.email,
                description: report.description,
                attachment: report.file_path ? `{{ asset('storage/') }}/${report.file_path}` : null
            });
        });

        return row;
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

    // Change page function
    window.changePage = function(page) {
        currentPage = page;
        const searchTerm = searchInput.value.trim();
        let displayReports = filteredReports;
        
        if (searchTerm) {
            displayReports = searchReports(filteredReports, searchTerm);
        }
        
        renderReports(displayReports, searchTerm);
    };

    // Reset to first page
    function resetToFirstPage() {
        currentPage = 1;
    }

    // Filter reports based on criteria
    function filterReports(reports, filterType, startDate = null, endDate = null) {
        const now = new Date();
        
        return reports.filter(report => {
            const reportDate = new Date(report.created_at);
            
            switch(filterType) {
                case 'today':
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const repToday = new Date(reportDate);
                    repToday.setHours(0, 0, 0, 0);
                    return repToday.getTime() === today.getTime();
                    
                case 'this-week':
                    const weekStart = new Date(now);
                    weekStart.setDate(now.getDate() - now.getDay());
                    weekStart.setHours(0, 0, 0, 0);
                    const weekEnd = new Date(weekStart);
                    weekEnd.setDate(weekStart.getDate() + 6);
                    weekEnd.setHours(23, 59, 59, 999);
                    return reportDate >= weekStart && reportDate <= weekEnd;
                    
                case 'this-month':
                    return reportDate.getMonth() === now.getMonth() && 
                           reportDate.getFullYear() === now.getFullYear();
                           
                case 'custom':
                    if (startDate && endDate) {
                        const start = new Date(startDate);
                        start.setHours(0, 0, 0, 0);
                        const end = new Date(endDate);
                        end.setHours(23, 59, 59, 999);
                        return reportDate >= start && reportDate <= end;
                    } else if (startDate) {
                        const start = new Date(startDate);
                        start.setHours(0, 0, 0, 0);
                        return reportDate >= start;
                    } else if (endDate) {
                        const end = new Date(endDate);
                        end.setHours(23, 59, 59, 999);
                        return reportDate <= end;
                    }
                    return true;
                    
                default:
                    return true;
            }
        });
    }

    // Sort reports
    function sortReports(reports, sortType) {
        const sorted = [...reports];
        
        if (sortType === 'newest') {
            return sorted.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        } else if (sortType === 'oldest') {
            return sorted.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        }
        
        return sorted;
    }

    // Search reports
    function searchReports(reports, searchTerm) {
        if (!searchTerm) return reports;
        
        const term = searchTerm.toLowerCase();
        return reports.filter(report => {
            const dateStr = new Date(report.created_at).toLocaleDateString();
            const reportId = `RPT-${String(report.id).padStart(3, '0')}`;
            const email = report.email || '';
            const description = report.description || '';
            
            return dateStr.toLowerCase().includes(term) ||
                   reportId.toLowerCase().includes(term) ||
                   email.toLowerCase().includes(term) ||
                   description.toLowerCase().includes(term);
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
        const success = await fetchAllReports();
        if (!success) {
            console.error('Failed to load reports data');
            return;
        }
        renderReports(filteredReports);
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
            
            // Store current filter state
            currentFilterType = filterType;
            if (filterType !== 'custom') {
                currentStartDate = null;
                currentEndDate = null;
            }
            
            resetToFirstPage();
            startDateInput.value = '';
            endDateInput.value = '';

            // Remove active state from all buttons
            quickFilterBtns.forEach(b => b.classList.remove('bg-[#F5E6E6]'));
            // Add active state to clicked button
            this.classList.add('bg-[#F5E6E6]');

            // Apply filter
            if (filterType === 'newest' || filterType === 'oldest') {
                filteredReports = sortReports(allReports, filterType);
            } else {
                filteredReports = filterReports(allReports, filterType);
            }

            // Apply current search term if exists
            const searchTerm = searchInput.value.trim();
            currentSearchTerm = searchTerm;
            if (searchTerm) {
                filteredReports = searchReports(filteredReports, searchTerm);
            }

            renderReports(filteredReports, searchTerm);
            filterModal.classList.add('hidden');
        });
    });

    // Apply custom range filter
    applyFiltersBtn.addEventListener('click', function() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        
        if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
            alert('Start date cannot be after end date');
            return;
        }

        currentFilterType = 'custom';
        currentStartDate = startDate ? new Date(startDate) : null;
        currentEndDate = endDate ? new Date(endDate) : null;

        resetToFirstPage();
        quickFilterBtns.forEach(btn => btn.classList.remove('bg-[#F5E6E6]'));

        filteredReports = filterReports(allReports, 'custom', startDate, endDate);

        const searchTerm = searchInput.value.trim();
        currentSearchTerm = searchTerm;
        if (searchTerm) {
            filteredReports = searchReports(filteredReports, searchTerm);
        }

        renderReports(filteredReports, searchTerm);
        filterModal.classList.add('hidden');
    });

    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        currentSearchTerm = searchTerm;
        
        resetToFirstPage();
        
        let searchResults = [...filteredReports];
        
        if (searchTerm) {
            searchResults = searchReports(filteredReports, searchTerm);
        }

        renderReports(searchResults, searchTerm);
    });

    // Clear all filters and search
    function clearAllFiltersAndSearch() {
        searchInput.value = '';
        startDateInput.value = '';
        endDateInput.value = '';

        currentFilterType = null;
        currentStartDate = null;
        currentEndDate = null;
        currentSearchTerm = '';

        resetToFirstPage();
        quickFilterBtns.forEach(btn => btn.classList.remove('bg-[#F5E6E6]'));

        filteredReports = [...allReports];
        renderReports(filteredReports);
        filterModal.classList.add('hidden');
    }

    clearFiltersBtn.addEventListener('click', clearAllFiltersAndSearch);
    clearSearchBtn.addEventListener('click', clearAllFiltersAndSearch);

    // Initialize the page
    initializePage();

    // Helper function to apply current filters to reports
    function applyCurrentFilters(reports) {
        let result = [...reports];
        
        if (currentFilterType) {
            if (currentFilterType === 'newest' || currentFilterType === 'oldest') {
                result = sortReports(result, currentFilterType);
            } else if (currentFilterType === 'custom') {
                result = filterReports(result, 'custom', currentStartDate, currentEndDate);
            } else {
                result = filterReports(result, currentFilterType);
            }
        }
        
        if (currentSearchTerm) {
            result = searchReports(result, currentSearchTerm);
        }
        
        return result;
    }

    // Generate filter description for PDF header
    function getFilterDescription() {
        if (!currentFilterType && !currentSearchTerm) {
            return {
                description: 'All Problem Reports',
                recordCount: `Total Records: ${allReports.length}`
            };
        }
        
        let filterParts = [];
        
        if (currentFilterType) {
            switch(currentFilterType) {
                case 'today':
                    filterParts.push("Today's Reports");
                    break;
                case 'this-week':
                    filterParts.push("This Week's Reports");
                    break;
                case 'this-month':
                    filterParts.push("This Month's Reports");
                    break;
                case 'newest':
                    filterParts.push('All Reports (Newest First)');
                    break;
                case 'oldest':
                    filterParts.push('All Reports (Oldest First)');
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
        
        const filteredData = applyCurrentFilters(allReports);
        return {
            description: filterParts.join(' | '),
            recordCount: `Filtered Records: ${filteredData.length} of ${allReports.length}`
        };
    }

    // PDF Generation functionality - Landscape only
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

            try {
                let reports = [];
                if (allReports.length > 0) {
                    reports = [...allReports];
                } else {
                    const response = await fetch('/super-admin/reports/all');
                    reports = await response.json();
                }

                if (!reports.length) {
                    alert('No reports found.');
                    return;
                }

                const filteredData = applyCurrentFilters(reports);

                if (!filteredData.length) {
                    alert('No reports found matching the current filter.');
                    return;
                }

                const { description, recordCount } = getFilterDescription();

                const pdfContainer = document.createElement('div');
                pdfContainer.style.cssText = `
                    padding: 20px;
                    background-color: white;
                    font-family: Arial, sans-serif;
                    color: black;
                    width: 100%;
                `;

                const style = document.createElement('style');
                style.textContent = `
                    table, tr, td, th, tbody, thead, tfoot {
                        page-break-inside: avoid !important;
                        break-inside: avoid !important;
                    }
                `;
                pdfContainer.appendChild(style);

                const header = document.createElement('div');
                header.innerHTML = `
                    <div style="text-align: center; margin-bottom: 15px;">
                        <h1 style="color: #4D0F0F; font-size: 20px; margin-bottom: 5px; font-family: Arial, sans-serif;">Problem Reports Summary</h1>
                        <p style="color: #666; font-size: 11px; font-family: Arial, sans-serif; margin: 2px 0;">Generated on ${new Date().toLocaleString()}</p>
                        <p style="color: #4D0F0F; font-size: 12px; font-weight: bold; margin: 3px 0; font-family: Arial, sans-serif;">${description}</p>
                        <p style="color: #666; font-size: 10px; font-family: Arial, sans-serif; margin: 2px 0;">${recordCount}</p>
                        <hr style="border: 0.5px solid #4D0F0F; margin: 10px 0;">
                    </div>
                `;
                pdfContainer.appendChild(header);

                const cleanTable = document.createElement('table');
                cleanTable.style.cssText = `
                    width: 100%;
                    border-collapse: collapse;
                    font-family: Arial, sans-serif;
                    margin-top: 20px;
                `;

                const thead = document.createElement('thead');
                const headerRow = document.createElement('tr');
                
                // Landscape layout headers
                const headers = ['Timestamp', 'Report ID', 'Email', 'Problem Description'];
                const headerWidths = ['18%', '15%', '25%', '42%'];

                headers.forEach((headerText, index) => {
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
                        width: ${headerWidths[index]};
                    `;
                    th.textContent = headerText;
                    headerRow.appendChild(th);
                });
                thead.appendChild(headerRow);
                cleanTable.appendChild(thead);

                const tbody = document.createElement('tbody');
                filteredData.forEach((report, index) => {
                    const newRow = document.createElement('tr');
                    newRow.style.cssText = `
                        background-color: ${index % 2 === 0 ? 'white' : '#f9f9f9'};
                    `;

                    const dateObj = new Date(report.created_at);

                    // Timestamp cell
                    const timestampCell = document.createElement('td');
                    timestampCell.style.cssText = `
                        padding: 6px;
                        border: 1px solid #ddd;
                        font-size: 10px;
                        vertical-align: top;
                        font-family: Arial, sans-serif;
                    `;
                    timestampCell.innerHTML = `
                        <div style="font-weight: bold;">${dateObj.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' })}</div>
                        <div style="color: #666; font-size: 9px;">${dateObj.toLocaleTimeString()}</div>
                    `;
                    newRow.appendChild(timestampCell);

                    // Report ID cell
                    const reportIdCell = document.createElement('td');
                    reportIdCell.style.cssText = `
                        padding: 6px;
                        border: 1px solid #ddd;
                        font-size: 10px;
                        vertical-align: top;
                        font-family: Arial, sans-serif;
                        font-weight: bold;
                    `;
                    reportIdCell.textContent = `RPT-${String(report.id).padStart(3, '0')}`;
                    newRow.appendChild(reportIdCell);

                    // Email cell
                    const emailCell = document.createElement('td');
                    emailCell.style.cssText = `
                        padding: 6px;
                        border: 1px solid #ddd;
                        font-size: 10px;
                        vertical-align: top;
                        font-family: Arial, sans-serif;
                    `;
                    emailCell.textContent = report.email || '';
                    newRow.appendChild(emailCell);

                    // Description cell
                    const descCell = document.createElement('td');
                    descCell.style.cssText = `
                        padding: 6px;
                        border: 1px solid #ddd;
                        font-size: 10px;
                        vertical-align: top;
                        word-wrap: break-word;
                        font-family: Arial, sans-serif;
                    `;
                    descCell.textContent = report.description || '';
                    newRow.appendChild(descCell);

                    tbody.appendChild(newRow);
                });

                cleanTable.appendChild(tbody);
                pdfContainer.appendChild(cleanTable);

                // Generate filename
                let filename = 'problem-reports';
                if (currentFilterType) {
                    switch(currentFilterType) {
                        case 'today': filename += '-today'; break;
                        case 'this-week': filename += '-this-week'; break;
                        case 'this-month': filename += '-this-month'; break;
                        case 'custom': filename += '-custom-range'; break;
                        case 'newest': filename += '-newest-first'; break;
                        case 'oldest': filename += '-oldest-first'; break;
                    }
                }
                if (currentSearchTerm) {
                    filename += '-search';
                }
                filename += `-${new Date().toISOString().split('T')[0]}.pdf`;

                const opt = {
                    margin: [8, 8, 8, 8],
                    filename: filename,
                    image: { 
                        type: 'jpeg', 
                        quality: 0.98 
                    },
                    html2canvas: { 
                        scale: 1.3,
                        useCORS: true,
                        allowTaint: true,
                        backgroundColor: '#ffffff',
                        removeContainer: true
                    },
                    jsPDF: { 
                        unit: 'mm', 
                        format: 'a4', 
                        orientation: 'landscape' // Fixed to landscape
                    }
                };

                html2pdf().set(opt).from(pdfContainer).save().then(() => {
                    button.innerHTML = originalContent;
                    button.disabled = false;
                }).catch(error => {
                    console.error('PDF generation failed:', error);
                    alert('Failed to generate PDF. Please try again.');
                    button.innerHTML = originalContent;
                    button.disabled = false;
                });

            } catch (error) {
                console.error('PDF generation failed:', error);
                alert('Failed to generate PDF. Please try again.');
            } finally {
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        });
    }

    // Update the openReportModal function in reports.blade.php
    window.openReportModal = function(report) {
        console.log('Opening report modal for:', report);
        
        // Mark report as viewed via AJAX
        fetch(`{{ url('/super-admin/reports') }}/${report.id}/mark-as-viewed`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.success) {
                // Update the report's viewed status in local data
                const reportIndex = allReports.findIndex(r => r.id === report.id);
                if (reportIndex !== -1) {
                    allReports[reportIndex].viewed = true;
                    const filteredIndex = filteredReports.findIndex(r => r.id === report.id);
                    if (filteredIndex !== -1) {
                        filteredReports[filteredIndex].viewed = true;
                    }
                    // Re-render to remove highlighting
                    const currentDisplayReports = applyCurrentFilters(allReports);
                    renderReports(currentDisplayReports, currentSearchTerm);
                }
                
                // Update notification badge immediately on the reports page
                const notificationBadge = document.getElementById('reportsNotificationBadge');
                if (notificationBadge) {
                    notificationBadge.textContent = data.newCount;
                    if (data.newCount === 0) {
                        notificationBadge.classList.add('hidden');
                    } else {
                        notificationBadge.classList.remove('hidden');
                    }
                }

                // Dispatch event for navigation component - with more specific data
                window.dispatchEvent(new CustomEvent('reportViewed', {
                    detail: { 
                        newCount: data.newCount,
                        reportId: report.id,
                        timestamp: Date.now()
                    }
                }));

                // Also dispatch a force update event to ensure the navigation updates
                window.dispatchEvent(new CustomEvent('forceUpdateReportsBadge', {
                    detail: { 
                        count: data.newCount,
                        timestamp: Date.now()
                    }
                }));

                console.log('Events dispatched with count:', data.newCount);
            } else {
                console.error('Failed to mark report as viewed:', data.message);
            }
        })
        .catch(error => {
            console.error('Error marking report as viewed:', error);
        });

        // Rest of your existing modal code...
        document.getElementById('modalReportId').textContent = 'RPT-' + String(report.id).padStart(3, '0');

        const date = new Date(report.created_at);
        document.getElementById('modalTimestamp').textContent = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();

        document.getElementById('modalEmail').textContent = report.email;

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

        const attachmentDiv = document.getElementById('modalAttachment');
        if (report.attachment) {
            attachmentDiv.innerHTML = `
            <a href="#" 
               onclick="openDocumentViewer('${report.attachment}', '${report.attachment.split('/').pop()}'); return false;" 
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

        const modal = document.getElementById('reportModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    window.closeReportModal = function() {
        const modal = document.getElementById('reportModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    window.emailResponse = function() {
        const email = document.getElementById('modalEmail').textContent;
        const reportId = document.getElementById('modalReportId').textContent;

        const subject = encodeURIComponent('Response to ' + reportId);
        const mailtoLink = 'mailto:' + email + '?subject=' + subject;

        window.location.href = mailtoLink;
    };

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('reportModal');
        if (event.target === modal) {
            window.closeReportModal();
        }
    });

    // PDF.js worker setup
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.worker.min.js';

    window.openDocumentViewer = function(filePath, fileName) {
        const modal = document.getElementById('documentViewerModal');
        const pdfViewer = document.getElementById('pdfViewer');
        const imageViewer = document.getElementById('imageViewer');
        const downloadView = document.getElementById('downloadView');
        const titleElement = document.getElementById('documentTitle');
        const downloadFileName = document.getElementById('downloadFileName');
        const downloadButton = document.getElementById('downloadButton');

        // Set document title and download info
        titleElement.textContent = fileName;
        downloadFileName.textContent = fileName;
        downloadButton.setAttribute('href', filePath);

        // Clear previous content
        pdfViewer.innerHTML = '';
        imageViewer.innerHTML = '';

        // Determine file type
        const fileExtension = fileName.split('.').pop().toLowerCase();

        // Initially show PDF viewer and hide others
        pdfViewer.classList.remove('hidden');
        imageViewer.classList.add('hidden');
        downloadView.classList.add('hidden');

        // Reset tab styling
        document.getElementById('previewTab').classList.add('bg-blue-500', 'text-white');
        document.getElementById('previewTab').classList.remove('text-gray-700');
        document.getElementById('downloadTab').classList.remove('bg-blue-500', 'text-white');
        document.getElementById('downloadTab').classList.add('text-gray-700');

        // Handle different file types
        if (['pdf'].includes(fileExtension)) {
            // PDF file - use PDF.js
            const loadingTask = pdfjsLib.getDocument(filePath);
            loadingTask.promise.then(function(pdf) {
                pdf.getPage(1).then(function(page) {
                    const viewport = page.getViewport({scale: 1.5});
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    pdfViewer.appendChild(canvas);
                    const renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };
                    page.render(renderContext);
                });
            }).catch(function(error) {
                console.error('Error loading PDF:', error);
                pdfViewer.innerHTML = '<div class="p-4 bg-red-100 text-red-700">Failed to load PDF. Please try downloading the file instead.</div>';
            });
        } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
            pdfViewer.classList.add('hidden');
            imageViewer.classList.remove('hidden');
            const img = document.createElement('img');
            img.src = filePath;
            img.className = 'max-h-full max-w-full';
            imageViewer.appendChild(img);
        } else {
            pdfViewer.classList.add('hidden');
            downloadView.classList.remove('hidden');
        }

        // Show the modal
        modal.classList.remove('hidden');

        // Set up tab switching (remove previous event listeners first)
        const previewTab = document.getElementById('previewTab');
        const downloadTab = document.getElementById('downloadTab');
        const newPreviewTab = previewTab.cloneNode(true);
        const newDownloadTab = downloadTab.cloneNode(true);
        previewTab.parentNode.replaceChild(newPreviewTab, previewTab);
        downloadTab.parentNode.replaceChild(newDownloadTab, downloadTab);

        document.getElementById('previewTab').addEventListener('click', function() {
            if (['pdf'].includes(fileExtension)) {
                pdfViewer.classList.remove('hidden');
                imageViewer.classList.add('hidden');
            } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                imageViewer.classList.remove('hidden');
                pdfViewer.classList.add('hidden');
            }
            downloadView.classList.add('hidden');
            this.classList.add('bg-blue-500', 'text-white');
            this.classList.remove('text-gray-700');
            document.getElementById('downloadTab').classList.remove('bg-blue-500', 'text-white');
            document.getElementById('downloadTab').classList.add('text-gray-700');
        });

        document.getElementById('downloadTab').addEventListener('click', function() {
            pdfViewer.classList.add('hidden');
            imageViewer.classList.add('hidden');
            downloadView.classList.remove('hidden');
            this.classList.add('bg-blue-500', 'text-white');
            this.classList.remove('text-gray-700');
            document.getElementById('previewTab').classList.remove('bg-blue-500', 'text-white');
            document.getElementById('previewTab').classList.add('text-gray-700');
        });
    }

    function closeDocumentViewer() {
        const modal = document.getElementById('documentViewerModal');
        const pdfViewer = document.getElementById('pdfViewer');
        const imageViewer = document.getElementById('imageViewer');
        pdfViewer.innerHTML = '';
        imageViewer.innerHTML = '';
        modal.classList.add('hidden');
    }
    window.closeDocumentViewer = closeDocumentViewer;
});
</script>
@endsection