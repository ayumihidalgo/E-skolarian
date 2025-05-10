<table class="min-w-full bg-white border border-gray-300 shadow-md rounded-lg">
    <thead>
        <tr class="bg-gray-200 text-gray-700">
            <th class="py-2 px-4 border-b text-left">Tag</th>
            <th class="py-2 px-4 border-b text-left">Title</th>
            <th class="py-2 px-4 border-b text-left">Date Created</th>
            <th class="py-2 px-4 border-b text-left">Type</th>
            <th class="py-2 px-4 border-b text-left">Status</th>
        </tr>
    </thead>
    <tbody>
        <tr class="border-b">
            @forelse($records as $record)
        <tr>
            <td class="py-2 px-4">{{ $record->control_tag }}</td>
            <td class="py-2 px-4">{{ $record->subject }}</td>
            <td class="py-2 px-4">{{ $record->created_at->format('m/d/Y') }}</td>
            <td class="py-2 px-4">{{ $record->type }}</td>
            <td class="py-2 px-4 text-green-600 font-semibold">{{ $record->status }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="py-2 px-4">No records found.</td>
        </tr>
        @endforelse

    </tbody>
</table>
