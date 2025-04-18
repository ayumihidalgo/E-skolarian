@extends('base')

@section('content')
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-1/4 h-screen bg-[#7A1212] text-white p-6">
            <div class="flex items-center space-x-1">
                <!-- Logo on the left -->
                <a href="#">
                    <img src="{{ asset('images/officialLogo.svg') }}" alt="Logo" class="h-20 w-20">
                </a>
                <!-- Text on the right -->
                <div>
                    <a href="#">
                        <h1 class="font-[Marcellus_SC] text-2xl leading-none">E-SKOLARIAN</h1>
                    </a>
                    <a href = "#">
                        <p class="text-sm mt-1 tracking-wide font-[Marcellus_SC]">TEMPORARY LOGO</p>
                    </a>
                </div>
            </div>
            <!-- Navigation Links -->
            <nav class="space-y-4 text-lg font-[Marcellus_SC] mr-[-10] mt-5">
                <a href="#" class="flex items-center space-x-3 hover:text-yellow-400 transition duration-200">
                    <img src="{{ asset('images/account.svg') }}" class="h-5 w-5 " alt="Account Icon">
                    <span>Home</span>
                </a>
                <a href="#" class="flex items-center space-x-3 hover:text-yellow-400 transition duration-200">
                    <img src="{{ asset('images/review.svg') }}" class="h-5 w-5" alt="Submit Icon">
                    <span>Review</span>
                </a>
                <a href="#" class="flex items-center space-x-3 hover:text-yellow-400 transition duration-200">
                    <img src="{{ asset('images/archive.svg') }}" class="h-5 w-5" alt="Archive Icon">
                    <span>Archive</span>
                </a>
                <a href="#" class="flex items-center space-x-3 hover:text-yellow-400 transition duration-200">
                    <img src="{{ asset('images/calendar.svg') }}" class="h-5 w-5" alt="Calendar Icon">
                    <span>Calendar</span>
                </a>

                <a href="#" class="flex items-center space-x-3 hover:text-yellow-400 transition duration-200">
                    <img src="{{ asset('images/settings.svg') }}" class="h-5 w-5" alt="Settings Icon">
                    <span>Settings</span>
                </a>
            </nav>
        </div>
        <div class="flex-grow">
            <!-- Navigation Bar -->
            <nav class="w-full bg-[#4d0F0F] h-[10%] p-4 text-white flex justify-end items-center space-x-6">
                <!-- Email Icon -->
                <a href="#" class="flex items-center space-x-3 hover:text-yellow-400 transition duration-200">
                    <img src="{{ asset('images/mail.svg') }}" class="h-5 w-5" alt="Settings Icon">
                </a>
                <div>
                    <a href="#" class="font-semibold">Admin</a>
                </div>
            </nav>
        </div>
    </div>
@endsection
