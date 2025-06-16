@extends('base')
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Top Navigation Header -->
<div class="w-full bg-[#4d0F0F] h-[90px] flex items-center justify-between px-4 md:px-10 lg:px-15">
    <!-- Left side: Logo and Text -->
    <div class="flex items-center space-x-3">
        <img src="{{ asset('images/superAdminIcon.svg') }}" alt="Logo" class="h-12 w-12 sm:h-15 sm:w-15">
        <div class="text-white">
            <h1 class="font-[Marcellus_SC] text-lg sm:text-xl leading-none">E-SKOLARI<span class="text-yellow-400">★</span>N</h1>
            <p class="text-xs tracking-wide font-[Marcellus_SC]">DOCUMENT MANAGEMENT</p>
        </div>
    </div>

    <!-- Right side: Reports and Super Admin Dropdown -->
    <div class="flex items-center space-x-3 sm:space-x-4 md:space-x-6 text-white font-[Manrope]">
        <!-- Reports Button -->
        <a href="{{ route('super-admin.reports') }}"
            class="relative flex items-center rounded border border-white space-x-2 p-1 hover:bg-gray-100/50 hover:text-red-500 transition duration-200 cursor-pointer group text-xs sm:text-base">
            <svg class="w-4 h-4 align-middle group-hover:fill-red-500" viewBox="0 0 24 24" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z"
                    stroke="#FFF8E7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="group-hover:stroke-red-500" />
                <path d="M14 2V8H20" stroke="#FFF8E7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="group-hover:stroke-red-500" />
                <path d="M16 13H8" stroke="#FFF8E7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="group-hover:stroke-red-500" />
                <path d="M16 17H8" stroke="#FFF8E7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="group-hover:stroke-red-500" />
                <path d="M10 9H9H8" stroke="#FFF8E7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="group-hover:stroke-red-500" />
            </svg>
            <span class="text-[14px] sm:text-[18px] group-hover:text-red-500">Reports</span>

            @php
                $newReportsCount = \App\Models\ProblemReport::where('viewed', false)->count();
            @endphp

            @if ($newReportsCount > 0)
                <div id="reportsNotificationBadge"
                    class="absolute -top-2 -right-2 bg-[#4D0F0F] border-1 border-white text-white text-xs font-bold px-2 py-1 rounded-full min-w-[20px] text-center">
                    {{ $newReportsCount }}
                </div>
            @else
                <div id="reportsNotificationBadge"
                    class="absolute -top-2 -right-2 bg-[#4D0F0F] border-1 border-white text-white text-xs font-bold px-2 py-1 rounded-full min-w-[20px] text-center hidden">
                    0
                </div>
            @endif
        </a>

        <!-- Profile Picture Container -->
        <div class="flex justify-center">
            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full overflow-hidden bg-gray-200">
                @php
                    $currentUser = auth()->user();
                    $profilePic = null;
                    if ($currentUser) {
                        $profilePic = $currentUser->profile_pic ?? null;
                    }
                @endphp

                @if ($profilePic)
                    <img src="{{ url('/profile-picture/' . basename($currentUser->profile_pic)) }}" alt="Profile"
                        class="w-full h-full object-cover">
                @else
                    <img src="{{ asset('images/dprofile.svg') }}" alt="Default Profile"
                        class="w-full h-full object-cover">
                @endif
            </div>
        </div>

        <!-- Super Admin Dropdown -->
        <div class="relative" id="adminDropdownContainer">
            <button id="adminDropdownBtn" type="button" class="flex items-center space-x-1 sm:space-x-2 cursor-pointer text-base sm:text-xl">
                <span class="xs:inline">Super Admin</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="6" viewBox="0 0 16 6" fill="none">
                    <path d="M8 6L0.205772 0.75H15.7942L8 6Z" fill="white" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div id="adminDropdownMenu"
                class="absolute right-0 mt-2 w-36 sm:w-40 bg-white rounded-[16px] shadow-lg py-1 hidden z-50 border-2 border-[#4D0F0F] cursor-pointer">
                <!-- Settings Option -->
                <a href="{{ route('superadmin.settings') }}"
                    class="flex items-center justify-center px-4 py-2 text-base sm:text-xl text-[#332B2B]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 21 20"
                        fill="none" class="mr-2">
                        <path
                            d="M7.3 20L6.9 16.8C6.68333 16.7167 6.47933 16.6167 6.288 16.5C6.09667 16.3833 5.909 16.2583 5.725 16.125L2.75 17.375L0 12.625L2.575 10.675C2.55833 10.5583 2.55 10.446 2.55 10.338V9.663C2.55 9.55433 2.55833 9.44167 2.575 9.325L0 7.375L2.75 2.625L5.725 3.875C5.90833 3.74167 6.1 3.61667 6.3 3.5C6.5 3.38333 6.7 3.28333 6.9 3.2L7.3 0H12.8L13.2 3.2C13.4167 3.28333 13.621 3.38333 13.813 3.5C14.005 3.61667 14.1923 3.74167 14.375 3.875L17.35 2.625L20.1 7.375L17.525 9.325C17.5417 9.44167 17.55 9.55433 17.55 9.663V10.337C17.55 10.4457 17.5333 10.5583 17.5 10.675L20.075 12.625L17.325 17.375L14.375 16.125C14.1917 16.2583 14 16.3833 13.8 16.5C13.6 16.6167 13.4 16.7167 13.2 16.8L12.8 20H7.3ZM10.1 13.5C11.0667 13.5 11.8917 13.1583 12.575 12.475C13.2583 11.7917 13.6 10.9667 13.6 10C13.6 9.03333 13.2583 8.20833 12.575 7.525C11.8917 6.84167 11.0667 6.5 10.1 6.5C9.11667 6.5 8.28733 6.84167 7.612 7.525C6.93667 8.20833 6.59933 9.03333 6.6 10C6.60067 10.9667 6.93833 11.7917 7.613 12.475C8.28767 13.1583 9.11667 13.5 10.1 13.5Z"
                            fill="#332B2B" />
                    </svg>
                    <span class="text-center">Settings</span>
                </a>
                <hr class="my-1 border-[#4D0F0F]">
                <!-- Logout Option -->
                <form id="logoutForm" method="POST" action="{{ route('superadmin.logout') }}" class="block">
                    @csrf
                    <button type="button" onclick="openLogoutModal()"
                        class="w-full flex items-center justify-center px-4 py-2 text-base sm:text-xl text-[#332B2B] cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" class="mr-2">
                            <path
                                d="M15.855 18.825C15.645 19.0275 15.36 19.14 15.075 19.14C14.925 19.14 14.775 19.11 14.64 19.05C14.22 18.87 13.95 18.465 13.95 18.015V15.885H12.5325V20.16C12.5325 20.67 12.195 21.1125 11.7 21.2475L2.98501 23.5875C2.89501 23.61 2.79751 23.625 2.70001 23.625C2.45254 23.625 2.21254 23.5425 2.01003 23.3925C1.73253 23.1825 1.57501 22.8525 1.57501 22.5V1.5C1.57501 1.14748 1.73253 0.81748 2.01003 0.607503C2.28753 0.39748 2.64756 0.322503 2.98502 0.412495L11.7 2.7525C12.195 2.88749 12.5325 3.33 12.5325 3.84V8.11501H13.95V5.98499C13.95 5.535 14.22 5.12998 14.64 4.94999C15.0525 4.77 15.5325 4.85999 15.855 5.17498L22.0875 11.19C22.305 11.4 22.425 11.6925 22.425 12C22.425 12.3075 22.305 12.6 22.0875 12.81L15.855 18.825ZM10.2825 4.70247L3.82501 2.96997V21.03L10.2825 19.2975V15.885H8.08506C7.46254 15.885 6.96006 15.3825 6.96006 14.76V9.24C6.96006 8.61749 7.46254 8.115 8.08506 8.115H10.2825V4.70247Z"
                                fill="#332B2B" />
                        </svg>
                        <span class="text-center">Log Out</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="logoutConfirmationModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-[9999]">
    <div
        class="bg-white rounded-2xl w-[545px] max-w-[340px] sm:max-w-md shadow-xl relative space-y-2 px-6 py-5 md:py-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="sm:text-base md:text-lg font-semibold font-['Lexend']">Logout?</h2>
            <button type="button" onclick="closeLogoutModal()"
                class="text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                <i class="text-base sm:text-xl fas fa-times"></i>
            </button>
        </div>
        <p class="text-xs sm:text-xs md:text-sm text-gray-700 mb-6">
            Are you sure you want to logout? You will need to login again to access your account.
        </p>
        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeLogoutModal()"
                class="rounded-lg text-gray-900 font-medium px-4 py-2 border-1 border-gray-300 text-[11px] md:text-[12px] lg:text-[14px] font-[Lexend] hover:bg-gray-400 transition cursor-pointer">Cancel</button>
            <button type="button" onclick="confirmLogout()"
                class="rounded-lg bg-red-900 text-white font-medium px-4 py-2 text-[11px] md:text-[12px] lg:text-[14px] font-[Lexend] hover:bg-red-900 transition cursor-pointer">Yes,
                Logout</button>
        </div>
    </div>
