@php
    $role = request()->query('role', 'student'); // Default to 'student' if not provided
if ($role === 'super admin') {
    $role = 'admin'; // treat super admin same as admin
}
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $role }}">
<head>
    <meta charset="UTF-8" >
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="icon" href="{{ asset('images/officialLogo.svg') }}" type="image/svg+xml">

    <title>Reset Password</title>
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

            /* Toggle Show/Hide Password */
        function togglePassword() {
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

        function togglePasswordConfirm() {
            const input = document.getElementById('password_confirmation');

            showPassIcon = document.getElementById('showPassConfirm');
            hidePassIcon = document.getElementById('hidePassConfirm');

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
<body id="box" class="min-h-screen flex items-center justify-center font-['Manrope'] font-bold bg-gradient-to-r from-[var(--login-color-left)] to-[var(--login-color-right)]  md:backdrop-blur-xs ">
    <div class="p-5 w-full">
        <div class="w-full mx-auto py-10 rounded-[40px] max-md:max-w-[520px] max-md:bg-white/60 max-md:shadow-md">
            <div class="flex justify-center pb-4">
                <img class="md:h-20" src="{{asset('images/e-skolarianLogo.svg')}}" alt="E-skolarian Logo">
            </div>
            <div class="w-full max-w-[550px] mx-auto  md:bg-[var(--forgot-color-bg)]/50 px-8 md:py-12 rounded-[40px] md:shadow-md md:backdrop-blur-lg">
                <h1 class="text-2xl md:text-3xl font-bold text-center mb-6 font-['Lexend'] uppercase text-[var(--secondary-color)]">Reset Password</h1>

                  {{-- Success Message --}}
                    @if (session('status'))
                        <div class="status-message mb-4 text-green-600 font-medium text-xs text-center w-full max-w-[380px] mx-auto">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Error Messages --}}
                    @if ($errors->any())
                        <div class="status-message text-red-600 text-xs mb-4 w-full max-w-[380px] mx-auto">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ request()->get('email') }}">
                    <input type="hidden" name="role" value="{{ $role }}">

                    <div class="mt-5 mb-2">
                        <label class="w-full rounded-full max-w-[380px] mx-auto px-4 py-3 outline bg-white flex focus-within:outline-3 focus-within:outline-[var(--secondary-color)]">
                            <input id="password" type="password" name="password" placeholder="Password" required
                            class="w-0 flex-grow outline-none mr-3">
                            <button type="button" onclick="togglePassword()" class="cursor-pointer">
                                <img id="showPass" src="{{ asset('images/show_pass.svg') }}" alt="Show Password" class="w-5 md:w-6" />
                                <img id="hidePass" src="{{ asset('images/hide_pass.svg') }}" alt="Hide Password" class="w-5 md:w-6 hidden" />
                            </button>
                        </label>
                        <p id="password-requirements" class="hidden text-red-600 text-xs mt-2 w-full rounded-full max-w-[380px] mx-auto">
                            Password must be at least 8 characters long and contain an uppercase letter, a lowercase letter, a number, and a special character (@$!%*?&#).
                        </p>
                    </div>

                    <div class="mt-5 mb-2">
                        <label class="w-full rounded-full max-w-[380px] mx-auto px-4 py-3 outline bg-white flex focus-within:outline-3 focus-within:outline-[var(--secondary-color)]">
                            <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm Password" required
                            class="w-0 flex-grow outline-none mr-3">
                            <button type="button" onclick="togglePasswordConfirm();" class="cursor-pointer">
                                <img id="showPassConfirm" src="{{ asset('images/show_pass.svg') }}" alt="Show Password" class="w-5 md:w-6" />
                                <img id="hidePassConfirm" src="{{ asset('images/hide_pass.svg') }}" alt="Hide Password" class="w-5 md:w-6 hidden" />
                            </button>
                        </label>
                        <p id="match-message" class="w-full rounded-full max-w-[380px] mx-auto text-xs mt-2 text-red-500 hidden">Passwords do not match</p>
                    </div>

                    <button type="submit" id="submit-button" disabled
                        class="disabled:opacity-50 mt-6 w-full rounded-full text-white max-w-[380px] block mx-auto mb-5 bg-[var(--secondary-color)] py-2 md:hover:text-white md:hover:bg-[var(--primary-color)] transition-all duration-200">
                        Reset Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        const submitButton = document.getElementById('submit-button');

        const requirements = {
            length: document.getElementById('length'),
            uppercase: document.getElementById('uppercase'),
            lowercase: document.getElementById('lowercase'),
            number: document.getElementById('number'),
            special: document.getElementById('special'),
        };

        const matchMessage = document.getElementById('match-message');

        function validatePassword() {
            const password = passwordInput.value;
            const requirementsText = document.getElementById('password-requirements');

            if (password.length > 0) {
                requirementsText.classList.remove('hidden');
            }

            const lengthValid = password.length >= 8;
            const uppercaseValid = /[A-Z]/.test(password);
            const lowercaseValid = /[a-z]/.test(password);
            const numberValid = /[0-9]/.test(password);
            const specialValid = /[@$!%*?&#]/.test(password);

            const allValid = lengthValid && uppercaseValid && lowercaseValid && numberValid && specialValid;

            requirementsText.classList.toggle('text-green-700', allValid);
            requirementsText.classList.toggle('text-red-500', !allValid);
            requirementsText.classList.toggle('hidden', allValid); // Hide if valid

            return allValid;
        }


        function validateMatch() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            const match = password === confirmPassword;

            if (confirmPassword.length > 0) {
                matchMessage.classList.toggle('hidden', match);
            } else {
                matchMessage.classList.add('hidden'); // Always hide if user hasn't typed anything
            }

            return match;
        }


        function validateForm() {
            const passwordValid = validatePassword();
            const matchValid = validateMatch();

            // Enable the submit button only if all conditions are met
            submitButton.disabled = !(passwordValid && matchValid);
        }

        // Attach event listeners
        passwordInput.addEventListener('input', validateForm);
        confirmPasswordInput.addEventListener('input', validateForm);
    </script>
</body>
</html>
