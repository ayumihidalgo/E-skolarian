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
        <button id="addUserBtn" class="bg-[#7A1212] hover:bg-red-800 text-white px-4 py-2 rounded-[16px] font-semibold font-[Lexend] inline-flex items-center">
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
    <div class="overflow-hidden rounded-[25px] shadow bg-[#D9D9D9]"  style="width: 100%; height: 408px; flex-shrink:0;">
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
                <tr class="border-y-[0.1px] border-[#7A1212] bg-[#d9c698] hover:bg-[#DAA520] transition duration-300">
                        <td class="px-6 py-4 text-left pl-40 text-[Lexend] text-[17px] text-black text-semibold">
                            <!-- Make the username clickable to show user details modal -->
                            <button
                                type="button"
                                class="user-details-btn hover:underline hover:text-[#7A1212] focus:outline-none cursor-pointer text-left"
                                data-user="{{ $user->toJson() }}"
                            >
                                {{ $user->username }}
                            </button>
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

    <!-- Modal Content -->
    <div class="bg-white rounded-[25px] shadow-xl w-full max-w-md relative z-50 overflow-hidden">
        <!-- Header -->
        <div class="bg-[#DAA520] py-4 px-6 flex justify-between items-center">
            <h3 class="text-xl font-bold text-white font-[Lexend]">User Details</h3>
            <button type="button" id="closeUserDetailsBtn" class="text-white hover:bg-[#7A1212]/20 rounded-full p-1 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- User Details -->
        <div class="p-6">
            <div class="flex items-center justify-center mb-6">
                <!-- User Icon/Avatar -->
                <div class="bg-[#7A1212]/20 p-4 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-[#7A1212]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>

            <div class="space-y-4">
                <div class="border-b border-gray-200 pb-3">
                    <h4 class="text-sm font-medium text-gray-500 mb-1 font-[Manrope]">Username</h4>
                    <p id="userUsername" class="text-lg font-semibold text-[#332B2B] font-[Lexend]"></p>
                </div>

                <div class="border-b border-gray-200 pb-3">
                    <h4 class="text-sm font-medium text-gray-500 mb-1 font-[Manrope]">Email</h4>
                    <p id="userEmail" class="text-lg font-semibold text-[#332B2B] font-[Lexend]"></p>
                </div>

                <div class="border-b border-gray-200 pb-3">
                    <h4 class="text-sm font-medium text-gray-500 mb-1 font-[Manrope]">Role</h4>
                    <p id="userRole" class="text-lg font-semibold text-[#332B2B] font-[Lexend]"></p>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1 font-[Manrope]">Joined</h4>
                    <p id="userJoined" class="text-lg font-semibold text-[#332B2B] font-[Lexend]"></p>
                </div>
            </div>
        </div>

        <!-- Footer with Close Button -->
        <div class="bg-gray-50 px-6 py-4 flex justify-center">
            <button
                type="button"
                id="closeUserDetailsModalBtn"
                class="bg-[#7A1212] hover:bg-red-800 text-white px-6 py-2 rounded-[16px] font-semibold font-[Lexend] transition duration-200 flex items-center cursor-pointer focus:outline-none"
            >
                Close
            </button>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 flex items-center justify-center z-50 {{ session()->has('success') ? '' : 'hidden' }}">
    <!-- Modal Backdrop -->
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm success-modal-backdrop"></div>

    <!-- Modal Content -->
    <div class="bg-white rounded-[25px] shadow-xl w-full max-w-md relative z-50 p-6">
        <!-- Success Message -->
        <div class="text-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900 text-[Inter] mb-2">User Successfully Added!</h3>
            <p class="text-sm text-gray-500">{{ session('success') }}</p>
        </div>

        <!-- Okay Button -->
        <div class="flex justify-center">
            <button type="button"
                id="closeSuccessModalBtn"
                class="bg-[#7A1212] hover:bg-red-800 text-white px-8 py-2 rounded-[16px] font-semibold font-[Lexend] transition duration-200">
                Okay
            </button>
        </div>
    </div>
</div>

