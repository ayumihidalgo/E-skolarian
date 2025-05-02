<!--STUDENT NAVIGATION-->
@extends('base')
<!-- Main Content -->
<div class="flex-grow">
    <nav class="w-full bg-[#4d0F0F] h-[10%] p-4 text-white flex justify-end items-center space-x-6">
        <a href="#" class="hover:text-yellow-400 transition duration-200">
            <img src="{{ asset('images/mail.svg') }}" class="h-6 w-6" alt="Mail Icon">
        </a>
        <div>
            <img src="#" alt="Profile" class="h-10 w-10 rounded-full border-2 border-white">
        </div>

        <!-- Navigation Links -->
        <nav class="space-y-4 text-lg font-[Marcellus_SC] mt-6">
            @foreach ([['Home', 'account.svg'], ['Submit Documents', 'submitDocument.svg'], ['Tracker', 'tracker.svg'], ['Calendar', 'calendar.svg'], ['Archive', 'archive.svg'], ['Settings', 'settings.svg']] as [$label, $icon])
                <a href="#" class="flex items-center space-x-3 hover:text-yellow-400 transition duration-200">
                    <img src="{{ asset("images/$icon") }}" class="h-6 w-6" alt="{{ $label }} Icon">
                    <span class="sidebar-text">{{ $label }}</span>
                </a>
            @endforeach
        </nav>

            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="mt-40">
                @csrf
                <button type="submit" class="flex items-center space-x-3 text-white hover:text-yellow-400 transition duration-200">
                    <img src="{{ asset('images/logout.svg') }}" class="h-6 w-6" alt="Logout Icon">
                    <span class="sidebar-text font-[Marcellus_SC]">Logout</span>
                </button>
            </form>
        </div>

        <!-- Fixed Toggle Button -->
        <button onclick="toggleSidebar()" class="absolute top-11 left-[24%] z-10 transition-all duration-300" id="toggleBtn">
            <img src="{{ asset('images/toggleSidebar.svg') }}" alt="Toggle Sidebar"
                 class="h-10 w-10 transition-transform duration-300" id="toggleIcon">
        </button>

        <!-- Main Content Area -->
        <div class="flex-grow flex flex-col h-screen overflow-hidden">
            <!-- Top Nav -->
            <nav class="w-full bg-[#4d0F0F] h-[10%] p-4 text-white flex justify-end items-center space-x-6">
                <a href="#" class="hover:text-yellow-400 transition duration-200">
                    <img src="{{ asset('images/mail.svg') }}" class="h-6 w-6" alt="Mail Icon">
                </a>
                <div>
                    <img src="#" alt="Profile" class="h-10 w-10 rounded-full border-2 border-white">
                </div>
                <div>
                    <a href="#" class="font-semibold">Organization</a>
                </div>
            </nav>

            <!-- Page Content from Child Views -->
            <div class="p-0">
                @yield('studentContent')
            </div>
        </div>
    </div>

           <div>
                <a href="#" class="font-semibold">{{ auth()->user()->username }}</a>
            </div>  
        </nav>
    </div>
</div>


<!-- Toggle Sidebar Script -->
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const texts = sidebar.querySelectorAll('.sidebar-text');
        const toggleBtn = document.getElementById('toggleBtn');
        const toggleIcon = document.getElementById('toggleIcon');

        sidebar.classList.toggle('w-1/4');
        sidebar.classList.toggle('w-20');

        // Move toggle button position
        if (sidebar.classList.contains('w-20')) {
            toggleBtn.classList.add('left-[4rem]');
            toggleBtn.classList.remove('left-[24%]');
        } else {
            toggleBtn.classList.remove('left-[4rem]');
            toggleBtn.classList.add('left-[24%]');
        }

        // Toggle hidden text
        texts.forEach(text => text.classList.toggle('hidden'));

        // Rotate toggle icon
        toggleIcon.classList.toggle('rotate-180');
    }
</script>
