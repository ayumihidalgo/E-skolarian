{{-- admin sidebar component --}}
<div id="sidebar"
    class="fixed top-0 left-0 w-1/5 h-screen bg-[#7A1212] text-white p-6 z-50 transition-all duration-300 flex flex-col">
    <div class="flex items-center space-x-2">
        <a href="#">
            <img src="{{ asset('images/officialLogo.svg') }}" alt="Logo" class="h-20 w-20">
        </a>
        <div class="sidebar-text">
            <a href="#">
                <h1 class="font-[Marcellus_SC] text-xl leading-none">E-SKOLARI<span class="text-yellow-400">★</span>N
                </h1>
            </a>
            <a href="#">
                <p class="text-sm mt-1 tracking-wide font-[Marcellus_SC]">Document Management</p>
            </a>
        </div>
    </div>

    <button id="sidebarToggleBtn" class="absolute top-11 -right-5 bg-[#7A1212] rounded-r-lg p-1 z-10 transition-all duration-300">
        <img src="{{ asset('images/toggleSidebar.svg') }}" alt="Toggle Sidebar" class="h-10 w-10 transition-transform duration-300" id="toggleIcon">
    </button>

    <!-- Navigation Links -->
    <nav class="space-y-4 text-lg font-[Marcellus_SC] mt-6">
        @foreach ([['Home', 'account.svg', '#'], ['Review', 'archive.svg', route('admin.review')], ['Archive', 'tracker.svg', route('admin.documentArchive')], ['Calendar', 'calendar.svg', '#'], ['Settings', 'settings.svg', '#']] as [$label, $icon, $route])
            <a href="{{ $route }}"
                class="flex items-center space-x-3 hover:text-yellow-400 transition duration-200">
                <img src="{{ asset("images/$icon") }}" class="h-6 w-6" alt="{{ $label }} Icon">
                <span class="sidebar-text">{{ $label }}</span>
            </a>
        @endforeach

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center space-x-3 hover:text-yellow-400 transition duration-200 mt-60">
                <img src="{{ asset('images/logout.svg') }}" class="h-6 w-6" alt="Logout Icon">
                <span class="sidebar-text font-[Marcellus_SC] text-lg">Logout</span>
            </button>
        </form>
    </nav>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const toggleIcon = document.getElementById('toggleIcon');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');

        // Initialize from localStorage if available
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

        if (sidebarCollapsed) {
            collapseSidebar();
        }

        toggleBtn.addEventListener('click', function() {
            if (sidebar.classList.contains('w-1/5')) {
                collapseSidebar();
                localStorage.setItem('sidebarCollapsed', 'true');
            } else {
                expandSidebar();
                localStorage.setItem('sidebarCollapsed', 'false');
            }
        });

        function collapseSidebar() {
            sidebar.classList.remove('w-1/5');
            sidebar.classList.add('w-20');
            mainContent.classList.remove('ml-[20%]');
            mainContent.classList.add('ml-20');
            toggleIcon.classList.add('rotate-180');
            sidebarTexts.forEach(text => text.classList.add('hidden'));
        }

        function expandSidebar() {
            sidebar.classList.add('w-1/5');
            sidebar.classList.remove('w-20');
            mainContent.classList.add('ml-[20%]');
            mainContent.classList.remove('ml-20');
            toggleIcon.classList.remove('rotate-180');
            sidebarTexts.forEach(text => text.classList.remove('hidden'));
        }

        // Handle responsive design for smaller screens
        function handleResponsive() {
            if (window.innerWidth < 768) {
                collapseSidebar();
            } else if (!sidebarCollapsed && !sidebar.classList.contains('w-1/5')) {
                expandSidebar();
            }
        }

        window.addEventListener('resize', handleResponsive);
        handleResponsive(); // Run on initial load
    });
</script>
