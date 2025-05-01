<!-- resources/views/components/generalcomponents/notification.blade.php -->

<div class="relative">
    <button id="notificationBtn" class="p-2 rounded-full hover:bg-white-200 cursor-pointer transition">
        <svg class="text-gray-500 hover:text-gray-700" width="24" height="24" viewBox="0 0 24 24" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path d="M20 4H4C2.89543 4 2 4.89543 2 6V18C2 19.1046 2.89543 20 4 20H20C21.1046 20 22 19.1046 22 18V6C22 4.89543 21.1046 4 20 4Z"
                stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M22 7L13.03 12.7C12.7213 12.8934 12.3643 12.996 12 12.996C11.6357 12.996 11.2787 12.8934 10.97 12.7L2 7"
                stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </button>

    <div id="notificationPanel"
        class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
        <div class="p-4 border-b">
            <h2 class="text-lg font-semibold text-gray-800">Notifications</h2>
        </div>

        <div class="flex text-sm font-medium text-gray-600 border-b">
            <button id="allTab"
                class="w-1/2 text-center py-2 border-b-2 border-red-600 text-red-600 font-semibold bg-gray-50">All</button>
            <button id="unreadTab" class="w-1/2 text-center py-2 hover:bg-gray-100">Unread</button>
        </div>

        <div class="p-6 text-center text-gray-500 text-sm">
            No notifications
        </div>
    </div>
</div>

<script>
    const btn = document.getElementById('notificationBtn');
    const panel = document.getElementById('notificationPanel');
    const unreadTab = document.getElementById('unreadTab');
    const allTab = document.getElementById('allTab');

    btn.addEventListener('click', () => {
        panel.classList.toggle('hidden');
    });

    unreadTab.addEventListener('click', () => {
        unreadTab.classList.add('border-b-2', 'border-red-600', 'text-red-600', 'font-semibold', 'bg-gray-50');
        allTab.classList.remove('border-b-2', 'border-red-600', 'text-red-600', 'font-semibold', 'bg-gray-50');
    });

    allTab.addEventListener('click', () => {
        allTab.classList.add('border-b-2', 'border-red-600', 'text-red-600', 'font-semibold', 'bg-gray-50');
        unreadTab.classList.remove('border-b-2', 'border-red-600', 'text-red-600', 'font-semibold', 'bg-gray-50');
    });
</script>
