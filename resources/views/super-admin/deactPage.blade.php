@extends('base')
@section('content')
@include('components.superAdminNavigation')

<div class="max-h-9/10 bg-white bg-opacity-30 p-13">
    <div class="flex justify-between items-center mb-1">
        <!-- Back to Dashboard Button -->
        <a href="{{ route('super-admin.dashboard') }}" 
            class="bg-white hover:text-red-800 text-[#7A1212] px-4 py-2 rounded-[16px] font-sm font-[Lexend] inline-flex items-center self-start mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Dashboard
        </a>
</div>
        <div>
        <h1 class="text-2xl font-bold font-[Lexend] text-[#332B2B] mb-6">DEACTIVATED ACCOUNTS</h1>
        </div>

    <!-- Table Header and Container -->
    <div class="overflow-hidden rounded-[25px] shadow bg-[#FFFFFFA6] mt-4" style="width: 100%; height: 400px; flex-shrink:0;">
        <table class="min-w-full bg-[#625B5B] text-white rounded-t-[24px] table-fixed">
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
                            <span>Deactivation Date</span>
                            <div class="flex flex-col ml-2">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'updated_at', 'direction' => 'asc']) }}"
                                class="focus:outline-none hover:bg-gray-100/20 rounded-sm p-0.5 {{ ($sortField === 'updated_at' && $sortDirection === 'asc') ? 'text-yellow-300' : '' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M6 0L11.1962 9H0.803848L6 0Z" fill="white"/>
                                    </svg>
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'updated_at', 'direction' => 'desc']) }}"
                                class="focus:outline-none hover:bg-gray-100/20 rounded-sm p-0.5 -mt-1 {{ ($sortField === 'updated_at' && $sortDirection === 'desc') ? 'text-yellow-300' : '' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M6 12L0.803848 3L11.1962 3L6 12Z" fill="white"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#7A1212]/70">
            @forelse ($users as $user)
            <tr class="border-y-[0.1px] border-[#7A1212] bg-[#D9D9D9] hover:bg-[#7e7f80] transition duration-300 cursor-pointer"
                    data-user="{{ json_encode([
                        'username' => $user->username,
                        'email' => $user->email,
                        'role_name' => $user->role_name,
                        'updated_at' => $user->updated_at->format('M-d-Y')
            ]       ) }}">
                <td class="px-6 py-4 text-left pl-40 text-[Lexend] text-[17px] text-black text-semibold">
                    {{ $user->username }}
                </td>
                <td class="px-6 py-4 text-center text-[Lexend] text-[17px] text-black text-semibold">
                    {{ $user->role_name }}
                </td>
                <td class="px-6 py-4 text-right pr-40 text-[Lexend] text-[17px] text-black text-semibold flex justify-end items-center space-x-4">
                    <span>{{ $user->updated_at->format('M-d-Y') }}</span>
                    <button 
                        type="button"
                        class="reactivate-btn hover:scale-110 transition-transform duration-200"
                        data-user-id="{{ $user->id }}"
                        data-user-email="{{ $user->email }}"
                        title="Reactivate Account">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 30 30" fill="none" class="hover:opacity-75">
                            <g clip-path="url(#clip0_4491_18334)">
                                <path d="M28.75 4.99997V12.5M28.75 12.5H21.25M28.75 12.5L22.95 7.04997C21.6066 5.70586 19.9445 4.72398 18.119 4.19594C16.2934 3.6679 14.3639 3.61091 12.5104 4.0303C10.6568 4.44968 8.93975 5.33176 7.51933 6.59425C6.09892 7.85673 5.02146 9.45845 4.3875 11.25M1.25 25V17.5M1.25 17.5H8.75M1.25 17.5L7.05 22.95C8.39343 24.2941 10.0555 25.276 11.881 25.804C13.7066 26.332 15.6361 26.389 17.4896 25.9697C19.3432 25.5503 21.0602 24.6682 22.4807 23.4057C23.9011 22.1432 24.9785 20.5415 25.6125 18.75" stroke="#4D0F0F" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_4491_18334">
                                    <rect width="30" height="30" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </button>
                </td>
            </tr>
            @empty
            <tr class="h-[68px] border-t-[0.3px] border-[#7A1212] bg-[#FFFFFFA6]">
                <td colspan="3" class="px-6 py-4 text-center font-['Manrope'] text-[17px] text-[#625B5BB2]">No deactivated users found</td>
            </tr>
            @endforelse
        </tbody>
        </table>
        @if($users->isEmpty())
            <div class="bg-[#FFFFFFA6] h-[480px] flex-grow flex items-center justify-center text-gray-600 rounded-b-[25px] px-6" style="height: 100%;">
                <span class="font-['Manrope'] text-[17px] text-[#625B5BB2]">No deactivated users found.</span>
            </div>
        @endif
    </div>
