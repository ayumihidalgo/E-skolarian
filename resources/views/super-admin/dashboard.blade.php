@include('components.superAdminNavigation') <!-- Include the super admin navigation component -->
@extends('base')<!-- Extend the base component -->
@section('content')<!-- Content section -->
<!-- This is the main content area for the super admin dashboard -->
<!-- Super admin word under the nav var --><div x-data="{ showAddUserModal: false }">
    <div class="min-h-screen bg-white bg-opacity-30 p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold font-[Lexend] text-[#332B2B] ">SUPER ADMIN</h1>
        </div>
        
        <!-- Modified Add User Button with Alpine.js click handler -->
        <div class="mb-4 flex justify-between items-center">
            <button @click="showAddUserModal = true" class="bg-[#7A1212] hover:bg-red-800 text-white px-4 py-2 rounded-[16px] font-semibold font-[Lexend] inline-flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" class="mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 5v10m5-5H5" stroke-width="2"/>
                </svg>
                ADD USER
            </button>
            
            <!-- Activity Log Button -->

        <button class="group flex items-center bg-white border border-[#4D0F0F] px-3 py-2 rounded-[10px] shadow-sm text-sm font-bold text-[#4D0F0F] hover:bg-red-800 hover:text-white">
            ACTIVITY LOG
                <svg width="15" height="15" viewBox="0 0 15 15" fill="currentColor" xmlns="http://www.w3.org/2000/svg" class="ml-2 transition-colors duration-200 group-hover:fill-current">
                    <g id="radix-icons:activity-log">
                        <path id="Vector" fill-rule="evenodd" clip-rule="evenodd" d="M0 1.5C0 1.36739 0.0526784 1.24021 0.146447 1.14645C0.240215 1.05268 0.367392 1 0.5 1H2.5C2.63261 1 2.75979 1.05268 2.85355 1.14645C2.94732 1.24021 3 1.36739 3 1.5C3 1.63261 2.94732 1.75979 2.85355 1.85355C2.75979 1.94732 2.63261 2 2.5 2H0.5C0.367392 2 0.240215 1.94732 0.146447 1.85355C0.0526784 1.75979 0 1.63261 0 1.5ZM4 1.5C4 1.36739 4.05268 1.24021 4.14645 1.14645C4.24021 1.05268 4.36739 1 4.5 1H14.5C14.6326 1 14.7598 1.05268 14.8536 1.14645C14.9473 1.24021 15 1.36739 15 1.5C15 1.63261 14.9473 1.75979 14.8536 1.85355C14.7598 1.94732 14.6326 2 14.5 2H4.5C4.36739 2 4.24021 1.94732 4.14645 1.85355C4.05268 1.75979 4 1.63261 4 1.5ZM4 4.5C4 4.36739 4.05268 4.24021 4.14645 4.14645C4.24021 4.05268 4.36739 4 4.5 4H11.5C11.6326 4 11.7598 4.05268 11.8536 4.14645C11.9473 4.24021 12 4.36739 12 4.5C12 4.63261 11.9473 4.75979 11.8536 4.85355C11.7598 4.94732 11.6326 5 11.5 5H4.5C4.36739 5 4.24021 4.94732 4.14645 4.85355C4.05268 4.75979 4 4.63261 4 4.5ZM0 7.5C0 7.36739 0.0526784 7.24021 0.146447 7.14645C0.240215 7.05268 0.367392 7 0.5 7H2.5C2.63261 7 2.75979 7.05268 2.85355 7.14645C2.94732 7.24021 3 7.36739 3 7.5C3 7.63261 2.94732 7.75979 2.85355 7.85355C2.75979 7.94732 2.63261 8 2.5 8H0.5C0.367392 8 0.240215 7.94732 0.146447 7.85355C0.0526784 7.75979 0 7.63261 0 7.5ZM4 7.5C4 7.36739 4.05268 7.24021 4.14645 7.14645C4.24021 7.05268 4.36739 7 4.5 7H14.5C14.6326 7 14.7598 7.05268 14.8536 7.14645C14.9473 7.24021 15 7.36739 15 7.5C15 7.63261 14.9473 7.75979 14.8536 7.85355C14.7598 7.94732 14.6326 8 14.5 8H4.5C4.36739 8 4.24021 7.94732 4.14645 7.85355C4.05268 7.75979 4 7.63261 4 7.5ZM4 10.5C4 10.3674 4.05268 10.2402 4.14645 10.1464C4.24021 10.0527 4.36739 10 4.5 10H11.5C11.6326 10 11.7598 10.0527 11.8536 10.1464C11.9473 10.2402 12 10.3674 12 10.5C12 10.6326 11.9473 10.7598 11.8536 10.8536C11.7598 10.9473 11.6326 11 11.5 11H4.5C4.36739 11 4.24021 10.9473 4.14645 10.8536C4.05268 10.7598 4 10.6326 4 10.5ZM0 13.5C0 13.3674 0.0526784 13.2402 0.146447 13.1464C0.240215 13.0527 0.367392 13 0.5 13H2.5C2.63261 13 2.75979 13.0527 2.85355 13.1464C2.94732 13.2402 3 13.3674 3 13.5C3 13.6326 2.94732 13.7598 2.85355 13.8536C2.75979 13.9473 2.63261 14 2.5 14H0.5C0.367392 14 0.240215 13.9473 0.146447 13.8536C0.0526784 13.7598 0 13.6326 0 13.5ZM4 13.5C4 13.3674 4.05268 13.2402 4.14645 13.1464C4.24021 13.0527 4.36739 13 4.5 13H14.5C14.6326 13 14.7598 13.0527 14.8536 13.1464C14.9473 13.2402 15 13.3674 15 13.5C15 13.6326 14.9473 13.7598 14.8536 13.8536C14.7598 13.9473 14.6326 14 14.5 14H4.5C4.36739 14 4.24021 13.9473 4.14645 13.8536C4.05268 13.7598 4 13.6326 4 13.5Z" />
                    </g>
                </svg>
        </button>
        </div>

        <!-- Table Header and Container -->
    <div class="overflow-hidden rounded-[25px] shadow">
        <table class="min-w-full bg-[#DAA520] text-white rounded-t-[24px] table-fixed">
            <thead>
            <tr>
                <th class="px-6 py-3 text-left pl-40 text-white font-['Manrope'] text-[17px] font-bold">Username</th>
                <th class="px-6 py-3 text-center text-white font-['Manrope'] text-[17px] font-bold">Role</th>
                <th class="px-6 py-3 text-right pr-40 text-white font-['Manrope'] text-[17px] font-bold">Creation Date</th>
            </tr>
            </thead>
        </table>
        <div class="bg-[#D9D9D9] flex-grow flex items-center justify-center text-gray-600 rounded-b-[25px] px-6" style="height: 100%;">
            <span class="font-['Manrope'] text-[17px] text-[#625B5BB2]">No added user.</span>
        </div>
    </div>
<!-- Previous and Next Button -->
    <div class="flex justify-between items-center mt-4 text-sm text-gray-600">
        <button class="flex items-center bg-[#7A121280] px-4 py-2 rounded-[8px] hover:bg-red-800 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18" />
            </svg>
            Previous
        </button>
        <button class="flex items-center bg-[#7A121280] px-4 py-2 rounded-[8px] hover:bg-red-800 text-white">
            Next
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 ml-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
        </svg>
    </div>
    </div>

    <!-- Modal for Add User Button -->
    <div x-show="showAddUserModal" 
    class="fixed inset-0 flex items-center justify-center z-50 absolute inset-0 bg-black/30 backdrop-blur-sm"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">
    
    <!-- Modal Content -->
    <div class="bg-white rounded-[25px] shadow-xl w-full max-w-lg relative z-50"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="transform scale-95 opacity-0"
        x-transition:enter-end="transform scale-100 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="transform scale-100 opacity-100"
        x-transition:leave-end="transform scale-95 opacity-0">

        <!-- Include the Add User component -->
        @include('super-admin.super-admin-component.AddUser')
    </div>
</div>
@endsection

