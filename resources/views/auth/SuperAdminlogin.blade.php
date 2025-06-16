<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="admin">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        (function () {
          // Pre-set form role value if needed
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('formContainer');
                if (form) form.classList.remove('opacity-0');
            });
        })();


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
    </script>
</head>
<body id="box" class="min-h-screen flex flex-col justify-center bg-gradient-to-r from-[var(--login-color-left)] to-[var(--login-color-right)] font-['Manrope'] font-bold">
    @include('loading')

    <div id="formWrapper" class="max-md:p-[20px] max-md:max-w-md max-md:mx-auto">
        <div id="formContainer" class="opacity-0 flex flex-row md:w-[85%] md:h-[630px] mx-auto px-6 p-4 md:backdrop-blur-xs md:transition-all md:duration-1000">
            <div class="flex flex-col justify-center items-center basis-[45%] bg-[#F5D4D4A6] md:rounded-l-[100px] p-5 max-md:hidden">
                <img class="w-[320px]" src=" {{ asset('images/eskolarianStar.svg') }} " alt="Eskolarian Star Logo">
                <img class="w-[320px]" src="{{ asset('images/EskolarianBlack.svg') }}" alt="Eskolarian White Text">
            </div>
            <div class="md:basis-[55%] flex flex-col justify-center items-center max-md:mx-auto bg-[#FFFFFFA6] md:rounded-r-[100px] p-5 max-md:rounded-[40px]">
                <div class="h-35 flex justify-center gap-x-4 items-center ml-[-20px]">
                    <h1 class="text-[#7A1212] font-[Lexend] text-center text-4xl">
                        <img class="inline-block h-14 pb-1" src="{{ asset('images/adminIcon.svg') }}" alt="">
                        SUPER ADMIN LOGIN
                    </h1>
                </div>
                <div class="w-full max-w-[410px] mx-auto pt-14 md:pt-5">
                    <form method="POST" action="{{ route('superadmin.login') }}" class="space-y-4 md:space-y-2">
                        @csrf
                        <input type="hidden" name="role" id="role" value="super admin">
                        <!-- Email -->
                        <div class="relative pb-8">
                            <label id="emailLabel" class="w-full rounded-2xl px-3 py-2 md:p-4 ring bg-white flex focus-within:ring-3 focus-within:ring-[var(--secondary-color)]">
                                <input type="email" id="emailInput" name="email" placeholder="Email Address" required
                                    class="w-0 flex-grow outline-none mr-3" maxlength="100">
                                <button type="button" class="focus:outline-none" tabindex="-1">
                                    <img src="{{ asset('images/email.svg') }}" alt="Email Icon" class="w-5 md:w-6" />
                                </button>
                            </label>
                            <div id="emailLengthWarning" class="text-red-600 text-sm mt-0.5 pl-[10px] font-[Lexend] font-normal hidden absolute">
                                <p>*Email must not exceed 50 characters.</p>
                            </div>
                            <div id="emailFormatWarning" class="text-red-600 text-sm mt-0.5 pl-[10px] font-[Lexend] font-normal hidden absolute">
                                <p>*Invalid email format. Please check your email address.</p>
                            </div>
                        </div>
                        <!-- Password -->
                        <div class="relative">
                            <label id="passwordLabel" class="w-full rounded-2xl px-3 py-2 md:p-4 bg-white flex ring focus-within:ring-3 focus-within:ring-[var(--secondary-color)]">
                                <input id="password" type="password" name="password" placeholder="Password" required
                                    class="w-0 flex-grow outline-none mr-3">
                                <button type="button" onclick="togglePassword(event)" class="cursor-pointer">
                                    <img id="showPass" src="{{ asset('images/show_pass.svg') }}" alt="Show Password" class="w-5 md:w-6" />
                                    <img id="hidePass" src="{{ asset('images/hide_pass.svg') }}" alt="Hide Password" class="w-5 md:w-6 hidden" />
                                </button>
                            </label>
                            <div id="passwordLengthWarning" class="text-red-600 text-sm mt-0.5 pl-[10px] font-[Lexend] font-normal hidden absolute">
                                <p>*Password must not exceed 50 characters.</p>
                            </div>
                        </div>
                        <!-- Error Message -->
                        @if ($errors->any() && !$errors->has('lockout_time'))
                        <div class="status-message text-red-600 text-sm mt-1 font-[Lexend] font-normal">
                            <p>{{ $errors->first() }} </p>
                        </div>
                        @endif
                        <!-- Lockout Message -->
                        <div id="lockout-message-container" class="text-red-600 text-sm mt-1 font-[Lexend] font-normal" style="display:none;">
                            <p id="lockout-message">
                                Too many login attempts. Please try again in <span id="lockout-timer"></span>.
                            </p>
                        </div>

                        <div class="flex justify-end items-center pt-1">
                        <a href="{{ route('superadmin.password.request') }}" class="inline-block font-normal text-[14px] active:text-[var(--secondary-color)] transition-all duration-75">Forgot Password?</a>
                    </div>
                        <!-- Submit -->
                        <div class="pt-8 flex justify-center">
                            <button type="submit" id="signInButton"
                                class="opacity-50 w-full rounded-2xl mx-auto bg-[var(--secondary-color)] cursor-pointer text-white py-2 md:py-4  hover:bg-[var(--primary-color)] transition font-semibold">
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
                    </form>
                </div>
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


    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const hasFormErrors = {!! json_encode($errors->any()) !!};

        const emailInput = document.getElementById('emailInput');
        const passwordInput = document.getElementById('password');

        const emailLabel = document.getElementById('emailLabel');
        const emailWarning = document.getElementById('emailLengthWarning');

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

        let emailFormatWarning = document.getElementById('emailFormatWarning');
        if (!emailFormatWarning) {
            emailFormatWarning = document.createElement('div');
            emailFormatWarning.id = 'emailFormatWarning';
            emailFormatWarning.className = 'text-red-600 text-sm mt-0.5 pl-[10px] font-[Lexend] font-normal hidden absolute';
            emailInput.parentNode.parentNode.appendChild(emailFormatWarning);
        }

        function isValidEmail(email) {
            // Only allow TLDs with 2-10 letters (no numbers)
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

        // Prevent spaces in password
        passwordInput.addEventListener('keydown', function (e) {
            if (e.key === ' ') e.preventDefault();
        });

        passwordInput.addEventListener('paste', function (e) {
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            if (/\s/.test(pastedText)) {
                e.preventDefault();
                alert('Spaces are not allowed in the password.');
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
    let storedEnd = parseInt(localStorage.getItem('lockoutEnd_superadmin')) || 0;
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
        localStorage.setItem('lockoutEnd_superadmin', lockoutEnd);
    } else {
        // No lockout, remove any previous lockoutEnd and hide message
        localStorage.removeItem('lockoutEnd_superadmin');
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
            localStorage.removeItem('lockoutEnd_superadmin');

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


function termModal() {
    document.getElementById('termsModal').classList.remove('hidden');
    document.getElementById('termsModal').classList.add('flex');
}

function privacyModal() {
    document.getElementById('privacyModal').classList.remove('hidden');
    document.getElementById('privacyModal').classList.add('flex');
}

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
</body>
</html>
