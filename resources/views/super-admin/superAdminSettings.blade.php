@extends('base')<!-- Extend the base component -->
@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <!-- Content section -->
    <!-- This is the main content area for the super admin dashboard -->
    @include('components.superAdminNavigation')
    <div id="main-content" class="flex flex-col min-h-screen transition-all duration-300 bg-[#F2F4F7]">
        <div class="flex-grow mb-10">
            @if (session('success'))
                <div id="Toast"
                    class="fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white border-l-4 border-green-400 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50"
                    role="alert">
                    <div class="w-full flex justify-between">
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('images/successful.svg') }}" alt="Success Icon" id="docTypeIcon"
                                draggable="false" class="select-none pointer-events-none">
                            <div>
                                <h6 class="font-bold font-['Manrope']">Profile Updated Successfully!</h6>
                                <p class="sm:inline inline text-sm font-['Manrope']">{{ session('success') }}
                                </p>
                            </div>
                        </div>
                        <button type="button"
                            class="Cursor-pointer text-gray-500 hover:text-gray-700 text-2xl leading-none cursor-pointer"
                            onclick="document.getElementById('Toast').style.display='none';">&times;</button>
                    </div>
                </div>
            @endif
            <div class="p-8">
                <!-- Profile Settings Heading -->
                <h2 class="text-2xl font-bold mb-6 font-['Lexend']">Profile Settings</h2>
                <!-- Profile Card -->
                <div
                    class="bg-white px-13 py-6 flex items-center gap-8 mb-8 [box-shadow:1px_2px_7px_rgba(0,0,0,0.3)] rounded-3xl">
                    <div class="relative  w-36 h-36 rounded-full">
                        @if ($user->profile_pic)
                            <!-- Show uploaded profile image -->
                            <div class="border-3 border-gray-300 rounded-full">
                                <img src="{{ asset('storage/' . $user->profile_pic) }}" alt="Profile" draggable="false"
                                    lass="select-none pointer-events-none w-35 h-35 rounded-full object-cover">
                            </div>
                        @else
                            <!-- Default profile with initials -->
                            <div
                                class="w-full h-full rounded-full bg-maroon-700 flex items-center justify-center text-white text-3xl font-bold">
                                <img src="{{ asset('images/dprofile.svg') }}" class="w-36 h-36" alt="camera icon">
                            </div>
                        @endif
                        <input type="file" name="profile_image" id="profileImageInput" class="hidden" accept="image/*">
                        <!-- Camera icon overlay -->
                        <button onclick="openProfilePreviewModal()"
                            class="absolute bottom-[-5px] right-2 bg-yellow-500 p-[5px] rounded-full cursor-pointer z-10">
                            <img src="{{ asset('images/camera.svg') }}" class="w-6 h-6" alt="camera icon">
                        </button>

                    </div>
                    <div class="flex flex-col justify-end h-full">
                        <h3 class="text-3xl mb-4 font-black tracking-wider font-['Lexend']">
                            {{ strtoupper($user->role_name) }}
                        </h3>
                        <div id="" class="mt-2 text-sm relative flex items-center gap-7">
                            <div
                                class="flex items-center min-w-[300px] gap-4 px-4 py-3 bg-[#F2F4F7] rounded-xl border border-gray-200">
                                <img src="{{ asset('images/Smail.svg') }}" class="w-6 h-6" alt="email icon">
                                <div>
                                    <p class="font-extrabold text-sm">Email</p>
                                    <p class="font-extrabold font-['Manrope'] text-sm">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div
                                class="flex items-center min-w-[300px] gap-4 px-4 py-3 bg-[#F2F4F7] rounded-xl border border-gray-200">
                                <img src="{{ asset('images/department.svg') }}" class="w-6 h-6" alt="department icon">
                                <div>
                                    <p class="font-extrabold text-sm">Department</p>
                                    <p class="font-extrabold font-['Manrope'] text-sm">{{ $user->organization_acronym }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Info -->
                <div class="bg-white w-full [box-shadow:1px_2px_7px_rgba(0,0,0,0.3)] rounded-3xl  px-5 py-3 pb-7 space-y-5">
                    <div class="w-full">
                        <h4 class="text-2xl font-bold mb-2 mt-1 font-['Lexend']">SECURITY INFO</h4>
                        <p class="text-base text-gray-600">Manage your password settings here to reset your password and
                            enhance your account security.</p>
                    </div>
                    <div class="space-y-5 px-2">
                        <!-- Change Password -->
                        <div class="border w-full flex px-4 py-4 rounded-2xl ">
                            <div class="flex items-center gap-4 w-1/3">
                                <img src="{{ asset('images/dpassword.svg') }}" class="w-6 h-6" alt="password icon">
                                <p class="font-['Lexend'] text-base">Password</p>
                            </div>
                            <div class="flex  flex-col items-center w-1/3 text-center">
                                <p class="font-['Lexend'] text-base">Last Updated:</p>
                                @if ($user->password_changed_at)
                                    <p class="text-gray-400 text-sm">
                                        {{ \Carbon\Carbon::parse($user->password_changed_at)->diffForHumans() }}
                                    </p>
                                @else
                                    <p class="text-gray-400 text-sm">Never</p>
                                @endif
                            </div>
                            <div class="flex items-center justify-end w-1/3">
                                <button
                                    class="border px-5 py-2 border-red-950 font-regular cursor-pointer rounded-xl text-white bg-red-950 text-base hover:bg-red-800 hover:border-red-800 hover:text-white transition-colors duration-300"
                                    onclick="openChangePasswordModal()">Change</button>
                            </div>
                        </div>
                        <!-- Primary Email -->
                        <div class="border w-full flex px-4 py-4 rounded-2xl">
                            <div class="flex items-center gap-4 w-1/3">
                                <img src="{{ asset('images/Smail.svg') }}" class="w-6 h-6" alt="email icon">
                                <p class="font-['Lexend'] text-base">Primary Email</p>
                            </div>
                            <div class="flex flex-col items-center justify-center w-1/3 text-center">
                                @if (!empty($user->email))
                                    <p class="text-black font-['Lexend'] text-base flex items-center gap-2">

                                        <span class="relative group">
                                            <i class="fas fa-info-circle text-red-600 w-4 h-4 cursor-pointer"></i>
                                            <span
                                                class="absolute left-8 top-2 z-20 hidden group-hover:block bg-gray-800 text-white text-xs rounded-lg px-4 py-2 whitespace-normal font-normal shadow-2xl max-w-2xl min-w-[300px]">
                                                For your security and to ensure you receive important system notifications,
                                                please update your primary email if it is outdated or incorrect.
                                            </span>
                                        </span>
                                        {{ $user->email }}
                                    </p>
                                @else
                                    <p class="text-red-600 font-['Lexend'] text-base flex items-center gap-2">
                                        <span class="relative group">
                                            <i class="fas fa-info-circle text-red-600 w-4 h-4 cursor-pointer"></i>
                                            <span
                                                class="absolute left-6 top-1 z-10 hidden group-hover:block bg-gray-800 text-white text-sm rounded px-2 py-1 whitespace-nowrap font-normal shadow-lg">
                                                Add a recovery email to help you reset your password if you lose access to
                                                your main email.
                                            </span>
                                        </span>
                                        Not Configured
                                    </p>
                                @endif
                            </div>
                            <div class="flex items-center justify-end w-1/3">
                                @if (!empty($user->recovery_email))
                                    <button
                                        class="border px-5 py-2 border-red-950 font-regular cursor-pointer  rounded-xl text-red-950 text-base hover:bg-red-800 hover:border-red-800 hover:text-white transition-colors duration-300"
                                        onclick="openPreRemoveRecoveryEmailModal()">Remove</button>
                                @else
                                    <button
                                        class="border px-5 py-2 border-red-950 font-regular cursor-pointer rounded-xl text-white bg-red-950 text-base hover:bg-red-800 hover:border-red-800 hover:text-white transition-colors duration-300"
                                        onclick="openRecoveryEmail()">Change</button>
                                @endif
                            </div>
                        </div>
                        <!-- Recovery Email -->
                        <div class="border w-full flex px-4 py-4 rounded-2xl">
                            <div class="flex items-center gap-4 w-1/3">
                                <img src="{{ asset('images/Smail.svg') }}" class="w-6 h-6" alt="email icon">
                                <p class="font-['Lexend'] text-base">Recovery Email</p>
                            </div>
                            <div class="flex flex-col items-center justify-center w-1/3 text-center">
                                @if (!empty($user->recovery_email))
                                    <p class="text-black font-['Lexend'] text-base">
                                        {{ $user->recovery_email }}
                                    </p>
                                @else
                                    <p class="text-red-600 font-['Lexend'] text-base flex items-center gap-2">
                                        <span class="relative group">
                                            <i class="fas fa-info-circle text-red-600 w-4 h-4 cursor-pointer"></i>
                                            <span
                                                class="absolute left-8 top-2 z-20 hidden group-hover:block bg-gray-800 text-white text-xs rounded-lg px-4 py-2 whitespace-normal font-normal shadow-2xl max-w-2xl min-w-[300px]">
                                                Add a recovery email to help you reset your password if you lose access to
                                                your main email.
                                            </span>
                                        </span>
                                        Not Configured

                                    </p>
                                @endif
                            </div>
                            <div class="flex items-center justify-end w-1/3">
                                @if (!empty($user->recovery_email))
                                    <button
                                        class="border px-5 py-2 border-red-950 font-regular cursor-pointer  rounded-xl text-red-950 text-base hover:bg-red-800 hover:border-red-800 hover:text-white transition-colors duration-300"
                                        onclick="openPreRemoveRecoveryEmailModal()">Remove</button>
                                @else
                                    <button
                                        class="border px-5 py-2 border-red-950 font-regular cursor-pointer rounded-xl text-white bg-red-950 text-base hover:bg-red-800 hover:border-red-800 hover:text-white transition-colors duration-300"
                                        onclick="openRecoveryEmail()">Add</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('components.footer')
    </div>

    <input type="file" name="profile_image" id="profileImageInput" class="hidden" accept="image/*">

    <!-- Add Recovery Email Modal -->
    <div id="recoveryEmailModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-xl relative space-y-2">
            <form id="recoveryEmailForm" action="POST" method="POST" class="px-6 pb-6 space-y-3">
                <div class="w-full flex justify-between pt-5 gap-10">
                    <h2 class="text-xl font-semibold font-['Lexend']">Add Recovery Email</h2>
                    <div class="top-2 right-2">
                        <button type="button" onclick="closeRecoveryEmailModal()"
                            class="text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                            <i class="text-xl fas fa-times"></i></button>
                    </div>
                </div>
                <p class="pb-1 text-sm text-gray-600">Add a recovery email to help secure your account and recover
                    access if you forget your password.</p>
                @csrf
                <div class="relative">
                    <input type="email" name="recovery_email" id="recovery_email" required
                        placeholder="Enter your recovery email"
                        class=" w-full rounded-lg border border-black px-4 py-2 focus:border-gray-500 focus:ring-gray-500 placeholder:text-black placeholder:sm placeholder:font-[Lexend] pr-10">
                    <div id="recoveryEmailError" class="text-red-500 text-xs mt-0.5 ml-1 absolute font-['Lexend']"></div>
                </div>
                <div class="mt-5">
                    <h3 class="font-normal text-sm text-gray-600">Important:</h3>
                    <ul class="list-disc ml-6 space-y-1 font-normal text-sm text-gray-600">
                        <li> Use an email you have regular access to</li>
                        <li>Don't use the same email as your main account</li>
                        <li>We'll send a verification code to confirm</li>
                    </ul>
                </div>
                <div class="pt-2">
                    <button type="submit" id="addRecoveryEmailButton"
                        class="w-full rounded-lg bg-red-900 px-4 py-2 border-red-900 text-white font-medium text-sm font-[Lexend] hover:bg-red-800 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        disabled>
                        <span id="addRecoveryEmailSpinner"
                            class="hidden animate-spin border-2 text-sm border-white border-t-transparent rounded-full w-5 h-5"></span>
                        <span id="addRecoveryEmailBtnText" class="text-sm">Add Recovery Email</span>
                    </button>
                </div>
            </form>
            <div id="codeVerificationForm" class="hidden px-6 pb-6 space-y-3">
                <div class="w-full flex justify-between pt-5 gap-10">
                    <div class="flex items-center gap-2">
                        <button type="button" id="backToRecoveryFormBtn"
                            class="text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer mr-2"
                            title="Back">
                            <i class="fas fa-arrow-left text-normal"></i>
                        </button>
                        <h2 class="text-xl font-semibold font-['Lexend']">Verify Email</h2>
                    </div>
                    <div class="top-2 right-2">
                        <button type="button" onclick="closeRecoveryEmailModal()"
                            class="text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                            <i class="text-xl fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <p class="pb-1 text-sm text-gray-600 text-center">
                    We sent a 6-digit verification code to:
                    <span id="recoveryEmailDisplay" class="font-normal"></span>
                </p>
                <div class="relative space-y-2">
                    <input type="text" id="verification_code" placeholder="Enter the code sent to your email"
                        class="border rounded-lg w-full px-4 py-2 pr-32 placeholder:text-black placeholder:text-[14px] placeholder:font-[Lexend]" />
                    <div id="codeError" class="text-red-500 text-xs font-['Lexend']"></div>
                    <div class="flex justify-end pt- items-center gap-2">
                        <span id="resendTimer" class="text-xs text-gray-600"></span>
                        <button id="resendCodeBtn" class="text-xs text-blue-700 font-semibold hidden cursor-pointer"
                            type="button">
                            Resend code
                        </button>
                    </div>
                </div>
                <div class="pb-1">
                    <p class="text-xs text-gray-600"><strong>Didn't receive the code?</strong> Check your spam folder or
                        make sure the email address is
                        correct.</p>
                </div>
                <button onclick="verifyRecoveryCode()"
                    class="w-full rounded-lg bg-red-900 px-4 py-2 border-red-900 text-white font-medium text-sm font-[Lexend] hover:bg-red-800 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    disabled><span id="addRecoveryEmailSpinner"
                        class="hidden animate-spin border-2 text-sm border-white border-t-transparent rounded-full w-5 h-5"></span></button>
            </div>
        </div>
    </div>
    <!-- Confirm Remove Recovery Email Pre-Modal -->
    <div id="preRemoveRecoveryEmailModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-xl relative space-y-2 px-6 py-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold font-['Lexend']">Remove Recovery Email?</h2>
                <button type="button" onclick="closePreRemoveRecoveryEmailModal()"
                    class="text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                    <i class="text-xl fas fa-times"></i>
                </button>
            </div>
            <p class="text-gray-700 mb-6">
                Are you sure you want to remove your recovery email? This action cannot be undone.
            </p>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closePreRemoveRecoveryEmailModal()"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-200 transition">Cancel</button>
                <button type="button" onclick="acceptRemoveRecoveryEmail()"
                    class="px-4 py-2 rounded-lg bg-red-900 text-white font-semibold hover:bg-red-800 transition">Yes,
                    Remove</button>
            </div>
        </div>
    </div>
    <!-- Remove Recovery Email Modal -->
    <div id="removeRecoveryEmailModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-xl relative space-y-2">
            <form id="removeRecoveryEmailForm" class="px-6 pb-6 space-y-3">
                <div class="w-full flex justify-between pt-5 gap-10">
                    <h2 class="text-xl font-semibold font-['Lexend']">Remove Recovery Email</h2>
                    <div class="top-2 right-2">
                        <button type="button" onclick="closeRemoveRecoveryEmailModal()"
                            class="text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                            <i class="text-xl fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <p class="pb-1 text-sm text-gray-600">
                    Are you sure you want to remove your recovery email? A verification code will be sent to <span
                        class="font-semibold">{{ $user->recovery_email }}</span> to confirm this action.
                </p>
                <div class="relative space-y-2">
                    <input type="text" id="removeVerificationCode"
                        placeholder="Enter the 6-digit code sent to your email"
                        class="border rounded-lg w-full px-4 py-2 pr-32 placeholder:text-black placeholder:text-[14px] placeholder:font-[Lexend]" />
                    <div id="removeCodeError" class="text-red-500 text-xs font-['Lexend']"></div>
                    <div class="flex justify-end items-center gap-2">
                        <span id="removeResendTimer" class="text-xs text-gray-600"></span>
                        <button id="removeResendCodeBtn" class="text-xs text-blue-700 font-semibold hidden cursor-pointer"
                            type="button">
                            Resend code
                        </button>
                    </div>
                </div>
                <div class="pb-1">
                    <p class="text-xs text-gray-600"><strong>Didn't receive the code?</strong> Check your spam folder or
                        make sure the email address is correct.</p>
                </div>
                <button type="submit"
                    class="w-full rounded-lg bg-red-900 px-4 py-2 border-red-900 text-white font-medium text-sm font-[Lexend] hover:bg-red-800 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    id="confirmRemoveRecoveryEmailBtn" disabled>
                    <span id="removeRecoveryEmailSpinner"
                        class="hidden animate-spin border-2 text-sm border-white border-t-transparent rounded-full w-5 h-5"></span>
                    <span id="removeRecoveryEmailBtnText" class="text-sm">Confirm Remove</span>
                </button>
            </form>
        </div>
    </div>
    <!-- Profile Preview Modal -->
    <div id="profilePreviewModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-xl relative">
            <div class="w-full flex justify-between pt-5 px-5 gap-10">
                <h2 class="text-lg font-semibold font-['Lexend'] mb-4">Edit Profile Picture</h2>
                <div class="top-2 right-2">
                    <button onclick="closeProfilePreviewModal()"
                        class="text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                        <i class="text-xl fas fa-times"></i></button>
                </div>
            </div>
            <div class="flex justify-center mb-4">
                <div class="space-y-4">
                    <img id="profilePreviewImage"
                        src="{{ $user->profile_pic ? asset('storage/' . $user->profile_pic) : asset('images/dprofile.svg') }}"
                        alt="Profile Preview" class="w-70 h-70 rounded-full border-5 border-gray-300 object-cover">
                </div>
            </div>

            <div class="flex flex-col items-center justify-center mb-4">
                <p id="requirements" class="text-xs text-gray-400">Supported formats: JPG, JPEG, JFIF, PNG (Max
                    size: 10MB)</p>
                <p id="toLargefile" class="text-xs text-red-700 hidden">Image size must not exceed 10MB.</p>
                <p id="toFiletype" class="text-xs text-red-700 hidden">Only JPG, JPEG, and PNG images are allowed.</p>
            </div>
            <div class="flex justify-end p-6 gap-4">
                @if ($user->profile_pic != null)
                    <form action="{{ route('superadmin.settings.remove-profile-picture') }}" method="POST">
                        @csrf
                        <input type="hidden" name="profile_image" value="{{ $user->profile_pic }}">
                    </form>
                    <label id="removeProfileButton" onclick="openRemoveProfileModal()"
                        class="rounded-lg text-gray-900 font-medium px-4 py-2 text-[14px] border-1 border-gray-300 font-[Lexend] hover:bg-gray-400 transition cursor-pointer">
                        Remove Profile
                    </label>
                @endif

                <label for="profileImageInput"
                    class="rounded-lg bg-red-900 px-4 py-2 text-white font-medium text-[14px] font-[Lexend] hover:bg-red-800 transition cursor-pointer">
                    Upload Profile
                </label>
            </div>

        </div>
    </div>
    <!-- Image Preview Modal -->
    <div id="imagePreviewModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
                <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3"
                    onclick="this.parentElement.style.display='none';">
                    <span class="text-red-500">&times;</span>
                </button>
            </div>
        @endif
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-xl relative">
            <div class="w-full flex justify-center py-6 px-4">
                <h2 class="text-2xl font-semibold font-['Lexend']">Edit Profile Picture</h2>
            </div>
            <!-- Cropping preview area -->
            <div class="relative w-full h-80 mx-auto bg-black/10 overflow-hidden">
                <div id="cropContainer" class="w-full h-full flex items-center justify-center">
                    <img id="previewImage" class="w-80" />
                </div>
            </div>
            <!-- Zoom slider -->
            <div class="mt-5 flex items-center justify-center gap-2">
                <input type="range" id="zoomRange" min="0" max="3" step="0.01" value="1"
                    class="w-56">
            </div>
            <!-- Buttons -->
            <form id="uploadForm" action="{{ route('superadmin.settings.update-profile-picture') }}" method="POST"
                enctype="multipart/form-data" class="w-full flex justify-end items-center py-6 px-4">
                @csrf
                <input type="hidden" name="profile_image_base64" id="profileImageBase64">
                <div class="flex justify-between items-center gap-4">
                    <div class="flex gap-2">
                        <button type="button" onclick="closeModal()"
                            class="rounded-lg text-gray-900 font-medium px-4 py-2 border-1 border-gray-300 text-[14px] font-[Lexend] hover:bg-gray-400 transition cursor-pointer">Cancel</button>
                    </div>
                    <button id="saveProfileButton"
                        class="rounded-lg bg-red-900 text-white font-medium px-4 py-2 text-[14px] font-[Lexend] hover:bg-red-900 transition cursor-pointer">Save</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Remove Profile Modal -->
    <div id="removeProfileModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-100">
        <div class="bg-white rounded-2xl w-[545px] shadow-xl relative space-y-4">
            <div class="w-full flex justify-between pt-5 px-5">
                <h2 class="text-lg font-semibold font-['Lexend']">Remove Profile Picture?</h2>
            </div>
            <div class="px-6 pb-6">
                <p class="text-sm text-gray-600 pb-3">Are you sure you want to remove your profile picture? This action
                    cannot be undone.
                </p>
                <div class="flex justify-end gap-4 mt-4">
                    <button type="button" onclick="closeRemoveProfileModal()"
                        class="rounded-lg text-gray-900 font-medium px-4 py-2 text-[14px] border-1 border-gray-300 font-[Lexend] hover:bg-gray-400 transition cursor-pointer">Cancel</button>
                    <button type="button" onclick="submitRemoveProfileForm()"
                        class="rounded-lg bg-red-900 text-white font-medium px-4 py-2 text-[14px] font-[Lexend] hover:bg-red-900 transition cursor-pointer">Remove
                        Profile</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Save Changes for Update Profile Modal -->
    <div id="saveChangesModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-100">
        <div class="bg-white rounded-2xl w-[545px] shadow-xl relative space-y-4">
            <div class="w-full flex justify-between pt-5 px-5">
                <h2 class="text-lg font-semibold font-['Lexend']">Save Changes?</h2>
            </div>
            <div class="px-6 pb-6">
                <p class="text-sm text-gray-600 pb-3">Do you want to save this profile picture? Your changes will be
                    updated immediately.
                </p>
                <div class="flex justify-end gap-4 mt-4">
                    <button type="button" onclick="closeChangesModal()"
                        class="rounded-lg text-gray-900 font-medium px-4 py-2 text-[14px] border-1 border-gray-300 font-[Lexend] hover:bg-gray-400 transition cursor-pointer">Cancel</button>
                    <button type="button" onclick="keepEditing()"
                        class="rounded-lg bg-red-900 text-white font-medium px-4 py-2 text-[14px] font-[Lexend] hover:bg-red-900 transition cursor-pointer">Save
                        Changes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Cancel Edit Image Modal -->
    <div id="cancelEditImageModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-100">
        <div class="bg-white rounded-2xl w-[545px] shadow-xl relative space-y-4">
            <div class="w-full flex justify-between pt-5 px-5">
                <h2 class="text-lg font-semibold font-['Lexend']">Discard Changes?</h2>
            </div>
            <div class="px-6 pb-6">
                <p class="text-sm text-gray-600 pb-3">You have unsaved changes. Are you sure you want to leave without
                    saving?
                </p>
                <div class="flex justify-end gap-4 mt-4">
                    <button type="button" onclick="closeModal()"
                        class="rounded-lg text-gray-900 font-medium px-4 py-2 border-1 border-gray-300 text-[14px] font-[Lexend] hover:bg-gray-400 transition cursor-pointer">Close
                        without saving</button>
                    <button type="button" onclick="keepEditing()"
                        class="rounded-lg bg-red-900 text-white font-medium px-4 py-2 text-[14px] font-[Lexend] hover:bg-red-900 transition cursor-pointer">Keep
                        editing</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Change Password Modal -->
    <div id="changePasswordModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-100">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-xl relative space-y-2">
            <div class="w-full flex justify-between py-5 px-5 gap-10">
                <div>
                    <h2 class="text-xl font-semibold font-['Lexend']">Change Password</h2>
                    <p class="text-[14px]">Manage your password to keep your account secure.</p>
                </div>
                <div class="top-2 right-2">
                    <button type="button" onclick="closeChangePasswordModal()"
                        class="text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                        <i class="text-xl fas fa-times"></i></button>
                </div>
            </div>
            <form id="changePasswordForm" action="{{ route('superadmin.settings.change-password') }}" method="POST"
                class="px-6 pb-6 space-y-5">
                @csrf
                <div class="relative">
                    <input type="password" name="current_password" id="current_password" required
                        placeholder="Current Password"
                        class="block w-full rounded-lg border border-black px-4 py-1 focus:border-gray-500 focus:ring-gray-500 placeholder:text-black placeholder:text-[14px] placeholder:font-[Lexend] pr-10">
                    <button type="button" onclick="togglePassword(event, 'current_password')"
                        class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer">
                        <img id="showPass_current_password" src="{{ asset('images/show_pass.svg') }}"
                            alt="Show Password" class="w-5 md:w-6 opacity-80" />
                        <img id="hidePass_current_password" src="{{ asset('images/hide_pass.svg') }}"
                            alt="Hide Password" class="w-5 md:w-6 hidden opacity-80" />
                    </button>
                    <div id="currentPasswordError" class="text-red-500 text-xs mt-0.5 ml-1 absolute font-['Lexend']">
                    </div>
                </div>
                <div>
                    <div class="relative">
                        <input type="password" name="new_password" id="new_password" required minlength="8"
                            placeholder="New Password"
                            class="block w-full rounded-lg border border-black px-4 py-1 focus:border-gray-500 focus:ring-gray-500 placeholder:text-black placeholder:text-[14px] placeholder:font-[Lexend] pr-10">
                        <button type="button" onclick="togglePassword(event, 'new_password')"
                            class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer">
                            <img id="showPass_new_password" src="{{ asset('images/show_pass.svg') }}"
                                alt="Show Password" class="w-5 md:w-6 opacity-80" />
                            <img id="hidePass_new_password" src="{{ asset('images/hide_pass.svg') }}"
                                alt="Hide Password" class="w-5 md:w-6 hidden opacity-80" />
                        </button>
                        <div id="newPasswordError" class="text-red-500 text-xs mt-0.5 ml-1 absolute font-['Lexend']">
                        </div>
                    </div>
                </div>
                <div>
                    <div class="relative">
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                            placeholder="Confirm Password"
                            class="mb-2 block w-full rounded-lg border border-black px-4 py-1 focus:border-gray-500 focus:ring-gray-500 placeholder:text-black placeholder:text-[14px] placeholder:font-[Lexend] pr-10">
                        <button type="button" onclick="togglePassword(event, 'new_password_confirmation')"
                            class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer">
                            <img id="showPass_new_password_confirmation" src="{{ asset('images/show_pass.svg') }}"
                                alt="Show Password" class="w-5 md:w-6 opacity-80" />
                            <img id="hidePass_new_password_confirmation" src="{{ asset('images/hide_pass.svg') }}"
                                alt="Hide Password" class="w-5 md:w-6 hidden opacity-80" />
                        </button>
                    </div>
                    <div id="confirmPasswordError" class="text-red-500 text-xs font-['Lexend']"></div>
                </div>
                <div class="mt-3 ml-3 space-y-3" id="passwordRequirements">
                    <div id="characterLimit" class="text-gray-600 text-xs font-light font-['Lexend']">
                        •&nbsp; Require at least 8 characters
                    </div>
                    <div id="oneNumber" class="text-gray-600 text-xs font-light font-['Lexend']">
                        •&nbsp; Require at least one number
                    </div>
                    <div id="lowerCase" class="text-gray-600 text-xs font-light font-['Lexend']">
                        •&nbsp; Require at least one lower case letter
                    </div>
                    <div id="upperCase" class="text-gray-600 text-xs font-light font-['Lexend']">
                        •&nbsp; Require at least one uppercase letter
                    </div>
                    <div id="specialCharacter" class="text-gray-600 text-xs font-light font-['Lexend']">
                        •&nbsp; Require at least one special character
                    </div>
                    <div id="noSpaces" class="text-gray-600 text-xs font-light font-['Lexend']">
                        •&nbsp; Password cannot contain spaces
                    </div>
                </div>
                <div class="pt-2">
                    <button type="submit" id="changePasswordButton" disabled
                        class="w-full rounded-lg bg-red-900 px-4 py-2 text-white font-medium text-[14px] font-[Lexend] hover:bg-red-800 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        Change Password
                    </button>
                </div>

            </form>
        </div>
    </div>
    <!-- Leave Without Saving Modal -->
    <div id="leaveWithoutSavingModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-100">
        <div class="bg-white rounded-2xl w-[545px]  shadow-xl relative space-y-4">
            <div class="w-full flex justify-between pt-5 px-5">
                <h2 class="text-lg font-semibold font-['Lexend']">Discard Changes?</h2>
            </div>
            <div class="px-6 pb-6">
                <p class="text-sm text-gray-600 pb-3">You have unsaved changes. Are you sure you want to leave without
                    saving?
                </p>
                <div class="flex justify-end gap-4 mt-4">
                    <button type="button" onclick="closeUnsavedModal()"
                        class="rounded-lg text-gray-900 font-medium border-1 border-gray-300 px-4 py-2 text-[14px] font-[Lexend] hover:bg-gray-400 transition cursor-pointer">Close
                        without saving</button>
                    <button type="button" onclick="keepEditing()"
                        class="rounded-lg bg-red-900 text-white font-medium px-4 py-2 text-[14px] font-[Lexend] hover:bg-red-900 transition cursor-pointer">Keep
                        editing</button>
                </div>
            </div>
        </div>
    </div>
    <div id="leaveWithoutSavingRevModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-100">
        <div class="bg-white rounded-2xl w-[545px]  shadow-xl relative space-y-4">
            <div class="w-full flex justify-between pt-5 px-5">
                <h2 class="text-lg font-semibold font-['Lexend']">Discard Changes?</h2>
            </div>
            <div class="px-6 pb-6">
                <p class="text-sm text-gray-600 pb-3">You have unsaved changes. Are you sure you want to leave without
                    saving?
                </p>
                <div class="flex justify-end gap-4 mt-4">
                    <button type="button" onclick="closeUnsavedRevModal()"
                        class="rounded-lg text-gray-900 font-medium border-1 border-gray-300 px-4 py-2 text-[14px] font-[Lexend] hover:bg-gray-400 transition cursor-pointer">Close
                        without saving</button>
                    <button type="button" onclick="keepRevEditing()"
                        class="rounded-lg bg-red-900 text-white font-medium px-4 py-2 text-[14px] font-[Lexend] hover:bg-red-900 transition cursor-pointer">Keep
                        editing</button>
                </div>
            </div>
        </div>
    </div>
    <div id="leaveWithoutSavingRevRemoveModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-100">
        <div class="bg-white rounded-2xl w-[545px]  shadow-xl relative space-y-4">
            <div class="w-full flex justify-between pt-5 px-5">
                <h2 class="text-lg font-semibold font-['Lexend']">Discard Changes?</h2>
            </div>
            <div class="px-6 pb-6">
                <p class="text-sm text-gray-600 pb-3">You have unsaved changes. Are you sure you want to leave without
                    saving?
                </p>
                <div class="flex justify-end gap-4 mt-4">
                    <button type="button" onclick="closeUnsavedRevRemoveModal()"
                        class="rounded-lg text-gray-900 font-medium border-1 border-gray-300 px-4 py-2 text-[14px] font-[Lexend] hover:bg-gray-400 transition cursor-pointer">Close
                        without saving</button>
                    <button type="button" onclick="keepRevRemoveEditing()"
                        class="rounded-lg bg-red-900 text-white font-medium px-4 py-2 text-[14px] font-[Lexend] hover:bg-red-900 transition cursor-pointer">Keep
                        editing</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-100">
        <div class="bg-white rounded-2xl w-[545] shadow-xl relative p-6">
            <h2 class="text-lg font-semibold font-['Lexend'] mb-4">Password Changed Successfully</h2>
            <p class="text-sm text-gray-600 mb-6">Your password has been updated. Please log in again to continue.</p>
            <form method="POST" action="{{ route('superadmin.logout') }}" class="mt-4 flex justify-end">
                @csrf
                <button type="submit"
                    class="px-4 py-2 bg-red-900 text-white font-['Lexend'] rounded-lg hover:bg-red-800 transition duration-200 cursor-pointer">
                    Okay
                </button>
            </form>
        </div>
    </div>
    <style>
        input[type="password"]::-ms-reveal {
            display: none;
        }
    </style>
    <!-- Change Password Script -->
    @vite(['resources/js/settings/changepassword.js'])
    <!-- Update profile Sript -->
    @vite(['resources/js/settings/changeprofile.js'])
    <!-- Recovery Email Script -->
    <script>
        let isDirty = false;

        function openRecoveryEmail() {
            const modal = document.getElementById('recoveryEmailModal');
            modal.classList.remove('hidden');
            modal.style.display = 'flex';

            // Reset forms
            document.getElementById('recoveryEmailForm').classList.remove('hidden');
            document.getElementById('codeVerificationForm').classList.add('hidden');
            document.getElementById('recovery_email').value = '';
            document.getElementById('verification_code').value = '';
            document.getElementById('recoveryEmailError').innerText = '';
            document.getElementById('codeError').innerText = '';
        }

        function closeRecoveryEmailModal() {
            const modal = document.getElementById('recoveryEmailModal');
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Check if we need to show toast after page reload
            const pendingToast = sessionStorage.getItem('pendingToast');
            if (pendingToast) {
                const toastData = JSON.parse(pendingToast);
                sessionStorage.removeItem('pendingToast'); // Clear it immediately

                // Show toast after a brief delay to ensure page is fully loaded
                setTimeout(() => {
                    showToast(toastData.title, toastData.message);
                }, 100);
            }

            const verifyBtn = document.querySelector(
                '#codeVerificationForm button[onclick="verifyRecoveryCode()"]');
            const verifySpinner = document.createElement('span');
            verifySpinner.className =
                'hidden animate-spin border-2 border-white border-t-transparent rounded-full w-5 h-5 mr-2';
            verifyBtn.prepend(verifySpinner);

            const verifyBtnText = document.createElement('span');
            verifyBtnText.textContent = 'Verify Email';
            verifyBtnText.className = 'verify-btn-text';
            verifyBtn.appendChild(verifyBtnText);

            const codeInput = document.getElementById('verification_code');
            const codeError = document.getElementById('codeError');

            // Disable button until 6 digits
            codeInput.addEventListener('input', function() {
                if (/^\d{6}$/.test(codeInput.value.trim())) {
                    verifyBtn.disabled = false;
                    codeError.textContent = '';
                } else {
                    verifyBtn.disabled = true;
                    if (codeInput.value.trim() !== '') {
                        codeError.textContent = 'Enter the 6-digit code sent to your email.';
                    } else {
                        codeError.textContent = '';
                    }
                }
            });

            // Initial state
            verifyBtn.disabled = true;

            // Override verifyRecoveryCode to add loading and reload-first behavior
            window.verifyRecoveryCode = function(e) {
                if (verifyBtn.disabled) return;
                verifyBtn.disabled = true;
                verifySpinner.classList.remove('hidden');
                verifyBtnText.textContent = 'Verifying...';

                const verifyPromise = fetch('{{ route('superadmin.settings.verifyRecoveryCode') }}', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            code: codeInput.value.trim()
                        })
                    })
                    .then(res => res.json())
                const delayPromise = new Promise(resolve => setTimeout(resolve, 2000));

                Promise.all([verifyPromise, delayPromise])
                    .then(([data]) => {
                        if (data.success) {
                            isDirty = false;
                            document.getElementById('recoveryEmailModal').classList.add('hidden');
                            document.getElementById('recoveryEmailModal').style.display = 'none';

                            // Store toast data in sessionStorage before reload
                            sessionStorage.setItem('pendingToast', JSON.stringify({
                                title: 'Recovery Email Verified!',
                                message: 'Your recovery email has been verified successfully.'
                            }));

                            // Reload immediately - toast will show after reload
                            window.location.reload();
                        } else {
                            codeError.textContent = data.message || 'Invalid code. Please try again.';
                        }
                    })
                    .catch(() => {
                        codeError.textContent = 'An error occurred. Please try again.';
                    })
                    .finally(() => {
                        verifyBtn.disabled = false;
                        verifySpinner.classList.add('hidden');
                        verifyBtnText.textContent = 'Verify Email';
                    });
            };

            // Toast function using your existing Toast
            window.showToast = function(title, message) {
                let toast = document.getElementById('Toast');
                if (!toast) {
                    toast = document.createElement('div');
                    toast.id = 'Toast';
                    toast.className =
                        "fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white border-l-4 border-green-400 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50";
                    toast.innerHTML = `
                <div class="w-full flex justify-between">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('images/successful.svg') }}" alt="Success Icon" id="docTypeIcon"
                            draggable="false" class="select-none pointer-events-none">
                        <div>
                            <h6 class="font-bold font-['Manrope']">${title}</h6>
                            <p class="sm:inline inline text-sm font-['Manrope']">${message}</p>
                        </div>
                    </div>
                    <button type="button"
                        class="Cursor-pointer text-gray-500 hover:text-gray-700 text-2xl leading-none cursor-pointer"
                        onclick="document.getElementById('Toast').style.display='none';">&times;</button>
                </div>
            `;
                    document.body.appendChild(toast);
                } else {
                    toast.querySelector('h6').textContent = title;
                    toast.querySelector('p').textContent = message;
                    toast.style.display = 'flex';
                }
                setTimeout(() => {
                    if (toast) toast.style.display = 'none';
                }, 4000);
            };
        });

        document.getElementById('recoveryEmailForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const email = document.getElementById('recovery_email').value;
            const addBtn = document.getElementById('addRecoveryEmailButton');
            const spinner = document.getElementById('addRecoveryEmailSpinner');
            const btnText = document.getElementById('addRecoveryEmailBtnText');

            addBtn.disabled = true;
            spinner.classList.remove('hidden');
            btnText.textContent = 'Sending...';

            fetch('{{ route('superadmin.settings.sendRecoveryCode') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        recovery_email: email
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        isDirty = false;
                        document.getElementById('recoveryEmailForm').classList.add('hidden');
                        document.getElementById('codeVerificationForm').classList.remove('hidden');
                    }
                })
                .finally(() => {
                    addBtn.disabled = false;
                    spinner.classList.add('hidden');
                    btnText.textContent = 'Add Recovery Email';
                });
        });

        function verifyRecoveryCode() {
            const code = document.getElementById('verification_code').value;
            fetch('{{ route('superadmin.settings.verifyRecoveryCode') }}', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    code
                })
            })
        }
        document.addEventListener('DOMContentLoaded', function() {
            const recoveryEmailInput = document.getElementById('recovery_email');
            const recoveryEmailDisplay = document.getElementById('recoveryEmailDisplay');
            // When switching to code verification, update the display
            document.getElementById('recoveryEmailForm').addEventListener('submit', function(e) {
                setTimeout(function() {
                    if (recoveryEmailDisplay && recoveryEmailInput) {
                        recoveryEmailDisplay.textContent = recoveryEmailInput.value;
                    }
                }, 100); // Wait for DOM update
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            let resendSeconds = 60;
            let resendInterval;
            const resendBtn = document.getElementById('resendCodeBtn');
            const resendTimer = document.getElementById('resendTimer');
            const recoveryEmailInput = document.getElementById('recovery_email');
            const recoveryEmailForm = document.getElementById('recoveryEmailForm');
            const codeVerificationForm = document.getElementById('codeVerificationForm');
            const backBtn = document.getElementById('backToRecoveryFormBtn');

            function startResendTimer() {
                resendBtn.classList.add('hidden');
                resendTimer.classList.remove('hidden');
                resendSeconds = 60;
                resendTimer.textContent = `Resend code in ${resendSeconds}s`;
                resendInterval = setInterval(() => {
                    resendSeconds--;
                    if (resendSeconds > 0) {
                        resendTimer.textContent = `Resend code in ${resendSeconds}s`;
                    } else {
                        clearInterval(resendInterval);
                        resendTimer.classList.add('hidden');
                        resendBtn.classList.remove('hidden');
                    }
                }, 1000);
            }

            // Start timer when switching to code verification form
            recoveryEmailForm.addEventListener('submit', function(e) {
                setTimeout(startResendTimer, 200); // Wait for DOM update
            });

            // Resend code logic
            resendBtn.addEventListener('click', function() {
                resendBtn.disabled = true;
                resendBtn.textContent = 'Resending...';
                fetch('{{ route('superadmin.settings.sendRecoveryCode') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            recovery_email: recoveryEmailInput.value
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            resendBtn.textContent = 'Resend code';
                            resendBtn.disabled = false;
                            startResendTimer();
                        } else {
                            resendBtn.textContent = 'Resend code';
                            resendBtn.disabled = false;
                            resendTimer.textContent = 'Failed to resend. Try again.';
                        }
                    })
                    .catch(() => {
                        resendBtn.textContent = 'Resend code';
                        resendBtn.disabled = false;
                        resendTimer.textContent = 'Failed to resend. Try again.';
                    });
            });

            // Back button logic
            backBtn.addEventListener('click', function() {
                codeVerificationForm.classList.add('hidden');
                recoveryEmailForm.classList.remove('hidden');
                document.getElementById('verification_code').value = '';
                document.getElementById('codeError').innerText = '';
                clearInterval(resendInterval);
                resendBtn.classList.add('hidden');
                resendTimer.classList.add('hidden');
            });

            // Reset timer when modal is closed
            window.closeRecoveryEmailModal = function() {
                const modal = document.getElementById('recoveryEmailModal');
                modal.classList.add('hidden');
                modal.style.display = 'none';
                clearInterval(resendInterval);
                resendBtn.classList.add('hidden');
                resendTimer.classList.add('hidden');
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('recovery_email');
            const addBtn = document.getElementById('addRecoveryEmailButton');
            const emailError = document.getElementById('recoveryEmailError');

            function validateEmail(email) {
                // Basic email regex
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }

            emailInput.addEventListener('input', function() {
                if (validateEmail(emailInput.value.trim())) {
                    addBtn.disabled = false;
                    emailError.textContent = '';
                } else {
                    addBtn.disabled = true;
                    if (emailInput.value.trim() !== '') {
                        emailError.textContent = 'Please enter a valid email address.';
                    } else {
                        emailError.textContent = '';
                    }
                }
            });

            // Optionally, reset on modal open
            if (typeof openRecoveryEmail === 'function') {
                const origOpen = openRecoveryEmail;
                window.openRecoveryEmail = function() {
                    origOpen();
                    addBtn.disabled = true;
                    emailError.textContent = '';
                }
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const recoveryEmailModal = document.getElementById('recoveryEmailModal');
            const recoveryEmailInput = document.getElementById('recovery_email');
            const addBtn = document.getElementById('addRecoveryEmailButton');
            const codeInput = document.getElementById('verification_code');
            const verifyBtn = document.querySelector(
                '#codeVerificationForm button[onclick="verifyRecoveryCode()"]');
            const leaveWithoutSavingModal = document.getElementById('leaveWithoutSavingModal');
            let pendingClose = null;

            // Helper to clear and disable
            function resetRecoveryEmailForm() {
                recoveryEmailInput.value = '';
                addBtn.disabled = true;
                document.getElementById('recoveryEmailError').textContent = '';
            }

            function resetCodeVerificationForm() {
                codeInput.value = '';
                verifyBtn.disabled = true;
                document.getElementById('codeError').textContent = '';
            }

            // Override closeRecoveryEmailModal
            window.closeRecoveryEmailModal = function() {
                // If either input has value, show leave modal
                if ((recoveryEmailModal && !recoveryEmailModal.classList.contains('hidden')) &&
                    ((recoveryEmailInput && recoveryEmailInput.value.trim() !== '') ||
                        (codeInput && codeInput.value.trim() !== ''))
                ) {
                    leaveWithoutSavingRevModal.classList.remove('hidden');
                    leaveWithoutSavingRevModal.style.display = 'flex';
                    pendingClose = 'recoveryEmailModal';
                } else {
                    recoveryEmailModal.classList.add('hidden');
                    recoveryEmailModal.style.display = 'none';
                    resetRecoveryEmailForm();
                    resetCodeVerificationForm();
                }
            };

            // Leave Without Saving Modal buttons
            window.closeUnsavedRevModal = function() {
                // Actually close and reset
                if (pendingClose === 'recoveryEmailModal') {
                    recoveryEmailModal.classList.add('hidden');
                    recoveryEmailModal.style.display = 'none';
                    resetRecoveryEmailForm();
                    resetCodeVerificationForm();
                }
                leaveWithoutSavingRevModal.classList.add('hidden');
                leaveWithoutSavingRevModal.style.display = 'none';
                pendingClose = null;
            };
            window.keepRevEditing = function() {
                leaveWithoutSavingRevModal.classList.add('hidden');
                leaveWithoutSavingRevModal.style.display = 'none';
                pendingClose = null;
            };
        });
        document.addEventListener('DOMContentLoaded', function() {
            const recoveryEmailInput = document.getElementById('recovery_email');
            const codeVerificationForm = document.getElementById('codeVerificationForm');
            isDirty = false;
            let ignoreNextBeforeUnload = false;

            function checkDirty() {
                // Only dirty if user has typed in the input or is in code verification form
                if (codeVerificationForm && !codeVerificationForm.classList.contains('hidden')) {
                    return true;
                }
                if (recoveryEmailInput && recoveryEmailInput.value.trim() !== '') {
                    return true;
                }
                return false;
            }

            // Listen for input changes
            recoveryEmailInput.addEventListener('input', function() {
                isDirty = checkDirty();
            });

            // When switching forms, temporarily ignore beforeunload for this event loop
            const observer = new MutationObserver(() => {
                // If switching to code verification form, ignore beforeunload for this tick
                if (!codeVerificationForm.classList.contains('hidden')) {
                    ignoreNextBeforeUnload = true;
                    setTimeout(() => {
                        ignoreNextBeforeUnload = false;
                    }, 100);
                }
                isDirty = checkDirty();
            });
            observer.observe(codeVerificationForm, {
                attributes: true,
                attributeFilter: ['class']
            });

            // Prevent leaving if isDirty, but not on form switch
            window.addEventListener('beforeunload', function(e) {
                if (isDirty && !ignoreNextBeforeUnload) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            // Reset isDirty when modal is closed and fields are cleared
            window.closeRecoveryEmailModal = (function(orig) {
                return function() {
                    isDirty = false;
                    if (typeof orig === 'function') orig();
                };
            })(window.closeRecoveryEmailModal);
        });

        //Remove Recovery Email Modal
        function openPreRemoveRecoveryEmailModal() {
            document.getElementById('preRemoveRecoveryEmailModal').classList.remove('hidden');
            document.getElementById('preRemoveRecoveryEmailModal').style.display = 'flex';
        }

        function closePreRemoveRecoveryEmailModal() {
            document.getElementById('preRemoveRecoveryEmailModal').classList.add('hidden');
            document.getElementById('preRemoveRecoveryEmailModal').style.display = 'none';
        }

        function acceptRemoveRecoveryEmail() {
            closePreRemoveRecoveryEmailModal();
            openRemoveRecoveryEmailModal(); // This will send the code and open the real modal
        }

        function openRemoveRecoveryEmailModal() {
            const modal = document.getElementById('removeRecoveryEmailModal');
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            document.getElementById('removeVerificationCode').value = '';
            document.getElementById('removeCodeError').textContent = '';
            document.getElementById('confirmRemoveRecoveryEmailBtn').disabled = true;

            // Send code to recovery email
            fetch('{{ route('superadmin.settings.sendRecoveryCode') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    recovery_email: '{{ $user->recovery_email }}'
                })
            });

            startRemoveResendTimer();
        }

        function closeRemoveRecoveryEmailModal() {
            const modal = document.getElementById('removeRecoveryEmailModal');
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
        document.addEventListener('DOMContentLoaded', function() {
            const removeRecoveryEmailModal = document.getElementById('removeRecoveryEmailModal');
            const removeCodeInput = document.getElementById('removeVerificationCode');
            pendingCloseRemove = null;

            // Override closeRemoveRecoveryEmailModal to check for unsaved input
            window.closeRemoveRecoveryEmailModal = function() {
                if (
                    removeRecoveryEmailModal &&
                    !removeRecoveryEmailModal.classList.contains('hidden') &&
                    removeCodeInput &&
                    removeCodeInput.value.trim() !== ''
                ) {
                    leaveWithoutSavingRevRemoveModal.classList.remove('hidden');
                    leaveWithoutSavingRevRemoveModal.style.display = 'flex';
                    pendingCloseRemove = 'removeRecoveryEmailModal';
                } else {
                    removeRecoveryEmailModal.classList.add('hidden');
                    removeRecoveryEmailModal.style.display = 'none';
                    removeCodeInput.value = '';
                    document.getElementById('removeCodeError').textContent = '';
                }
            };

            // Leave Without Saving Modal buttons for remove recovery email
            window.closeUnsavedRevRemoveModal = (function() {
                return function() {
                    if (pendingCloseRemove === 'removeRecoveryEmailModal') {
                        removeRecoveryEmailModal.classList.add('hidden');
                        removeRecoveryEmailModal.style.display = 'none';
                        removeCodeInput.value = '';
                        document.getElementById('removeCodeError').textContent = '';
                    }
                    leaveWithoutSavingRevRemoveModal.classList.add('hidden');
                    leaveWithoutSavingRevRemoveModal.style.display = 'none';
                    pendingCloseRemove = null;
                };
            })(window.closeUnsavedRevModal);

            window.keepRevRemoveEditing = (function(orig) {
                return function() {
                    leaveWithoutSavingRevRemoveModal.classList.add('hidden');
                    leaveWithoutSavingRevRemoveModal.style.display = 'none';
                    pendingCloseRemove = null;
                    if (typeof orig === 'function') orig();
                };
            })(window.keepRevEditing);
        });
        let removeResendSeconds = 60;
        let removeResendInterval;
        let isRemoveDirty = false;
        let pendingCloseRemove = null;

        function startRemoveResendTimer() {
            const resendBtn = document.getElementById('removeResendCodeBtn');
            const resendTimer = document.getElementById('removeResendTimer');
            resendBtn.classList.add('hidden');
            resendTimer.classList.remove('hidden');
            removeResendSeconds = 60;
            resendTimer.textContent = `Resend code in ${removeResendSeconds}s`;
            removeResendInterval = setInterval(() => {
                removeResendSeconds--;
                if (removeResendSeconds > 0) {
                    resendTimer.textContent = `Resend code in ${removeResendSeconds}s`;
                } else {
                    clearInterval(removeResendInterval);
                    resendTimer.classList.add('hidden');
                    resendBtn.classList.remove('hidden');
                }
            }, 1000);
        }
        document.getElementById('removeResendCodeBtn').addEventListener('click', function() {
            this.disabled = true;
            this.textContent = 'Resending...';
            fetch('{{ route('superadmin.settings.sendRecoveryCode') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    recovery_email: '{{ $user->recovery_email }}'
                })
            }).then(() => {
                this.textContent = 'Resend code';
                this.disabled = false;
                startRemoveResendTimer();
            });
        });
        const removeCodeInput = document.getElementById('removeVerificationCode');
        const removeConfirmBtn = document.getElementById('confirmRemoveRecoveryEmailBtn');
        const removeCodeError = document.getElementById('removeCodeError');
        removeCodeInput.addEventListener('input', function() {
            if (/^\d{6}$/.test(removeCodeInput.value.trim())) {
                removeConfirmBtn.disabled = false;
                removeCodeError.textContent = '';
            } else {
                removeConfirmBtn.disabled = true;
                if (removeCodeInput.value.trim() !== '') {
                    removeCodeError.textContent = 'Enter the 6-digit code sent to your email.';
                } else {
                    removeCodeError.textContent = '';
                }
            }
        });

        document.getElementById('removeRecoveryEmailForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('confirmRemoveRecoveryEmailBtn');
            const spinner = document.getElementById('removeRecoveryEmailSpinner');
            const btnText = document.getElementById('removeRecoveryEmailBtnText');
            btn.disabled = true;
            spinner.classList.remove('hidden');
            btnText.textContent = 'Verifying...';

            fetch('{{ route('superadmin.settings.removeRecoveryEmail') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        code: document.getElementById('removeVerificationCode').value.trim()
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        pendingCloseRemove = null;
                        isRemoveDirty = false;
                        document.getElementById('removeRecoveryEmailModal').classList.add('hidden');
                        document.getElementById('removeRecoveryEmailModal').style.display = 'none';

                        // Store toast data in sessionStorage before reload
                        sessionStorage.setItem('pendingToast', JSON.stringify({
                            title: 'Recovery Email Removed!',
                            message: 'Your recovery email has been unlinked from your account.'
                        }));

                        // Reload immediately - toast will show after reload
                        window.location.reload();
                    } else {
                        removeCodeError.textContent = data.message || 'Invalid code. Please try again.';
                    }
                })
                .catch(() => {
                    removeCodeError.textContent = 'An error occurred. Please try again.';
                })
                .finally(() => {
                    btn.disabled = false;
                    spinner.classList.add('hidden');
                    btnText.textContent = 'Confirm Remove';
                });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const removeRecoveryEmailModal = document.getElementById('removeRecoveryEmailModal');
            const removeCodeInput = document.getElementById('removeVerificationCode');
            isRemoveDirty = false;

            function checkRemoveDirty() {
                // If modal is visible and input has value, consider dirty
                return (
                    removeRecoveryEmailModal &&
                    !removeRecoveryEmailModal.classList.contains('hidden') &&
                    removeCodeInput &&
                    removeCodeInput.value.trim() !== ''
                );
            }

            // Listen for input changes
            removeCodeInput.addEventListener('input', function() {
                isRemoveDirty = checkRemoveDirty();
            });

            // Listen for modal open/close (class changes)
            const removeObserver = new MutationObserver(() => {
                isRemoveDirty = checkRemoveDirty();
            });
            removeObserver.observe(removeRecoveryEmailModal, {
                attributes: true,
                attributeFilter: ['class']
            });

            // Prevent leaving if isRemoveDirty
            window.addEventListener('beforeunload', function(e) {
                if (isRemoveDirty) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            // Reset isRemoveDirty when modal is closed and input is cleared
            window.closeRemoveRecoveryEmailModal = (function(orig) {
                return function() {
                    isRemoveDirty = false;
                    if (typeof orig === 'function') orig();
                };
            })(window.closeRemoveRecoveryEmailModal);
        });
    </script>
@endsection
