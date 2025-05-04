@include('components.superAdminNavigation') <!-- Include the super admin navigation component -->
@extends('base')<!-- Extend the base component -->
@section('content')<!-- Content section -->
<!-- This is the main content area for the super admin dashboard -->

<!-- Super admin word under the nav var -->
<div class="max-h-9/10 bg-white bg-opacity-30 p-13">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold font-[Lexend] text-[#332B2B] ">SUPER ADMIN</h1>
    </div>

    <!-- Add User Button -->
    <div class="mb-4 flex justify-between items-center">
        <button id="addUserBtn" class="bg-[#7A1212] hover:bg-red-800 text-white px-4 py-2 rounded-[16px] font-semibold font-[Lexend] inline-flex items-center cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" class="mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 5v10m5-5H5" stroke-width="2"/>
            </svg>
            ADD USER
        </button>

    <!-- Activity Log Button -->
    <!-- <button class="group flex items-center bg-white border border-[#4D0F0F] px-3 py-2 rounded-[10px] shadow-sm text-sm font-bold text-[#4D0F0F] hover:bg-red-800 hover:text-white cursor-pointer">
        ACTIVITY LOG
            <svg width="15" height="15" viewBox="0 0 15 15" fill="currentColor" xmlns="http://www.w3.org/2000/svg" class="ml-2 transition-colors duration-200 group-hover:fill-current">
                <g id="radix-icons:activity-log">
                    <path id="Vector" fill-rule="evenodd" clip-rule="evenodd" d="M0 1.5C0 1.36739 0.0526784 1.24021 0.146447 1.14645C0.240215 1.05268 0.367392 1 0.5 1H2.5C2.63261 1 2.75979 1.05268 2.85355 1.14645C2.94732 1.24021 3 1.36739 3 1.5C3 1.63261 2.94732 1.75979 2.85355 1.85355C2.75979 1.94732 2.63261 2 2.5 2H0.5C0.367392 2 0.240215 1.94732 0.146447 1.85355C0.0526784 1.75979 0 1.63261 0 1.5ZM4 1.5C4 1.36739 4.05268 1.24021 4.14645 1.14645C4.24021 1.05268 4.36739 1 4.5 1H14.5C14.6326 1 14.7598 1.05268 14.8536 1.14645C14.9473 1.24021 15 1.36739 15 1.5C15 1.63261 14.9473 1.75979 14.8536 1.85355C14.7598 1.94732 14.6326 2 14.5 2H4.5C4.36739 2 4.24021 1.94732 4.14645 1.85355C4.05268 1.75979 4 1.63261 4 1.5ZM4 4.5C4 4.36739 4.05268 4.24021 4.14645 4.14645C4.24021 4.05268 4.36739 4 4.5 4H11.5C11.6326 4 11.7598 4.05268 11.8536 4.14645C11.9473 4.24021 12 4.36739 12 4.5C12 4.63261 11.9473 4.75979 11.8536 4.85355C11.7598 4.94732 11.6326 5 11.5 5H4.5C4.36739 5 4.24021 4.94732 4.14645 4.85355C4.05268 4.75979 4 4.63261 4 4.5ZM0 7.5C0 7.36739 0.0526784 7.24021 0.146447 7.14645C0.240215 7.05268 0.367392 7 0.5 7H2.5C2.63261 7 2.75979 7.05268 2.85355 7.14645C2.94732 7.24021 3 7.36739 3 7.5C3 7.63261 2.94732 7.75979 2.85355 7.85355C2.75979 7.94732 2.63261 8 2.5 8H0.5C0.367392 8 0.240215 7.94732 0.146447 7.85355C0.0526784 7.75979 0 7.63261 0 7.5ZM4 7.5C4 7.36739 4.05268 7.24021 4.14645 7.14645C4.24021 7.05268 4.36739 7 4.5 7H14.5C14.6326 7 14.7598 7.05268 14.8536 7.14645C14.9473 7.24021 15 7.36739 15 7.5C15 7.63261 14.9473 7.75979 14.8536 7.85355C14.7598 7.94732 14.6326 8 14.5 8H4.5C4.36739 8 4.24021 7.94732 4.14645 7.85355C4.05268 7.75979 4 7.63261 4 7.5ZM4 10.5C4 10.3674 4.05268 10.2402 4.14645 10.1464C4.24021 10.0527 4.36739 10 4.5 10H11.5C11.6326 10 11.7598 10.0527 11.8536 10.1464C11.9473 10.2402 12 10.3674 12 10.5C12 10.6326 11.9473 10.7598 11.8536 10.8536C11.7598 10.9473 11.6326 11 11.5 11H4.5C4.36739 11 4.24021 10.9473 4.14645 10.8536C4.05268 10.7598 4 10.6326 4 10.5ZM0 13.5C0 13.3674 0.0526784 13.2402 0.146447 13.1464C0.240215 13.0527 0.367392 13 0.5 13H2.5C2.63261 13 2.75979 13.0527 2.85355 13.1464C2.94732 13.2402 3 13.3674 3 13.5C3 13.6326 2.94732 13.7598 2.85355 13.8536C2.75979 13.9473 2.63261 14 2.5 14H0.5C0.367392 14 0.240215 13.9473 0.146447 13.8536C0.0526784 13.7598 0 13.6326 0 13.5ZM4 13.5C4 13.3674 4.05268 13.2402 4.14645 13.1464C4.24021 13.0527 4.36739 13 4.5 13H14.5C14.6326 13 14.7598 13.0527 14.8536 13.1464C14.9473 13.2402 15 13.3674 15 13.5C15 13.6326 14.9473 13.7598 14.8536 13.8536C14.7598 13.9473 14.6326 14 14.5 14H4.5C4.36739 14 4.24021 13.9473 4.14645 13.8536C4.05268 13.7598 4 13.6326 4 13.5Z" />
                </g>
            </svg>
    </button> -->
</div>

    <!-- Table Header and Container -->
    <div class="overflow-hidden rounded-[25px] shadow bg-[#D9D9D9]"  style="width: 100%; height: 384px; flex-shrink:0;">
        <table class="min-w-full bg-[#DAA520] text-white rounded-t-[24px] table-fixed">
                    <thead>
                <tr>
                    <th class="px-6 py-3 text-left pl-40 font-['Manrope'] text-[17px] font-bold">
                        <div class="flex items-center">
                            <span>Username</span>
                            <div class="flex flex-col ml-2">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'username', 'direction' => 'asc']) }}"
                            class="focus:outline-none hover:bg-gray-100/20 rounded-sm p-0.5 {{ ($sortField === 'username' && $sortDirection === 'asc') ? 'text-yellow-300' : '' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M6 0L11.1962 9H0.803848L6 0Z" fill="white"/>
                                    </svg>
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'username', 'direction' => 'desc']) }}"
                            class="focus:outline-none hover:bg-gray-100/20 rounded-sm p-0.5 -mt-1 {{ ($sortField === 'username' && $sortDirection === 'desc') ? 'text-yellow-300' : '' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M6 12L0.803848 3L11.1962 3L6 12Z" fill="white"/>
                                    </svg>
                            </a>
                            </div>
                        </div>
                    </th>
                    <th class="px-6 py-3 text-center font-['Manrope'] text-[17px] font-bold">
                        <div class="flex items-center justify-center">
                            <span>Role</span>
                            <div class="flex flex-col ml-2">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'role_name', 'direction' => 'asc']) }}"
                                class="focus:outline-none hover:bg-gray-100/20 rounded-sm p-0.5 {{ ($sortField === 'role_name' && $sortDirection === 'asc') ? 'text-yellow-300' : '' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M6 0L11.1962 9H0.803848L6 0Z" fill="white"/>
                                    </svg>
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'role_name', 'direction' => 'desc']) }}"
                                class="focus:outline-none hover:bg-gray-100/20 rounded-sm p-0.5 -mt-1 {{ ($sortField === 'role_name' && $sortDirection === 'desc') ? 'text-yellow-300' : '' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M6 12L0.803848 3L11.1962 3L6 12Z" fill="white"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </th>
                    <th class="px-6 py-3 text-right pr-40 font-['Manrope'] text-[17px] font-bold">
                        <div class="flex items-center justify-end">
                            <span>Creation Date</span>
                            <div class="flex flex-col ml-2">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => 'asc']) }}"
                                class="focus:outline-none hover:bg-gray-100/20 rounded-sm p-0.5 {{ ($sortField === 'created_at' && $sortDirection === 'asc') ? 'text-yellow-300' : '' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M6 0L11.1962 9H0.803848L6 0Z" fill="white"/>
                                    </svg>
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => 'desc']) }}"
                            class="focus:outline-none hover:bg-gray-100/20 rounded-sm p-0.5 -mt-1 {{ ($sortField === 'created_at' && $sortDirection === 'desc') ? 'text-yellow-300' : '' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M6 12L0.803848 3L11.1962 3L6 12Z" fill="white"/>
                                    </svg>
                            </a>
                            </div>
                        </div>
                    </th>
                </tr>
            </thead>
            <!-- For fetching table contents from database -->
            <tbody class="divide-y divide-[#7A1212]/70">
                @forelse ($users as $user)
                <tr class="border-y-[0.1px] border-[#7A1212] bg-[#d9c698] hover:bg-[#DAA520] transition duration-300 cursor-pointer user-details-row"
                    data-user="{{ $user->toJson() }}">
                    <td class="px-6 py-4 text-left pl-40 text-[Lexend] text-[17px] text-black text-semibold">
                        {{ $user->username }}
                    </td>
                    <td class="px-6 py-4 text-center text-[Lexend] text-[17px] text-black text-semibold">
                        {{ $user->role_name }}
                    </td>
                    <td class="px-6 py-4 text-right pr-40 text-[Lexend] text-[17px] text-black text-semibold">
                        {{ $user->created_at->format('M-d-Y') }}
                    </td>

                </tr>
                @empty
                <tr class="h-[68px] border-t-[0.3px] border-[#7A1212] bg-[#DAA52080]">
                    <td colspan="3" class="px-6 py-4 text-center font-['Manrope'] text-[17px] text-[#625B5BB2]">No users found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <!-- This shows when there are no users to be displayed -->
        @if($users->isEmpty())
            <div class="bg-[#D9D9D9] h-[480px] flex-grow flex items-center justify-center text-gray-600 rounded-b-[25px] px-6" style="height: 100%;">
                <span class="font-['Manrope'] text-[17px] text-[#625B5BB2]">No added user.</span>
            </div>
        @endif
    </div>
