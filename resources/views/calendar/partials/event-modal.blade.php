<div id="eventModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-[Marcellus_SC] text-[#7A1212]">Add Event</h3>
            <button type="button" onclick="closeEventModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="eventForm">
            <div class="mb-4">
                <label for="event-title" class="block text-sm font-medium text-gray-700 mb-1">Event Title</label>
                <input type="text" id="event-title" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#7A1212] focus:border-[#7A1212]" required>
            </div>
            
            <div class="mb-4">
                <label for="event-start" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="datetime-local" id="event-start" name="start" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#7A1212] focus:border-[#7A1212]" required>
            </div>
            
            <div class="mb-4">
                <label for="event-end" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="datetime-local" id="event-end" name="end" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#7A1212] focus:border-[#7A1212]">
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeEventModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 font-[Marcellus_SC]">
                    Cancel
                </button>
                <button type="button" onclick="saveEvent()" class="px-4 py-2 bg-[#7A1212] text-white rounded-md hover:bg-[#8A2222] font-[Marcellus_SC]">
                    Save Event
                </button>
            </div>
        </form>
    </div>
</div>