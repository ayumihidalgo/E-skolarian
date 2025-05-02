@php
    $role = request()->query('role', 'student'); // Default to 'student' if not provided
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $role }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="icon" href="{{ asset('images/officialLogo.svg') }}" type="image/svg+xml">


    <title>Forgot Password</title>
    @vite('resources/css/app.css')

    <script>
        window.addEventListener('load', setBackgroundImage)
        window.addEventListener('resize', setBackgroundImage);

        function setBackgroundImage() {
            const box = document.getElementById('box');
            if (!box) return;

            if (window.innerWidth >= 768) {
                box.style.backgroundImage = `linear-gradient(var(--login-bg-color), var(--login-bg-color)), url('{{ asset('images/PUP_Bg1.jpg') }}')`;
                box.style.backgroundRepeat = 'no-repeat';
                box.style.backgroundSize = 'cover';
            } else {
                box.style.backgroundImage = '';
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
<body id="box" class="min-h-screen flex items-center justify-center font-['Manrope'] font-bold bg-gradient-to-r from-[var(--login-color-left)] to-[var(--login-color-right)]  md:backdrop-blur-xs ">
    <div class="p-5 w-full">
        <div class="w-full mx-auto py-10 rounded-[40px] max-md:max-w-[520px] max-md:bg-white/60 max-md:shadow-md">
            <div class="flex justify-center pb-4">
                <img class="md:h-20" src="{{asset('images/e-skolarianLogo.svg')}}" alt="E-skolarian Logo">
            </div>
            <div class="w-full max-w-[550px] mx-auto  md:bg-[var(--forgot-color-bg)]/50 px-8 md:py-12 rounded-[40px] md:shadow-md md:backdrop-blur-lg">
                <h1 class="text-2xl md:text-3xl font-bold text-center mb-6 font-['Lexend'] uppercase text-[var(--secondary-color)]">Password Reset Request</h1>
                <h2 class="md:text-[var(--forgot-color-text)] text-center text-[20px] md:text-[25px] mb-1">Forgot Password?</h2>
                <p class="md:text-[var(--forgot-color-text)] text-center font-normal text-xs">Enter your email to reset your password</p>
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <input type="hidden" name="role" value="{{ $role }}"> <!-- Add this hidden input -->

                    <div class="mt-5 mb-2">
                        <label id="emailLabel" class="w-full rounded-full max-w-[380px] mx-auto px-4 py-3 ring bg-white flex focus-within:ring-3 focus-within:ring-[var(--secondary-color)]">
                            <input type="email" id="emailInput" name="email" placeholder="Email Address" required
                                class="w-0 flex-grow outline-none mr-3 text-[14px]">
                            <button type="button" class="focus:outline-none" tabindex="-1">
                                <img src="{{ asset('images/email.svg') }}" alt="Email Icon" class="w-4 mr-1" />
                            </button>
                        </label>
                        <div id="emailLengthWarning" class="w-full max-w-[380px] mx-auto px-4 text-red-600 text-sm mt-0.5 pl-[10px] font-[Lexend] font-normal hidden">
                            <p></p>
                        </div>
                    </div>

                    @if (session('status'))
                        <div class="status-message text-green-600 text-center text-xs mt-3 w-full max-w-[380px] mx-auto font-[Lexend] font-normal">
                            *{{ session('status') }}
                        </div>
                        <div id="emailSentFlag" data-sent="true" class="hidden"></div>
                    @endif
                    {{-- Error Messages --}}
                    @if ($errors->any())
                        <div class="status-message text-red-600 text-center text-xs mt-3 w-full max-w-[380px] mx-auto font-[Lexend] font-normal">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>*{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <button id="sendEmailBtn" type="submit"
                        class="mt-6 w-full rounded-full text-white max-w-[380px] block mx-auto mb-5 bg-[var(--secondary-color)] py-2 md:hover:text-white md:hover:bg-[var(--primary-color)] transition-all duration-200 disabled:opacity-50">
                        Send Email
                    </button>

                </form>
                <div class="mt-4 text-center">
                    <a href="{{ route('login') }}" class="flex items-center justify-center md:text-[var(--secondary-color)] font-normal group transition-all duration-75">
                        @if ($role === 'student') <img class="md:h-[25px] pr-5 pt-0.5 group-hover:translate-x-1 transition-all duration-75" src="{{asset('images/arrow-left.svg')}}" alt="Arrow Left Icon">
                        @elseif ($role === 'admin') <img class="md:h-[25px] pr-5 pt-0.5 group-hover:translate-x-1 transition-all duration-75" src="{{asset('images/arrow-left-admin.svg')}}" alt="Arrow Left Icon">
                        @endif
                        <span class="border-b-2 border-transparent group-hover:border-[var(--secondary-color)] transition-all duration-75">Back to Login</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script>
        const hasFormErrors = {{ $errors->any() ? 'true' : 'false' }};
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const emailInput = document.getElementById('emailInput');
            const warningText = document.getElementById('emailLengthWarning');
            const sendEmailBtn = document.getElementById('sendEmailBtn');
            const emailLabel = document.getElementById('emailLabel');
            const form = sendEmailBtn.closest('form');

            const COUNTDOWN_SECONDS = 60;
            const STORAGE_KEY = 'emailResendTimestamp';

            // Validate email format
            function validateEmail(email) {
                const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return pattern.test(email);
            }

            // Countdown logic
            function startCountdown(remainingSeconds) {
                sendEmailBtn.disabled = true;
                sendEmailBtn.textContent = `Resend Email (${remainingSeconds})`;

                const interval = setInterval(() => {
                    remainingSeconds--;
                    sendEmailBtn.textContent = `Resend Email (${remainingSeconds})`;

                    if (remainingSeconds <= 0) {
                        clearInterval(interval);
                        sendEmailBtn.disabled = false;
                        sendEmailBtn.textContent = "Resend Email";
                        localStorage.removeItem(STORAGE_KEY);
                    }
                }, 1000);
            }

            // On page load, check if a countdown should resume
            const lastSent = localStorage.getItem(STORAGE_KEY);
            if (lastSent) {
                const elapsed = Math.floor((Date.now() - parseInt(lastSent)) / 1000);
                const remaining = COUNTDOWN_SECONDS - elapsed;
                if (remaining > 0) {
                    startCountdown(remaining);
                } else {
                    localStorage.removeItem(STORAGE_KEY);
                }
            }

            // Handle email length warning
            emailInput.addEventListener('input', function () {
                const email = emailInput.value.trim();
                const isTooLong = email.length > 50;

                if (isTooLong) {
                    warningText.textContent = "*Email must not exceed 50 characters.";
                    warningText.classList.remove('hidden');
                    sendEmailBtn.disabled = true;

                    emailLabel.classList.add('ring-3', !isTooLong);
                    emailLabel.classList.add('!ring-red-600', !isTooLong);
                } else {
                    warningText.classList.add('hidden');

                    emailLabel.classList.remove('ring-3', !isTooLong);
                    emailLabel.classList.remove('!ring-red-600', !isTooLong);
                    // Only enable the button if not in "Resend Email" mode
                    if (!sendEmailBtn.textContent.includes("Resend Email")) {
                        sendEmailBtn.disabled = false;
                    }
                }
            });

            emailInput.addEventListener('focus', function() {
                emailLabel.classList.remove('ring-3', '!ring-red-600');
            });

            // Handle form submission (validate, but don't start countdown here)
            form.addEventListener('submit', function (e) {
                const email = emailInput.value.trim();
                const invalidFormat = !validateEmail(email);

                if (invalidFormat) {
                    e.preventDefault();
                    warningText.classList.remove('hidden');

                    if (invalidFormat) {
                        warningText.textContent = "*Please enter a valid email address format.";
                    }

                    emailLabel.classList.add('ring-3', invalidFormat);
                    emailLabel.classList.add('!ring-red-600', invalidFormat);

                    sendEmailBtn.disabled = false;
                    return;
                }

                sendEmailBtn.disabled = true;
                sendEmailBtn.textContent = "Processing...";
            });

            // Start countdown ONLY if backend confirms email sent
            const sentFlag = document.getElementById('emailSentFlag');
            if (sentFlag && sentFlag.dataset.sent === 'true') {
                const now = Date.now();
                localStorage.setItem(STORAGE_KEY, now.toString());
                startCountdown(COUNTDOWN_SECONDS);
            }

             // Initial server-side red rings
            if (hasFormErrors === true || hasFormErrors === 'true') {
                emailLabel.classList.add('ring-3', '!ring-red-600');
            }
        });
        </script>

</body>
</html>
