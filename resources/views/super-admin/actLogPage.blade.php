<div class="mt-4 bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">ACTIVITY LOG</h2>
            <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Generate Report</button>
        </div>

        <div class="flex justify-between items-center mb-2">
            <div class="flex items-center gap-2">
                <button id="prevMonth" class="text-lg font-semibold">&larr;</button>
                <span class="font-semibold">January</span>
                <button id="nextMonth" class="text-lg font-semibold">&rarr;</button>
            </div>
            <div class="flex items-center gap-2">
                <button id="prevYear" class="text-lg font-semibold">&larr;</button>
                <span class="font-semibold">2025</span>
                <button id="nextYear" class="text-lg font-semibold">&rarr;</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm table-auto">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="p-3">Timestamp</th>
                        <th class="p-3">Role Name</th>
                        <th class="p-3">Role</th>
                        <th class="p-3">Activity</th>
                        <th class="p-3">Remarks</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($logs as $log)
                        <tr>
                            <td class="p-3 whitespace-nowrap">{{ $log->timestamp }}</td>
                            <td class="p-3 truncate max-w-xs" title="{{ $log->role_name }}">{{ $log->role_name }}</td>
                            <td class="p-3">{{ $log->role }}</td>
                            <td class="p-3">{{ $log->activity }}</td>
                            <td class="p-3 text-gray-600 italic">{{ $log->remarks ?? 'n/a' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>