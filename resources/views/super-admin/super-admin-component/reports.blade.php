@extends('base')<!-- Extend the base component -->
@section('content')<!-- Content section -->
<!-- This is the main content area for the super admin dashboard -->
@include('components.superAdminNavigation')

<div class="max-h-9/10 bg-white bg-opacity-30 p-13">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold font-[Lexend] text-[#332B2B] ">SUPER ADMIN</h1>
    </div>
    <div class = "w-[1375] h-[710] rounded  shadow-lg bg-white p-6">        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold font-[Lexend] text-[#332B2B]">Reports</h2>
            
            <!-- Separate Month and Year Filter Dropdowns -->
            <div class="flex items-center gap-4">                <div class="flex items-center">
                    <label for="monthFilter" class="mr-2 text-sm font-medium text-gray-700 font-[Lexend]">Month:</label>
                    <select id="monthFilter" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#7A1212] font-[Lexend]">
                        <option value="">All Months</option>
                        @php
                            $months = [
                                '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
                                '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
                                '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
                            ];
                            $currentYear = date('Y');
                            $currentMonth = date('n'); // Current month without leading zeros
                        @endphp
                        @foreach($months as $value => $name)
                            @php
                                $monthNumber = (int)$value;
                                // Show month if current year hasn't started yet (2025+) or if it's current/past month in current year
                                $showMonth = $currentYear < 2025 || $monthNumber <= $currentMonth;
                            @endphp
                            @if($showMonth)
                                <option value="{{ $value }}">{{ $name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div><div class="flex items-center">
                    <label for="yearFilter" class="mr-2 text-sm font-medium text-gray-700 font-[Lexend]">Year:</label>
                    <select id="yearFilter" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#7A1212] font-[Lexend]">
                        <option value="">All Years</option>                        @php
                            $currentYear = date('Y');
                            $startYear = 2025;
                            $endYear = max($currentYear, $startYear); // Only show up to current year, minimum 2025
                        @endphp
                        @for ($year = $startYear; $year <= $endYear; $year++)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>

        <!-- Reports Table -->
        <div class="overflow-hidden rounded-[15px]" style="height: 600px;">
            <table class="min-w-full  text-white rounded-t-[15px] table-fixed">
                <thead>
                    <tr>
                        <th class="w-[15%] px-4 py-3 text-left font-['Manrope'] text-[15px] font-bold">
                            <div class="flex items-center">
                                <span class="whitespace-nowrap text-black">Timestamp</span>
                                <div class="flex flex-col ml-2">
                                   
                                </div>
                            </div>
                        </th>
                        <th class="w-[15%] px-4 py-3 text-left font-['Manrope'] text-[15px] font-bold">
                            <div class="flex items-center">
                                <span class="whitespace-nowrap text-black">Report ID</span>
                                <div class="flex flex-col ml-2">
                                  
                                </div>
                            </div>
                        </th>
                        <th class="w-[25%] px-4 py-3 text-left font-['Manrope'] text-[15px]  text-black font-bold ">
                            <div class="flex items-center">
                                <span class="whitespace-nowrap">Email</span>
                                <div class="flex flex-col ml-2">
                                 
                                </div>
                            </div>
                        </th>
                        <th class="w-[45%] px-4 py-3 text-left font-['Manrope'] text-[15px]  text-black font-bold">
                            <span class="whitespace-nowrap">Problem Description</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D9D9D9]/70">
                    @forelse($reports as $report)
                        <tr class="cursor-pointer hover:bg-gray-50" onclick="openReportModal({{ json_encode($report) }})">
                            <td class="px-4 py-3 text-[13px] text-black font-[Lexend]">
                                {{ $report->created_at->format('Y-m-d') }}<br>
                                <span class="text-[11px] text-gray-600">{{ $report->created_at->format('h:i A') }}</span>
                            </td>
                            <td class="px-4 py-3 text-[13px] text-black font-[Lexend] font-semibold">
                                RPT-{{ str_pad($report->id, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-4 py-3 text-[13px] text-black font-[Lexend]">
                                {{ $report->email }}
                            </td>
                            <td class="px-4 py-3 text-[13px] text-black font-[Lexend]">
                                <div class="max-w-full overflow-hidden text-ellipsis">
                                    {{ Str::limit($report->description, 100) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="h-96 text-center bg-white rounded-b-[15px]">
                                <div class="flex items-center justify-center h-full">
                                    <span class="font-['Manrope'] text-[18px] text-[#625B5BB2]">No reports found.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
          <!-- Pagination -->
        @if($reports->hasPages())
            <div class="mt-6 flex justify-center">
            <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center space-x-2">
            {{-- Previous Page Link --}}
            @if ($reports->onFirstPage())
            <span class="px-3 py-1  text-black rounded cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </span>
            @else
            <a href="{{ $reports->previousPageUrl() }}" class="px-3 py-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($reports->getUrlRange(1, $reports->lastPage()) as $page => $url)
            @if ($page == $reports->currentPage())
                <span class="px-3 py-1 text-white bg-[#7A1212] rounded">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="px-3 py-1 text-[#7A1212] bg-white border border-[#7A1212] rounded hover:bg-[#f5f5f5]">{{ $page }}</a>
            @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($reports->hasMorePages())
            <a href="{{ $reports->nextPageUrl() }}" class="px-3 py-1 text-black ">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            @else
            <span class="px-3 py-1 rounded cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </span>
            @endif
            </nav>
            </div>
        
        @endif
       
    </div>
</div>

<!-- Report Details Modal -->
<div id="reportModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg w-[80%] max-h-[90vh] overflow-y-auto relative">
        <!-- Modal Header -->
        <div class="relative flex justify-between items-center p-6 border shadow">
            <h2 id="modalReportId" class="text-2xl font-bold font-[Lexend] text-[#332B2B] mx-auto text-center"></h2>
            <button onclick="closeReportModal()" class="text-gray-400 hover:text-gray-600 text-2xl absolute top-1/2 right-6 transform -translate-y-1/2">
            &times;
            </button>
        </div>
        
        <!-- Modal Content -->
        <div class="p-6">
            <!-- Timestamp -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 font-[Lexend] mb-1">Timestamp:</label>
                <p id="modalTimestamp" class="text-sm text-gray-900 font-[Lexend]"></p>
            </div>
            
            <!-- Email -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 font-[Lexend] mb-1">Email:</label>
                <p id="modalEmail" class="text-sm text-gray-900 font-[Lexend]"></p>
            </div>
            
            <!-- Problem Description -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 font-[Lexend] mb-1">Problem Description:</label>
                <div id="modalDescription" class="text-sm text-gray-900 font-[Lexend] bg-gray-50 p-3   rounded shadow min-h-[150px] whitespace-pre-wrap"></div>
            </div>
            
            <!-- Attachment -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 font-[Lexend] mb-1">Attachment:</label>
                <div id="modalAttachment" class="text-sm text-gray-500 font-[Lexend]">No attachment provided</div>
            </div>
            
            <!-- Email Response Button -->
            <div class="text-center">
                <button onclick="emailResponse()" class="bg-[#28B309] text-white px-6 py-2 rounded-md hover:bg-[#5A0D0D] font-[Lexend] text-sm transition ease duration-200 cursor-pointer">
                    Email Response
                </button>
            </div>
        </div>
    </div>
</div>

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
    document.getElementById('modalReportId').textContent = 'RPT-' + String(report.id).padStart(3, '0');
    
    const date = new Date(report.created_at);
    document.getElementById('modalTimestamp').textContent = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    
    document.getElementById('modalEmail').textContent = report.email;
    document.getElementById('modalDescription').textContent = report.description;
    
    // Handle attachment
    const attachmentDiv = document.getElementById('modalAttachment');
    if (report.attachment && report.attachment !== '') {
        attachmentDiv.innerHTML = '<a href="' + report.attachment + '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">View Attachment</a>';
    } else {
        attachmentDiv.textContent = 'No attachment provided';
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

@endsection