<script>
// Execute when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Add User Modal
    const addUserModal = document.getElementById('addUserModal');
    const addUserBtn = document.getElementById('addUserBtn');
    const closeAddUserModalBtn = document.getElementById('closeAddUserModalBtn');
    const addUserBackdrop = document.querySelector('.add-user-backdrop');

    // User Details Modal
    const userDetailsModal = document.getElementById('userDetailsModal');
    const userDetailsButtons = document.querySelectorAll('.user-details-btn');
    const closeUserDetailsBtn = document.getElementById('closeUserDetailsBtn');
    const closeUserDetailsModalBtn = document.getElementById('closeUserDetailsModalBtn');
    const userDetailsBackdrop = document.querySelector('.user-details-backdrop');

    // Success Modal
    const successModal = document.getElementById('successModal');
    const closeSuccessModalBtn = document.getElementById('closeSuccessModalBtn');
    const successModalBackdrop = document.querySelector('.success-modal-backdrop');

    // Add User Modal Opening
    if (addUserBtn) {
        addUserBtn.addEventListener('click', function() {
            addUserModal.classList.remove('hidden');
        });
    }

    // Add User Modal Closing
    if (closeAddUserModalBtn) {
        closeAddUserModalBtn.addEventListener('click', function() {
            addUserModal.classList.add('hidden');
        });
    }

    if (addUserBackdrop) {
        addUserBackdrop.addEventListener('click', function() {
            addUserModal.classList.add('hidden');
        });
    }

    // Form Submission Handling
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Get form data
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const role_name = document.getElementById('role_name').value;

            // Create form data object
            const formData = new FormData();
            formData.append('username', username);
            formData.append('email', email);
            formData.append('role_name', role_name);
            formData.append('_token', '{{ csrf_token() }}');

            // Send the form data via fetch API
            fetch('/users', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(JSON.stringify(data));
                    });
                }
                return response.json();
            })
            .then(data => {
                // Close the add user modal
                addUserModal.classList.add('hidden');

                // Show success message
                if (successModal) {
                    // Update success message if needed
                    const successMessageElement = successModal.querySelector('p');
                    if (successMessageElement) {
                        successMessageElement.textContent = 'User added successfully!';
                    }

                    // Show success modal
                    successModal.classList.remove('hidden');

                    // Refresh the page after a delay to show the new user
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            })
            .catch(error => {
                let errorData;
                try {
                    errorData = JSON.parse(error.message);
                } catch (e) {
                    errorData = { message: 'An error occurred while adding the user.' };
                }

                // Handle validation errors
                if (errorData.errors) {
                    // Display the first error
                    const firstErrorKey = Object.keys(errorData.errors)[0];
                    alert(errorData.errors[firstErrorKey][0]);
                } else {
                    // Display general error
                    alert(errorData.message || 'An error occurred while adding the user.');
                }
            });
        });
    }

    // User Details Modal
    userDetailsButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userData = JSON.parse(this.getAttribute('data-user'));

            // Fill user details in the modal
            document.getElementById('userUsername').textContent = userData.username;
            document.getElementById('userEmail').textContent = userData.email;
            document.getElementById('userRole').textContent = userData.role;

            // Format the date
            const joinedDate = new Date(userData.created_at);
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('userJoined').textContent = joinedDate.toLocaleDateString('en-US', options);

            // Show the modal
            userDetailsModal.classList.remove('hidden');
        });
    });

    if (closeUserDetailsBtn) {
        closeUserDetailsBtn.addEventListener('click', function() {
            userDetailsModal.classList.add('hidden');
        });
    }

    if (closeUserDetailsModalBtn) {
        closeUserDetailsModalBtn.addEventListener('click', function() {
            userDetailsModal.classList.add('hidden');
        });
    }

    if (userDetailsBackdrop) {
        userDetailsBackdrop.addEventListener('click', function() {
            userDetailsModal.classList.add('hidden');
        });
    }

    // Success Modal
    if (closeSuccessModalBtn) {
        closeSuccessModalBtn.addEventListener('click', function() {
            successModal.classList.add('hidden');
        });
    }

    if (successModalBackdrop) {
        successModalBackdrop.addEventListener('click', function() {
            successModal.classList.add('hidden');
        });
    }

    // Close the modals when Escape key is pressed
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            addUserModal.classList.add('hidden');
            userDetailsModal.classList.add('hidden');
            successModal.classList.add('hidden');
        }
    });
});
</script>
@endsection
