<div id="activityLogModal" class="fixed hidden z-50" style="margin-top: 10px;">
    <!-- Modal Content -->
    <div class="bg-white rounded-[16px] shadow-xl w-[400px] relative">
        <div class="p-4">
            <button id="closeActivityLogBtn" 
                    class="absolute top-4 right-4 text-gray-500 hover:text-[#7A1212] transition-colors duration-200 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <h3 class="font-bold text-lg mb-3 text-[#4D0F0F]">Activity Log</h3>
            <div class="divide-y divide-gray-200 max-h-[300px] overflow-y-auto text-sm">
                <!-- Example Activity -->
                <div class="py-2">
                    <div class="flex justify-between">
                        <span class="font-semibold">ELITE</span>
                        <span class="text-gray-500 text-xs">10:45 AM</span>
                    </div>
                    <p class="text-gray-600">uploaded a new document</p>
                </div>
                <!-- ... rest of your activity items ... -->
            </div>
            <button id="viewAllActivities" 
                class="text-[#7A1212] text-sm hover:underline block text-right mt-3 ml-auto cursor-pointer">
                View all
            </button>
        </div>
    </div>
</div>