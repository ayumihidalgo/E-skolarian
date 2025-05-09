@extends('base')

@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    @include('components.adminNavBarComponent')
    @include('components.adminSideBarComponent')
    <div id="main-content" class="transition-all duration-300 ml-[20%]">

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
                    <label for="profileImageInput"
                        class="absolute bottom-[-5px] right-2 bg-yellow-500 p-[5px] rounded-full cursor-pointer z-10">
                        <img src="{{ asset('images/camera.svg') }}" class="w-6 h-6" alt="camera icon">
                    </label>
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
            <form id="uploadForm" action="{{ route('settings.update-profile-picture') }}" method="POST"
                enctype="multipart/form-data" class="w-full flex justify-end items-center py-6 px-4">
                @csrf
                <input type="hidden" name="profile_image_base64" id="profileImageBase64">
                <div class="flex justify-between items-center gap-4">
                    <div class="flex gap-2">
                        <button type="button" onclick="closeModal()"
                            class="px-4 py-2 text-gray-700 hover:bg-gray-200 rounded transition">Cancel</button>
                    </div>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Save</button>
                </div>
            </form>
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
                        class="text-gray-500 hover:text-gray-700 focus:outline-none">
                        <i class="text-xl fas fa-times"></i></button>
                </div>
            </div>
            <form id="changePasswordForm" action="{{ route('settings.change-password') }}" method="POST"
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
                        class="block w-full rounded-lg border border-black px-4 py-1 focus:border-gray-500 focus:ring-gray-500 placeholder:text-black placeholder:text-[14px] placeholder:font-[Lexend]">
                </div>
                <div class="pt-2">
                    <button type="submit"
                        class="w-full rounded-lg bg-red-900 px-4 py-2 text-white font-medium text-[14px] font-[Lexend] hover:bg-red-900 transition">
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <script>
        const changePasswordModal = document.getElementById('changePasswordModal');

        function openChangePasswordModal() {
            changePasswordModal.classList.remove('hidden');
            changePasswordModal.style.display = 'flex';
        }

        function closeChangePasswordModal() {
            changePasswordModal.classList.add('hidden');
            changePasswordModal.style.display = 'none';
        }
    </script>
    <script>
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

        document.getElementById('uploadForm').addEventListener('submit', function(e) {
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
            e.target.submit();
        });

        function closeModal() {
            modal.classList.add('hidden');
            modal.style.display = 'none';
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            preview.src = '';
            input.value = '';
        }
    </script>
@endsection
