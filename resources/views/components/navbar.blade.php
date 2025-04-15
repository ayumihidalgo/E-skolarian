<nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo and application name -->
            <div class="flex items-center">
                <div class="flex-shrink-0 flex items-center">
                    <!-- Your logo would go here -->
                    <span class="text-xl font-bold text-gray-800">E-SKOLARIAN</span>
                    <span class="ml-2 text-xl text-gray-600">HUNDRED EDGE</span>
                </div>
            </div>

            <!-- Right side navigation -->
            <div class="flex items-center">
                @if($userName)
                    <div class="ml-4 flex items-center md:ml-6">
                        <!-- User dropdown -->
                        <div class="ml-3 relative">
                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ $userName }}</p>
                                    <p class="text-xs text-gray-500">{{ $userRole }}</p>
                                </div>

                                <!-- Logout form -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-red-600 hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red active:bg-red-700 transition duration-150 ease-in-out">
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</nav>
