<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="icon" href="{{ asset('images/officialLogo.svg') }}" type="image/svg+xml">

    <title>E-skolarian</title>

    @vite('resources/css/app.css')

    <script>
        /* Set Role Type to be shown onload */
        window.onload = function () {
            const chosenType = localStorage.getItem('activeLoginRole') || 'student';

            saveInputs(chosenType);
            setRole(chosenType);
            visibilityRememberMe(chosenType);
            changeRadiusPanel(chosenType);

            slidePanel(chosenType, false);
        };

        /* Temporary fix for translate */
        window.addEventListener('resize', function () {
            changeRole('student');
        });

        function changeRole(role) {
            saveInputs(role);
            setRole(role);
            visibilityRememberMe(role);
            changeRadiusPanel(role);
            slidePanel(role, true);
        }

       let emailInputs = {
            student: '',
            admin: ''
        };

        function saveInputs(role) {
            const emailInput = document.getElementById('emailInput');
            const passwordInput = document.getElementById('password');

            // Check if the emailInputs object exists and initialize if not
            if (!emailInputs[role]) {
                emailInputs[role] = '';
            }

            // Save the current role's email before switching
            const otherRole = role === 'admin' ? 'student' : 'admin';
            emailInputs[otherRole] = emailInput.value;

            // Restore email for selected role
            emailInput.value = emailInputs[role];

            // Always clear password on switch
            passwordInput.value = '';

            localStorage.setItem('activeLoginRole', role);
        }


        function setRole(role) {
            // Set Color Theme based on Role Type
            document.documentElement.setAttribute('data-theme', role);

             // Update Forgot Password Links with Role
             document.querySelectorAll('.forgot-password-link').forEach(link => {
                link.href = `/forgot-password?role=${role}`;  // Update the href with the new URL
            });

            // Set role value
            const roleInput = document.getElementById('role');
            if (roleInput) {
                roleInput.value = role; // Set role to either 'admin' or 'student'
            }

            // Toggle button styles
            const studentBtn = document.getElementById('studentBtn');
            const studentIcon = document.getElementById('studentIcon');

            const adminBtn = document.getElementById('adminBtn');
            const adminIcon = document.getElementById('adminIcon');

            if (studentBtn && adminBtn) {
                studentBtn.classList.remove('bg-[var(--primary-color)]', 'text-white');
                studentIcon.classList.remove('invert');

                adminBtn.classList.remove('bg-[var(--primary-color)]', 'text-white');
                adminIcon.classList.remove('invert');

                const roleButton = document.getElementById(role + 'Btn');
                const roleIcon = document.querySelector(`#${role}Btn div`);

                if (roleButton) {
                    if (role === 'student') roleButton.classList.add('bg-[var(--primary-color)]', 'text-white');
                    else if (role === 'admin') roleButton.classList.add('bg-[var(--primary-color)]', 'text-white');
                    roleIcon.classList.add('invert');
                }
            }
        }


        function visibilityRememberMe(role) {
            const formContainer = document.getElementById('formContainer');
            const rememberMeContainer = document.getElementById('rememberMeContainer');
            if (rememberMeContainer) {
                if (role === 'student') {
                    rememberMeContainer.classList.remove('invisible');
                } else {
                    rememberMeContainer.classList.add('invisible');
                }
            }
        }

        function changeRadiusPanel(role) {
            const panel = document.getElementById('formContainer');
            if (!panel) return;

            // Remove both variations
            panel.classList.remove(
                'md:rounded-tl-none', 'md:rounded-bl-none',
                'md:rounded-tr-[100px]', 'md:rounded-br-[100px]',
                'md:rounded-tr-none', 'md:rounded-br-none',
                'md:rounded-tl-[100px]', 'md:rounded-bl-[100px]'
            );

            // Apply based on role
            if (role === 'student') {
                panel.classList.add(
                    'md:rounded-tl-none', 'md:rounded-bl-none',
                    'md:rounded-tr-[100px]', 'md:rounded-br-[100px]'
                );
            } else if (role === 'admin') {
                panel.classList.add(
                    'md:rounded-tr-none', 'md:rounded-br-none',
                    'md:rounded-tl-[100px]', 'md:rounded-bl-[100px]'
                );
            }
        }

        /* Slide to the left/right with animation */
        function slidePanel(role, isSlide) {
            const panel = document.getElementById('formContainer');
            if (!panel) return;

            !isSlide ? panel.classList.remove('md:transition-all', 'md:duration-1000') : panel.classList.add('md:transition-all', 'md:duration-1000');

            const panelWidth = panel.offsetWidth;
            const windowWidth = window.innerWidth;

            // Apply the transform for animation
            if (role === 'admin' && windowWidth >= 768) {
                panel.style.transform = `translateX(${windowWidth - panelWidth}px)`;
            } else if (role === 'student' && windowWidth >= 768) {
                panel.style.transform = `translateX(0)`;
            }
        }


        /* To carousel set of images */
        const images = [
            "{{ asset('images/PUP_Bg1.jpg') }}",
            "{{ asset('images/PUP_Bg2.jpg') }}",
            "{{ asset('images/PUP_Bg3.jpg') }}",
            "{{ asset('images/PUP_Bg4.jpg') }}",
            "{{ asset('images/PUP_Bg5.jpg') }}",
            "{{ asset('images/PUP_Bg6.jpg') }}"
        ];

        // Pick a random starting index
        let currentIndex = Math.floor(Math.random() * images.length);

        window.addEventListener('load', startBackgroundImageCycle);
        window.addEventListener('resize', setBackgroundImage);

        function setBackgroundImage() {
            const box = document.getElementById('box');
            if (!box) return;

            if (window.innerWidth >= 768) {
                box.style.backgroundImage = `linear-gradient(var(--login-bg-color), var(--login-bg-color)), url(${images[currentIndex]})`;
                box.style.backgroundRepeat = 'no-repeat';
                box.style.backgroundSize = 'cover';
                box.style.backgroundPosition = 'bottom';
            } else {
                box.style.backgroundImage = '';
            }
        }

        function startBackgroundImageCycle() {
            setBackgroundImage();
            setInterval(() => {
                currentIndex = (currentIndex + 1) % images.length;
                setBackgroundImage();
            }, 10000);
        }


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
                setTimeout(function () {
                    message.classList.add('opacity-0');
                    message.classList.add('transition-opacity');


                    setTimeout(function () {
                        message.remove();
                    }, 500);
                }, 3000);
            });
        });

    </script>