</div>
<script>
    function openLogoutModal() {
        document.getElementById('logoutConfirmationModal').classList.remove('hidden');
        document.getElementById('logoutConfirmationModal').classList.add('flex');
    }

    function closeLogoutModal() {
        document.getElementById('logoutConfirmationModal').classList.add('hidden');
        document.getElementById('logoutConfirmationModal').classList.remove('flex');
    }

    function confirmLogout() {
        document.getElementById('logoutForm').submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        let lastKnownCount = null;

        // Function to update the notification badge
        function updateReportsNotificationBadge(count) {
            const badge = document.getElementById('reportsNotificationBadge');
            if (badge) {
                lastKnownCount = count; // Store the last known count
                badge.textContent = count;
                if (count > 0) {
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
        }

        // Check for updates every 30 seconds
        function checkForNewReports() {
            fetch('/super-admin/reports/unviewed-count', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content') || ''
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Only update if we don't have a last known count or if the server count is different
                        // This prevents overriding real-time updates from the reports page
                        if (lastKnownCount === null || data.count !== lastKnownCount) {
                            updateReportsNotificationBadge(data.count);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error checking for new reports:', error);
                });
        }

        // Initial check
        checkForNewReports();

        // Check every 30 seconds for new reports (but don't override real-time updates)
        setInterval(checkForNewReports, 30000);

        // Listen for report viewed events from the reports page
        window.addEventListener('reportViewed', function(event) {
            console.log('Report viewed event received:', event.detail.newCount);
            updateReportsNotificationBadge(event.detail.newCount);
        });

        // Listen for custom events to force update the badge
        window.addEventListener('forceUpdateReportsBadge', function(event) {
            console.log('Force update badge event received:', event.detail.count);
            updateReportsNotificationBadge(event.detail.count);
        });
    });
</script>