</div>

<!-- Pagination -->
<div class="flex justify-between items-center px-15 mt-4" style="width: 100%;">
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
<!-- Reactivation Confirmation Modal -->
<div id="reactivateConfirmModal" class="fixed inset-0 flex items-center justify-center z-[60] hidden">
    <!-- Modal Backdrop -->
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm reactivate-confirm-backdrop"></div>
    
    <!-- Modal Content -->
    <div class="bg-white rounded-[16px] shadow-xl w-full max-w-md relative z-[70] p-6">
        <button id="closeReactivateConfirmBtn" type="button"
            class="absolute top-6 right-5 text-gray-500 hover:text-[#7A1212] transition-colors duration-200 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <div class="text-left">
        <h3 class="text-lg font-semibold text-gray-900 mb-2 font-[Lexend]">Reactivate Account Confirmation</h3>
        <p class="text-sm text-gray-500 mb-4">Are you sure you want to reactivate this account?</p>
        
        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4">
            <button type="button"
                id="closeReactivateConfirmBtn2"
                class="px-4 py-2 bg-gray-100 text-gray-800 rounded-[10px] border border-gray-300 font-[Lexend] hover:bg-gray-200 transition duration-200 cursor-pointer">
                Cancel
            </button>
            <button type="button"
                id="confirmReactivateBtn"
                class="bg-[#7A1212] hover:bg-red-800 text-white px-4 py-2 rounded-[10px] font-normal font-[Lexend] inline-flex items-center hover:bg-red-700 transition duration-200 cursor-pointer">
                Confirm
            </button>
        </div>
        </div>
    </div>
