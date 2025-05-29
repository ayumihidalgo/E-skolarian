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
            <table class="min-w-full bg-[#DAA520] text-white rounded-t-[15px] table-fixed">
                <thead>
                    <tr>
                        <th class="w-[15%] px-4 py-3 text-left font-['Manrope'] text-[15px] font-bold">
                            <div class="flex items-center">
                                <span class="whitespace-nowrap">Timestamp</span>
                                <div class="flex flex-col ml-2">
                                   
                                </div>
                            </div>
                        </th>
                        <th class="w-[15%] px-4 py-3 text-left font-['Manrope'] text-[15px] font-bold">
                            <div class="flex items-center">
                                <span class="whitespace-nowrap">Report ID</span>
                                <div class="flex flex-col ml-2">
                                  
                                </div>
                            </div>
                        </th>
                        <th class="w-[25%] px-4 py-3 text-left font-['Manrope'] text-[15px] font-bold">
                            <div class="flex items-center">
                                <span class="whitespace-nowrap">Email</span>
                                <div class="flex flex-col ml-2">
                                 
                                </div>
                            </div>
                        </th>
                        <th class="w-[45%] px-4 py-3 text-left font-['Manrope'] text-[15px] font-bold">
                            <span class="whitespace-nowrap">Problem Description</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#7A1212]/70">
                    <!-- Sample Data Row -->
                    <tr class="border-y-[0.1px] border-[#7A1212] bg-[#d9c698] hover:bg-[#DAA520] transition duration-300 cursor-pointer">
                        <td class="px-4 py-3 text-[13px] text-black font-[Lexend]">
                            2024-01-15<br>
                            <span class="text-[11px] text-gray-600">10:30 AM</span>
                        </td>
                        <td class="px-4 py-3 text-[13px] text-black font-[Lexend] font-semibold">
                            RPT-001
                        </td>
                        <td class="px-4 py-3 text-[13px] text-black font-[Lexend]">
                            user@example.com
                        </td>
                        <td class="px-4 py-3 text-[13px] text-black font-[Lexend]">
                            <div class="max-w-full overflow-hidden text-ellipsis">
                                Unable to access dashboard after login. System shows error message.
                            </div>
                        </td>
                    </tr>
                    <!-- Add more sample rows as needed -->
                    <tr class="border-y-[0.1px] border-[#7A1212] bg-[#d9c698] hover:bg-[#DAA520] transition duration-300 cursor-pointer">
                        <td class="px-4 py-3 text-[13px] text-black font-[Lexend]">
                            2024-01-14<br>
                            <span class="text-[11px] text-gray-600">02:15 PM</span>
                        </td>
                        <td class="px-4 py-3 text-[13px] text-black font-[Lexend] font-semibold">
                            RPT-002
                        </td>
                        <td class="px-4 py-3 text-[13px] text-black font-[Lexend]">
                            admin@school.edu
                        </td>
                        <td class="px-4 py-3 text-[13px] text-black font-[Lexend]">
                            <div class="max-w-full overflow-hidden text-ellipsis">
                                Report generation feature not working properly. CSV export fails.
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
              <!-- No reports message when empty -->
            <div class="bg-[#D9D9D9] h-[200px] flex-grow flex items-center justify-center text-gray-600 rounded-b-[15px] px-6 hidden" id="noReportsMessage">
                <span class="font-['Manrope'] text-[15px] text-[#625B5BB2]">No reports found.</span>
            </div>
        </div>
          <!-- Pagination - Only show when there are multiple pages -->
        <div class="mt-6 flex justify-center" id="paginationContainer" style="display: none;">
            <nav class="flex items-center space-x-1">
                <!-- Previous Button -->
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed font-[Lexend]" disabled>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                
                <!-- Page Numbers -->
                <button class="px-3 py-2 text-sm font-medium text-white bg-[#7A1212] border border-[#7A1212] hover:bg-[#5A0E0E] font-[Lexend]">1</button>
                <button class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-[Lexend]">2</button>
                <button class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-[Lexend]">3</button>
                <span class="px-3 py-2 text-sm font-medium text-gray-700 font-[Lexend]">...</span>
                <button class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-[Lexend]">10</button>
                
                <!-- Next Button -->
                <button class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 hover:text-gray-900 font-[Lexend]">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
            </nav>
        </div>
        
        <!-- Items per page and showing info -->
        {{-- <div class="mt-4 flex justify-between items-center text-sm text-gray-700 font-[Lexend]">
            <div class="flex items-center space-x-2">
                <span>Show</span>
                <select class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-[#7A1212]">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>entries</span>
            </div>
            <div class="text-gray-600">
                Showing 1 to 10 of 98 results
            </div>`
        </div> --}}
    </div>

@endsection