</div>

<!-- Pagination Buttons -->
<div class="flex justify-between items-center px-15" style="width: 100%;">
    <button
        class="flex items-center bg-[#7A121280] px-4 py-2 rounded-[8px] hover:bg-red-800 text-white disabled:opacity-50 disabled:cursor-not-allowed"
        {{ $users->onFirstPage() ? 'disabled' : '' }}
        onclick="window.location.href='{{ $users->previousPageUrl() }}'"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18" />
        </svg>
        Previous
    </button>
    <!-- Pagination Indicator -->
    <div class="flex items-center space-x-2 font-[Lexend] text-black">
        <span>Page</span>
        <span class="border-b-4 rounded-[3px] border-[#7A1212] px-2">{{ $users->currentPage() }}</span>
        <span>of</span>
        <span class="border-b-4 rounded-[3px] border-[#7A1212] px-2">{{ $users->lastPage() }}</span>
    </div>
    <button
        class="flex items-center bg-[#7A121280] px-4 py-2 rounded-[8px] hover:bg-red-800 text-white disabled:opacity-50 disabled:cursor-not-allowed"
        {{ !$users->hasMorePages() ? 'disabled' : '' }}
        onclick="window.location.href='{{ $users->nextPageUrl() }}'"
    >
        Next
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 ml-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
        </svg>
    </button>
