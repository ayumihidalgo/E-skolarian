@extends('components.studentNavigation')

@section('studentContent')
<div class="w-full min-h-screen bg-[#f2f4f7] px-6 py-8">
    {{-- Header --}}
    <h2 class="text-2xl font-extrabold mb-4">Document Archive Table</h2>

    {{-- Search and Filters --}}
    <div class="flex flex-wrap items-center gap-4 mb-4">
        <input
            type="text"
            placeholder="Search..."
            class="border border-[#9099A5] px-4 py-2 rounded-full w-full md:w-1/3 bg-white"
        >

        <div class="flex gap-4 ml-auto">
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
    <div class="bg-white rounded-2xl shadow-md overflow-hidden p-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-0" id="documentTable">
                <thead>
                    <tr>
                        <th class="px-6 py-2 cursor-pointer rounded-tl-full" onclick="sortTable(0)">Tag ⬍</th>
                        <th class="px-6 py-2 cursor-pointer" onclick="sortTable(1)">Title ⬍</th>
                        <th class="px-6 py-2 cursor-pointer whitespace-nowrap" onclick="sortTable(2)">Date Archived ⬍</th>
                        <th class="px-6 py-2 cursor-pointer" onclick="sortTable(3)">Type ⬍</th>
                        <th class="px-6 py-2 cursor-pointer rounded-tr-full" onclick="sortTable(4)">Status ⬍</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < $rows; $i++)
                        @php
                            $tag = 'IT_00' . ($i + 1);
                            $status = $i % 2 == 0 ? 'Approved' : 'Rejected';
                        @endphp
                        <tr class="bg-white cursor-pointer hover:bg-gray-100 border-b border-gray-200"
                            onclick="window.location.href='{{ route('document.preview', ['id' => $i + 1]) }}'"
                            data-status="{{ $status }}">
                            <td class="px-6 py-2 font-semibold text-orange-500">{{ $tag }}</td>
                            <td class="px-6 py-2 truncate max-w-[200px]" title="{{ $i === 4 ? 'AVR_Request' : 'ELITE_IT_Week' }}">
                                {{ $i === 4 ? 'AVR_Request' : 'ELITE_IT_Week' }}
                            </td>
                            <td class="px-6 py-2">
                                {{ $i === 4 ? '4/01/2025' : '4/15/2025' }}
                            </td>
                            <td class="px-6 py-2 truncate max-w-[200px]" title="Event Proposal">Event Proposal</td>
                            <td class="px-6 py-2">
                                <span class="status-pill {{ $status === 'Approved' ? 'bg-[#10B981]' : 'bg-[#EF4444]' }} text-white px-4 py-1 min-w-[100px] text-center rounded-full inline-block">
                                    {{ $status }}
                                </span>
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

        let sortDirection = [true, true, true, true, true];
        function sortTable(columnIndex) {
            const table = document.getElementById("documentTable");
            const tbody = table.tBodies[0];
            const rows = Array.from(tbody.rows);
            const isDateColumn = columnIndex === 2;

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
