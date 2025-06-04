@extends('base')

@section('content')
    @include('components.superAdminNavigation')

    <div class="max-h-screen bg-[#F2F4F7] bg-opacity-30 px-10 py-10">
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
        <div class="bg-white rounded-[25px] shadow-lg overflow-hidden" style= "width: 100%; height: 710px; flex-shrink:0;">
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
                    class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-2 py-1 rounded-lg font-[Lexend] inline-flex items-center">
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
                                    <div class="text-m">
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
                                            <div class="text-l font-medium text-gray-900 font-[Lexend] max-w-[450px] truncate">
                                                @if (in_array($activity->user_role_name, ['Admin', 'Superadmin']))
                                                    {{ $activity->user_role_name }}
                                                @else
                                                    {{ $activity->user_name }}
                                                @endif
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
                                        class="mt-6 px-4 py-2 bg-[#7A1212] text-white rounded-lg font-[Lexend] hover:bg-red-800 transition cursor-pointer">
                                        Clear Search
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3 flex justify-center mb-3 absolute bottom-10 left-0 w-full p-2 text-center">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const tableBody = document.getElementById('activityTableBody');
            const noResultsRow = document.getElementById('noResultsRow');

            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const rows = Array.from(tableBody.querySelectorAll('tr')).filter(row => row !==
                    noResultsRow);

                let visibleCount = 0;

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    const matches = text.includes(searchTerm);
                    row.style.display = matches ? '' : 'none';

                    if (matches) visibleCount++;
                });

                noResultsRow.classList.toggle('hidden', visibleCount > 0);
            });

            // Optional: Clear search functionality
            const clearSearchBtn = document.getElementById('clearSearchBtn');
            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    const rows = Array.from(tableBody.querySelectorAll('tr')).filter(row => row !==
                        noResultsRow);
                    rows.forEach(row => (row.style.display = ''));
                    noResultsRow.classList.add('hidden');
                });
            }
        });
    </script>
@endsection
