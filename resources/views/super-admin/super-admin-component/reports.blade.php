@extends('base')<!-- Extend the base component -->
@section('content')<!-- Content section -->
<!-- This is the main content area for the super admin dashboard -->
@include('components.superAdminNavigation')

<div class="max-h-9/10 bg-white bg-opacity-30 p-4 sm:p-6 md:p-13">
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
    <div class="w-full max-w-full md:max-w-[1375px] h-auto rounded shadow-lg bg-white p-4 md:p-6">
        <!-- Header with title and filters -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-4">
            <h2 class="text-xl font-bold font-[Lexend] text-[#332B2B] mb-4 md:mb-0">Reports</h2>
            <!-- Filter Controls: stack vertically on mobile/tablet -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
            <!-- Month Filter -->
            <div class="flex items-center">

                <div class="flex items-center ">
                <button id="prevMonth" class="px-2 py-2 focus:outline-none">
                   <svg width="12" height="22" viewBox="0 0 12 22" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M11.1667 21.4167L0.75 11L11.1667 0.583374V21.4167Z" fill="#1D1B20"/>
</svg>

                </button>
                <select id="monthFilter"
  class="block w-[160px] text-[24px] focus:outline-none focus:ring-2 focus:ring-[#7A1212] font-[Lexend] font-bold appearance-none text-center">


                    <option value="">All Months</option>
                    @php
                    $months = [
                        '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
                        '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
                        '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
                    ];
                    @endphp
                    @foreach($months as $value => $name)
                    <option value="{{ $value }}">{{ $name }}</option>
                    @endforeach
                </select>
                <button id="nextMonth" class="px-2 py-2 focus:outline-none">
                   <svg width="12" height="22" viewBox="0 0 12 22" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M0.833374 21.4167L11.25 11L0.833374 0.583374V21.4167Z" fill="#1D1B20"/>
</svg>

                </button>
                </div>
            </div>
            <!-- Year Filter -->
            <div class="flex items-center">
                <div class="flex items-center">
                <button id="prevYear" class="px-2 py-2 focus:outline-none">
                     <svg width="12" height="22" viewBox="0 0 12 22" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M11.1667 21.4167L0.75 11L11.1667 0.583374V21.4167Z" fill="#1D1B20"/>
</svg>
                </button>
                <select id="yearFilter" class="block w-[160px] text-[24px] focus:outline-none focus:ring-2 focus:ring-[#7A1212] font-[Lexend] font-bold appearance-none text-center"
                    <option value="">All Years</option>
                    @php
                    $currentYear = date('Y');
                    $startYear = 2025;
                    $endYear = max($currentYear, $startYear);
                    @endphp
                    @for ($year = $startYear; $year <= $endYear; $year++)
                    <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
                <button id="nextYear" class="px-2 py-2 focus:outline-none">
                     <svg width="12" height="22" viewBox="0 0 12 22" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M0.833374 21.4167L11.25 11L0.833374 0.583374V21.4167Z" fill="#1D1B20"/>
</svg>
                </button>
                </div>
            </div>
            </div>
        </div>
        <!-- Reports Table Container with vertical scrollbar -->
        <div class="overflow-x-auto rounded-md mx-auto">
            <!-- Reports Table -->
            <div class="overflow-y-scroll rounded-[15px] max-h-[700px]">
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
                                        // Use Storage::url() for correct file path
                                        'attachment' => $report->screenshot_path ? Storage::url($report->screenshot_path) : ''
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
              <!-- Pagination always visible -->
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
            <button onclick="closeReportModal()" class="absolute right-6 w-6 h-6 bg-red-600 text-white rounded flex items-center justify-center hover:bg-red-700 transition-colors cursor-pointer ">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthFilter = document.getElementById('monthFilter');
    const yearFilter = document.getElementById('yearFilter');
    const prevMonth = document.getElementById('prevMonth');
    const nextMonth = document.getElementById('nextMonth');
    const prevYear = document.getElementById('prevYear');
    const nextYear = document.getElementById('nextYear');

    prevMonth.addEventListener('click', function() {
        if (monthFilter.selectedIndex > 0) {
            monthFilter.selectedIndex--;
            monthFilter.dispatchEvent(new Event('change'));
        }
    });

    nextMonth.addEventListener('click', function() {
        if (monthFilter.selectedIndex < monthFilter.options.length - 1) {
            monthFilter.selectedIndex++;
            monthFilter.dispatchEvent(new Event('change'));
        }
    });

    prevYear.addEventListener('click', function() {
        if (yearFilter.selectedIndex > 0) {
            yearFilter.selectedIndex--;
            yearFilter.dispatchEvent(new Event('change'));
        }
    });

    nextYear.addEventListener('click', function() {
        if (yearFilter.selectedIndex < yearFilter.options.length - 1) {
            yearFilter.selectedIndex++;
            yearFilter.dispatchEvent(new Event('change'));
        }
    });
});
</script>

@endsection
