@extends('base')<!-- Extend the base component -->
@section('content')<!-- Content section -->
<!-- This is the main content area for the super admin dashboard -->
@include('components.superAdminNavigation')

<div class="max-h-9/10 bg-white bg-opacity-30 p-13">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold font-[Lexend] text-[#332B2B] ">SUPER ADMIN</h1>
    </div>
     <div class="flex justify-between items-center mb-1">
        <!-- Back to Dashboard Button -->
        <a href="{{ route('super-admin.dashboard') }}" 
            class="bg-white hover:text-red-800 text-[#7A1212] px-4 py-2 rounded-[16px] font-sm font-[Lexend] inline-flex items-center self-start mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Dashboard
        </a>
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
        <div class="overflow-hidden rounded-[15px] h-[700px]" >
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
                        <tr class="cursor-pointer hover:bg-gray-50" 
                            onclick="openReportModal({{ 
                                json_encode([
                                    'id' => $report->id,
                                    'created_at' => $report->created_at,
                                    'email' => $report->email,
                                    'description' => $report->description,
                                    // Always use asset('storage/...')
                                    'attachment' => $report->screenshot_path ? asset('storage/' . $report->screenshot_path) : ''
                                ]) 
                            }})">
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


<div id="reportModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 hidden flex items-center justify-center">
  <div class="bg-white w-full h-full relative flex flex-col">
    
    <!-- Fixed navigation header at top -->
    <div class="sticky top-0 z-20 bg-white">
      @include('components.superAdminNavigation')
    </div>

    <!-- Main content area -->
    <div class="flex-1 bg-gray-100 px-8 py-6 overflow-y-auto">
      
      <!-- SUPER ADMIN Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-black font-[Lexend]">SUPER ADMIN</h1>
      </div>

      <!-- Report Card Container -->
      <div class="w-full mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
          
          <!-- Report Header with close button -->
          <div class="bg-white px-6 py-4 border-b border-gray-200 flex justify-center items-center relative">
            <h2 id="modalReportId" class="text-lg font-semibold text-black font-[Lexend]">REPORT-001</h2>
            <button onclick="closeReportModal()" class="absolute right-6 w-6 h-6 bg-red-600 text-white rounded flex items-center justify-center hover:bg-red-700 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Report Content -->
          <div class="px-6 py-6 space-y-4">
            
            <!-- Timestamp -->
            <div class="text-sm text-gray-600 font-[Lexend]">
              <span id="modalTimestamp"></span>
            </div>

            <!-- Email -->
            <div>
              <span class="text-sm font-semibold text-black font-[Lexend]">Email: </span>
              <span id="modalEmail" class="text-sm text-black font-[Lexend]"></span>
            </div>

            <!-- Problem Description -->
            <div>
              <div class="text-sm font-semibold text-black font-[Lexend] mb-2">Problem Description:</div>
              <div id="modalDescription" class="text-sm text-black font-[Lexend] leading-relaxed"></div>
            </div>

            <!-- Attachment -->
            <div>
              <div class="text-sm font-semibold text-black font-[Lexend] mb-2">Attachment:</div>
              <span id="modalAttachment" class="text-sm text-black font-[Lexend]  font-bold bg-yellow-500 rounded p-2">No attachment provided</span>
            </div>

          </div>
        </div>

        <!-- Email Response Button -->
        <div class="text-center mt-6">
          <button onclick="emailResponse()" class="bg-green-600 text-white px-6 py-2 rounded shadow font-[Lexend] text-sm font-bold hover:bg-green-700 transition-colors curosr-pointer">
            Email Response
          </button>
        </div>

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
        const fileName = report.attachment.split('/').pop();
        attachmentDiv.innerHTML = '<a href="' + report.attachment + '" target="_blank" class="text-black font-bold hover:text-gray-800">' + fileName + '</a>';
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