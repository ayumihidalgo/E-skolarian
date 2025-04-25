<!--SUPER ADMIN-->

@extends('base')
    <!-- Top Navigation Header -->
    <div class="w-full bg-[#4d0F0F] h-[90px] flex items-center justify-between px-6">
        <!-- Left side: Logo and Text -->
        <div class="flex items-center space-x-3">
            <img src="{{ asset('images/superAdminIcon.svg') }}" alt="Logo" class="h-14 w-14">
            <div class="text-white">
                <h1 class="font-[Marcellus_SC] text-xl leading-none">E-SKOLARI<span class="text-yellow-400">★</span>N</h1>
                <p class="text-xs tracking-wide font-[Marcellus_SC]">DOCUMENT MANAGEMENT</p>
            </div>
        </div>

        <!-- Right side: Super Admin and Logout -->
        <div class="flex items-center space-x-6 text-white font-[Marcellus_SC]">
            <span>Super Admin</span>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="flex items-center space-x-2 hover:text-yellow-400 transition duration-200">
                    <img src="{{ asset('images/logout.svg') }}" class="h-5 w-5" alt="Logout Icon">
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

