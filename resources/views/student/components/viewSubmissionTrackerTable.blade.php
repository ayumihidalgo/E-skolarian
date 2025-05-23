<!-- student/components/viewSubmissionTrackerTable.blade.php -->
@php
    $hasRecords = count($records) > 0;
@endphp

<style>
    /* Ensure table cells and headers have consistent font size */
    th, td {
        text-align: center;
        font-size: 14px;  /* Specify the font size to retain consistency */
    }

    /* Optional: Add some spacing for the sort icons */
    th .sort-icon {
        margin-left: 5px;
    }

    /* Make the empty state container taller */
    .empty-state-cell {
        min-height: 250px; /* Adjust as needed */
        height: 500px;
        width: 100%;
        vertical-align: middle;
    }

    /* Custom Pagination Styles */
    .pagination-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 20px;
        gap: 8px;
    }

    .pagination-item {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 4px 8px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        border-radius: 4px;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .pagination-item:not(.active):not(.disabled) {
        background-color: #f8f9fa;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .pagination-item:not(.active):not(.disabled):hover {
        background-color: #e5e7eb;
        color: #1f2937;
    }

    .pagination-item.active {
        background-color: #7c2d12;
        color: white;
        border: 1px solid #7c2d12;
    }

    .pagination-item.disabled {
        background-color: #f3f4f6;
        color: #9ca3af;
        border: 1px solid #e5e7eb;
        cursor: not-allowed;
    }
</style>

<div>
    <table class="min-w-full border border-gray-300 rounded-[20px] shadow-md bg-white overflow-hidden" id="recordsTable">
        @if($hasRecords)
        <thead>
            <tr class="text-gray-800 text-sm text-center bg-white">
                <th class="py-3 px-4 border-b font-extrabold" data-column="0" onclick="sortTable(0)">Organization <i class="sort-icon fas fa-sort"></i></th>
                <th class="py-3 px-4 border-b font-extrabold" data-column="1" onclick="sortTable(1)">Title <i class="sort-icon fas fa-sort"></i></th>
                <th class="py-3 px-4 border-b font-extrabold" data-column="2" onclick="sortTable(2)">Date Created <i class="sort-icon fas fa-sort"></i></th>
                <th class="py-3 px-4 border-b font-extrabold" data-column="3" onclick="sortTable(3)">Type <i class="sort-icon fas fa-sort"></i></th>
                <th class="py-3 px-4 border-b font-extrabold" data-column="4" onclick="sortTable(4)">Status <i class="sort-icon fas fa-sort"></i></th>
            </tr>
        </thead>
        @endif
        <tbody class="text-sm text-gray-700 text-center font-medium bg-white">
            @forelse($records as $record)
            <tr
                onclick="window.location='{{ url('/records/' . $record->id) }}'"
                class="hover:bg-gray-100 cursor-pointer transition"
            >
                <td class="py-3 px-4">DOC-{{ $record->user->organization_acronym ?? 'N/A' }}</td>
                <td class="py-3 px-4">{{ $record->subject }}</td>
                <td class="py-3 px-4">{{ \Carbon\Carbon::parse($record->created_at)->format('m/d/Y') }}</td>
                <td class="py-3 px-4">{{ $record->type }}</td>
                <td class="py-3 px-4">
                    @if(strtolower($record->status) === 'approved')
                        <span class="inline-block bg-green-100 text-green-700 text-xs font-extrabold px-3 py-1 rounded-full">Approved</span>
                    @elseif(strtolower($record->status) === 'rejected')
                        <span class="inline-block bg-red-100 text-red-700 text-xs font-extrabold px-3 py-1 rounded-full">Rejected</span>
                    @else
                        <span class="inline-block bg-gray-200 text-gray-800 text-xs font-extrabold px-3 py-1 rounded-full">{{ $record->status }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-4 px-4 text-center text-gray-500 font-extrabold empty-state-cell">
                    <img src="{{ asset('images/viewNoFileFound.svg') }}" alt="No File Found" class="mx-auto mb-10" style="height: 80px;">
                    No records found at the moment.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Custom Pagination -->
    @if($records->hasPages())
    <div class="pagination-container">
        {{-- First Page Link --}}
        @if($records->currentPage() > 1)
            <a href="{{ $records->url(1) }}" class="pagination-item">First</a>
        @else
            <span class="pagination-item disabled">First</span>
        @endif

        {{-- Previous Page Link --}}
        @if($records->onFirstPage())
            <span class="pagination-item disabled">&laquo;</span>
        @else
            <a href="{{ $records->previousPageUrl() }}" class="pagination-item">&laquo;</a>
        @endif

        {{-- Pagination Elements --}}
        @foreach($records->getUrlRange(max(1, $records->currentPage() - 2), min($records->lastPage(), $records->currentPage() + 2)) as $page => $url)
            @if($page == $records->currentPage())
                <span class="pagination-item active">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="pagination-item">{{ $page }}</a>
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if($records->hasMorePages())
            <a href="{{ $records->nextPageUrl() }}" class="pagination-item">&raquo;</a>
        @else
            <span class="pagination-item disabled">&raquo;</span>
        @endif

        {{-- Last Page Link --}}
        @if($records->currentPage() < $records->lastPage())
            <a href="{{ $records->url($records->lastPage()) }}" class="pagination-item">Last</a>
        @else
            <span class="pagination-item disabled">Last</span>
        @endif
    </div>
    @endif
</div>

<script>
    let currentSortColumn = -1;
    let currentSortDirection = 'asc';  // Default sort direction is ascending

    function sortTable(columnIndex) {
        const table = document.getElementById('recordsTable');
        const rows = Array.from(table.getElementsByTagName('tr')).slice(1); // Skip header row

        const isNumeric = columnIndex === 2; // For date column, we treat it as numeric

        // If the same column is clicked, toggle the direction
        if (columnIndex === currentSortColumn) {
            currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            currentSortColumn = columnIndex;
            currentSortDirection = 'asc';  // Reset to ascending for a new column
        }

        // Sort the rows based on the column and direction
        rows.sort((rowA, rowB) => {
            const cellA = rowA.cells[columnIndex].innerText.trim();
            const cellB = rowB.cells[columnIndex].innerText.trim();

            let comparison = 0;
            if (isNumeric) {
                const dateA = new Date(cellA);
                const dateB = new Date(cellB);
                comparison = dateA - dateB; // Compare date objects
            } else {
                comparison = cellA.localeCompare(cellB); // Compare text
            }

            return currentSortDirection === 'asc' ? comparison : -comparison; // Return comparison based on direction
        });

        // Reattach the sorted rows to the table
        rows.forEach(row => table.appendChild(row));

        // Update the icons for the current column
        updateSortIcons(columnIndex);
    }

    function updateSortIcons(columnIndex) {
        const icons = document.querySelectorAll('.sort-icon');
        const headers = document.querySelectorAll('th');

        // Reset all icons to default (no sorting)
        icons.forEach(icon => icon.classList.replace('fa-sort-up', 'fa-sort').replace('fa-sort-down', 'fa-sort'));

        // Update the icon for the current column
        const currentIcon = icons[columnIndex];
        if (currentSortDirection === 'asc') {
            currentIcon.classList.replace('fa-sort', 'fa-sort-up');
        } else {
            currentIcon.classList.replace('fa-sort', 'fa-sort-down');
        }
    }
</script>