</div>

<!-- Modal for Add User Button -->
<div id="addUserModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    
    <!-- Modal Backdrop -->
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm add-user-backdrop"></div>
    
    <!-- Modal Content -->
    <div class="bg-white rounded-[25px] shadow-xl w-full max-w-lg relative z-50">
        
        <!-- Include the Add User component -->
        @include('super-admin.super-admin-component.AddUser')
    </div>
</div>

<!-- User Details Modal -->
<div id="userDetailsModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    
    <!-- Modal Backdrop -->
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm user-details-backdrop"></div>

    <!-- Include the View Account Details component -->
     @include('super-admin.super-admin-component.viewAccDeets')

    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 flex items-center justify-center z-50 {{ session()->has('success') ? '' : 'hidden' }}">

    <!-- Modal Backdrop -->
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm success-modal-backdrop"></div>

    <!-- Modal Content -->
    <div class="bg-white rounded-[16px] shadow-xl w-full max-w-md relative z-50 p-6">
        <!-- Success Message -->
        <div class="text-center mb-6">
            <h3 class="text-xl font-semibold text-gray-800">Account Successfully Added!</h3>
            <p class="text-sm text-gray-500">{{ session('success') }}</p>
        </div>

        <!-- Okay Button -->
        <div class="flex justify-center">
            <button type="button"
                id="closeSuccessModalBtn"
                class="bg-[#7A1212] hover:bg-red-800 text-white px-5 py-2 rounded-[14px] font-semibold font-[Lexend] transition duration-200 cursor-pointer">
                Okay
            </button>
        </div>
    </div>
</div>

<!-- Edit User Deatils Modal -->
<div id="editUserModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    
    <!-- Modal Backdrop -->
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm edit-user-backdrop"></div>
    
    <!-- Include the Edit User Account Details component -->
    @include('super-admin.super-admin-component.editUserDeets')
    </div>
</div>
@vite('resources/js/superAdmin.js') <!-- Include the JavaScript file -->
@endsection