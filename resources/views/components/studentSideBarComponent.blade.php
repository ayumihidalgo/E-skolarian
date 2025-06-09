{{-- Student sidebar component components/studentSideBarComponent.blade.php --}}
<div id="sidebar"
    class="fixed top-0 left-0 w-1/5 h-screen bg-[#7A1212] text-white p-6 z-50 transition-all duration-300 flex flex-col
           md:translate-x-0 max-md:w-64 max-md:-translate-x-full">

    <!-- Logo & Title -->
    <div class="flex items-center space-x-2">
        <a href="#">
            <img src="{{ asset('images/officialLogo.svg') }}" alt="Logo" class="h-20 w-20">
        </a>
        <div class="sidebar-text space-y-1">
            <a href="#">
                <h1 class="font-[Marcellus_SC] text-xl leading-none">E-SKOLARI<span class="text-yellow-400">★</span>N
                </h1>
            </a>
            <a href="#">
                <p class="text-sm tracking-wide font-[Marcellus_SC]">Document Management</p>
            </a>
        </div>
    </div>

    <!-- Desktop Toggle Button (hidden on mobile) -->
    <button id="sidebarToggleBtn"
        class="absolute top-11 -right-5 rounded-r-lg p-1 z-10 transition-all duration-300 cursor-pointer max-md:hidden">
        <img src="{{ asset('images/toggleSidebar.svg') }}" alt="Toggle Sidebar"
            class="h-10 w-10 transition-transform duration-300" id="toggleIcon">
    </button>

    <!-- Mobile Close Button (visible on mobile only) -->
    <button id="closeMobileSidebar"
        class="absolute top-6 right-6 text-white hover:text-yellow-400 transition duration-200 md:hidden">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <nav class="flex flex-col justify-between h-full mt-6">
        <div class="space-y-4 text-lg font-[Manrope]">
            @foreach ([['Dashboard', 'newDashboard.svg', route('student.dashboard')], ['Submit Documents', 'submitDocument.svg', route('student.submit-documents')], ['Tracker', 'tracker.svg', route('student.studentTracker')], ['Calendar', 'calendar.svg', route('calendar.index')], ['History', 'archive.svg', route('student.documentHistory')], ['Settings', 'settings.svg', route('student.settings')]] as [$label, $icon, $route])
                <a href="{{ $route }}"
                    class="flex items-center space-x-3 hover:text-yellow-400 transition duration-200 sidebar-link">
                    <img src="{{ asset("images/$icon") }}" class="h-6 w-6" alt="{{ $label }} Icon">
                    <span class="sidebar-text">{{ $label }}</span>
                </a>
            @endforeach
        </div>
        <form method="POST" action="{{ route('student.logout') }}" class="mt-60">
            @csrf
            <button type="submit"
                class="flex items-center space-x-3 text-white hover:text-yellow-400 transition duration-200 cursor-pointer">
                <img src="{{ asset('images/logout.svg') }}" class="h-6 w-6" alt="Logout Icon">
                <span class="sidebar-text font-[Manrope]">Logout</span>
            </button>
        </form>
    </nav>
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

