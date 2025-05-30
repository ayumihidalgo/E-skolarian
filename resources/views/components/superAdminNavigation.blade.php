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
        <div class="flex items-center space-x-6 text-white font-[Manrope]">
            <a href="{{ route('super-admin.reports') }}" class="flex items-center rounded border border-white space-x-2 p-1
           hover:bg-gray-100/50 hover:text-red-500
          transition duration-200 cursor-pointer group">
            <svg class="w-4 h-4 align-middle group-hover:fill-red-500" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M8 12.423C8.17467 12.423 8.321 12.364 8.439 12.246C8.55633 12.128 8.615 11.982 8.615 11.808C8.615 11.634 8.556 11.4877 8.438 11.369C8.32 11.2503 8.174 11.1913 8 11.192C7.826 11.1927 7.68 11.2517 7.562 11.369C7.444 11.4863 7.385 11.6327 7.385 11.808C7.385 11.9833 7.444 12.1293 7.562 12.246C7.68 12.3627 7.826 12.4217 8 12.423ZM7.5 9.462H8.5V3.384H7.5V9.462ZM4.673 16L0 11.336V4.673L4.664 0H11.327L16 4.664V11.327L11.336 16H4.673ZM5.1 15H10.9L15 10.9V5.1L10.9 1H5.1L1 5.1V10.9L5.1 15Z" class="group-hover:fill-red-500" fill="#FFF8E7"/>
            </svg>
            <span class="text-[15px] group-hover:text-red-500">Reports</span>
          </a>
 

            
            <span> <a href = "#">Super Admin</a></span>
            <form method="POST" action="{{ route('superadmin.logout') }}" class="mt-0">
                @csrf
                <button type="submit" class="flex items-center space-x-2 top-10 hover:text-yellow-400 transition duration-200 cursor-pointer">
                    <img src="{{ asset('images/logout.svg') }}" class="h-5 w-5" alt="Logout Icon">
                    <span>Logout</span>
                </button>
            </form>
        </div>

    </nav>
    </div>
