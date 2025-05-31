@extends('base')

@section('content')
@include('components.superAdminNavigation')

<div class="max-h-9/10 bg-white bg-opacity-30 px-8 py-6">
    <!-- Back to Dashboard Button -->
    <div class="flex justify-between items-center">
        <a href="{{ route('super-admin.dashboard') }}" 
            class="bg-white hover:text-red-800 text-[#7A1212] px-4 py-2 rounded-[16px] font-sm font-[Lexend] inline-flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Dashboard
        </a>
    </div>

    <div class="p-6 bg-white rounded-[25px] shadow-md">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-10">
                <h2 class="text-2xl font-bold font-[Lexend]">ACTIVITY LOG</h2>
                <div class="flex items-center space-x-2">
                    <button class="px-4 py-1">◀ January</button>
                    <button class="px-4 py-1">▶</button>
                    <button class="px-4 py-1">◀ 2025</button>
                    <button class="px-4 py-1">▶</button>
                </div>
            </div>
            <button class="bg-[#4D0F0F] px-2 py-1 mb-2 rounded-[8px] text-white font-[Lexend] hover:bg-red-800 transition duration-200 flex items-center cursor-pointer"  title="Download in PDF">
                Generate Report
            </button>
        </div>

        <!-- Table Container - Fixed Height -->
        <div class="overflow-hidden" style="width: 100%; height: 440px;">
            <table class="min-w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-gray-200 text-black">
                        <th class="px-4 py-3 font-[Lexend] flex items-center">
                            Timestam
                            <div class="flex items-center" title="YYYY-DD-MM">
                                p
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                </svg>
                            </div>
                        </th>
                        <th class="px-4 py-3 font-[Lexend]">Role Name</th>
                        <th class="px-4 py-3 font-[Lexend]">Role</th>
                        <th class="px-4 py-3 font-[Lexend]">Activity</th>
                        <th class="px-4 py-3 font-[Lexend]">Remarks</th>
                    </tr>
                </thead>
                <!-- <tbody class="divide-y divide-[#7A1212]/70">
                    @for ($i = 1; $i <= 8; $i++)
                    <tr class="border-y-[0.1px] border-[#D9D9D9]">
                        <td class="px-4 py-3 text-[Lexend] text-[17px] text-black">2025-03-01 14:32:10</td>
                        <td class="px-4 py-3 text-[Lexend] text-[17px] text-black">Sample Role Name</td>
                        <td class="px-4 py-3 text-[Lexend] text-[17px] text-black">Sample Role</td>
                        <td class="px-4 py-3 text-[Lexend] text-[17px] text-black">Sample Activity</td>
                        <td class="px-4 py-3 text-[Lexend] text-[17px] text-black">Sample Remarks</td>
                    </tr>
                    @endfor
                </tbody> -->
            </table>

            <!-- Empty State Message - Centered -->
            @if(true) <!-- Replace with your empty check logic -->
            <div class="bg-[#FFFFFFA6] h-[480px] flex items-center justify-center text-gray-600 rounded-b-[25px] px-6">
                <span class="font-['Manrope'] text-[17px] text-[#625B5BB2]">No activity logs found.</span>
            </div>
            @endif
        </div>

        <!-- Pagination -->
        <div class="mt-4 flex justify-center">
            <nav>
                <ul class="inline-flex items-center space-x-2">
                    <li>
                        <a href="#" 
                            class="pagination-btn-prev px-3 py-1 rounded-lg {{ true ? 'cursor-not-allowed opacity-50' : '' }}"
                            {{ true ? 'disabled' : '' }}>
                            <
                        </a>
                    </li>

                    <!-- Page Numbers -->
                    @for ($i = 1; $i <= 2; $i++)
                        <li>
                            <a href="#"
                                class="pagination-btn px-3 py-1 rounded-lg {{ $i == 1 ? 'bg-[#7A1212] text-white' : '' }}">
                                {{ $i }}
                            </a>
                        </li>
                    @endfor

                    <!-- Next Page Button -->
                    <li>
                        <a href="#"
                            class="pagination-btn-next px-3 py-1 rounded-lg {{ false ? 'cursor-not-allowed opacity-50' : '' }}"
                            {{ false ? 'disabled' : '' }}>
                            >
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
@endsection