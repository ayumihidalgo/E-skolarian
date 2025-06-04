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
                        <tr>
                            <td colspan="4" class="h-96 text-center bg-white rounded-b-[15px]">
                                <div class="flex items-center justify-center h-full">
                                    <span class="font-['Manrope'] text-[22px] text-[#625B5BB2]">No reports found.</span>
                                </div>
                            </td>
                        </tr>
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