<div class="relative">
    <!-- Notification Button -->
    <button id="notificationBtn" class="p-2 rounded-full cursor-pointer transition-all duration-300">
        <svg class="text-gray-500 hover:text-gray-700 transition-colors duration-300" width="24" height="24" viewBox="0 0 24 24" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path d="M20 4H4C2.89543 4 2 4.89543 2 6V18C2 19.1046 2.89543 20 4 20H20C21.1046 20 22 19.1046 22 18V6C22 4.89543 21.1046 4 20 4Z"
                stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M22 7L13.03 12.7C12.7213 12.8934 12.3643 12.996 12 12.996C11.6357 12.996 11.2787 12.8934 10.97 12.7L2 7"
                stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </button>

    <!-- Notification Panel -->
    <div id="notificationPanel"
        class="hidden absolute right-0 mt-2 bg-white rounded-xl shadow-lg border border-gray-200 z-50 transform opacity-0 scale-95 transition-all duration-300">
        
        <!-- Header -->
        <div class="notif-top-content p-4 border-b flex flex-row justify-between" style="width: 500px; height: 40px;">
            <h2 class="text-lg font-semibold text-gray-800">Notifications</h2>
            <div class="right-nav flex flex-row space-x-5">
                <!-- Dots Icon -->
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg" class="cursor-pointer hover:text-gray-700 transition-colors duration-300">
                    <path d="M4.75 8.5C3.925 8.5 3.25 9.175 3.25 10C3.25 10.825 3.925 11.5 4.75 11.5C5.575 11.5 6.25 10.825 6.25 10C6.25 9.175 5.575 8.5 4.75 8.5ZM15.25 8.5C14.425 8.5 13.75 9.175 13.75 10C13.75 10.825 14.425 11.5 15.25 11.5C16.075 11.5 16.75 10.825 16.75 10C16.75 9.175 16.075 8.5 15.25 8.5ZM10 8.5C9.175 8.5 8.5 9.175 8.5 10C8.5 10.825 9.175 11.5 10 11.5C10.825 11.5 11.5 10.825 11.5 10C11.5 9.175 10.825 8.5 10 8.5Z" fill="#525866"/>
                </svg>

                <!-- Settings Icon -->
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg" class="cursor-pointer hover:text-gray-700 transition-colors duration-300">
                    <path d="M10 1.75L17.125 5.875V14.125L10 18.25L2.875 14.125V5.875L10 1.75ZM10 3.48325L4.375 6.73975V13.2603L10 16.5167L15.625 13.2603V6.73975L10 3.48325ZM10 13C9.20435 13 8.44129 12.6839 7.87868 12.1213C7.31607 11.5587 7 10.7956 7 10C7 9.20435 7.31607 8.44129 7.87868 7.87868C8.44129 7.31607 9.20435 7 10 7C10.7956 7 11.5587 7.31607 12.1213 7.87868C12.6839 8.44129 13 9.20435 13 10C13 10.7956 12.6839 11.5587 12.1213 12.1213C11.5587 12.6839 10.7956 13 10 13ZM10 11.5C10.3978 11.5 10.7794 11.342 11.0607 11.0607C11.342 10.7794 11.5 10.3978 11.5 10C11.5 9.60218 11.342 9.22064 11.0607 8.93934C10.7794 8.65804 10.3978 8.5 10 8.5C9.60218 8.5 9.22064 8.65804 8.93934 9.22064C8.65804 9.60218 8.5 10 8.5 10.3978 8.65804 10.7794 8.93934 11.0607C9.22064 11.342 9.60218 11.5 10 11.5Z" fill="#525866"/>
                </svg>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex items-center justify-between text-sm font-medium text-gray-600 border-b px-4">
            <div class="flex">
                <button id="allTab" class="px-4 py-2 border-b-2 border-purple-600 text-black font-semibold bg-gray-50">All</button>
                <button id="unreadTab" class="px-4 py-2 hover:bg-gray-100 text-gray-500">Unread</button>
            </div>
            <div class="p-2 hover:bg-gray-100 rounded cursor-pointer transition-colors duration-300" id="collapseArrow">
                <svg id="arrowIcon" width="20" height="20" viewBox="0 0 20 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg" class="transform transition-transform duration-300">
                    <path d="M10.0001 10.879L13.7126 7.1665L14.7731 8.227L10.0001 13L5.22705 8.227L6.28755 7.1665L10.0001 10.879Z" fill="#525866"/>
                </svg>
            </div>
        </div>

        <!-- Notification Content -->
        <div id="notificationBody" class="flex items-center justify-center overflow-hidden transition-all duration-300" style="width: 500px; height: 512px; max-height: 512px;">
            <div class="text-gray-500 text-sm">No notifications</div>
        </div>
    </div>
</div>

<!-- JS -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('notificationBtn');
        const panel = document.getElementById('notificationPanel');
        const unreadTab = document.getElementById('unreadTab');
        const allTab = document.getElementById('allTab');
        const collapseArrow = document.getElementById('collapseArrow');
        const notificationBody = document.getElementById('notificationBody');
        const arrowIcon = document.getElementById('arrowIcon');
        
        // Fixed height for notification body
        const NOTIFICATION_HEIGHT = '512px';
        let isCollapsed = false;
        let isPanelVisible = false;
        
        // Function to toggle panel with animation
        function togglePanel() {
            isPanelVisible = !isPanelVisible;
            
            if (isPanelVisible) {
                // Show panel first (to enable animation)
                panel.classList.remove('hidden');
                
                // Allow browser to process display change before adding animation classes
                setTimeout(() => {
                    panel.classList.remove('opacity-0', 'scale-95');
                    panel.classList.add('opacity-100', 'scale-100');
                }, 10);
            } else {
                // Start hiding animation
                panel.classList.remove('opacity-100', 'scale-100');
                panel.classList.add('opacity-0', 'scale-95');
                
                // Wait for animation to complete before hiding
                setTimeout(() => {
                    panel.classList.add('hidden');
                }, 300);
            }
        }
        
        // Toggle notification panel visibility
        btn.addEventListener('click', (event) => {
            event.stopPropagation();
            togglePanel();
        });
        
        // Close panel when clicking outside
        document.addEventListener('click', (event) => {
            if (isPanelVisible && !panel.contains(event.target) && event.target !== btn) {
                togglePanel();
            }
        });
        
        // Toggle between tabs
        unreadTab.addEventListener('click', () => {
            unreadTab.classList.add('border-b-2', 'border-purple-600', 'text-black', 'font-semibold', 'bg-gray-50');
            unreadTab.classList.remove('text-gray-500');
            allTab.classList.add('text-gray-500');
            allTab.classList.remove('border-b-2', 'border-purple-600', 'text-black', 'font-semibold', 'bg-gray-50');
        });
        
        allTab.addEventListener('click', () => {
            allTab.classList.add('border-b-2', 'border-purple-600', 'text-black', 'font-semibold', 'bg-gray-50');
            allTab.classList.remove('text-gray-500');
            unreadTab.classList.add('text-gray-500');
            unreadTab.classList.remove('border-b-2', 'border-purple-600', 'text-black', 'font-semibold', 'bg-gray-50');
        });
        
        // Toggle content collapse with arrow rotation
        collapseArrow.addEventListener('click', () => {
            isCollapsed = !isCollapsed;
            
            // Rotate arrow
            if (isCollapsed) {
                arrowIcon.style.transform = 'rotate(180deg)';
                notificationBody.style.height = '0px';
            } else {
                arrowIcon.style.transform = 'rotate(0deg)';
                notificationBody.style.height = NOTIFICATION_HEIGHT;
            }
        });
    });
</script>