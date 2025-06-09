<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="student">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="icon" href="{{ asset('images/officialLogo.svg') }}" type="image/svg+xml">

    <title>E-skolarian</title>
    <style>
        input[type="password"]::-ms-reveal {
            display: none;
        }
    </style>

    @vite('resources/css/app.css')

    <script>
        /* To carousel set of images */
        const images = [
            "{{ asset('images/PUP_Bg1.jpg') }}",
            "{{ asset('images/PUP_Bg2.jpg') }}",
            "{{ asset('images/PUP_Bg3.jpg') }}",
            "{{ asset('images/PUP_Bg4.jpg') }}",
            "{{ asset('images/PUP_Bg5.jpg') }}",
            "{{ asset('images/PUP_Bg6.jpg') }}",
            "{{ asset('images/PUP_Bg7.jpg') }}",
            "{{ asset('images/PUP_Bg8.jpg') }}"
        ];

        const preloadImages = images.map((src) => {
            const img = new Image();
            img.src = src;
            return img;
        });

        let currentIndex = Math.floor(Math.random() * images.length);
        let showingA = true;

        function setLayerBackground(element, url) {
            element.style.backgroundImage = `linear-gradient(var(--login-bg-color), var(--login-bg-color)), url(${url})`;
            element.style.backgroundRepeat = 'no-repeat';
            element.style.backgroundSize = 'cover';
            element.style.backgroundPosition = 'center';
        }

        function transitionBackground() {
            const bgA = document.getElementById('bgA');
            const bgB = document.getElementById('bgB');

            const nextIndex = (currentIndex + 1) % images.length;
            const nextImage = images[nextIndex];

            if (showingA) {
                setLayerBackground(bgB, nextImage);
                bgB.classList.remove('opacity-0');
                bgB.classList.add('opacity-100');
                bgA.classList.remove('opacity-100');
                bgA.classList.add('opacity-0');
            } else {
                setLayerBackground(bgA, nextImage);
                bgA.classList.remove('opacity-0');
                bgA.classList.add('opacity-100');
                bgB.classList.remove('opacity-100');
                bgB.classList.add('opacity-0');
            }
            showingA = !showingA;
            currentIndex = nextIndex;
        }

        window.addEventListener('load', () => {
            setInterval(transitionBackground, 10000); // Change image every 10s

            const form = document.getElementById('formContainer');
            if (form) form.classList.remove('opacity-0');
        });

        /* Toggle Show/Hide Password */
        function togglePassword(event) {
            const input = document.getElementById('password');
            showPassIcon = document.getElementById('showPass');
            hidePassIcon = document.getElementById('hidePass');
            if (input.type === 'password') {
                input.type = 'text';
                showPassIcon.classList.add('hidden');
                hidePassIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                showPassIcon.classList.remove('hidden');
                hidePassIcon.classList.add('hidden');
            }
        }

        /* Fade Messages  */
        document.addEventListener('DOMContentLoaded', function () {
            const statusMessages = document.querySelectorAll('.status-message');
            statusMessages.forEach(function (message) {
                // Skip fading out if it's the lockout message
                if (message.id === 'lockout-message') return;
                setTimeout(function () {
                    message.classList.add('opacity-0', 'transition-opacity');
                    setTimeout(function () {
                        message.remove();
                    }, 500);
                }, 3000);
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const reportBtn = document.getElementById('reportBtn');
            const reportModal = document.getElementById('reportModal');

            reportBtn.addEventListener('click', () => {
            reportModal.classList.remove('hidden');
            });
        });
    </script>
    @php
        $randomIndex = rand(1, 8);
        $randomImage = asset("images/PUP_Bg$randomIndex.jpg");
    @endphp
</head>

@include('loading')
<body id="box" class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-r from-[var(--login-color-left)] to-[var(--login-color-right)] md:bg-[var(--secondary-color)] font-['Manrope'] font-bold">
    <div id="bgA" class="fixed inset-0 z-0 transition-all duration-1000 ease-in-out opacity-100" style="background: linear-gradient(var(--login-bg-color), var(--login-bg-color)), url('{{ $randomImage }}'); background-size: cover; background-repeat: no-repeat; background-position: center;"></div>
    <div id="bgB" class="fixed inset-0 z-0 transition-opacity duration-1000 ease-in-out opacity-0"></div>
    <div id="formWrapper" class="w-full h-full max-md:p-[20px] max-md:max-w-md md:absolute relative">
        <div id="formContainer" class="opacity-0 flex flex-col items-center justify-center h-full px-6 bg-[#FFFFFFCC] p-4 md:w-[50%] md:max-w-[600px] rounded-4xl md:rounded-l-none md:rounded-r-[80px] md:backdrop-blur-xs md:transition-all md:duration-1000 md:absolute md:left-0 md:top-0 md:bottom-0">
            <div class="h-35 flex items-center">
                <img class="mx-auto h-19 md:h-22" src="{{ asset('images/e-skolarianLogo.svg') }}" alt="E-skolarian Logo">
            </div>
            <h1 class="font-[Lexend] text-[#A98018] text-2xl md:text-3xl text-center md:pt-6">STUDENT ORGANIZATION <br> LOGIN</h1>
            <div class="w-full max-w-[400px] mx-auto pt-14 md:pt-10">
                <form method="POST" action="{{ route('student.login') }}" class="space-y-2 pb-10">
                    @csrf
                    <input type="hidden" name="role" id="role" value="student">
                    <!-- Email -->
                    <div class="pb-10">
                        <label id="emailLabel" class="w-full rounded-2xl px-3 py-2 md:p-4 ring bg-white flex focus-within:ring-3 focus-within:ring-[var(--secondary-color)]">
                            <input type="email" id="emailInput" name="email" placeholder="Email Address" required
                                class="w-0 flex-grow outline-none mr-3" maxlength="100">
                            <button type="button" class="focus:outline-none" tabindex="-1">
                                <img src="{{ asset('images/email.svg') }}" alt="Email Icon" class="w-5 md:w-6" />
                            </button>
                        </label>
                        <div id="emailLengthWarning" class="text-red-600 text-sm mt-0.5 pl-[10px] font-[Lexend] font-normal hidden">
                            <p>*Email must not exceed 50 characters.</p>
                        </div>
                    </div>
                    <!-- Password -->
                    <div>
                        <label id="passwordLabel" class="w-full rounded-2xl px-3 py-2 md:p-4 bg-white flex ring focus-within:ring-3 focus-within:ring-[var(--secondary-color)]">
                            <input id="password" type="password" name="password" placeholder="Password" required
                                class="w-0 flex-grow outline-none mr-3">
                            <button type="button" onclick="togglePassword(event)" class="cursor-pointer">
                                <img id="showPass" src="{{ asset('images/show_pass.svg') }}" alt="Show Password" class="w-5 md:w-6" />
                                <img id="hidePass" src="{{ asset('images/hide_pass.svg') }}" alt="Hide Password" class="w-5 md:w-6 hidden" />
                            </button>
                        </label>
                        <div id="passwordLengthWarning" class="text-red-600 text-sm mt-0.5 pl-[10px] font-[Lexend] font-normal hidden">
                            <p>*Password must not exceed 50 characters.</p>
                        </div>
                    </div>

                    <!-- Error Message -->
                    @if ($errors->any() && !$errors->has('lockout_time'))
                    <div class="status-message text-red-600 text-sm mt-1 pb-1.5 font-[Lexend] font-normal">
                        <p>{{ $errors->first() }} </p>
                    </div>
                    @endif

                    <!-- Lockout Message (ADDED) -->
                    <div id="lockout-message-container" class="text-red-600 text-sm mt-1 pb-1.5 font-[Lexend] font-normal" style="display:none;">
                        <p id="lockout-message">
                            Too many login attempts. Please try again in <span id="lockout-timer"></span>.
                        </p>
                    </div>

                    <div class="flex justify-between items-center pt-1">
                        <!-- Remember Me -->
                        <div class="flex items-center mt-0.5">
                            <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-[var(--secondary-color)] border-gray-300 rounded focus:ring-[var(--secondary-color)]">
                            <label for="remember" class="ml-2 block text-sm text-gray-900 font-normal">
                                Remember Me
                            </label>
                        </div>
                        <a href="{{ route('student.password.request') }}" class="inline-block font-normal text-[14px] active:text-[var(--secondary-color)] transition-all duration-75">Forgot Password?</a>
                    </div>


                    <!-- Submit -->
                    <div class="pt-4 md:pt-8 flex justify-center">
                        <button type="submit" id="signInButton"
                            class="opacity-50 w-full max-md:max-w-[280px] rounded-full md:rounded-2xl mx-auto bg-[var(--secondary-color)] cursor-pointer text-white py-4 hover:bg-[var(--primary-color)] transition font-semibold">
                            Sign In
                        </button>
                    </div>
                    <!-- Terms & Privacy Buttons -->
                    <div class="flex justify-center text-[14px] text-[#00000066] gap-x-[30px]">
                        <button onclick="termModal()" id="termsBtn" type="button" class="cursor-pointer">
                            Terms & Conditions
                        </button>
                        <button onclick="privacyModal()" id="privacyBtn" type="button" class="cursor-pointer">
                            Privacy Policy
                        </button>
                    </div>

                    <!-- Return to Main Page -->
                    <div class="flex justify-center pt-4 md:absolute md:top-0 md:left-5">
                            <svg width="25" height="25" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_9223_35351)">
                            <path d="M4.8479 15.0573C4.59794 15.3073 4.45752 15.6464 4.45752 15.9999C4.45752 16.3535 4.59794 16.6926 4.8479 16.9426L12.3906 24.4853C12.5136 24.6126 12.6607 24.7142 12.8234 24.7841C12.986 24.854 13.161 24.8907 13.338 24.8923C13.5151 24.8938 13.6906 24.8601 13.8545 24.793C14.0184 24.726 14.1672 24.627 14.2924 24.5018C14.4176 24.3766 14.5166 24.2278 14.5837 24.0639C14.6507 23.9 14.6844 23.7245 14.6829 23.5474C14.6814 23.3704 14.6446 23.1954 14.5747 23.0327C14.5048 22.8701 14.4032 22.7229 14.2759 22.5999L9.00923 17.3333H26.6666C27.0202 17.3333 27.3593 17.1928 27.6094 16.9428C27.8594 16.6927 27.9999 16.3536 27.9999 15.9999C27.9999 15.6463 27.8594 15.3072 27.6094 15.0571C27.3593 14.8071 27.0202 14.6666 26.6666 14.6666H9.00923L14.2759 9.39995C14.5188 9.14848 14.6532 8.81168 14.6501 8.46208C14.6471 8.11249 14.5069 7.77807 14.2597 7.53086C14.0124 7.28365 13.678 7.14342 13.3284 7.14038C12.9788 7.13734 12.642 7.27174 12.3906 7.51461L4.8479 15.0573Z" fill="#A98018"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_9223_35351">
                            <rect width="32" height="32" fill="white" transform="matrix(0 -1 1 0 0 32)"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <a class="font-[Manrope] font-normal text-[var(--primary-color)] underline" href="{{ route('landing') }}">Return to Main Page</a>
                    </div>
                </form>


                <!-- Report Button -->
                <button id="reportBtn"
                    class="absolute bottom-10 md:bottom-5 right-10 md:left-5 bg-transparent rounded-full w-6 h-6 md:w-8 md:h-8 shadow-none focus:outline-none z-50 flex items-center justify-center cursor-pointer"
                    title="Report a Problem">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-full h-full">
                        <path d="M7.757 2h8.486l5.757 5.757v8.486l-5.757 5.757H7.757L2 16.243V7.757L7.757 2z"
                            fill="transparent" stroke="black" stroke-width="2.5"/>
                        <rect x="11" y="8" width="2" height="4" fill="black" />
                        <rect x="11" y="14" width="2" height="2" fill="black" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
<!-- Terms & Conditions Modal -->
<div id="termsModal" class="fixed inset-0 bg-black/60 backdrop-blur-[3px] hidden items-center justify-center z-50">
    <div
        class="bg-white rounded-2xl p-4 sm:p-6 w-full max-w-xs sm:max-w-lg md:max-w-2xl max-h-[90vh] overflow-hidden relative flex flex-col">
        <!-- Header -->
        <div class="flex items-center mb-4 px-2 sm:px-4">
            <img src="{{ asset('images/terms.svg') }}" alt="Terms Icon" class="h-10 w-auto sm:h-12 mr-2 sm:mr-4">
            <h2 class="flex-1 font-bold text-lg sm:text-2xl text-[#7A1212]" style="font-family: 'Roboto', sans-serif;">
                Terms & Conditions
            </h2>
        </div>

        <!-- Scrollable Content -->
        <div class="text-xs sm:text-sm overflow-y-auto px-2 sm:px-4 pr-1 sm:pr-2 space-y-4 leading-relaxed text-[#6B6B6B]"
            style="font-family: 'Roboto Flex', sans-serif; max-height: 50vh; sm:max-height: 60vh;">

            <div>
                <h3 class="font-semibold">1. Acceptance of Terms</h3><br>
                <p>By accessing or using the E-SKOLARIAN Document Management System (the "System"), you agree to be
                    bound by these Terms and Conditions ("Terms"). If you do not agree with these Terms, you should
                    immediately cease using the System.</p>
            </div>

            <div>
                <h3 class="font-semibold">2. Use of the System</h3><br>
                <p>The E-SKOLARIAN Document Management System is intended for the secure management, storage, and
                    sharing of documents related to educational purposes. You are granted a limited, non-exclusive, and
                    non-transferable license to access and use the System in accordance with these Terms.</p>
            </div>

            <div>
                <h3 class="font-semibold">3. User Responsibilities</h3><br>
                <ul class="list-disc ml-6 space-y-1">
                    <li>You agree to provide accurate, current, and complete information during the registration
                        process.</li>
                    <li>You are responsible for maintaining the confidentiality of your account credentials.</li>
                    <li>You agree to use the System only for lawful purposes and will not engage in any activities that
                        could harm, disrupt, or interfere with the System's operation.</li>
                    <li>You are responsible for backing up your own documents stored in the System. The provider is not
                        responsible for data loss due to technical issues.</li>
                </ul>
            </div>

            <div>
                <h3 class="font-semibold">4. Prohibited Activities</h3><br>
                <ul class="list-disc ml-6 space-y-1">
                    <li>Uploading or sharing illegal, offensive, or harmful content.</li>
                    <li>Attempting to hack, modify, or gain unauthorized access to the System.</li>
                    <li>Engaging in activities that may damage or impair the functionality of the System.</li>
                </ul>
            </div>

            <div>
                <h3 class="font-semibold">5. Account Termination</h3><br>
                <p>We reserve the right to suspend or terminate your account if you violate these Terms. Upon
                    termination, you will no longer have access to your account, and any stored documents may be
                    deleted.</p>
            </div>

            <div>
                <h3 class="font-semibold">6. Limitation of Liability</h3><br>
                <p>The E-SKOLARIAN Document Management System is provided "as is" without any warranty of any kind. The
                    provider is not liable for any direct, indirect, incidental, or consequential damages arising from
                    the use or inability to use the System.</p>
            </div>

            <div>
                <h3 class="font-semibold">7. Changes to the Terms</h3><br>
                <p>We may update or modify these Terms at any time. Any changes will be effective immediately upon
                    posting to this page. You are encouraged to review these Terms periodically.</p>
            </div>

            <div>
                <h3 class="font-semibold">8. Governing Law</h3><br>
                <p>These Terms are governed by the laws of the Philippines, and any disputes shall be resolved in the
                    courts of [Insert Jurisdiction].</p>
            </div>
        </div>
        <div class="flex justify-end pt-6 sm:pt-10">
            <button
                class="px-3 sm:px-4 py-1 bg-red-900 text-white rounded-xl hover:bg-red-600 w-28 sm:w-40 cursor-pointer hover:text-white transition-colors duration-200 text-xs sm:text-base"
                onclick="document.getElementById('termsModal').classList.add('hidden')">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Privacy Policy Modal -->
<div id="privacyModal" class="fixed inset-0 bg-black/60 backdrop-blur-[3px] items-center justify-center z-50 hidden">
    <div
        class="bg-white rounded-2xl p-4 sm:p-6 w-full max-w-xs sm:max-w-lg md:max-w-2xl max-h-[90vh] overflow-hidden relative flex flex-col">
        <!-- Header -->
        <div class="flex items-center mb-4 px-2 sm:px-4">
            <img src="{{ asset('images/privacy.svg') }}" alt="Privacy Icon" class="h-10 w-auto sm:h-12 mr-2 sm:mr-4">
            <h2 class="flex-1 font-bold text-lg sm:text-2xl text-[#7A1212]" style="font-family: 'Roboto', sans-serif;">
                Privacy Policy
            </h2>
        </div>

        <!-- Scrollable Content -->
        <div class="text-xs sm:text-sm overflow-y-auto px-2 sm:px-4 pr-1 sm:pr-2 space-y-4 leading-relaxed text-[#6B6B6B]"
            style="font-family: 'Roboto Flex', sans-serif; max-height: 50vh; sm:max-height: 60vh;">

            <div>
                <p class="font-semibold">1. Information Collection</p><br>
                <p>We collect personal information when you register for an account or use certain features of the
                    E-SKOLARIAN Document Management System. This information may include your name, email address,
                    institution, and other information necessary to provide our services.</p>
            </div>
            <div>
                <p class="font-semibold">2. Use of Information</p><br>
                <p>The information we collect is used to:</p>
                <ul class="list-disc list-inside ml-4">
                    <li>Provide and improve the System’s features and functionality.</li>
                    <li>Communicate with you regarding updates, maintenance, or other important notices related to the
                        System.</li>
                    <li>Comply with legal obligations and enforce our Terms and Conditions.</li>
                </ul>
            </div>
            <div>
                <p class="font-semibold">3. Document Storage</p><br>
                <p>Documents you upload to the System are stored securely. We do not share your documents with third
                    parties unless required by law or as stated in this Privacy Policy.</p>
            </div>
            <div>
                <p class="font-semibold">4. Data Security</p><br>
                <p>We employ reasonable technical and organizational measures to protect your personal information from
                    unauthorized access, use, alteration, or disclosure. However, no security system is completely
                    foolproof, and we cannot guarantee the absolute security of your information.</p>
            </div>
            <div>
                <p class="font-semibold">5. Sharing of Information</p><br>
                <p>We do not sell, rent, or share your personal information with third parties except as described in
                    this Privacy Policy or as required by law.</p>
            </div>
            <div>
                <p class="font-semibold">6. Cookies and Tracking Technologies</p><br>
                <p>The System may use cookies and similar technologies to enhance your user experience. These
                    technologies help us analyze usage patterns and improve the functionality of the System. You can
                    control cookies through your browser settings.</p>
            </div>
            <div>
                <p class="font-semibold">7. Retention of Data</p><br>
                <p>We retain personal information and documents for as long as necessary to fulfill the purposes
                    outlined in this Privacy Policy, unless a longer retention period is required by law.</p>
            </div>
            <div>
                <p class="font-semibold">8. Your Rights</p><br>
                <p>You have the right to:</p>
                <ul class="list-disc list-inside ml-4">
                    <li>Access the personal information we hold about you.</li>
                    <li>Correct or update your personal information.</li>
                    <li>Request the deletion of your personal information, subject to certain legal restrictions.</li>
                </ul>
                <p>To exercise your rights, please contact us at [Insert Contact Information].</p>
            </div>
            <div>
                <p class="font-semibold">9. Changes to the Privacy Policy</p><br>
                <p>We may update this Privacy Policy from time to time. Any changes will be posted on this page, and the
                    "Effective Date" will be updated accordingly. We encourage you to review this Privacy Policy
                    periodically.</p>
            </div>
            <div>
                <p class="font-semibold">10. Contact Information</p><br>
                <p>If you have any questions about these Terms and Conditions or our Privacy Policy, please contact us
                    at:</p>
                <ul class="list-none ml-4">
                    <li><span class="font-semibold">Email:</span>
                        {{ \App\Models\User::where('role', 'super_admin')->first()?->email ?? '[Not Available]' }}
                    </li>
                    <li><span class="font-semibold">Address:</span> Polytechnic University of the Philippines Santa Rosa
                        City</li>
                    <li>Arambulo St, Barangay Kanluran, Santa Rosa, 4026 Laguna, Sta. Rosa</li>
                </ul>
            </div>
        </div>
        <div class="flex justify-end pt-6 sm:pt-10">
            <button
                class="px-3 sm:px-4 py-1 bg-red-900 text-white rounded-xl hover:bg-red-600 w-28 sm:w-40 cursor-pointer hover:text-white transition-colors duration-200 text-xs sm:text-base"
                onclick="document.getElementById('privacyModal').classList.add('hidden')">
                Close
            </button>
        </div>
    </div>
</div>



   <!-- Report Problem Modal -->
    <div id="reportModal" class="fixed px-[10px] inset-0 flex items-center z-50 backdrop-blur-sm w-full text-black hidden">
        <div class="bg-[#ffffffe8] p-6 shadow-lg mx-auto rounded-3xl max-w-[600px]">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-20 h-20 mx-auto pb-4">
                <path d="M7.757 2h8.486l5.757 5.757v8.486l-5.757 5.757H7.757L2 16.243V7.757L7.757 2z"
                    fill="transparent" stroke="black" stroke-width="2.5"/>
                <rect x="11" y="8" width="2" height="4" fill="black" />
                <rect x="11" y="14" width="2" height="2" fill="black" />
            </svg>
            <div class="mb-4 text-center mx-auto w-[90%] md:w-[75%]">
            <h1 class="text-3xl font-[Lexend] font-bold mb-2 text-[var(--secondary-color)]">Report a Problem</h1>
            <p class="text-black font-[Manrope] font-normal text-xs">
                Noticed something wrong or not working as expected? Tell us what issue you encountered so we can look into it and improve your experience.
            </p>
            </div>

            <form id="reportForm" class="mx-auto w-[90%] md:w-[75%] text-xs" method="POST" action="{{ route('report.problem.store') }}" enctype="multipart/form-data" onsubmit="submitReport(event)">
            @csrf
            <div class="mb-7">
                <label class="pl-[9px] block font-semibold mb-1">PUP Webmail<span class="text-red-600">*</span></label>
                <input id="webmailInput" type="email" name="email" placeholder="PUP Email Address" class="w-full border bg-white rounded-lg px-3 py-2" required maxlength="51" />
                <p id="webmailLengthWarning" class="text-red-600 mt-1 hidden">*Webmail must not exceed 50 characters.</p>
            </div>

            <div class="mb-3 relative">
            <label class="pl-[9px] block font-semibold mb-1">Problem Description<span class="text-red-600">*</span></label>
            <textarea
                id="descriptionInput"
                name="description"
                placeholder="Describe the problem here..."
                class="w-full border rounded-lg px-3 py-2 bg-white resize-none"
                rows="4"
                maxlength="255"
                required
            ></textarea>

            <span
                id="descCounter"
                class="absolute bottom-2 right-3 text-xs text-gray-500 select-none pointer-events-none"
            >0 / 255</span>
            <p id="descLengthWarning" class="text-red-600 mt-1 hidden">*Description must be 255 characters or less.</p>
            </div>

            <div class="mb-7">
                <label class="pl-[9px] block font-semibold mb-1">Attach a File (optional)</label>
                <input id="fileInput" type="file" name="screenshot" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg"
                    class="text-slate-500 w-full max-w-[300px] text-sm rounded-lg leading-6 file:bg-[var(--secondary-color)] file:text-white file:border-none file:px-4 file:py-1 file:mr-6 file:rounded-lg hover:file:brightness-75 border bg-[#0000000c] cursor-pointer border-gray-300 transition duration-200">
                <p class="pl-[5px] text-[8px]">Choose a file up to 5MB. Valid file types: PDF, DOCX, DOC, PNG, JPG</p>
            </div>

            <div class="text-[16px]">
                <button type="submit" id="reportSubmitBtn" class="block w-full bg-[var(--secondary-color)] text-white px-4 py-2 mb-4 rounded-full hover:brightness-75">Submit</button>
                <button type="button" id="cancelReportBtn" class="block w-full bg-gray-300 text-gray-700 px-4 py-2 rounded-full hover:bg-gray-400">Cancel</button>
            </div>
            </form>
        </div>
        </div>

<!-- Confirmation Modal -->
<div id="confirmCloseModal" class="hidden font-[Manrope] fixed inset-0 bg-transparent flex items-center justify-center z-60 backdrop-blur-sm">
    <div class="bg-white/90 font-[Manrope] rounded-4xl shadow-lg py-6 px-4 max-w-lg w-full text-center">
        <div class="w-[80%] mx-auto">
            <h1 class="text-xl mb-4 text-[var(--secondary-color)]">Are you sure you want to cancel?</h1>
            <p class="mb-6 font-normal text-black">You have unsaved changes. Are you sure you want to close?</p>
            <div class="flex justify-around gap-5">
                <button id="confirmCloseYes" class="bg-[var(--secondary-color)] text-white px-4 py-2 w-full max-w-md rounded-full hover:brightness-75">Yes</button>
                <button id="confirmCloseNo" class="bg-[#D9D9D9CC] text-black px-4 py-2 rounded-full w-full max-w-md hover:bg-gray-400">Go Back</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="hidden font-[Manrope] fixed inset-0 bg-transparent flex items-center justify-center z-60 backdrop-blur-sm">
    <div class="bg-white/90 font-[Manrope] rounded-4xl shadow-lg py-6 px-4 max-w-lg w-full text-center">
        <div class="w-[80%] mx-auto">
            <h1 class="text-[20px] font-[Lexend] mb-3 text-[var(--secondary-color)]">✅ Report Submitted Successfully!</h1>
            <div class="text-center text-md">
                <p class="mb-3 font-normal text-black">Thank you for your feedback.</p>
                <p class="mb-6 font-normal text-black">Our team will review your report and get back to you if necessary. You may now close this window or return to the previous page.</p>
            </div>
            <div class="flex justify-center">
                <button onclick="closeSuccessModal()" class="bg-[var(--secondary-color)] text-white px-6 py-2 w-full max-w-md rounded-full hover:brightness-75 transition-colors duration-200">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="hidden font-[Manrope] fixed inset-0 bg-transparent flex items-center justify-center z-60 backdrop-blur-sm">
    <div class="bg-white/90 font-[Manrope] rounded-4xl shadow-lg py-6 px-4 max-w-lg w-full text-center">
        <div class="w-[80%] mx-auto">
            <h1 class="text-[20px] font-[Lexend] mb-3 text-[var(--secondary-color)]">❌ Report Submitted Failed!</h1>
            <div class="text-center text-md">
                <p class="mb-3 font-normal text-black">Sorry for the inconvenience.</p>
                <p class="mb-6 font-normal text-black">Please try to submit again or provide a valid format.</p>
            </div>
            <div class="flex justify-center">
                <button onclick="closeErrorModal()" class="bg-[var(--secondary-color)] text-white px-6 py-2 w-full max-w-md rounded-full hover:brightness-75 transition-colors duration-200">Close</button>
            </div>
        </div>
    </div>
</div>



    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const hasFormErrors = {!! json_encode($errors->any()) !!};

        const emailInput = document.getElementById('emailInput');
        const passwordInput = document.getElementById('password');

        const emailLabel = document.getElementById('emailLabel');
        const emailWarning = document.getElementById('emailLengthWarning');

        // Add this for custom email format warning
        let emailFormatWarning = document.getElementById('emailFormatWarning');
        if (!emailFormatWarning) {
            emailFormatWarning = document.createElement('div');
            emailFormatWarning.id = 'emailFormatWarning';
            emailFormatWarning.className = 'text-red-600 text-sm mt-0.5 pl-[10px] font-[Lexend] font-normal hidden';
            emailInput.parentNode.parentNode.appendChild(emailFormatWarning);
        }

        const passwordLabel = document.getElementById('passwordLabel');
        const passwordWarning = document.getElementById('passwordLengthWarning');

        const signInButton = document.getElementById('signInButton');
        const form = emailInput.closest('form');

        document.querySelectorAll("input").forEach((field) => {
            field.addEventListener("dragover", (e) => e.preventDefault());
            field.addEventListener("drop", (e) => e.preventDefault());
        });

        let serverErrorEmail = hasFormErrors;
        let serverErrorPassword = hasFormErrors;

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,10}$/.test(email);
        }

        function validateInputs() {
            const email = emailInput.value.trim();
            const password = passwordInput.value.trim();

            const isEmailTooLong = email.length > 50;
            const isPasswordTooLong = password.length > 50;

            const isEmailValid = email.length > 0 && !isEmailTooLong && isValidEmail(email);
            const isPasswordValid = password.length > 0 && !isPasswordTooLong;

            // Email length warning
            if (email.length > 0 && isEmailTooLong) {
                emailLabel.classList.add('ring-3', '!ring-red-600');
                emailWarning.classList.remove('hidden');
            } else {
                emailWarning.classList.add('hidden');
            }

            // Email format warning
            if (email.length > 0 && !isEmailTooLong && !isValidEmail(email)) {
                emailLabel.classList.add('ring-3', '!ring-red-600');
                emailFormatWarning.textContent = 'Invalid email format. Please check your email address.';
                emailFormatWarning.classList.remove('hidden');
            } else {
                emailFormatWarning.classList.add('hidden');
            }

            if (!serverErrorEmail) {
                if (email.length > 0 && isEmailTooLong) {
                    emailLabel.classList.add('ring-3', '!ring-red-600');
                    emailWarning.classList.remove('hidden');
                } else {
                    emailLabel.classList.remove('ring-3', '!ring-red-600');
                    emailWarning.classList.add('hidden');
                }
            }

            if (!serverErrorPassword) {
                if (password.length > 0 && isPasswordTooLong) {
                    passwordLabel.classList.add('ring-3', '!ring-red-600');
                    passwordWarning.classList.remove('hidden');
                } else {
                    passwordLabel.classList.remove('ring-3', '!ring-red-600');
                    passwordWarning.classList.add('hidden');
                }
            }

            // Only enable Sign In button if both fields are filled and valid
            const shouldEnableButton = isEmailValid && isPasswordValid;
            signInButton.disabled = !shouldEnableButton;
            signInButton.classList.toggle('opacity-50', !shouldEnableButton);
            signInButton.classList.toggle('cursor-not-allowed', !shouldEnableButton);
        }

        // Prevent spaces in email
        emailInput.addEventListener('keydown', function (e) {
            if (e.key === ' ') e.preventDefault();
        });

        emailInput.addEventListener('paste', function (e) {
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            if (/\s/.test(pastedText)) {
                e.preventDefault();
                alert('Spaces are not allowed in the email address.');
            }
        });

        // Input event handlers
        emailInput.addEventListener('input', function () {
            if (/\s/.test(emailInput.value)) {
                emailInput.value = emailInput.value.replace(/\s/g, '');
            }

            if (serverErrorEmail) {
                emailLabel.classList.remove('ring-3', '!ring-red-600');
                serverErrorEmail = false;
            }

            validateInputs();
        });

        emailInput.addEventListener('focus', function () {
            if (serverErrorEmail) {
                emailLabel.classList.remove('ring-3', '!ring-red-600');
                serverErrorEmail = false;
            }
        });

        passwordInput.addEventListener('input', function () {
            if (serverErrorPassword) {
                passwordLabel.classList.remove('ring-3', '!ring-red-600');
                serverErrorPassword = false;
            }

            validateInputs();
        });

        form.addEventListener('submit', function (e) {
            const email = emailInput.value.trim();
            if (email.length > 50 || passwordInput.value.length > 50) {
                e.preventDefault();
                alert('Email or password exceeds the allowed length.');
                return;
            }
            if (!isValidEmail(email)) {
                e.preventDefault();
                emailLabel.classList.add('ring-3', '!ring-red-600');
                emailFormatWarning.textContent = 'Invalid email format. Please check your email address.';
                emailFormatWarning.classList.remove('hidden');
                return;
            }
            // Disable button to prevent multiple submissions
            signInButton.disabled = true;
            signInButton.innerText = 'Signing in...'; // Optional: change button text

            // Show loader
            document.getElementById('loader').classList.toggle('hidden');
            document.getElementById('loader').classList.toggle('flex');
        });

        passwordInput.addEventListener('focus', function () {
            if (serverErrorPassword) {
                passwordLabel.classList.remove('ring-3', '!ring-red-600');
                serverErrorPassword = false;
            }
        });

        // Initial server-side red rings
        if (hasFormErrors) {
            emailLabel.classList.add('ring-3', '!ring-red-600');
            passwordLabel.classList.add('ring-3', '!ring-red-600');
        }

        // Initial validation on page load
        validateInputs();
    });
    </script>
    <script>
