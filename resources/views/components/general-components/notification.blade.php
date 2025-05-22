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
            <div class="right-nav flex flex-row space-x-5">
                <!-- Dots Icon -->
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg" class="cursor-pointer hover:text-gray-700 transition-colors duration-300">
                    <path d="M4.75 8.5C3.925 8.5 3.25 9.175 3.25 10C3.25 10.825 3.925 11.5 4.75 11.5C5.575 11.5 6.25 10.825 6.25 10C6.25 9.175 5.575 8.5 4.75 8.5ZM15.25 8.5C14.425 8.5 13.75 9.175 13.75 10C13.75 10.825 14.425 11.5 15.25 11.5C16.075 11.5 16.75 10.825 16.75 10C16.75 9.175 16.075 8.5 15.25 8.5ZM10 8.5C9.175 8.5 8.5 9.175 8.5 10C8.5 10.825 9.175 11.5 10 11.5C10.825 11.5 11.5 10.825 11.5 10C11.5 9.175 10.825 8.5 10 8.5Z" fill="#525866"/>
                </svg>
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
            <div class="hover:bg-gray-100 rounded cursor-pointer transition-colors duration-300" id="collapseArrow">
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
                        <div class="flex items-start space-x-2">
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
                        </div>
                        {{-- <div class="flex items-center">
                            <input type="checkbox" 
                                class="mark-as-read-checkbox w-4 h-4 text-purple-600 rounded border-gray-300 focus:ring-purple-500"
                                data-notification-id="{{ $notification->id }}"
                                @if($notification->is_read) checked @endif
                            >
                        </div> --}}
                    </div>
                </div>
                @endforeach
            </div>
            </a>
            
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
                            <div class="flex items-start space-x-2">
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
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                    class="mark-as-read-checkbox w-4 h-4 text-purple-600 rounded border-gray-300 focus:ring-purple-500"
                                    data-notification-id="{{ $notification->id }}"
                                >
                            </div>
                        </div>
                    </div>
                    </a>
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

