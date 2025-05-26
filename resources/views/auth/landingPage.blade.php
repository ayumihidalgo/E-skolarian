<!-- filepath: c:\xampp\htdocs\E-skolarian\resources\views\auth\landingPage.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>E-skolarian | Login Selection</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-r from-[var(--login-color-left)] to-[var(--login-color-right)] font-['Manrope']">
    <div class="flex flex-col items-center justify-center h-screen">
        <img src="{{ asset('images/e-skolarianLogo.svg') }}" alt="E-skolarian Logo" class="mb-10 h-24">
        <h1 class="text-3xl font-bold mb-8 text-gray-800">Welcome to E-skolarian</h1>
        <div class="flex flex-col gap-6 w-full max-w-xs">
            <a href="{{ route('student.login.form') }}"
               class="w-full py-3 px-6 rounded-lg bg-[var(--primary-color)] text-white text-lg font-semibold text-center shadow hover:bg-[var(--secondary-color)] transition">
                Student Organization Login
            </a>
            <a href="{{ route('admin.login.form') }}"
               class="w-full py-3 px-6 rounded-lg bg-[var(--secondary-color)] text-white text-lg font-semibold text-center shadow hover:bg-[var(--primary-color)] transition">
                Admin Login
            </a>
        </div>
    </div>
</body>
</html>