function formatTime(seconds) {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${m}:${s.toString().padStart(2, '0')}`;
}

function removeLockoutMsg(labelId) {
    const lockoutMessage = document.getElementById('lockout-message');
    const label = document.getElementById(labelId);
    if (lockoutMessage && label) {
        const parent = lockoutMessage.closest('div');
        if (parent) {
            parent.classList.add('opacity-0', 'transition-opacity');
            setTimeout(() => parent.remove(), 500);
        }
        label.classList.remove('ring-3', '!ring-red-600');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const emailInput = document.getElementById('emailInput');
    const passwordInput = document.getElementById('password');
    const emailLabel = document.getElementById('emailLabel');
    const passwordLabel = document.getElementById('passwordLabel');
    const lockoutMessage = document.getElementById('lockout-message');
    const lockoutMessageContainer = document.getElementById('lockout-message-container');
    const lockoutTimer = document.getElementById('lockout-timer');

    const formInputs = Array.from(document.querySelectorAll('input, button[type="submit"]'))
        .filter(input => !(input.name === '_token' || input.id === 'role'));

    const now = Math.floor(Date.now() / 1000);

    // Always check localStorage for lockoutEnd
    let storedEnd = parseInt(localStorage.getItem('lockoutEnd_student')) || 0;
    let backendLockoutTime = parseInt({{ $errors->first('lockout_time') ?? '0' }});
    let lockoutEnd = 0;
    let lockoutTime = 0;

    if (storedEnd > now) {
        // Use lockout from storage
        lockoutEnd = storedEnd;
        lockoutTime = lockoutEnd - now;
    } else if (backendLockoutTime > 0) {
        // Use backend lockout if present
        lockoutEnd = now + backendLockoutTime;
        lockoutTime = backendLockoutTime;
        localStorage.setItem('lockoutEnd_student', lockoutEnd);
    } else {
        // No lockout, remove any previous lockoutEnd and hide message
        localStorage.removeItem('lockoutEnd_student');
        if (lockoutMessageContainer) lockoutMessageContainer.style.display = 'none';
        return;
    }

    // Show lockout message and timer
    if (lockoutMessageContainer && lockoutTimer) {
        lockoutMessageContainer.style.display = '';
        lockoutTimer.innerText = formatTime(lockoutTime);
    }

    // Disable form inputs
    formInputs.forEach(input => input.disabled = true);

    const timerInterval = setInterval(() => {
        const current = Math.floor(Date.now() / 1000);
        const remaining = lockoutEnd - current;

        if (remaining <= 0) {
            clearInterval(timerInterval);
            if (lockoutMessage) lockoutMessage.innerText = "You can now try logging in again.";
            formInputs.forEach(input => input.disabled = false);
            localStorage.removeItem('lockoutEnd_student');

            if (emailInput) {
                emailInput.addEventListener('focus', () => {
                    removeLockoutMsg('emailLabel');
                }, { once: true });
            }

            if (passwordInput) {
                passwordInput.addEventListener('focus', () => {
                    removeLockoutMsg('passwordLabel');
                }, { once: true });
            }
        } else {
            if (lockoutTimer) lockoutTimer.innerText = formatTime(remaining);
        }
    }, 1000);
});

document.addEventListener('DOMContentLoaded', function () {
  const reportBtn = document.getElementById('reportBtn');
  const reportModal = document.getElementById('reportModal');
  const reportForm = document.getElementById('reportForm');
  const cancelReportBtn = document.getElementById('cancelReportBtn');
  const submitBtn = document.getElementById('reportSubmitBtn');

  let isDirty = false;

  // Disable submit button initially with styles
  function disableSubmit() {
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
  }

  // Enable submit button with styles removed
  function enableSubmit() {
    submitBtn.disabled = false;
    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
  }

  // Validate inputs and enable/disable submit button accordingly
  function validateInputs() {
  const email = reportForm.email.value.trim();
  const desc = reportForm.description.value.trim();

  const maxEmailLength = 50;
  const maxDescLength = 255;

  const isEmailTooLong = email.length > maxEmailLength;
  const isDescTooLong = desc.length > maxDescLength;

  const isEmailValid = email.length > 0 && !isEmailTooLong;
  const isDescValid = desc.length > 0 && !isDescTooLong;

  // Show warnings only if too long
  document.getElementById('webmailLengthWarning').classList.toggle('hidden', !isEmailTooLong);
  document.getElementById('descLengthWarning').classList.toggle('hidden', !isDescTooLong);

  const shouldEnableSubmit = isEmailValid && isDescValid;

    if (shouldEnableSubmit) {
        enableSubmit();
    } else {
        disableSubmit();
    }
    }

    const webmailInput = document.getElementById('webmailInput');

     // Prevent spaces in email
    webmailInput.addEventListener('keydown', function (e) {
        if (e.key === ' ') e.preventDefault();
        });

    webmailInput.addEventListener('paste', function (e) {
        const pastedText = (e.clipboardData || window.clipboardData).getData('text');
        if (/\s/.test(pastedText)) {
            e.preventDefault();
            alert('Spaces are not allowed in the email address.');
        }
    });
    // Update description counter
    const descriptionInput = document.getElementById('descriptionInput');
    const descCounter = document.getElementById('descCounter');
    const descWarning = document.getElementById('descLengthWarning');

    descriptionInput.addEventListener('input', () => {
        const currentLength = descriptionInput.value.length;
        descCounter.textContent = `${currentLength} / 255`;

        if (currentLength > 255) {
            descWarning.classList.remove('hidden');
        } else {
            descWarning.classList.add('hidden');
        }
    });

    // Initialize counter on load if there's pre-filled text
    descCounter.textContent = `${descriptionInput.value.length} / 255`;


  // On modal open: reset form, reset flags, disable submit and validate inputs
  reportBtn.addEventListener('click', () => {
    reportModal.classList.remove('hidden');
    reportForm.reset();
    isDirty = false;
    disableSubmit();
    validateInputs();
  });

  // Listen for input on required fields
  reportForm.email.addEventListener('input', () => {
    isDirty = true;
    validateInputs();
  });

  reportForm.description.addEventListener('input', () => {
    isDirty = true;
    validateInputs();
  });

  // Cancel button logic with confirmation if dirty
  cancelReportBtn.addEventListener('click', () => {
    if (isDirty) {
      document.getElementById('confirmCloseModal').classList.remove('hidden');
    } else {
      closeReportModal();
    }
  });

  // Confirmation modal buttons
  document.getElementById('confirmCloseYes').addEventListener('click', () => {
    closeReportModal();
    document.getElementById('confirmCloseModal').classList.add('hidden');
    resetFormState();
  });

  document.getElementById('confirmCloseNo').addEventListener('click', () => {
    document.getElementById('confirmCloseModal').classList.add('hidden');
  });

  function closeReportModal() {
    reportModal.classList.add('hidden');
  }

  function resetFormState() {
    reportForm.reset();
    isDirty = false;
    disableSubmit();
  }

  // Submit report function with fetch
  window.submitReport = function(event) {
    event.preventDefault();

    disableSubmit();
    submitBtn.innerText = 'Submitting...';

    const formData = new FormData(reportForm);

    fetch(reportForm.action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: formData
    })
    .then(response => {
      const contentType = response.headers.get("content-type") || "";
      if (contentType.includes("application/json")) {
        return response.json();
      } else {
        return response.text();
      }
    })
    .then(data => {
      showSuccessModal();
      closeReportModal();
      resetFormState();
    })
    .catch(error => {
      showErrorModal();
      console.error('Error:', error);
    })
    .finally(() => {
      submitBtn.disabled = false;
      submitBtn.innerText = 'Submit';
      validateInputs();
    });
  };

  // Initial state
  disableSubmit();
});

const fileInput = document.getElementById('fileInput');

fileInput.addEventListener('change', function () {
    if (fileInput.files.length > 0) {
    fileInput.classList.remove('bg-gray-300');
    fileInput.classList.add('bg-white');
    } else {
    fileInput.classList.remove('bg-white');
    fileInput.classList.add('bg-gray-300');
    }
});

function termModal() {
    document.getElementById('termsModal').classList.remove('hidden');
    document.getElementById('termsModal').classList.add('flex');
}

function privacyModal() {
    document.getElementById('privacyModal').classList.remove('hidden');
    document.getElementById('privacyModal').classList.add('flex');
}

function showSuccessModal() {
    document.getElementById('successModal').classList.remove('hidden');
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
}

function showErrorModal() {
    document.getElementById('errorModal').classList.remove('hidden');
}

function closeErrorModal() {
    document.getElementById('errorModal').classList.add('hidden');
}

    let isSafeExit = false;
    document.getElementById('forgotPasswordLink').addEventListener('click', function () {
    isSafeExit = true; // Prevent beforeunload
    });
    // Trigger beforeunload only if NOT a safe exit
    window.addEventListener('beforeunload', function (e) {
        if (!isSafeExit) {
            e.preventDefault();
            e.returnValue = ''; // Needed for Chrome/Edge
        }
    });

    // Also make sure form submission sets safe exit
    const form = document.querySelector('form');
    form.addEventListener('submit', function () {
        isSafeExit = true;
    });

    // Hide loader on bfcache restore
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            const loader = document.getElementById('loader');
            if (loader) {
                loader.style.display = 'none';
            }
        }
    });

</script>
</body>
</html>