<!-- Confirmation Modal -->
<div id="markAsReadModal" class="fixed inset-0 z-500 hidden flex items-center justify-center bg-gray-900/75 w-screen min-h-screen">
    <div class="bg-white rounded-lg shadow-lg p-6 w-[90vw] sm:w-full sm:max-w-xs">
        <h3 class="text-lg font-semibold mb-2 text-gray-800">Mark as Read?</h3>
        <p class="text-gray-600 mb-4 text-sm sm:text-base">Are you sure you want to mark this notification as read?</p>
        <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-2">
            <button id="cancelMarkAsRead" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 w-full sm:w-auto">Cancel</button>
            <button id="confirmMarkAsRead" class="px-4 py-2 rounded bg-purple-600 text-white hover:bg-purple-700 w-full sm:w-auto">Yes</button>
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
        const modal = document.getElementById('markAsReadModal');
        const confirmBtn = document.getElementById('confirmMarkAsRead');
        const cancelBtn = document.getElementById('cancelMarkAsRead');
        const backBtn = document.getElementById('backBtn');
        
        // Fixed height for notification body
        const NOTIFICATION_HEIGHT = '24rem'; // Adjust as needed
        let isCollapsed = false;
        let isPanelVisible = false;
        let pendingCheckbox = null;
        let pendingNotificationId = null;
        
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

        // Handle mark as read functionality
        document.querySelectorAll('.mark-as-read-checkbox').forEach(checkbox => {
            checkbox.addEventListener('click', function(event) {
                // Prevent the checkbox click from triggering the link navigation
                event.stopPropagation();
                event.preventDefault();

                // If checked, show modal for confirmation
                if (this.checked) {
                    pendingCheckbox = this;
                    pendingNotificationId = this.dataset.notificationId;
                    modal.classList.remove('hidden');
                } else {
                    // If unchecked, just revert (no unmarking in this UI)
                    this.checked = false;
                }
            });
        });

        cancelBtn.addEventListener('click', (event) => {
            event.stopPropagation(); // Prevent the event from propagating to the document click listener
            if (pendingCheckbox) {
                pendingCheckbox.checked = false;
            }
            modal.classList.add('hidden');
            pendingCheckbox = null;
            pendingNotificationId = null;
            
        });

        confirmBtn.addEventListener('click', async () => {
        event.stopPropagation();
            if (!pendingCheckbox || !pendingNotificationId) return;
            
            try {
                const response = await fetch(`/notifications/${pendingNotificationId}/mark-as-read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    // Remove notification from unread tab
                    const notificationElement = document.querySelector(`#unreadNotifications [data-notification-id="${pendingNotificationId}"]`);
                    if (notificationElement) {
                        notificationElement.remove();
                    }
                    
                    // Update unread count in badge
                    const unreadCount = document.querySelector('#unreadTab span');
                    if (unreadCount) {
                        const currentCount = parseInt(unreadCount.textContent);
                        if (currentCount > 1) {
                            unreadCount.textContent = currentCount - 1;
                        } else {
                            unreadCount.remove();
                        }
                    }

                    // Update notification button badge
                    const buttonBadge = document.querySelector('#notificationBtn span');
                    if (buttonBadge) {
                        const currentCount = parseInt(buttonBadge.textContent);
                        if (currentCount > 1) {
                            buttonBadge.textContent = currentCount - 1;
                        } else {
                            buttonBadge.remove();
                        }
                    }

                    // Update the checkbox state in the All tab
                    const allTabNotification = document.querySelector(`#allNotifications [data-notification-id="${pendingNotificationId}"]`);
                    if (allTabNotification) {
                        const checkbox = allTabNotification.querySelector('.mark-as-read-checkbox');
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    }
                } else {
                    alert('Failed to update notification. Please try again.');
                    pendingCheckbox.checked = false;
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
                pendingCheckbox.checked = false;
            }
            
            modal.classList.add('hidden');
            pendingCheckbox = null;
            pendingNotificationId = null;
        });
        // Back button functionality
        backBtn.addEventListener('click', () => {
            if (isPanelVisible) {
                togglePanel();
            }
        });

        // Handle "mark as read" on click for unread notifications in the "Unread" tab
        document.querySelectorAll('#unreadNotifications a').forEach(link => {
            link.addEventListener('click', async (e) => {
                e.preventDefault();
                const notificationElement = link.querySelector('[data-notification-id]');
                if (!notificationElement) return;
                const notificationId = notificationElement.dataset.notificationId;
                
                try {
                    const response = await fetch(`/notifications/${notificationId}/mark-as-read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    if (response.ok) {
                        // Remove the unread notification from the unread view
                        notificationElement.closest('.p-4').remove();
                        
                        // Update unread badge in the Unread tab
                        const unreadBadge = document.querySelector('#unreadTab span');
                        if (unreadBadge) {
                            let count = parseInt(unreadBadge.textContent);
                            count = count > 1 ? count - 1 : 0;
                            if (count === 0) {
                                unreadBadge.remove();
                            } else {
                                unreadBadge.textContent = count;
                            }
                        }
                        
                        // Update the notification button badge
                        const btnBadge = document.querySelector('#notificationBtn span');
                        if (btnBadge) {
                            let count = parseInt(btnBadge.textContent);
                            count = count > 1 ? count - 1 : 0;
                            if (count === 0) {
                                btnBadge.remove();
                            } else {
                                btnBadge.textContent = count;
                            }
                        }
                    } else {
                        alert('Failed to mark notification as read. Please try again.');
                    }
                } catch (error) {
                    console.error('Error marking notification as read:', error);
                }
                
                // Navigate to the notification link after processing
                window.location.href = link.href;
            });
        });

        // Handle "mark as read" on click for notifications in the "All" tab
        document.querySelectorAll('#allNotifications a').forEach(link => {
            link.addEventListener('click', async (e) => {
                e.preventDefault();
                const notificationElement = link.querySelector('[data-notification-id]');
                if (!notificationElement) return;
                const notificationId = notificationElement.dataset.notificationId;

                try {
                    const response = await fetch(`/notifications/${notificationId}/mark-as-read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    if (response.ok) {
                        // Update unread badge in the Unread tab
                        const unreadBadge = document.querySelector('#unreadTab span');
                        if (unreadBadge) {
                            let count = parseInt(unreadBadge.textContent);
                            count = count > 1 ? count - 1 : 0;
                            if (count === 0) {
                                unreadBadge.remove();
                            } else {
                                unreadBadge.textContent = count;
                            }
                        }

                        // Update the notification button badge
                        const btnBadge = document.querySelector('#notificationBtn span');
                        if (btnBadge) {
                            let count = parseInt(btnBadge.textContent);
                            count = count > 1 ? count - 1 : 0;
                            if (count === 0) {
                                btnBadge.remove();
                            } else {
                                btnBadge.textContent = count;
                            }
                        }

                        // Update the checkbox state in the All tab
                        const checkbox = notificationElement.querySelector('.mark-as-read-checkbox');
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    } else {
                        alert('Failed to mark notification as read. Please try again.');
                    }
                } catch (error) {
                    console.error('Error marking notification as read:', error);
                }

                // Navigate to the notification link after processing
                window.location.href = link.href;
            });
        });

    });
</script>