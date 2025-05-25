@php
    use App\Models\Notification;

    $notifications = Auth::check() ? Notification::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get() : [];
    $unreadNotifications = Auth::check() ? Notification::where('user_id', Auth::id())->where('is_read', false)->orderBy('created_at', 'desc')->get() : [];
@endphp
<meta name="csrf-token" content="{{ csrf_token() }}">
<div id="notificationComponent" class="relative">
    <!-- Notification Button -->
    <button id="notificationBtn" class="relative p-2 rounded-full cursor-pointer  transition-all duration-300">
        <svg class="text-w hover: rounded-full transition-colors duration-300 w-[24px] h-[24px]" viewBox="0 0 24 24" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path d="M20 4H4C2.89543 4 2 4.89543 2 6V18C2 19.1046 2.89543 20 4 20H20C21.1046 20 22 19.1046 22 18V6C22 4.89543 21.1046 4 20 4Z"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M22 7L13.03 12.7C12.7213 12.8934 12.3643 12.996 12 12.996C11.6357 12.996 11.2787 12.8934 10.97 12.7L2 7"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        
        <!-- Notification Badge -->
        @php
            $unreadCount = Auth::user()->notifications()->where('is_read', false)->count();
        @endphp
        @if($unreadCount > 0)
        <span class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-full"></span>
        @endif
    </button>

    <!-- Notification Panel -->
   <div id="notificationPanel"
        class="hidden fixed sm:absolute inset-0 sm:inset-auto sm:right-0 sm:mt-2 z-500 bg-white sm:rounded-xl shadow-lg border border-gray-200 z-50 transform opacity-0 scale-95 transition-all duration-300 w-full h-full sm:w-[31.25rem] sm:h-auto">
        
        <!-- Header -->
      <div class="notif-top-content p-4 border-b flex flex-row justify-between w-full h-[40px]">
            <div class="flex items-center">
                <!-- Back Icon (visible only on mobile) -->
                <button id="backBtn" class="mr-2 sm:hidden text-gray-600 cursor-pointer hover:text-gray-800 transition-colors duration-300">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <h2 class="text-lg font-semibold text-gray-800">Notifications</h2>
            </div>
           <div class="right-nav flex flex-row space-x-5 relative">
                <!-- Dots Icon -->
                <svg id="optionsMenuBtn" width="20" height="20" viewBox="0 0 20 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg" class="cursor-pointer hover:text-gray-700 transition-colors duration-300 hidden">
                    <path d="M4.75 8.5C3.925 8.5 3.25 9.175 3.25 10C3.25 10.825 3.925 11.5 4.75 11.5C5.575 11.5 6.25 10.825 6.25 10C6.25 9.175 5.575 8.5 4.75 8.5ZM15.25 8.5C14.425 8.5 13.75 9.175 13.75 10C13.75 10.825 14.425 11.5 15.25 11.5C16.075 11.5 16.75 10.825 16.75 10C16.75 9.175 16.075 8.5 15.25 8.5ZM10 8.5C9.175 8.5 8.5 9.175 8.5 10C8.5 10.825 9.175 11.5 10 11.5C10.825 11.5 11.5 10.825 11.5 10C11.5 9.175 10.825 8.5 10 8.5Z" fill="#525866"/>
                </svg>

                <!-- Options Menu -->
                <div id="optionsMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50">
                    <ul class="py-1 text-sm text-gray-700">
                        <li>
                            <button id="markAsReadBtn" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-2"disabled>
                                <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Mark as Read</span>
                            </button>
                        </li>
                        <li>
                            <button id="markAsUnreadBtn" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-2"disabled>
                                <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h11M9 21V3m0 0L3 10m6-7l6 7" />
                                </svg>
                                <span>Mark as Unread</span>
                            </button>
                        </li>
                        <li>
                            <button id="deleteBtn" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-2" disabled>
                                <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span>Delete</span>
                            </button>
                        </li>
                        <li>
                            <button id="deleteAllBtn" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-2" disabled>
                                <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span>Clear All</span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>


        <!-- Tabs -->
        <div id="tabs-nav" class="flex items-center justify-between text-sm font-medium text-gray-600 border-b mt-4">
            <div class="flex">
                <button id="allTab" class="px-4 py-2 border-b-2 border-purple-600 text-black font-semibold bg-gray-50 cursor-pointer">All</button>
                <button id="unreadTab" class="px-4 py-2 hover:bg-gray-100 text-gray-500 cursor-pointer relative">
                    Unread
                    @if($unreadCount > 0)
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-1.5">{{ $unreadCount }}</span>
                    @endif
                </button>
            </div>
            <div class="hover:bg-gray-100 rounded cursor-pointer transition-colors duration-300 hidden" id="collapseArrow">
                <svg id="arrowIcon" width="20" height="20" viewBox="0 0 20 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg" class="transform transition-transform duration-300">
                    <path d="M10.0001 10.879L13.7126 7.1665L14.7731 8.227L10.0001 13L5.22705 8.227L6.28755 7.1665L10.0001 10.879Z" fill="#525866"/>
                </svg>
            </div>
        </div>

        <!-- Notification Content -->
        <div id="notificationBody" class="overflow-y-auto transition-all duration-300 w-full h-[24rem] sm:h-[32rem]">
            @if(Auth::check() && Auth::user()->notifications->count() > 0)
            <!-- All Notifications Tab Content -->
            <div id="allNotifications" class="block  cursor-default">
                @foreach($notifications as $notification)
                 @php
                        $link = $notification->url ?? '#';
                    @endphp
                    <a href="{{ $link }}" class="block">
              <div class="p-4 border-b hover:bg-gray-100 transition-colors duration-200" data-notification-id="{{ $notification->id }}">
    <div class="flex items-start justify-between">
        <a href="{{ $link }}" class="flex items-start space-x-2 flex-1">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <path d="M12 22C13.1046 22 14 21.1046 14 20H10C10 21.1046 10.8954 22 12 22ZM18 16V11C18 7.68629 16.2091 4.74121 13.5 3.51472V3C13.5 2.17157 12.8284 1.5 12 1.5C11.1716 1.5 10.5 2.17157 10.5 3V3.51472C7.79086 4.74121 6 7.68629 6 11V16L4 18V19H20V18L18 16Z" fill="currentColor"/>
            </svg>
            <div class="flex flex-col">
                <p class="font-bold text-black text-sm sm:text-base">{{ $notification->title }}</p>
                <p class="text-xs sm:text-sm text-gray-500">{{ $notification->message }}</p>
                <p class="text-xs text-gray-400 mt-2">
                    @if($notification->created_at->isToday())
                        Today at {{ $notification->created_at->format('h:i A') }}
                    @elseif($notification->created_at->isYesterday())
                        Yesterday at {{ $notification->created_at->format('h:i A') }}
                    @elseif($notification->created_at->isCurrentYear())
                        {{ $notification->created_at->format('M d') }} at {{ $notification->created_at->format('h:i A') }}
                    @else
                        {{ $notification->created_at->format('M d, Y') }} at {{ $notification->created_at->format('h:i A') }}
                    @endif
                </p>
            </div>
        </a>
        <div class="flex items-center">
            <input type="checkbox" 
                class="notification-checkbox w-4 h-4 text-purple-600 rounded border-gray-300 focus:ring-purple-500"
                data-notification-id="{{ $notification->id }}"
            >
        </div>
    </div>
