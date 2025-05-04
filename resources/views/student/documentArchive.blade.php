@extends('components.studentNavigation')

@section('content')
    <div class="h-full px-6 py-8">
        {{-- Header --}}
            <h2 class="text-2xl font-extrabold mb-4">Document Archive Table</h2>

            {{-- Search and Filters --}}
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <div class="flex-1 min-w-[200px] relative">
                    <input type="text" placeholder="Search..." class="border border-[#9099A5] px-4 py-2 pr-10 rounded-full w-full bg-white">
                    <img src="{{ asset('images/Magnifier.svg') }}" alt="Search" class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 pointer-events-none" />
                </div>

                <div class="flex flex-wrap items-center gap-4 justify-end">

                    {{-- Type Dropdown --}}
                    <div class="relative w-40">
                        <select id="typeFilter" class="appearance-none border px-4 py-2 rounded-full bg-[#7A1212] text-white w-full pr-8 hover:bg-[#DAA520] hover:text-white transition-colors duration-200 truncate">
                            <option class="bg-white text-black truncate" value="Type" disabled selected>Type</option>
                            <option class="bg-white text-black truncate" value="All">All Types</option>
                            <option class="bg-white text-black truncate" value="Event Proposal">Event Proposal</option>
                            <option class="bg-white text-black truncate" value="General Plan of Activities">General Plan of Activities</option>
                            <option class="bg-white text-black truncate" value="Calendar of Activities">Calendar of Activities</option>
                            <option class="bg-white text-black truncate" value="Accomplishment Report">Accomplishment Report</option>
                            <option class="bg-white text-black truncate" value="Contribution and By-Laws">Contribution and By-Laws</option>
                            <option class="bg-white text-black truncate" value="Request Letter">Request Letter</option>
                            <option class="bg-white text-black truncate" value="Off-Campus">Off-Campus</option>
                            <option class="bg-white text-black truncate" value="Petition and Concern">Petition and Concern</option>
                        </select>
                        <img src="{{ asset('images/dropdownIcon.svg') }}" alt="Dropdown Icon" class="absolute top-1/2 right-3 -translate-y-1/2 w-4 h-4 pointer-events-none" />
                    </div>

                    {{-- Status Dropdown --}}
                    <div class="relative w-40">
                        <select id="statusFilter" class="appearance-none border px-4 py-2 rounded-full bg-[#7A1212] text-white w-full pr-8 hover:bg-[#DAA520] hover:text-white transition-colors duration-200">
                            <option class="bg-white text-black" value="Status" disabled selected>Status</option>
                            <option class="bg-white text-black" value="All">All Status</option>
                            <option class="bg-white text-black" value="Approved">Approved</option>
                            <option class="bg-white text-black" value="Rejected">Rejected</option>
                        </select>
                        <img src="{{ asset('images/dropdownIcon.svg') }}" alt="Dropdown Icon" class="absolute top-1/2 right-3 -translate-y-1/2 w-4 h-4 pointer-events-none" />
                    </div>
                </div>
            </div>

            @php
                // Assuming we get these values from auth/session
                $userOrganization = 'ELITE'; // This should come from authenticated user's data

                // Organization mapping - only include user's organization
                $orgMap = [
                    'ELITE' => 'Eligible League of Information Technology Enthusiasts'
                ];

                // Only include relevant color mapping
                $tagColors = [
                    'IT' => 'text-orange-500'
                ];

                $types = [
                    'Event Proposal', 'General Plan of Activities', 'Calendar of Activities',
                    'Accomplishment Report', 'Contribution and By-Laws', 'Request Letter',
                    'Off-Campus', 'Petition and Concern'
                ];
            @endphp

            <div id="tableContainer" class="bg-white rounded-[24px] shadow-md overflow-hidden p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto" id="documentTable">
                            <thead class="bg-white text-left">
                                <tr>
                                    @php $headers = ['Tag', 'Title', 'Date Archived', 'Type', 'Status']; @endphp
                                    @foreach ($headers as $i => $header)
                                        <th class="px-4 py-2 whitespace-nowrap max-w-[160px] truncate">
                                            @if ($header !== 'Status')
                                                <button onclick="sortTable({{ $i }})" class="flex items-center gap-1 group">
                                                    <span>{{ $header }}</span>
                                                    <img src="{{ asset('images/sortIcon.svg') }}" alt="Sort" class="w-3 h-3 text-gray-500 group-hover:text-black transition">
                                                </button>
                                            @else
                                                <span>{{ $header }}</span>
                                            @endif
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < 20; $i++)
                                @php
                                    // Only use ELITE organization data
                                    $acronym = 'ELITE';
                                    $orgName = $orgMap[$acronym];
                                    $tag = $acronym . '_00' . ($i + 1);
                                    $status = $i % 2 === 0 ? 'Approved' : 'Rejected';
                                    $docType = $types[$i % count($types)];
                                    $tagColor = $tagColors['IT'];
                                @endphp
                                    <tr class="border-b border-gray-300 hover:bg-gray-100 cursor-pointer"
                                        onclick="window.location.href='{{ route('student.documentPreview', [
                                            'id' => $i + 1,
                                            'status' => $status,
                                            'title' => $acronym . '_Project_' . ($i + 1),
                                            'type' => $docType
                                        ]) }}'"
                                        data-status="{{ $status }}"
                                        data-type="{{ $docType }}">
                                        <td class="px-4 py-2 font-semibold truncate max-w-[120px]"><span class="{{ $tagColor }}">{{ $tag }}</span></td>
                                        <td class="px-4 py-2 truncate max-w-[160px]" title="Document Title">{{ $acronym }}_Project_{{ $i + 1 }}</td>
                                        <td class="px-4 py-2 truncate max-w-[120px]">{{ now()->subDays($i)->format('m/d/Y') }}</td>
                                        <td class="px-4 py-2 truncate max-w-[160px]" title="{{ $docType }}">{{ $docType }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-4 py-1 rounded-full text-white inline-block min-w-[100px] text-center {{ $status === 'Approved' ? 'bg-[#10B981]' : 'bg-[#EF4444]' }}">{{ $status }}</span>
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
    </div>

    {{-- Script --}}
        <script>
            const rowsPerPage = 6;
            let currentPage = 1;

            function showPage(page) {
                currentPage = page;
                const rows = document.querySelectorAll("#documentTable tbody tr");
                const visibleRows = Array.from(rows).filter(row => row.dataset.visible !== "false");

                visibleRows.forEach((row, i) => {
                    row.style.display = (i >= (page - 1) * rowsPerPage && i < page * rowsPerPage) ? "" : "none";
                });
            }

            function setActivePage(button) {
                const label = button.textContent;
                const rows = document.querySelectorAll("#documentTable tbody tr");
                const visibleRows = Array.from(rows).filter(row => row.dataset.visible !== "false");
                const totalPages = Math.ceil(visibleRows.length / rowsPerPage);

                if (label === "First") currentPage = 1;
                else if (label === "Last") currentPage = totalPages;
                else currentPage = parseInt(label);

                showPage(currentPage);

                document.querySelectorAll(".pagination-btn").forEach(btn => {
                    btn.classList.remove("bg-[#7A1212]", "text-white");
                });

                if (button.classList.contains("pagination-btn")) {
                    button.classList.add("bg-[#7A1212]", "text-white");
                }
            }

            function applyFilters() {
                const statusFilter = document.getElementById("statusFilter").value;
                const typeFilter = document.getElementById("typeFilter").value;
                const searchTerm = document.querySelector('input[placeholder="Search..."]').value.toLowerCase();

                const rows = document.querySelectorAll("#documentTable tbody tr");

                rows.forEach(row => {
                    const status = row.getAttribute("data-status");
                    const type = row.getAttribute("data-type");
                    const text = row.textContent.toLowerCase();

                    const matchesStatus = statusFilter === "Status" || statusFilter === "All" || status === statusFilter;
                    const matchesType = typeFilter === "Type" || typeFilter === "All" || type === typeFilter;
                    const matchesSearch = text.includes(searchTerm);

                    const isVisible = matchesStatus && matchesType && matchesSearch;
                    row.dataset.visible = isVisible ? "true" : "false";
                    row.style.display = isVisible ? "" : "none";
                });

                showPage(1);
            }

            document.getElementById("statusFilter").addEventListener("change", applyFilters);
            document.getElementById("typeFilter").addEventListener("change", applyFilters);
            document.querySelector('input[placeholder="Search..."]').addEventListener("input", applyFilters);

            let sortDirection = [true, true, true, true, true, true];

            function sortTable(columnIndex) {
                const table = document.getElementById("documentTable");
                const tbody = table.tBodies[0];
                const rows = Array.from(tbody.rows);

                    // Natural sort comparison function
                const naturalCompare = (a, b) => {
                    return a.localeCompare(b, undefined, {
                        numeric: true,
                        sensitivity: 'base'
                    });
                };

                rows.sort((a, b) => {
                    let valA = a.cells[columnIndex].textContent.trim().toLowerCase();
                    let valB = b.cells[columnIndex].textContent.trim().toLowerCase();

                    // Special handling for Tag column (index 0)
                    if (columnIndex === 0) {
                        // Extract the number part after the underscore (e.g., "ELITE_001" -> 1)
                        const numA = parseInt(valA.split('_').pop());
                        const numB = parseInt(valB.split('_').pop());
                        return sortDirection[columnIndex] ? numA - numB : numB - numA;
                    }

                    // Special handling for Title column (index 2)
                    if (columnIndex === 1) {
                        // Natural sort for titles
                        return sortDirection[columnIndex] ?
                            naturalCompare(valA, valB) :
                            naturalCompare(valB, valA);
                    }

                    if (columnIndex === 3) {
                        valA = new Date(valA);
                        valB = new Date(valB);
                    }

                    return sortDirection[columnIndex] ?
                        (valA > valB ? 1 : -1) :
                        (valA < valB ? 1 : -1);

                });

                sortDirection[columnIndex] = !sortDirection[columnIndex];
                rows.forEach(row => tbody.appendChild(row));
                applyFilters(); // Reapply filters after sorting
            }

            document.addEventListener("DOMContentLoaded", () => {
                applyFilters();
            });
        </script>
@endsection
