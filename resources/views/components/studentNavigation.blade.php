<!--STUDENT NAVIGATION-->
@extends('base')

@section('body')
<div class="flex relative">
    <!-- Sidebar -->
    <div id="sidebar" class="w-1/4 h-screen bg-[#7A1212] text-white p-6 transition-all duration-300 flex flex-col">
        <div class="flex items-center space-x-2">
            <a href="#">
                <img src="{{ asset('images/officialLogo.svg') }}" alt="Logo" class="h-20 w-20">
            </a>
            <div class="sidebar-text">
                <a href="#">
                    <h1 class="font-[Marcellus_SC] text-xl leading-none">E-SKOLARI<span
                            class="text-yellow-400">★</span>N</h1>
                </a>
                <a href="#">
                    <p class="text-sm mt-1 tracking-wide font-[Marcellus_SC]">Document Management</p>
                </a>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="space-y-4 text-lg font-[Marcellus_SC] mt-6">
            @foreach ([['Home', 'account.svg'], ['Submit Documents', 'submitDocument.svg'], ['Tracker', 'tracker.svg'], ['Calendar', 'calendar.svg'], ['Archive', 'archive.svg'], ['Settings', 'settings.svg']] as [$label, $icon])
                <a href="{{ $label === 'Archive' ? route('student.documentArchive') : '#' }}"
                   class="flex items-center space-x-3 hover:text-yellow-400 transition duration-200">
                    <img src="{{ asset("images/$icon") }}" class="h-6 w-6" alt="{{ $label }} Icon">
                    <span class="sidebar-text">{{ $label }}</span>
                </a>
            @endforeach
        </nav>


        <form method="POST" action="{{ route('logout') }}" class="mt-40">
            @csrf
            <button type="submit"
                class="flex items-center space-x-3 text-white hover:text-yellow-400 transition duration-200">
                <img src="{{ asset('images/logout.svg') }}" class="h-6 w-6" alt="Logout Icon">
                <span class="sidebar-text font-[Marcellus_SC]">Logout</span>
            </button>
        </form>
    </div>

    <!-- Fixed Toggle Button -->
    <button onclick="toggleSidebar()" class="absolute top-7 left-[24%] z-10 transition-all duration-300" id="toggleBtn">
        <img src="{{ asset('images/toggleSidebar.svg') }}" alt="Toggle Sidebar"
            class="h-10 w-10 transition-transform duration-300" id="toggleIcon">
    </button>


    <!-- Main Content -->
    <div class="flex-grow flex flex-col h-screen overflow-hidden">
        <nav class="w-full bg-[#4d0F0F] h-[10%] p-4 text-white flex justify-end items-center space-x-6">
            <a href="#" class="hover:text-yellow-400 transition duration-200">
                <img src="{{ asset('images/mail.svg') }}" class="h-6 w-6" alt="Mail Icon">
            </a>
            <div>
                <img src="{{ auth()->user()->profile_pic ? asset(auth()->user()->profile_pic) : asset('images/profiles/default.png') }}"
                    alt="Profile" class="h-10 w-10 rounded-full border-2 border-white">
            </div>
            <div>
                <a href="#" class="font-semibold">{{ auth()->user()->username }}</a>
            </div>
        </nav>

        <!-- Content Section -->
        <div class="flex-1 overflow-y-auto bg-[#f2f4f7]">
            @yield('content')
        </div>
    </div>
</div>

<!-- Toggle Sidebar Script -->
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const texts = sidebar.querySelectorAll('.sidebar-text');
        const toggleBtn = document.getElementById('toggleBtn');
        const toggleIcon = document.getElementById('toggleIcon');
        const logo = document.querySelector('img[alt="Logo"]');

        sidebar.classList.toggle('w-1/4');
        sidebar.classList.toggle('w-20');

        // Move toggle button position
        if (sidebar.classList.contains('w-20')) {
            toggleBtn.classList.add('left-[4rem]');
            toggleBtn.classList.remove('left-[24%]');

            // Increase logo size by 10% when sidebar is collapsed
            logo.classList.remove('h-20', 'w-20');
            logo.classList.add('h-22', 'w-22');
        } else {
            toggleBtn.classList.remove('left-[4rem]');
            toggleBtn.classList.add('left-[24%]');

            // Return logo to original size when sidebar is expanded
            logo.classList.remove('h-22', 'w-22');
            logo.classList.add('h-20', 'w-20');
        }

        // Toggle hidden text
        texts.forEach(text => text.classList.toggle('hidden'));

        // Rotate toggle icon
        toggleIcon.classList.toggle('rotate-180');
    }
</script>
