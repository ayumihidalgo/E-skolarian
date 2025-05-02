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

    <title>Password Reset Successful</title>
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
    </script>

</head>
<body id="box" class="min-h-screen flex items-center justify-center font-['Manrope'] font-bold bg-gradient-to-r from-[var(--login-color-left)] to-[var(--login-color-right)]  md:backdrop-blur-xs ">
    <div class="p-5 w-full">
        <div class="w-full mx-auto py-10 rounded-[40px] max-md:max-w-[520px] max-md:bg-white/60 max-md:shadow-md">
            <div class="flex justify-center pb-4">
                <img class="md:h-20" src="{{asset('images/e-skolarianLogo.svg')}}" alt="E-skolarian Logo">
            </div>
            <div class="w-full max-w-[550px] mx-auto  md:bg-[var(--forgot-color-bg)]/50 px-8 md:py-12 rounded-[40px] md:shadow-md md:backdrop-blur-lg">
                <h1 class="text-2xl md:text-3xl font-bold text-center mb-3 font-['Lexend'] uppercase text-[var(--secondary-color)]">Password Reset Successfully!</h1>
                <p class="md:text-[var(--forgot-color-text)] text-center font-normal md:text-lg">Your password has been successfully reset. You can now log in with your new password.</p>
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="bg-[var(--secondary-color)] px-5 py-3 rounded-full inline-block hover:bg-[var(--primary-color)] text-white font-bold transition-all duration-75">Go Back to Login Page</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