</div>
                @endforeach
            </div>
          
            
            <!-- Unread Notifications Tab Content -->
           <div id="unreadNotifications" class="hidden">
    @if($unreadNotifications->isEmpty())
        <div class="flex flex-col items-center justify-center min-h-[24rem] sm:min-h-[32rem] text-center text-gray-500">
            <svg class="w-16 h-16 text-gray-300 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <p class="text-sm sm:text-base">You have no unread notifications.</p>
        </div>
                @else
                    @foreach($unreadNotifications as $notification)
                    @php
                        $link = $notification->url ?? '#';
                    @endphp
                    <a href="{{ $link }}" class="block">
     <div class="p-4 border-b hover:bg-gray-100 transition-colors duration-200" data-notification-id="{{ $notification->id }}">
    <div class="flex items-start justify-between">
        <a href="{{ $link }}" class="flex items-start space-x-2 flex-1">
            <svg class="text-gray-400 flex-shrink-0 mt-1" width="1rem" height="1rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 22C13.1046 22 14 21.1046 14 20H10C10 21.1046 10.8954 22 12 22ZM18 16V11C18 7.68629 16.2091 4.74121 13.5 3.51472V3C13.5 2.17157 12.8284 1.5 12 1.5C11.1716 1.5 10.5 2.17157 10.5 3V3.51472C7.79086 4.74121 6 7.68629 6 11V16L4 18V19H20V18L18 16Z" fill="currentColor"/>
            </svg>
            <div class="flex flex-col">
                <p class="font-bold text-black text-sm sm:text-base">{{ $notification->title }}</p>
                <p class="text-xs sm:text-sm text-gray-500">{{ $notification->message }}</p>
                <p class="text-xs text-gray-400 mt-2">
                    @if($notification->created_at->isToday())
                        Today at {{ $notification->created_at->format('h:i A') }}
                    @elseif($notification->created_at->isYesterday())
                        Yesterday at {{ $notification->created_at->format('h:i A') }}
                    @elseif($notification->created_at->isCurrentYear())
                        {{ $notification->created_at->format('M d') }} at {{ $notification->created_at->format('h:i A') }}
                    @else
                        {{ $notification->created_at->format('M d, Y') }} at {{ $notification->created_at->format('h:i A') }}
                    @endif
                </p>
            </div>
        </a>
        <div class="flex items-center">
            <input type="checkbox" 
                class="notification-checkbox w-4 h-4 text-purple-600 rounded border-gray-300 focus:ring-purple-500"
                data-notification-id="{{ $notification->id }}"
            >
        </div>
    </div>
