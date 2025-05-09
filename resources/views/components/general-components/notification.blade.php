@php
    use App\Models\Notification;

    $notifications = Auth::check() ? Notification::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get() : [];
    $unreadNotifications = Auth::check() ? Notification::where('user_id', Auth::id())->where('is_read', false)->orderBy('created_at', 'desc')->get() : [];
@endphp
<div class="relative">
    <!-- Notification Button -->
    <button id="notificationBtn" class="relative p-2 rounded-full cursor-pointer transition-all duration-300">
        <svg class="text-gray-500 hover:text-gray-700 transition-colors duration-300" width="24" height="24" viewBox="0 0 24 24" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path d="M20 4H4C2.89543 4 2 4.89543 2 6V18C2 19.1046 2.89543 20 4 20H20C21.1046 20 22 19.1046 22 18V6C22 4.89543 21.1046 4 20 4Z"
                stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M22 7L13.03 12.7C12.7213 12.8934 12.3643 12.996 12 12.996C11.6357 12.996 11.2787 12.8934 10.97 12.7L2 7"
                stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        @php
            $unreadCount = Auth::user()->notifications()->where('is_read', false)->count();
        @endphp
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1">{{ $unreadCount }}</span>
        @endif
    </button>

    <!-- Notification Panel -->
    <div id="notificationPanel"
        class="hidden absolute right-0 mt-2 bg-white rounded-xl shadow-lg border border-gray-200 z-50 transform opacity-0 scale-95 transition-all duration-300">
        
        <!-- Header -->
        <div class="notif-top-content p-4 border-b flex flex-row justify-between " style="width: 500px; height: 40px;">
            <h2 class="text-lg font-semibold text-gray-800">Notifications</h2>
            <div class="right-nav flex flex-row space-x-5">
            <!-- Dots Icon -->
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                xmlns="http://www.w3.org/2000/svg" class="cursor-pointer hover:text-gray-700 transition-colors duration-300">
                    <path d="M4.75 8.5C3.925 8.5 3.25 9.175 3.25 10C3.25 10.825 3.925 11.5 4.75 11.5C5.575 11.5 6.25 10.825 6.25 10C6.25 9.175 5.575 8.5 4.75 8.5ZM15.25 8.5C14.425 8.5 13.75 9.175 13.75 10C13.75 10.825 14.425 11.5 15.25 11.5C16.075 11.5 16.75 10.825 16.75 10C16.75 9.175 16.075 8.5 15.25 8.5ZM10 8.5C9.175 8.5 8.5 9.175 8.5 10C8.5 10.825 9.175 11.5 10 11.5C10.825 11.5 11.5 10.825 11.5 10C11.5 9.175 10.825 8.5 10 8.5Z" fill="#525866"/>
                </svg>
            </div>
        </div>

        <!-- Tabs -->
        <div id="tabs-nav" class="flex items-center justify-between text-sm font-medium text-gray-600 border-b px-4 mt-4 ">
            <div class="flex">
            <button id="allTab" class="px-4 py-2 border-b-2 border-purple-600 text-black font-semibold bg-gray-50 cursor-pointer">All</button>
            <button id="unreadTab" class="px-4 py-2 hover:bg-gray-100 text-gray-500 cursor-pointer">Unread</button>
            </div>
            <div class=" hover:bg-gray-100 rounded cursor-pointer transition-colors duration-300" id="collapseArrow">
            <svg id="arrowIcon" width="20" height="20" viewBox="0 0 20 20" fill="none"
                xmlns="http://www.w3.org/2000/svg" class="transform transition-transform duration-300">
                <path d="M10.0001 10.879L13.7126 7.1665L14.7731 8.227L10.0001 13L5.22705 8.227L6.28755 7.1665L10.0001 10.879Z" fill="#525866"/>
            </svg>
            </div>
        </div>

        <!-- Notification Content -->
        <div id="notificationBody" class="overflow-y-auto transition-all duration-300" style="width: 500px; height: 512px; max-height: 512px;">
            @if(Auth::check() && Auth::user()->notifications->count() > 0)
                <!-- All Notifications Tab Content -->
                <div id="allNotifications" class="block">
                    @foreach(Auth::user()->notifications as $notification)
                       <div class="p-4 border-b hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <svg class="text-gray-400" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 22C13.1046 22 14 21.1046 14 20H10C10 21.1046 10.8954 22 12 22ZM18 16V11C18 7.68629 16.2091 4.74121 13.5 3.51472V3C13.5 2.17157 12.8284 1.5 12 1.5C11.1716 1.5 10.5 2.17157 10.5 3V3.51472C7.79086 4.74121 6 7.68629 6 11V16L4 18V19H20V18L18 16Z" fill="currentColor"/>
                                    </svg>
                                    <p class="font-strong text-black">{{ $notification->title }}</p>
                                </div>
                                <p class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            <p class="text-sm text-gray-500">{{ $notification->message }}</p>
                        </div>
                    @endforeach
                </div>
                
                <!-- Unread Notifications Tab Content -->
                <div id="unreadNotifications" class="hidden">
                    @foreach(Auth::user()->notifications->where('is_read', false) as $notification)
                        <div class="p-4 border-b hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <svg class="text-gray-400" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 22C13.1046 22 14 21.1046 14 20H10C10 21.1046 10.8954 22 12 22ZM18 16V11C18 7.68629 16.2091 4.74121 13.5 3.51472V3C13.5 2.17157 12.8284 1.5 12 1.5C11.1716 1.5 10.5 2.17157 10.5 3V3.51472C7.79086 4.74121 6 7.68629 6 11V16L4 18V19H20V18L18 16Z" fill="currentColor"/>
                                    </svg>
                                    <p class="font-strong text-black">{{ $notification->title }}</p>
                                </div>
                                <p class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            <p class="text-sm text-gray-500">{{ $notification->message }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex items-center justify-center h-full text-center">
                    <div class="text-gray-500 text-sm">
                        @if(Auth::check())
                            Hello, {{ Auth::user()->username }}! <br> You have no notifications.
                        @else
                            Hello, Guest! Please log in to see your notifications.
                        @endif
                    </div>
                </div>
            @endif
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
        const tabs = document.getElementById('tabs-nav');
        const allNotifications = document.getElementById('allNotifications');
        const unreadNotifications = document.getElementById('unreadNotifications');
        
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
            
            // Show unread notifications, hide all notifications
            if (allNotifications && unreadNotifications) {
                allNotifications.classList.add('hidden');
                allNotifications.classList.remove('block');
                unreadNotifications.classList.add('block');
                unreadNotifications.classList.remove('hidden');
            }
        });
        
        allTab.addEventListener('click', () => {
            allTab.classList.add('border-b-2', 'border-purple-600', 'text-black', 'font-semibold', 'bg-gray-50');
            allTab.classList.remove('text-gray-500');
            unreadTab.classList.add('text-gray-500');
            unreadTab.classList.remove('border-b-2', 'border-purple-600', 'text-black', 'font-semibold', 'bg-gray-50');
            
            // Show all notifications, hide unread notifications
            if (allNotifications && unreadNotifications) {
                allNotifications.classList.add('block');
                allNotifications.classList.remove('hidden');
                unreadNotifications.classList.add('hidden');
                unreadNotifications.classList.remove('block');
            }
        });
        
        // Toggle content collapse with arrow rotation
        collapseArrow.addEventListener('click', () => {
            isCollapsed = !isCollapsed;
            
            // Rotate arrow
            if (isCollapsed) {
                arrowIcon.style.transform = 'rotate(180deg)';
                notificationBody.style.height = '0px';
                tabs.style.borderBottom = 'none';
            } else {
                arrowIcon.style.transform = 'rotate(0deg)';
                notificationBody.style.height = NOTIFICATION_HEIGHT;
                tabs.style.borderBottom = '1px solid black';
            }
        });
    });
</script>