<!-- Updated Navigation Bar -->
<div>
    <nav class="w-full bg-[#4d0F0F] h-[10%] p-4 text-white flex items-center justify-between">
        <!-- Left side - Empty div to push content to right (desktop) and mobile logo -->
        <div class="flex items-center">
            <!-- Mobile logo (hidden on desktop) -->
            <div class="flex items-center space-x-2 md:hidden">
                <img src="{{ asset('images/officialLogo.svg') }}" alt="Logo" class="h-8 w-8">
                <h1 class="font-[Marcellus_SC] text-lg leading-none">E-SKOLARI<span class="text-yellow-400">★</span>N</h1>
            </div>

            <!-- Empty div to take up space on desktop -->
            <div class="hidden md:block flex-grow"></div>
        </div>

        <!-- Right side content -->
        <div class="flex items-center space-x-6">
            <!-- Mobile Menu Button (6 dots) - visible on mobile only -->
            <button id="mobileMenuBtn" class="text-white hover:text-yellow-400 transition duration-200 md:hidden">
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <circle cx="5" cy="5" r="2"/>
                    <circle cx="12" cy="5" r="2"/>
                    <circle cx="19" cy="5" r="2"/>
                    <circle cx="5" cy="12" r="2"/>
                    <circle cx="12" cy="12" r="2"/>
                    <circle cx="19" cy="12" r="2"/>
                    <circle cx="5" cy="19" r="2"/>
                    <circle cx="12" cy="19" r="2"/>
                    <circle cx="19" cy="19" r="2"/>

                </svg>
            </button>

            <!-- Notification and Username - always on right side -->
            <x-general-components.notification />
            <div>
                @auth
                    <a href="#" class="font-semibold">{{ Auth::user()->organization_acronym }}</a>
                @else
                    <a href="#" class="font-semibold">Guest</a>
                @endauth
            </div>
        </div>
    </nav>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const toggleIcon = document.getElementById('toggleIcon');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');

        // Mobile elements
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const closeMobileSidebar = document.getElementById('closeMobileSidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        // Desktop sidebar state
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (sidebarCollapsed && window.innerWidth >= 768) {
            collapseSidebar();
        }

        // Desktop toggle functionality
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                if (sidebar.classList.contains('w-1/5')) {
                    collapseSidebar();
                    localStorage.setItem('sidebarCollapsed', 'true');
                } else {
                    expandSidebar();
                    localStorage.setItem('sidebarCollapsed', 'false');
                }
            });
        }

        // Mobile menu functionality
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function() {
                openMobileSidebar();
            });
        }

        if (closeMobileSidebar) {
            closeMobileSidebar.addEventListener('click', function() {
                closeMobileSidebarFunc();
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                closeMobileSidebarFunc();
            });
        }

        function collapseSidebar() {
            sidebar.classList.remove('w-1/5');
            sidebar.classList.add('w-20');
            if (mainContent) {
                mainContent.classList.remove('ml-[20%]');
                mainContent.classList.add('ml-20');
            }
            if (toggleIcon) {
                toggleIcon.classList.add('rotate-180');
            }
            sidebarTexts.forEach(text => {
                text.classList.add('hidden');
            });
        }

        function expandSidebar() {
            sidebar.classList.add('w-1/5');
            sidebar.classList.remove('w-20');
            if (mainContent) {
                mainContent.classList.add('ml-[20%]');
                mainContent.classList.remove('ml-20');
            }
            if (toggleIcon) {
                toggleIcon.classList.remove('rotate-180');
            }
            sidebarTexts.forEach(text => {
                text.classList.remove('hidden');
            });
        }

        function openMobileSidebar() {
            sidebar.classList.remove('max-md:-translate-x-full');
            sidebar.classList.add('max-md:translate-x-0');
            sidebarOverlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeMobileSidebarFunc() {
            sidebar.classList.add('max-md:-translate-x-full');
            sidebar.classList.remove('max-md:translate-x-0');
            sidebarOverlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function handleResponsive() {
            if (window.innerWidth < 768) {
                // Mobile view - ensure sidebar is hidden and reset desktop states
                sidebar.classList.add('max-md:-translate-x-full');
                sidebar.classList.remove('max-md:translate-x-0');
                sidebarOverlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');

                // Reset desktop classes
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-1/5');
                sidebarTexts.forEach(text => {
                    text.classList.remove('hidden');
                });
                if (toggleIcon) {
                    toggleIcon.classList.remove('rotate-180');
                }
            } else {
                // Desktop view - apply saved state
                sidebar.classList.remove('max-md:-translate-x-full', 'max-md:translate-x-0');
                sidebarOverlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');

                if (sidebarCollapsed) {
                    collapseSidebar();
                } else {
                    expandSidebar();
                }
            }
        }

        window.addEventListener('resize', handleResponsive);
        handleResponsive(); // Initial check

        // Close mobile sidebar when clicking on navigation links
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    closeMobileSidebarFunc();
                }
            });
        });
    });
</script>