</div>
                   
                    @endforeach
                @endif
            </div>
            @else
            <div class="flex items-center justify-center h-full text-center">
                <div class="text-gray-500 text-sm sm:text-base">
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
        const backBtn = document.getElementById('backBtn');
        const optionsMenuBtn = document.getElementById('optionsMenuBtn');
        
        // Fixed height for notification body
        const NOTIFICATION_HEIGHT = '24rem';
        let isCollapsed = false;
        let isPanelVisible = false;

        // Handle checkbox selection to show/hide options menu (GitHub style)
        function updateOptionsMenu() {
            const checkedBoxes = document.querySelectorAll('.notification-checkbox:checked');
            const optionsMenuBtn = document.getElementById('optionsMenuBtn');
            const optionsMenu = document.getElementById('optionsMenu');
            
            if (checkedBoxes.length > 0) {
                // Show dots icon when checkboxes are selected
                optionsMenuBtn.classList.remove('hidden');
                
                // Enable all buttons
                document.getElementById('markAsReadBtn').disabled = false;
                document.getElementById('markAsUnreadBtn').disabled = false;
                document.getElementById('deleteBtn').disabled = false;
                document.getElementById('deleteAllBtn').disabled = false;
            } else {
                // Hide dots icon when no checkboxes are selected
                optionsMenuBtn.classList.add('hidden');
                optionsMenu.classList.add('hidden');
                
                // Disable all buttons
                document.getElementById('markAsReadBtn').disabled = true;
                document.getElementById('markAsUnreadBtn').disabled = true;
                document.getElementById('deleteBtn').disabled = true;
                document.getElementById('deleteAllBtn').disabled = true;
            }
        }

        // Handle checkbox clicks without interfering with notification links
      function initializeCheckboxes() {
    document.querySelectorAll('.notification-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function(event) {
            updateOptionsMenu();
        });
    });
}
        // Function to toggle options menu
        function toggleOptionsMenu(event) {
            event.stopPropagation();
            const optionsMenu = document.getElementById('optionsMenu');
            const checkedBoxes = document.querySelectorAll('.notification-checkbox:checked');
            
            // Only show if there are checked items
            if (checkedBoxes.length > 0) {
                optionsMenu.classList.toggle('hidden');
            }
        }

        // Close the options menu when clicking outside
        document.addEventListener('click', () => {
            const optionsMenu = document.getElementById('optionsMenu');
            if (!optionsMenu.classList.contains('hidden')) {
                optionsMenu.classList.add('hidden');
            }
        });

        // Attach the toggle function to the options menu button
        if (optionsMenuBtn) {
            optionsMenuBtn.addEventListener('click', toggleOptionsMenu);
        }

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
            
            // Reinitialize checkboxes after tab switch
            setTimeout(initializeCheckboxes, 100);
            updateOptionsMenu();
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
            
            // Reinitialize checkboxes after tab switch
            setTimeout(initializeCheckboxes, 100);
            updateOptionsMenu();
        });
        
        // Toggle content collapse with arrow rotation
        if (collapseArrow) {
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
        }

        // Back button functionality
        if (backBtn) {
            backBtn.addEventListener('click', () => {
                if (isPanelVisible) {
                    togglePanel();
                }
            });
        }

   
       

        // Handle "mark as read" on click for notifications in the "All" tab
       document.querySelectorAll('#allNotifications a, #unreadNotifications a').forEach(link => {
    link.addEventListener('click', async (e) => {
        e.preventDefault();
        const notificationElement = link.closest('[data-notification-id]');
        if (!notificationElement) return;
        const notificationId = notificationElement.dataset.notificationId;

        // Your existing mark-as-read logic here...
        try {
            const response = await fetch(`/notifications/${notificationId}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            if (response.ok) {
                // Your existing badge update logic...
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
        
        // Navigate to the notification link
        window.location.href = link.href;
    });
});
        // Handle options menu actions (mark as read, unread, delete, delete all)
        function getSelectedNotificationIds() {
            return Array.from(document.querySelectorAll('.notification-checkbox:checked'))
                .map(cb => cb.dataset.notificationId);
        }

        async function bulkAction(action, notificationIds) {
            let url = '';
            let method = 'POST';
            let body = { ids: notificationIds };
            if (action === 'markAsRead') {
                url = '/notifications/mark-as-read';
            } else if (action === 'markAsUnread') {
                url = '/notifications/mark-as-unread';
            } else if (action === 'delete') {
                url = '/notifications/delete';
            } else if (action === 'deleteAll') {
                url = '/notifications/delete-all';
                body = {}; // No ids needed for delete all
            }
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(body)
                });
                if (response.ok) {
                    // Remove or update notifications in UI
                    if (action === 'delete' || action === 'deleteAll') {
                        if (action === 'deleteAll') {
                            document.querySelectorAll('.notification-checkbox').forEach(cb => {
                                const el = cb.closest('[data-notification-id]');
                                if (el) el.remove();
                            });
                        } else {
                            notificationIds.forEach(id => {
                                const el = document.querySelector('[data-notification-id="' + id + '"]');
                                if (el) el.remove();
                            });
                        }
                    } else if (action === 'markAsRead' || action === 'markAsUnread') {
                        notificationIds.forEach(id => {
                            const el = document.querySelector('[data-notification-id="' + id + '"]');
                            if (el) {
                                // Optionally update style to indicate read/unread
                                el.classList.toggle('bg-gray-100', action === 'markAsRead');
                            }
                        });
                    }
                    // Uncheck all checkboxes and update menu
                    document.querySelectorAll('.notification-checkbox').forEach(cb => cb.checked = false);
                    updateOptionsMenu();
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            }
        }

        document.getElementById('markAsReadBtn').addEventListener('click', function() {
            const ids = getSelectedNotificationIds();
            if (ids.length === 0) return;
            showConfirmationModal('Mark selected notifications as read?', function() {
                bulkAction('markAsRead', ids);
            }, 'Mark as Read');
        });
        document.getElementById('markAsUnreadBtn').addEventListener('click', function() {
            const ids = getSelectedNotificationIds();
            if (ids.length === 0) return;
            showConfirmationModal('Mark selected notifications as unread?', function() {
                bulkAction('markAsUnread', ids);
            }, 'Mark as Unread');
        });
        document.getElementById('deleteBtn').addEventListener('click', function() {
            const ids = getSelectedNotificationIds();
            if (ids.length === 0) return;
            showConfirmationModal('Delete selected notifications?', function() {
                bulkAction('delete', ids);
            }, 'Delete Notifications');
        });
        document.getElementById('deleteAllBtn').addEventListener('click', function() {
            showConfirmationModal('Clear All notifications? This cannot be undone.', function() {
                bulkAction('deleteAll', []);
            }, 'Clear All Notifications');
        });

        // Initialize everything
        initializeCheckboxes();
        updateOptionsMenu();
    });
</script>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 z-500 flex  hidden items-center justify-center bg-gray-900/75 w-screen min-h-screen"">
    <div class="bg-white rounded-lg shadow-lg p-6 w-[90vw] sm:w-full sm:max-w-xs">
        <h3 id="confirmationModalTitle" class="text-lg font-semibold mb-2 text-gray-800">Are you sure?</h3>
        <p id="confirmationModalMessage" class="text-gray-600 mb-4 text-sm sm:text-base">Are you sure you want to proceed?</p>
        <div class="flex justify-end space-x-2">
            <button id="confirmationCancelBtn" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 w-full sm:w-auto">Cancel</button>
            <button id="confirmationOkBtn" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 w-full sm:w-auto">Yes</button>
        </div>
    </div>
</div>

<script>
// Confirmation Modal Logic
let confirmAction = null;
function showConfirmationModal(message, onConfirm, title = 'Are you sure?') {
    document.getElementById('confirmationModalMessage').textContent = message;
    document.getElementById('confirmationModalTitle').textContent = title;
    document.getElementById('confirmationModal').classList.remove('hidden');
    confirmAction = onConfirm;
}
function hideConfirmationModal() {
    document.getElementById('confirmationModal').classList.add('hidden');
    confirmAction = null;
}
document.getElementById('confirmationCancelBtn').addEventListener('click', hideConfirmationModal);
document.getElementById('confirmationOkBtn').addEventListener('click', function() {
    if (typeof confirmAction === 'function') confirmAction();
    hideConfirmationModal();
});
</script>