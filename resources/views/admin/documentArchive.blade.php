@extends('components.adminNavigation')

@section('adminContent') {{-- match the @yield above --}}
        <div class="w-full min-h-screen bg-[#f2f4f7] px-6 py-8">

        {{-- Header --}}
        <h2 class="text-2xl font-extrabold mb-4">Document Archive Table</h2>

        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
            {{-- Search Bar --}}
            <div class="flex-1 min-w-[200px]">
                <input
                    type="text"
                    placeholder="Search..."
                    class="border border-[#9099A5] px-4 py-2 rounded-full w-full bg-white"
                >
            </div>

            {{-- Filters (right side) --}}
            <div class="flex flex-wrap items-center gap-4 justify-end">
                {{-- Organization Dropdown --}}
                <div class="relative w-40">
                    <select class="filter-select border px-4 py-2 rounded-full bg-[#7A1212] text-white focus:ring-2 focus:ring-[#DAA520] w-full appearance-none pr-8 hover:bg-[#DAA520]">
                        <option disabled selected class="bg-white text-black">Organization</option>
                        <option class="bg-white text-black" value="OSC">OSC</option>
                        <option class="bg-white text-black" value="AECES">AECES</option>
                        <option class="bg-white text-black" value="ACAP">ACAP</option>
                        <option class="bg-white text-black" value="ELITE">ELITE</option>
                        <option class="bg-white text-black" value="GIVE">GIVE</option>
                        <option class="bg-white text-black" value="JEHRA">JEHRA</option>
                        <option class="bg-white text-black" value="JMAP">JMAP</option>
                        <option class="bg-white text-black" value="JPIA">JPIA</option>
                        <option class="bg-white text-black" value="TAPNOTCH">TAPNOTCH</option>
                        <option class="bg-white text-black" value="SIGMA">SIGMA</option>
                        <option class="bg-white text-black" value="AGDS">AGDS</option>
                        <option class="bg-white text-black" value="Chorale">Chorale</option>
                    </select>
                    <svg class="absolute top-1/2 right-3 transform -translate-y-1/2 pointer-events-none w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                {{-- Type Dropdown --}}
                <div class="relative w-40">
                    <select class="filter-select border px-4 py-2 rounded-full bg-[#7A1212] text-white focus:ring-2 focus:ring-[#DAA520] w-full appearance-none pr-8 hover:bg-[#DAA520]">
                        <option disabled selected class="bg-white text-black">Type</option>
                        <option class="bg-white text-black" value="Event Proposal">Event Proposal</option>
                        <option class="bg-white text-black" value="General Plan of Activities">General Plan of Activities</option>
                        <option class="bg-white text-black" value="Calendar of Activities">Calendar of Activities</option>
                        <option class="bg-white text-black" value="Accomplishment Report">Accomplishment Report</option>
                        <option class="bg-white text-black" value="Contribution and By-Laws">Contribution and By-Laws</option>
                        <option class="bg-white text-black" value="Request Letter">Request Letter</option>
                        <option class="bg-white text-black" value="Off-Campus">Off-Campus</option>
                        <option class="bg-white text-black" value="Petition and Concern">Petition and Concern</option>
                    </select>
                    <svg class="absolute top-1/2 right-3 transform -translate-y-1/2 pointer-events-none w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                {{-- Status Dropdown --}}
                <div class="relative w-40">
                    <select id="statusFilter" class="filter-select border px-4 py-2 rounded-full bg-[#7A1212] text-white focus:ring-2 focus:ring-[#DAA520] w-full appearance-none pr-8 hover:bg-[#DAA520]">
                        <option value="" class="bg-white text-black">Status</option>
                        <option class="bg-white text-black" value="Approved">Approved</option>
                        <option class="bg-white text-black" value="Rejected">Rejected</option>
                    </select>
                    <svg class="absolute top-1/2 right-3 transform -translate-y-1/2 pointer-events-none w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
        </div>


        {{-- Table Container --}}
        @php $rows = 9; @endphp
        <div class="bg-white rounded-2xl shadow-md overflow-hidden inline-block p-4">
            <div class="overflow-x-auto">
                <table class="w-auto text-left border-separate border-spacing-0" id="documentTable">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 cursor-pointer rounded-tl-full" onclick="sortTable(0)">Tag ⬍</th>
                            <th class="px-4 py-2 cursor-pointer" onclick="sortTable(1)">Organization ⬍</th>
                            <th class="px-4 py-2 cursor-pointer" onclick="sortTable(2)">Title ⬍</th>
                            <th class="px-4 py-2 cursor-pointer whitespace-nowrap" onclick="sortTable(3)">Date Archived ⬍</th>
                            <th class="px-4 py-2 cursor-pointer" onclick="sortTable(4)">Type ⬍</th>
                            <th class="px-4 py-2 cursor-pointer rounded-tr-full" onclick="sortTable(5)">Status ⬍</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < $rows; $i++)
                            @php
                                $tag = 'IT_00' . ($i + 1);
                                $tagPrefix = explode('_', $tag)[0];
                                $tagTextColors = [
                                    'OSC' => 'text-blue-500',
                                    'ECE' => 'text-red-500',
                                    'PSY' => 'text-purple-500',
                                    'IT' => 'text-orange-500',
                                    'HR' => 'text-pink-400',
                                    'ACC' => 'text-pink-400',
                                    'EDU' => 'text-blue-500',
                                    'MAR' => 'text-yellow-500',
                                    'IE' => 'text-green-500',
                                    'TAP' => 'text-green-500',
                                    'SIGMA' => 'text-yellow-900',
                                    'AGSD' => 'text-yellow-900',
                                    'CHO' => 'text-blue-500',
                                ];
                                $textColorClass = $tagTextColors[$tagPrefix] ?? 'text-gray-600';
                                $status = $i % 2 == 0 ? 'Approved' : 'Rejected';
                            @endphp
                            <tr class="bg-white cursor-pointer hover:bg-gray-100" style="border-bottom: 1px solid #e5e7eb;"
                                onclick="window.location.href='{{ route('document.preview', ['id' => $i + 1]) }}'"
                                data-status="{{ $status }}">
                                <td class="px-4 py-2 font-semibold {{ $textColorClass }} ">
                                    {{ $tag }}
                                </td>
                                <td class="px-4 py-2 max-w-[150px] md:max-w-none truncate overflow-hidden whitespace-nowrap" title="Eligible League of Information...">
                                    Eligible League of Information...
                                </td>
                                <td class="px-4 py-2 max-w-[150px] md:max-w-none truncate overflow-hidden whitespace-nowrap" title="{{ $i === 4 ? 'AVR_Request' : 'ELITE_IT_Week' }}">
                                    {{ $i === 4 ? 'AVR_Request' : 'ELITE_IT_Week' }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ $i === 4 ? '4/01/2025' : '4/15/2025' }}
                                </td>
                                <td class="px-4 py-2 max-w-[150px] md:max-w-none truncate overflow-hidden whitespace-nowrap" title="Event Proposal">
                                    Event Proposal
                                </td>
                                <td class="px-4 py-2 }">
                                    <span class="status-pill {{ $status === 'Approved' ? 'bg-[#10B981]' : 'bg-[#EF4444]' }} text-white px-4 py-1 min-w-[100px] text-center rounded-full inline-block">{{ $status }}</span>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4 flex justify-center">
            <nav>
                <ul class="inline-flex items-center space-x-2">
                    <li><button class="pagination-btn-first px-3 py-1 rounded-lg" onclick="setActivePage(this)">First</button></li>
                    <li><button class="pagination-btn px-3 py-1 rounded-lg bg-[#7A1212] text-white" onclick="setActivePage(this)">1</button></li>
                    <li><button class="pagination-btn px-3 py-1 rounded-lg" onclick="setActivePage(this)">2</button></li>
                    <li><button class="pagination-btn px-3 py-1 rounded-lg" onclick="setActivePage(this)">3</button></li>
                    <li><button class="pagination-btn-last px-3 py-1 rounded-lg" onclick="setActivePage(this)">Last</button></li>
                </ul>
            </nav>
        </div>

        <script>

            function setActivePage(button) {
                document.querySelectorAll('.pagination-btn, .pagination-btn-first, .pagination-btn-last').forEach(btn => {
                    btn.classList.remove('bg-[#7A1212]', 'text-white');
                });
                button.classList.add('bg-[#7A1212]', 'text-white');
            }

            let sortDirection = [true, true, true, true, true, true];
            function sortTable(columnIndex) {
                const table = document.getElementById("documentTable");
                const tbody = table.tBodies[0];
                const rows = Array.from(tbody.rows);
                const isDateColumn = columnIndex === 3;

                rows.sort((a, b) => {
                    let valA = a.cells[columnIndex].textContent.trim();
                    let valB = b.cells[columnIndex].textContent.trim();

                    if (isDateColumn) {
                        valA = new Date(valA);
                        valB = new Date(valB);
                    } else {
                        valA = valA.toLowerCase();
                        valB = valB.toLowerCase();
                    }

                    return sortDirection[columnIndex] ? valA.localeCompare(valB) : valB.localeCompare(valA);
                });

                sortDirection[columnIndex] = !sortDirection[columnIndex];
                rows.forEach(row => tbody.appendChild(row));
            }

            // Status filter functionality
            document.getElementById("statusFilter").addEventListener("change", function () {
                const selectedStatus = this.value;
                const rows = document.querySelectorAll("#documentTable tbody tr");

                rows.forEach(row => {
                    const rowStatus = row.getAttribute("data-status");
                    row.style.display = (selectedStatus === rowStatus || selectedStatus === "") ? "" : "none";
                });
            });
        </script>
    </div>
@endsection