</head>
<body id="box" class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-r from-[var(--login-color-left)] to-[var(--login-color-right)] transition-all duration-500 md:bg-none font-['Manrope'] font-bold md:relative">
    <div id="formWrapper" class="w-full h-full max-md:p-[20px] max-md:max-w-md ">
        <div id="formContainer" class="flex flex-col items-center justify-center md:w-[50%] md:max-w-[600px] md:absolute md:left-0 md:top-0 md:bottom-0 px-6 bg-[#D9D9D9]/70 p-4 rounded-3xl md:rounded-tl-none md:rounded-bl-none md:rounded-tr-[100px] md:rounded-br-[100px] md:backdrop-blur-xs md:bg-white/70 md:transition-all md:duration-1000">
            <div class="h-35 flex items-center">
                <img class="mx-auto h-19 md:h-22" src="{{ asset('images/e-skolarianLogo.svg') }}" alt="E-skolarian Logo">
            </div>
            <!-- Role Switch Buttons -->
            <div id="switchButton">
                <div class="p-1 max-w-[280px] mx-auto bg-[#D9D9D9] rounded-3xl flex flex-wrap justify-center gap-1 mb-6 font-['Lexend'] font-bold">
                    <button type="button" id="studentBtn" onclick="changeRole('student');"
                        class="group min-w-[120px] flex items-center px-5 py-3 border border-gray-300 rounded-4xl hover:bg-[var(--secondary-color)] hover:text-white transition">
                        <div id="studentIcon" class="pr-[10px] group-hover:invert"><img class="h-[15px]" src="{{ asset('images/student.png') }}" alt="Student Icon"></div>
                        <p class="uppercase font-bold text-[14px] font-['Lexend', 'Georgia']">Student</p>
                    </button>
                    <button type="button" id="adminBtn" onclick="changeRole('admin');"
                        class="group min-w-[120px] flex items-center px-5 py-3 border border-gray-300 rounded-4xl hover:bg-[var(--secondary-color)] hover:text-white transition">
                        <div id="adminIcon" class="pr-[10px] group-hover:invert"><img class="h[15px]" src="{{ asset('images/admin.png') }}" alt="Admin Icon"></div>
                        <p class="uppercase font-bold text-[14px]">Admin</p>
                    </button>
                </div>
            </div>
            <div class="w-full max-w-[400px] mx-auto pt-14 md:pt-5">
                <form method="POST" action="{{ route('login') }}" class="space-y-4 md:space-y-2">
                    @csrf
                    <input type="hidden" name="role" id="role" value="student">
                    <!-- Email -->
                    <div class="pb-6">
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
                    @if ($errors->has('lockout_time'))
                    <div class="text-red-600 text-sm mt-1 pb-1.5 font-[Lexend] font-normal">
                        <p id="lockout-message">Too many login attempts. Please try again in <span id="lockout-timer"></span> seconds.</p>
                    </div>
                    <script>
                        // Countdown Timer
                        let lockoutTime = {{ $errors->first('lockout_time') }};
                        const lockoutMessage = document.getElementById('lockout-message');
                        const lockoutTimer = document.getElementById('lockout-timer');
                        const formInputs = document.querySelectorAll('input, button[type="submit"]');
                        // Disable inputs
                        formInputs.forEach(input => input.disabled = true);
                        // Display initial time
                        lockoutTimer.innerText = lockoutTime;
                        const timerInterval = setInterval(() => {
                            lockoutTime--;
                            if (lockoutTime <= 0) {
                                clearInterval(timerInterval);
                                lockoutMessage.innerText = "You can now try logging in again.";
                                formInputs.forEach(input => input.disabled = false);
                            } else {
                                lockoutTimer.innerText = lockoutTime;
                            }
                        }, 1000);
                    </script>
                    @endif

                    <!-- Remember Me (Visible only for students) -->
                    <div class="flex justify-between items-center font-['Manrope'] font-normal">
                        <label id="rememberMeContainer" class="text-[14px] flex items-center cursor-pointer invisible">
                            <input type="checkbox" name="remember" class="cursor-pointer">
                            <span class="ml-2">Remember Me</span>
                        </label>
                        <a href="" class="forgot-password-link max-md:hidden text-[14px] hover:text-[var(--secondary-color)] active:text-[var(--secondary-color)] transition-all duration-75">Forgot Password?</a>
                    </div>

                    <!-- Submit -->
                    <div class="pt-4 flex justify-center">
                        <button type="submit" id="signInButton"
                            class="w-full rounded-2xl mx-auto bg-[var(--secondary-color)] cursor-pointer text-white py-2 md:py-4  hover:bg-[var(--primary-color)] transition font-semibold">
                            Sign In
                        </button>
                    </div>
                    <div class="pb-7 flex justify-center">
                        <a href="#" class="forgot-password-link md:hidden font-normal text-[14px] active:text-[var(--secondary-color)] transition-all duration-75">Forgot Password?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        const hasFormErrors = {{ $errors->any() ? 'true' : 'false' }};
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const emailInput = document.getElementById('emailInput');
        const passwordInput = document.getElementById('password');

        const emailLabel = document.getElementById('emailLabel');
        const emailWarning = document.getElementById('emailLengthWarning');

        const passwordLabel = document.getElementById('passwordLabel');
        const passwordWarning = document.getElementById('passwordLengthWarning');

        const signInButton = document.getElementById('signInButton');
        const form = emailInput.closest('form');

        let serverErrorEmail = (hasFormErrors === true || hasFormErrors === 'true');
        let serverErrorPassword = (hasFormErrors === true || hasFormErrors === 'true');

        function validateInputs() {
            const email = emailInput.value.trim();
            const password = passwordInput.value.trim();

            const isEmailTooLong = email.length > 50;
            const isPasswordTooLong = password.length > 50;

            const isEmailValid = email.length > 0 && !isEmailTooLong;
            const isPasswordValid = password.length > 0 && !isPasswordTooLong;

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
            if (emailInput.value.length > 50 || passwordInput.value.length > 50) {
                e.preventDefault();
                alert('Email or password exceeds the allowed length.');
            }
                // Disable button to prevent multiple submissions
            signInButton.disabled = true;
            signInButton.innerText = 'Signing in...'; // Optional: change button text
        });

        passwordInput.addEventListener('focus', function () {
            if (serverErrorPassword) {
                passwordLabel.classList.remove('ring-3', '!ring-red-600');
                serverErrorPassword = false;
            }
        });

        // Initial server-side red rings
        if (hasFormErrors === true || hasFormErrors === 'true') {
            emailLabel.classList.add('ring-3', '!ring-red-600');
            passwordLabel.classList.add('ring-3', '!ring-red-600');
        }

        // Initial validation on page load
        validateInputs();
    });
    </script>
    </body>
</body>
</html>
