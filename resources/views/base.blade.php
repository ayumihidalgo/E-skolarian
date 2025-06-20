<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="E-SKOLARIAN Document Management System">
    <meta name="keywords" content="E-SKOLARIAN, Document Management, School, Education">
    <title> ESKOLARIAN </title>

    <link rel="icon" href="{{ asset('images/officialLogo.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Marcellus+SC&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @stack('styles')

    <script src="{{ asset('js/calendar-loader.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.2.12/pdfobject.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>


    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <main>
        @yield('content')
    </main>
    @stack('scripts')
    <script src="{{ asset('js/super-admin/admin-dropdown.js') }}"></script>

    <!-- Logout Warning Modal -->
    <div id="logoutWarningModal" class="fixed inset-0 flex items-center justify-center z-[100] hidden">
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>

        <div class="bg-white rounded-[16px] shadow-xl w-full max-w-md relative z-[110] p-6">
            <div class="text-center mb-6">
                <h3 class="text-xl font-semibold text-gray-800 font-[Lexend] mb-2">Session Timeout Warning</h3>
                <p class="text-sm text-gray-600">
                    Your session is about to expire due to inactivity. You will be automatically logged out in 1 minute.
                </p>
                <p class="text-sm text-gray-600 mt-2">
                    Click "Stay Logged In" to continue your session.
                </p>
            </div>

            <div class="flex justify-center space-x-4">
                <button onclick="window.location.href='/logout'"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded-[14px] font-semibold font-[Lexend] transition duration-200">
                    Logout Now
                </button>
                <button onclick="document.getElementById('logoutWarningModal').classList.add('hidden'); resetTimer();"
                    class="bg-[#7A1212] hover:bg-red-800 text-white px-5 py-2 rounded-[14px] font-semibold font-[Lexend] transition duration-200">
                    Stay Logged In
                </button>
            </div>
        </div>
    </div>
</body>
<script>
    window.addEventListener("beforeunload", function(e) {
        navigator.sendBeacon("/logout");
    });
</script>

</html>
