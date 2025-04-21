@extends('base')

@section('content')
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
                @foreach ([['Home', 'account.svg'], ['Review', 'archive.svg'], ['Archive', 'tracker.svg'], ['Calendar', 'calendar.svg'], ['Settings', 'settings.svg']] as [$label, $icon])
                    <a href="#" class="flex items-center space-x-3 hover:text-yellow-400 transition duration-200">
                        <img src="{{ asset("images/$icon") }}" class="h-6 w-6" alt="{{ $label }} Icon">
                        <span class="sidebar-text">{{ $label }}</span>
                    </a>
                @endforeach
                <a href="{{ route('logout') }}"
                    class="flex items-center space-x-3 hover:text-yellow-400 transition duration-200 mt-60">
                    <img src="{{ asset('images/logout.svg') }}" class="h-6 w-6" alt="Logout Icon">
                    <span class="sidebar-text font-[Marcellus_SC] text-lg">Logout</span>
                </a>
            </nav>
        </div>

        <!-- Fixed Toggle Button -->
        <button onclick="toggleSidebar()" class="absolute top-11 left-[24%] z-10 transition-all duration-300"
            id="toggleBtn">
            <img src="{{ asset('images/toggleSidebar.svg') }}" alt="Toggle Sidebar"
                class="h-10 w-10 transition-transform duration-300" id="toggleIcon">
        </button>


        <!-- Main Content -->
        <div class="flex-grow">
            <nav class="w-full bg-[#4d0F0F] h-[10%] p-4 text-white flex justify-end items-center space-x-6">
                <a href="#" class="hover:text-yellow-400 transition duration-200">
                    <img src="{{ asset('images/mail.svg') }}" class="h-5 w-5" alt="Mail Icon">
                </a>
                <div>
                    <a href="#" class="font-semibold">Organization</a>
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
@endsection
