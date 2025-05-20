@extends('base')

@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    @include('components.adminNavBarComponent')
    @include('components.adminSidebarComponent')
    <div id="main-content" class="transition-all duration-300 ml-[20%]">
        @if (session('success'))
            <div id="Toast"
                class="fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white border-l-4 border-green-400 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50"
                role="alert">
                <div class="w-full flex justify-between">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('images/successful.svg') }}" alt="Success Icon" id="docTypeIcon" class="">
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
            <div class="flex items-center gap-8 mb-8">
                <div class="relative  w-36 h-36 rounded-full">
                    @if ($user->profile_pic)
                        <!-- Show uploaded profile image -->
                        <div class="border-3 border-gray-300 rounded-full">
                            <img src="{{ asset('storage/' . $user->profile_pic) }}" alt="Profile"
                                class="w-35 h-35 rounded-full object-cover">
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
                <div>
                    <h3 class="text-2xl font-black tracking-wider font-['Lexend']">ELITE</h3>
                    <p class="uppercase text-lg tracking-wider font-semibold font-['Lexend']">Academic Organization</p>
                    <div id="" class="mt-2 text-sm relative flex items-center gap-20">
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/Smail.svg') }}" class="w-6 h-6" alt="email icon">
                            <div>
                                <p class="font-extrabold text-[11px]">Email</p>
                                <p class="font-extrabold text-[12px]">elite@pup.edu.ph</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/department.svg') }}" class="w-6 h-6" alt="department icon">
                            <div>
                                <p class="font-extrabold text-[11px]">Department</p>
                                <p class="font-extrabold text-[12px]">BSIT</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Info -->
            <div class="bg-white w-full [box-shadow:1px_2px_7px_rgba(0,0,0,0.3)] rounded-3xl ">
                <div class="border-b w-full px-4 py-3">
                    <h4 class="text-2xl font-bold mb-2 mt-1 font-['Lexend']">SECURITY INFO</h4>
                    <p class="text-sm text-gray-600">Manage your password settings here to reset your password and
                        enhance
                        your account security.</p>
                </div>
                <div class="border-t-[0] w-full flex justify-between items-center px-6 py-5 ">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('images/dpassword.svg') }}" class="w-6 h-6" alt="password icon">
                        <p class="font-['Lexend'] text-sm">Password</p>
                    </div>
                    <div class="flex flex-col items-center text-center">
                        <p class="font-['Lexend'] text-sm">Password</p>
                        <p class="text-gray-400 text-xs">Last Updated: 6 months ago</p>
                    </div>
                    <button class="text-blue-600 font-bold bg-transparent border-none cursor-pointer text-sm"
                        onclick="openChangePasswordModal()">Change</button>
                </div>
            </div>
        </div>
    </div>
    <input type="file" name="profile_image" id="profileImageInput" class="hidden" accept="image/*">
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
                <img id="profilePreviewImage"
                    src="{{ $user->profile_pic ? asset('storage/' . $user->profile_pic) : asset('images/dprofile.svg') }}"
                    alt="Profile Preview" class="w-70 h-70 rounded-full border-5 border-gray-300 object-cover">
            </div>
            <div class="flex justify-end p-6 gap-4">
                @if ($user->profile_pic != null)
                    <form action="{{ route('student.settings.remove-profile-picture') }}" method="POST">
                        @csrf
                        <input type="hidden" name="profile_image" value="{{ $user->profile_pic }}">
                        <label id="removeProfileButton" onclick="openRemoveProfileModal()"
                            class="rounded-lg text-gray-900 font-medium px-4 py-2 text-[14px] font-[Lexend] hover:bg-gray-400 transition cursor-pointer">
                            Remove Profile
                        </label>
                    </form>
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
            <form id="uploadForm" action="{{ route('admin.settings.update-profile-picture') }}" method="POST"
                enctype="multipart/form-data" class="w-full flex justify-end items-center py-6 px-4">
                @csrf
                <input type="hidden" name="profile_image_base64" id="profileImageBase64">
                <div class="flex justify-between items-center gap-4">
                    <div class="flex gap-2">
                        <button type="button" onclick="closeModal()"
                            class="rounded-lg text-gray-900 font-medium px-4 py-2 text-[14px] font-[Lexend] hover:bg-gray-400 transition cursor-pointer">Cancel</button>
                    </div>
                    <button id="saveProfileButton"
                        class="rounded-lg bg-red-900 text-white font-medium px-4 py-2 text-[14px] font-[Lexend] hover:bg-red-900 transition cursor-pointer">Save</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Remove Profile Modal -->
    <div id="removeProfileModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-100">
        <div class="bg-white rounded-2xl w-full max-w-sm shadow-xl relative space-y-4">
            <div class="w-full flex justify-between pt-5 px-5">
                <h2 class="text-lg font-semibold font-['Lexend']">Remove Profile Picture?</h2>
            </div>
            <div class="px-6 pb-6">
                <p class="text-sm text-gray-600 pb-3">Are you sure you want to remove your profile picture? This action
                    cannot be undone.
                </p>
                <div class="flex justify-end gap-4 mt-4">
                    <button type="button" onclick="closeRemoveProfileModal()"
                        class="rounded-lg text-gray-900 font-medium px-4 py-2 text-[14px] font-[Lexend] hover:bg-gray-400 transition cursor-pointer">Cancel</button>
                    <button type="button" onclick="submitRemoveProfileForm()"
                        class="rounded-lg bg-red-900 text-white font-medium px-4 py-2 text-[14px] font-[Lexend] hover:bg-red-900 transition cursor-pointer">Remove
                        Profile</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Save Changes for Update Profile Modal -->
    <div id="saveChangesModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-100">
        <div class="bg-white rounded-2xl w-full max-w-sm shadow-xl relative space-y-4">
            <div class="w-full flex justify-between pt-5 px-5">
                <h2 class="text-lg font-semibold font-['Lexend']">Save Changes?</h2>
            </div>
            <div class="px-6 pb-6">
                <p class="text-sm text-gray-600 pb-3">Do you want to save this profile picture? Your changes will be
                    updated immediately.
                </p>
                <div class="flex justify-end gap-4 mt-4">
                    <button type="button" onclick="closeChangesModal()"
                        class="rounded-lg text-gray-900 font-medium px-4 py-2 text-[14px] font-[Lexend] hover:bg-gray-400 transition cursor-pointer">Cancel</button>
                    <button type="button" onclick="keepEditing()"
                        class="rounded-lg bg-red-900 text-white font-medium px-4 py-2 text-[14px] font-[Lexend] hover:bg-red-900 transition cursor-pointer">Save
                        Changes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Cancel Edit Image Modal -->
    <div id="cancelEditImageModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-100">
        <div class="bg-white rounded-2xl w-full max-w-sm shadow-xl relative space-y-4">
            <div class="w-full flex justify-between pt-5 px-5">
                <h2 class="text-lg font-semibold font-['Lexend']">Discard Changes?</h2>
            </div>
            <div class="px-6 pb-6">
                <p class="text-sm text-gray-600 pb-3">You have unsaved changes. Are you sure you want to leave without
                    saving?
                </p>
                <div class="flex justify-end gap-4 mt-4">
                    <button type="button" onclick="closeModal()"
                        class="rounded-lg text-gray-900 font-medium px-4 py-2 text-[14px] font-[Lexend] hover:bg-gray-400 transition cursor-pointer">Close
                        without saving</button>
                    <button type="button" onclick="keepEditing()"
                        class="rounded-lg bg-red-900 text-white font-medium px-4 py-2 text-[14px] font-[Lexend] hover:bg-red-900 transition cursor-pointer">Keep
                        editing</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Change Password Modal -->
    <div id="changePasswordModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
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
            <form id="changePasswordForm" action="{{ route('admin.settings.change-password') }}" method="POST"
                class="px-6 pb-6 space-y-5">
                @csrf
                <div>
                    <input type="password" name="current_password" id="current_password" required
                        placeholder="Current Password"
                        class="block w-full rounded-lg border border-black px-4 py-1 focus:border-gray-500 focus:ring-gray-500 placeholder:text-black placeholder:text-[14px] placeholder:font-[Lexend]">
                </div>
                <div>
                    <input type="password" name="new_password" id="new_password" required minlength="8"
                        placeholder="New Password"
                        class="block w-full rounded-lg border border-black px-4 py-1 focus:border-gray-500 focus:ring-gray-500 placeholder:text-black placeholder:text-[14px] placeholder:font-[Lexend]">
                </div>
                <div>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                        placeholder="Confirm Password"
                        class="mb-2 block w-full rounded-lg border border-black px-4 py-1 focus:border-gray-500 focus:ring-gray-500 placeholder:text-black placeholder:text-[14px] placeholder:font-[Lexend]">
                    <div id="currentPasswordError" class="text-red-500 text-xs font-['Lexend']"></div>
                    <div id="newPasswordError" class="text-red-500 text-xs font-['Lexend']"></div>
                    <div id="confirmPasswordError" class="text-red-500 text-xs font-['Lexend']"></div>

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
        <div class="bg-white rounded-2xl w-full max-w-sm shadow-xl relative space-y-4">
            <div class="w-full flex justify-between pt-5 px-5">
                <h2 class="text-lg font-semibold font-['Lexend']">Discard Changes?</h2>
            </div>
            <div class="px-6 pb-6">
                <p class="text-sm text-gray-600 pb-3">You have unsaved changes. Are you sure you want to leave without
                    saving?
                </p>
                <div class="flex justify-end gap-4 mt-4">
                    <button type="button" onclick="closeUnsavedModal()"
                        class="rounded-lg text-gray-900 font-medium px-4 py-2 text-[14px] font-[Lexend] hover:bg-gray-400 transition cursor-pointer">Close
                        without saving</button>
                    <button type="button" onclick="keepEditing()"
                        class="rounded-lg bg-red-900 text-white font-medium px-4 py-2 text-[14px] font-[Lexend] hover:bg-red-900 transition cursor-pointer">Keep
                        editing</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-100">
        <div class="bg-white rounded-2xl w-full max-w-sm shadow-xl relative p-6">
            <h2 class="text-lg font-semibold font-['Lexend'] mb-4">Password Changed Successfully</h2>
            <p class="text-sm text-gray-600 mb-6">Your password has been updated. Please log in again to continue.</p>
            <form method="POST" action="{{ route('logout') }}" class="mt-4 flex justify-end">
                @csrf
                <button type="submit"
                    class="px-4 py-2 bg-red-900 text-white font-['Lexend'] rounded-lg hover:bg-red-800 transition duration-200 cursor-pointer">
                    Okay
                </button>
            </form>
        </div>
    </div>

    <!-- Change Password Script -->
    <script>
        const changePasswordButton = document.getElementById('changePasswordButton');
        const currentPasswordInput = document.getElementById('current_password');
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('new_password_confirmation');

        function toggleButtonState() {
            if (currentPasswordInput.value.trim() && newPasswordInput.value.trim() && confirmPasswordInput.value.trim()) {
                changePasswordButton.disabled = false;
            } else {
                changePasswordButton.disabled = true;
            }
        }

        currentPasswordInput.addEventListener('input', toggleButtonState);
        newPasswordInput.addEventListener('input', toggleButtonState);
        confirmPasswordInput.addEventListener('input', toggleButtonState);
        // Initialize the change password modal
        const changePasswordModal = document.getElementById('changePasswordModal');
        const leaveWithoutSavingModal = document.getElementById('leaveWithoutSavingModal');
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            // Set button to loading state
            changePasswordButton.disabled = true;
            changePasswordButton.textContent = 'Changing...';

            // Clear previous error messages and remove error borders
            document.getElementById('currentPasswordError').textContent = '';
            document.getElementById('newPasswordError').textContent = '';
            document.getElementById('confirmPasswordError').textContent = '';
            ['current_password', 'new_password', 'new_password_confirmation'].forEach(id => {
                document.getElementById(id).classList.remove('border-red-500');
            });
            // Validate new password
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;

            if (newPassword === currentPassword) {
                document.getElementById('newPasswordError').textContent =
                    'The new password cannot be the same as the current password.';
                document.getElementById('new_password').classList.add('border-red-500');
                resetButtonState();
                return;
            }

            if (newPassword.trim() !== newPassword || /^\s*$/.test(newPassword)) {
                document.getElementById('newPasswordError').textContent =
                    'The new password cannot contain leading or trailing spaces or be all spaces.';
                document.getElementById('new_password').classList.add('border-red-500');
                resetButtonState();
                return;
            }

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            if (data.errors) {
                                // Display validation errors and add red border to fields with errors
                                if (data.errors.current_password) {
                                    document.getElementById('currentPasswordError').textContent = data
                                        .errors.current_password[0];
                                    document.getElementById('current_password').classList.add(
                                        'border-red-500');
                                }
                                if (data.errors.new_password) {
                                    document.getElementById('newPasswordError').textContent = data
                                        .errors.new_password[0];
                                    document.getElementById('new_password').classList.add(
                                        'border-red-500');
                                }
                                if (data.errors.new_password_confirmation) {
                                    document.getElementById('confirmPasswordError').textContent = data
                                        .errors.new_password_confirmation[0];
                                    document.getElementById('new_password_confirmation').classList.add(
                                        'border-red-500');
                                }
                            }
                            throw new Error('Validation failed');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Show success modal
                    const successModal = document.getElementById('successModal');
                    const changePasswordModal = document.getElementById('changePasswordModal');
                    changePasswordModal.classList.add('hidden');
                    changePasswordModal.style.display = 'none';
                    successModal.classList.remove('hidden');
                    successModal.style.display = 'flex';

                })
                .catch(error => {
                    console.error(error);
                })
                .finally(() => {
                    resetButtonState();
                });
        });

        function resetButtonState() {
            changePasswordButton.disabled = false;
            changePasswordButton.textContent = 'Change Password';
        }

        // Automatically remove error messages and red borders on input
        document.getElementById('current_password').addEventListener('input', function() {
            document.getElementById('currentPasswordError').textContent = '';
            this.classList.remove('border-red-500');
        });

        document.getElementById('new_password').addEventListener('input', function() {
            document.getElementById('newPasswordError').textContent = '';
            this.classList.remove('border-red-500');
        });

        document.getElementById('new_password_confirmation').addEventListener('input', function() {
            document.getElementById('confirmPasswordError').textContent = '';
            this.classList.remove('border-red-500');
        });

        function openChangePasswordModal() {
            changePasswordModal.classList.remove('hidden');
            changePasswordModal.style.display = 'flex';
        }

        function closeChangePasswordModal() {
            if (document.getElementById('current_password').value === '' &&
                document.getElementById('new_password').value === '' &&
                document.getElementById('new_password_confirmation').value === '') {
                changePasswordModal.classList.add('hidden');
                changePasswordModal.style.display = 'none';
            } else {
                leaveWithoutSavingModal.classList.remove('hidden');
                leaveWithoutSavingModal.style.display = 'flex';
            }
        }

        function closeUnsavedModal() {
            changePasswordModal.classList.add('hidden');
            changePasswordModal.style.display = 'none';
            leaveWithoutSavingModal.classList.add('hidden');
            leaveWithoutSavingModal.style.display = 'none';
            changePasswordButton.disabled = true;
            // Clear all input fields
            document.getElementById('current_password').value = '';
            document.getElementById('new_password').value = '';
            document.getElementById('new_password_confirmation').value = '';

            // Clear red borders
            ['current_password', 'new_password', 'new_password_confirmation'].forEach(id => {
                const input = document.getElementById(id);
                input.classList.remove('input-error', 'border-red-500'); // remove both if you mix class styles
            });

            // Clear error messages
            ['currentPasswordError', 'newPasswordError', 'confirmPasswordError'].forEach(errorId => {
                const errorDiv = document.getElementById(errorId);
                if (errorDiv) errorDiv.textContent = '';
            });

        }

        function keepEditing() {
            leaveWithoutSavingModal.classList.add('hidden');
            leaveWithoutSavingModal.style.display = 'none';
        }
    </script>
    <!-- Update profile Sript -->
    <script>
        function openProfilePreviewModal() {
            const profilePreviewModal = document.getElementById('profilePreviewModal');
            profilePreviewModal.classList.remove('hidden');
            profilePreviewModal.style.display = 'flex';
        }

        function closeProfilePreviewModal() {
            const profilePreviewModal = document.getElementById('profilePreviewModal');
            profilePreviewModal.classList.add('hidden');
            profilePreviewModal.style.display = 'none';
        }
        const input = document.getElementById('profileImageInput');
        const modal = document.getElementById('imagePreviewModal');
        const preview = document.getElementById('previewImage');
        const base64Input = document.getElementById('profileImageBase64');
        const zoomSlider = document.getElementById('zoomRange');
        let cropper;

        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {

                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    modal.classList.remove('hidden');
                    modal.style.display = 'flex';

                    const styleEl = document.createElement('style');
                    styleEl.id = 'cropperCustomStyles';
                    styleEl.innerHTML = `
                .cropper-line, .cropper-point {
                    background-color: white !important;
                }
                .cropper-view-box {
                    outline: 3px solid white !important;
                    outline-color: white !important;
                }
                .cropper-face {
                    background-color: transparent !important;
                }
                .cropper-dashed {
                    border-color: white !important;
                }
            `;
                    document.head.appendChild(styleEl);

                    preview.onload = function() {
                        if (cropper) cropper.destroy();

                        cropper = new Cropper(preview, {
                            aspectRatio: 1,
                            viewMode: 1,
                            dragMode: 'move',
                            autoCropArea: 1,
                            background: false,
                            zoomOnWheel: true,
                            guides: false,
                            highlight: false,
                            cropBoxMovable: false,
                            cropBoxResizable: false,
                            movable: true,
                            cropBoxHighlight: true,
                            modal: true,
                            minCropBoxWidth: 500,
                            minCropBoxHeight: 500,

                            ready() {
                                zoomSlider.value = 0;

                                // Make crop box circular
                                const cropBox = document.querySelector('.cropper-crop-box');
                                const viewBox = document.querySelector('.cropper-view-box');
                                const cropperFace = document.querySelector('.cropper-face');

                                if (cropBox && viewBox) {
                                    // Ensure the crop box is visible
                                    cropBox.style.display = 'block';
                                    viewBox.style.display = 'block';

                                    // Apply circular mask to the crop box
                                    cropBox.style.borderRadius = '50%';
                                    viewBox.style.borderRadius = '50%';

                                    if (cropperFace) {
                                        cropperFace.style.borderRadius = '50%';
                                    }

                                    // Manually set the crop box size to be larger (if needed)
                                    const containerData = cropper.getContainerData();
                                    const size = Math.min(containerData.width, containerData
                                        .height) * 0.9;

                                    // Center the crop box
                                    const left = (containerData.width - size) / 2;
                                    const top = (containerData.height - size) / 2;

                                    // Set the crop box data
                                    cropper.setCropBoxData({
                                        left: left,
                                        top: top,
                                        width: size,
                                        height: size
                                    });

                                    // Add proper highlight for circular area
                                    document.querySelector('.cropper-modal').style.opacity = '0.5';
                                }
                            }
                        });
                    };
                };
                reader.readAsDataURL(file);
            }
        });

        zoomSlider.addEventListener('input', function() {
            if (cropper) cropper.zoomTo(parseFloat(this.value));
        });

        document.getElementById('saveProfileButton').addEventListener('click', function(e) {
            e.preventDefault();
            if (!cropper) return;

            const size = 300;
            const canvas = cropper.getCroppedCanvas({
                width: size,
                height: size,
                imageSmoothingQuality: 'high',
                fillColor: 'transparent',
                imageSmoothingEnabled: true,
            });

            const circularCanvas = document.createElement('canvas');
            circularCanvas.width = size;
            circularCanvas.height = size;
            const ctx = circularCanvas.getContext('2d');

            ctx.beginPath();
            ctx.arc(size / 2, size / 2, size / 2, 0, Math.PI * 2);
            ctx.closePath();
            ctx.clip();
            ctx.drawImage(canvas, 0, 0, size, size);

            base64Input.value = circularCanvas.toDataURL('image/png');
            const saveChangesModal = document.getElementById('saveChangesModal');

            // Open Save Changes Modal
            saveChangesModal.classList.remove('hidden');
            saveChangesModal.style.display = 'flex';

            // Handle Save Changes Modal buttons
            document.querySelector('#saveChangesModal button[onclick="keepEditing()"]').addEventListener('click',
                function() {
                    saveChangesModal.classList.add('hidden');
                    saveChangesModal.style.display = 'none';
                    // Submit the form
                    document.getElementById('uploadForm').submit();
                });

            document.querySelector('#saveChangesModal button[onclick="closeChangesModal()"]').addEventListener(
                'click',
                function() {
                    saveChangesModal.classList.add('hidden');
                    saveChangesModal.style.display = 'none';
                });
        });

        function openRemoveProfileModal() {
            const removeProfileModal = document.getElementById('removeProfileModal');
            removeProfileModal.classList.remove('hidden');
            removeProfileModal.style.display = 'flex';
        }

        function closeRemoveProfileModal() {
            const removeProfileModal = document.getElementById('removeProfileModal');
            removeProfileModal.classList.add('hidden');
            removeProfileModal.style.display = 'none';
        }

        function submitRemoveProfileForm() {
            const form = document.querySelector('#profilePreviewModal form');
            if (form) {
                form.submit();
            }
        }

        function closeModal() {
            const cancelEditImageModal = document.getElementById('cancelEditImageModal');
            cancelEditImageModal.classList.remove('hidden');
            cancelEditImageModal.style.display = 'flex';

            document.querySelector('#cancelEditImageModal button[onclick="closeModal()"]').addEventListener('click',
                function() {
                    modal.classList.add('hidden');
                    modal.style.display = 'none';
                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }
                    preview.src = '';
                    input.value = '';
                    cancelEditImageModal.classList.add('hidden');
                    cancelEditImageModal.style.display = 'none';
                });
            document.querySelector('#cancelEditImageModal button[onclick="keepEditing()"]').addEventListener('click',
                function() {
                    const cancelEditImageModal = document.getElementById('cancelEditImageModal');
                    cancelEditImageModal.classList.add('hidden');
                    cancelEditImageModal.style.display = 'none';
                });
        }

        setTimeout(() => {
            const toast = document.getElementById('Toast');
            if (toast) {
                toast.style.display = 'none';
            }
        }, 5000);
    </script>
@endsection
