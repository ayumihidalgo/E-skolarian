<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loading - E-SKOLARI★N</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black h-screen flex items-center justify-center">
    <div class="text-center">
        <!-- Dotted Loader -->
        <div class="relative w-24 h-24 mx-auto mb-6">
            @for ($i = 0; $i < 8; $i++)
                @php
                    $angle = 360 / 8 * $i;
                    $radius = 40;
                    $x = 50 + $radius * cos(deg2rad($angle));
                    $y = 50 + $radius * sin(deg2rad($angle));
                @endphp
                <div class="absolute w-4 h-4 bg-white rounded-full animate-ping"
                     style="top: calc({{ $y }}% - 8px); left: calc({{ $x }}% - 8px); animation-delay: {{ $i * 0.1 }}s;">
                </div>
            @endfor
        </div>

        <!-- Logo Text -->
        <h1 class="text-white text-xl font-semibold tracking-wider">
            E-SKOLARI
            <span class="text-yellow-400">★</span>
            <span class="text-white">N</span>
        </h1>
    </div>
</body>
</html>
