<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="admin">
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

            box.style.backgroundImage = `linear-gradient(var(--login-bg-color), var(--login-bg-color)), url('{{ asset('images/PUP_Bg1.jpg') }}')`;
            box.style.backgroundRepeat = 'no-repeat';
            box.style.backgroundSize = 'cover';
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
    @include('loading')
    <div class="p-5 w-full">
        <div class="w-full mx-auto py-10 rounded-[40px] max-md:max-w-[520px] max-md:bg-[#FFFFFFCC] max-md:shadow-md">
            <div class="flex justify-center pb-4">
                <img class="md:h-20" src="{{asset('images/e-skolarianLogo.svg')}}" alt="E-skolarian Logo">
            </div>
            <div class="w-full max-w-[550px] mx-auto  md:bg-[var(--forgot-color-bg)]/50 px-8 md:py-12 rounded-[40px] md:shadow-md md:backdrop-blur-lg">
                <h1 class="text-2xl md:text-3xl font-bold text-center mb-6 font-['Lexend'] uppercase text-[var(--secondary-color)]">Password Reset Request</h1>
                <h2 class="md:text-[var(--forgot-color-text)] text-center text-[20px] md:text-[25px] mb-1">Forgot Password?</h2>
                <p class="md:text-[var(--forgot-color-text)] text-center font-normal text-xs">Enter your email to reset your password</p>
                <form method="POST" action="{{ route('admin.password.email') }}">
                    @csrf
                    <input type="hidden" name="role" value="admin"> <!-- Hardcoded to admin -->

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
                        <div id="emailFormatWarning" class="w-full max-w-[380px] mx-auto px-4 text-red-600 text-sm mt-0.5 pl-[10px] font-[Lexend] font-normal hidden">
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
                        <a href="#" id="backToLogin" class="flex items-center justify-center md:text-[var(--forgot-color-text)] font-normal group transition-all duration-75">
                        <img class="md:h-[25px] pr-5 pt-0.5 group-hover:translate-x-1 transition-all duration-75" src="{{asset('images/arrow-left-admin.svg')}}" alt="Arrow Left Icon">
                        <span class="border-b-2 border-transparent group-hover:border-[var(--forgot-color-text)] transition-all duration-75">Back to Login</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
<div id="unsavedChangesModal" class="fixed inset-0 flex flex-col items-center justify-center bg-black/60 hidden z-50">
    <div class="flex items-center justify-center gap-4 pb-3">
        <img class="h-[80px]" src="{{asset('images/e-skolarianIcon.svg')}}">
        <img class="h-[35px]" src="{{asset('images/E-skolarianWhite.svg')}}">
    </div>
    <div class="bg-white/90 font-[Manrope] rounded-4xl shadow-lg py-6 px-4 max-w-lg w-full text-center">
        <div class="w-[80%] mx-auto">
            <h1 class="text-xl mb-4 text-[var(--secondary-color)]">Go back to Login Page?</h1>
            <p class="mb-6 font-normal text-black">You have unsaved changes. Do you wish to go back to the Login Page?</p>
            <div class="flex justify-around gap-5">
                <button id="confirmLeave" class="bg-[var(--secondary-color)] text-white px-4 py-2 rounded-full hover:brightness-75">Back to Login Page</button>
                <button id="cancelLeave" class="bg-[#D9D9D9CC] text-black px-4 py-2 rounded-full hover:bg-gray-400">Cancel</button>
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
            const formatWarning = document.getElementById('emailFormatWarning');
            const sendEmailBtn = document.getElementById('sendEmailBtn');
            const emailLabel = document.getElementById('emailLabel');
            const form = sendEmailBtn.closest('form');

            // Validate email format (TLD 2-10 letters, no numbers)
            function validateEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,10}$/.test(email);
            }

            emailInput.addEventListener('keydown', function (e) {
                if (e.key === ' ') e.preventDefault();
            });

            emailInput.addEventListener('input', function () {
                const email = emailInput.value.trim();
                const isTooLong = email.length > 50;
                const isFormatInvalid = email.length > 0 && !isTooLong && !validateEmail(email);

                // Length warning
                if (isTooLong) {
                    warningText.querySelector('p').textContent = "*Email must not exceed 50 characters.";
                    warningText.classList.remove('hidden');
                    emailLabel.classList.add('ring-3', '!ring-red-600');
                } else {
                    warningText.classList.add('hidden');
                }

                // Format warning
                if (isFormatInvalid) {
                    formatWarning.querySelector('p').textContent = "*Invalid email format. Please check your email address.";
                    formatWarning.classList.remove('hidden');
                    emailLabel.classList.add('ring-3', '!ring-red-600');
                } else {
                    formatWarning.classList.add('hidden');
                    if (!isTooLong) emailLabel.classList.remove('ring-3', '!ring-red-600');
                }

                // Enable/disable button
                sendEmailBtn.disabled = isTooLong || isFormatInvalid || email.length === 0;
            });

            emailInput.addEventListener('focus', function() {
                emailLabel.classList.remove('ring-3', '!ring-red-600');
            });

            // Handle form submission (validate, but don't start countdown here)
            form.addEventListener('submit', function (e) {
                const email = emailInput.value.trim();
                const isTooLong = email.length > 50;
                const isFormatInvalid = !validateEmail(email);

                if (isTooLong || isFormatInvalid) {
                    e.preventDefault();
                    if (isTooLong) {
                        warningText.querySelector('p').textContent = "*Email must not exceed 50 characters.";
                        warningText.classList.remove('hidden');
                    }
                    if (isFormatInvalid) {
                        formatWarning.querySelector('p').textContent = "*Invalid email format. Please check your email address.";
                        formatWarning.classList.remove('hidden');
                    }
                    emailLabel.classList.add('ring-3', '!ring-red-600');
                    sendEmailBtn.disabled = false;
                    return;
                }

                sendEmailBtn.disabled = true;
                sendEmailBtn.textContent = "Processing...";

                // Show loader
                document.getElementById('loader').classList.toggle('hidden');
                document.getElementById('loader').classList.toggle('flex');
            });

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

        let isDirty = false;
        const emailInput = document.getElementById('emailInput');
        const backToLogin = document.getElementById('backToLogin');
        const modal = document.getElementById('unsavedChangesModal');
        const confirmLeave = document.getElementById('confirmLeave');
        const cancelLeave = document.getElementById('cancelLeave');

        // Detect changes
        emailInput.addEventListener('input', () => {
            if (emailInput.value.trim() !== '') {
                isDirty = true;
            } else {
                isDirty = false;
            }
        });

         let isSafeExit = false;

    // Mark exit as safe when user clicks "Back to Login"
    document.getElementById('backToLogin').addEventListener('click', function (e) {
    e.preventDefault();

    if (isDirty) {
        // Show the modal only if there are unsaved changes
        modal.classList.remove('hidden');
    } else {
        // If not dirty, navigate immediately without modal
        isSafeExit = true; // mark as safe exit to skip beforeunload
        window.location.href = "{{ route('admin.login') }}";
    }
});

    // If user confirms going back
    document.getElementById('confirmLeave').addEventListener('click', function () {
        isSafeExit = true; // Allow leaving without beforeunload
        window.location.href = "{{ route('admin.login') }}";
    });

    // Cancel going back
    document.getElementById('cancelLeave').addEventListener('click', function () {
        document.getElementById('unsavedChangesModal').classList.add('hidden');
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
