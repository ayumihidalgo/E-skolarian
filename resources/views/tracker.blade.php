<!--SCRUM 57 Updates-->

<!-- resources/views/student/tracker.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submitted Documents</title>
    @vite('resources/css/app.css') <!-- Assuming you're using Vite for Tailwind setup -->
</head>

<body class="bg-gray-100 min-h-screen">


    @include('components.studentNavigation') <!-- Navigation component -->
    <div class = "flex absolute top-20 left-110">
        <div class="flex-grow p-10">
            <div class="max-w-7xl mx-auto">

                <h1 class="text-2xl font-bold mb-6 underline underline-offset-4">Submitted Documents</h1>

                <!-- Search & Filters -->
                <div class="flex items-center space-x-4 mb-6 w-290">
                    <div class="flex-grow">
                        <div class="relative">
                            <input type="text" placeholder="Search..."
                                class="w-full p-3 pl-10 rounded-md border border-gray-300 focus:ring-yellow-400 focus:border-yellow-400">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 12.65z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <button class="bg-[#7A1212] text-white px-4 py-2 rounded-md flex items-center space-x-2">
                        <span>Document Type</span>
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <button class="bg-yellow-400 text-white px-4 py-2 rounded-md flex items-center space-x-2">
                        <span>Status</span>
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>

                <!-- No Records Section -->
                <div class="bg-white p-10 rounded-lg shadow flex flex-col justify-center items-center min-h-[300px]">
                    <img src="{{ asset('images/rafiki.svg') }}" alt="No Records" class="h-32 w-32 mb-6">
                    <p class="text-gray-500 text-center">No records found at the moment</p>
                </div>

            </div>
        </div>
    </div>


</body>

</html>