</div>
<!-- Email Confirmation Modal for Reactivation -->
<div id="reactivateEmailConfirmModal" class="fixed inset-0 flex items-center justify-center z-[80] hidden">
    <!-- Modal Backdrop -->
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm reactivate-email-confirm-backdrop"></div>
    
    <!-- Modal Content -->
    <div class="bg-white rounded-[16px] shadow-xl w-full max-w-md relative z-[90] p-6">
        <button id="closeReactivateEmailConfirmBtn" type="button"
            class="absolute top-6 right-5 text-gray-500 hover:text-[#7A1212] transition-colors duration-200 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <!-- Email Confirmation Form -->
        <div class="text-left">
            <h3 class="text-lg font-semibold text-gray-900 font-[Lexend] mb-2">Reactivate Account Confirmation</h3>
            <p class="text-sm text-gray-700 mb-4">Type the account email address to confirm reactivation</p>
            
            <div class="mb-4">
                <label for="confirmReactivateEmail" class="block text-sm font-medium text-gray-700 mb-1 font-[Lexend]">Email Address</label>
                <input type="email" 
                    id="confirmReactivateEmail" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212] transition duration-200"
                    placeholder="Enter email address">
                <p id="reactivateEmailError" class="mt-1 text-sm text-red-600 hidden">Email address does not match.</p>
            </div>
            
            <!-- Action Button -->
            <div class="flex justify-end space-x-4">
                <button type="button"
                    id="cancelReactivateEmailBtn"
                    class="px-4 py-2 bg-gray-100 text-gray-800 rounded-[10px] border border-gray-300 font-[Lexend] hover:bg-gray-200 transition duration-200 cursor-pointer">
                    Cancel
                </button>
                <button type="button"
                    id="finalReactivateBtn"
                    class="bg-[#7A1212] hover:bg-red-800 text-white px-4 py-2 rounded-[10px] font-normal font-[Lexend] inline-flex items-center transition duration-200 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    Reactivate
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Success Modal -->
<div id="reactivateSuccessModal" class="fixed inset-0 flex items-center justify-center z-[90] hidden">
    <!-- Modal Backdrop -->
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
    
    <!-- Modal Content -->
    <div class="bg-white rounded-[16px] shadow-xl w-full max-w-md relative z-[100] p-6">
        <button id="closeSuccessModalBtn" type="button"
            class="absolute top-6 right-5 text-gray-500 hover:text-[#7A1212] transition-colors duration-200 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Success Message -->
        <div class="text-center mb-6">
            <h3 class="text-xl font-semibold text-gray-800">Account Successfully Reactivated!</h3>
            <p class="text-sm text-gray-500 mt-2">The user account has been reactivated successfully.</p>
        </div>

        <!-- Okay Button -->
        <div class="flex justify-center">
            <button type="button"
                id="okaySuccessModalBtn"
                class="bg-[#7A1212] hover:bg-red-800 text-white px-5 py-2 rounded-[14px] font-semibold font-[Lexend] transition duration-200 cursor-pointer">
                Okay
            </button>
        </div>
    </div>
</div>
<!-- Deactivated User Details Modal -->
<div id="deactivatedUserDetailsModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <!-- Modal Backdrop -->
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm deactivated-user-details-backdrop"></div>
    
    <!-- Modal Content -->
    <div class="bg-white rounded-[25px] shadow-xl w-full max-w-md relative z-50 overflow-hidden">
        <!-- Close Button -->
        <button type="button" id="closeDeactivatedUserDetailsBtn" 
            class="absolute top-7 right-5 text-black-500 hover:text-[#7A1212] transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <!-- User Details -->
        <div class="p-8">
            <!-- Header section -->
            <div class="">
                <h3 class="text-xl font-bold text-[#181D27] text-[Lexend]">View Account Details</h3>
                <p class="text-gray-500 text-sm mb-6">View deactivated account details.</p>
            </div>
            
            <!-- User Details -->
            <div class="space-y-4">
                <div class="block text-sm font-medium text-gray-700 mb-1">
                    <h4 class="text-sm font-medium text-black mb-2 font-[Lexend]">Username</h4>
                    <p id="deactivatedUserUsername" class="text-lg font-semibold text-center text-[#3f434a] font-[DM Sans] w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]"></p>
                </div>
                <div class="block text-sm font-medium text-gray-700 mb-2">
                    <h4 class="text-sm font-medium text-black mb-1 font-[Lexend]">Email</h4>
                    <p id="deactivatedUserEmail" class="text-lg font-semibold text-center text-[#3f434a] font-[DM Sans] w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]"></p>
                </div>
                <div class="block text-sm font-medium text-gray-700 mb-2">
                    <h4 class="text-sm font-medium text-black mb-1 font-[Lexend]">Role</h4>
                    <p id="deactivatedUserRole" class="text-lg font-semibold text-center text-[#3f434a] font-[DM Sans] w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]"></p>
                </div>
                <div class="block text-sm font-medium text-gray-700 mb-2">
                    <h4 class="text-sm font-medium text-black mb-1 font-[Lexend]">Deactivation Date</h4>
                    <p id="deactivatedUserDate" class="text-lg font-semibold text-center text-[#3f434a] font-[DM Sans] w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]"></p>
                </div>
            </div>
        </div>
    </div>
</div>
@vite('resources/js/reactivation.js')
@endsection