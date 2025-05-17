@extends('base')

@include('components.adminNavBarComponent')
@include('components.adminSidebarComponent')
@section('content')
    <div id="main-content" class="transition-all duration-300 ml-[20%]">
        @if (session('success'))
            <div id="Toast"
                class="fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white border-l-4 border-green-400 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50"
                role="alert">
                <div class="w-full flex justify-between">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('images/successful.svg') }}" alt="Success Icon" id="docTypeIcon" class="">
                        <div>
                            <h6 class="font-bold font-['Manrope']">Announcement posted successfully!</h6>
                            <p class="sm:inline inline text-sm font-['Manrope']">{{ session('success') }}
                            </p>
                        </div>
                    </div>
                    <button type="button"
                        class="Cursor-pointer text-gray-500 hover:text-gray-700 text-2xl leading-none cursor-pointer"
                        onclick="document.getElementById('Toast').style.display='none';">&times;</button>
                </div>
            </div>
        @endif

        <div class="flex-grow p-6 space-y-6">
            <!-- Stats Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ([['Pending Documents', 'pendingicon.svg'], ['Under Review', 'reviewicon.svg'], ['Approved Documents', 'approvedicon.svg'], ['Total Documents', 'totaldocicon.svg']] as [$title, $icon])
                    <div class="bg-white p-4 rounded-xl shadow-md flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">{{ $title }}</p>
                            <div class="text-2xl font-bold">0</div>
                        </div>
                        <img src="{{ asset("images/$icon") }}" class="w-10 h-10" alt="{{ $title }}">
                    </div>
                @endforeach
            </div>

            <!-- Announcement and Documents Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
         <!-- Latest Announcements -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-4">
                <h2 class="text-lg font-semibold mb-2">📢 Announcements</h2>
                <div class="max-h-[350px] overflow-y-auto pr-1">
                    @if ($latestAnnouncements->count())
                        @foreach ($latestAnnouncements as $announcement)
                            <div class="mb-4 pb-4 border-b border-gray-300">
                                <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $announcement->title }}</h3>
                                <p class="text-sm text-gray-500 mb-1">
                                    Posted by {{ $announcement->user->username }} on {{ $announcement->created_at->format('F j, Y g:i A') }}
                                </p>
                                <div class="text-gray-700 whitespace-pre-line">
                                    {{ $announcement->content }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-gray-500 text-center py-8">No announcement at the moment</div>
                    @endif
                </div>
            </div>

            <!-- Previous Announcements -->
            <div class="bg-white rounded-xl shadow-md p-4">
                <h2 class="text-lg font-semibold mb-2">Previous Announcements</h2>
                <div class="max-h-[350px] overflow-y-auto pr-1">
                    @if ($previousAnnouncements->count())
                        @foreach ($previousAnnouncements as $announcement)
                            <div class="mb-4 pb-4 border-b border-gray-300">
                                <h3 class="text-md font-bold text-gray-800 mb-1">{{ $announcement->title }}</h3>
                                <p class="text-sm text-gray-500 mb-2">
                                    Posted by {{ $announcement->user->username }} on {{ $announcement->created_at->format('F j, Y g:i A') }}
                                </p>
                                <div class="text-gray-700 whitespace-pre-line">
                                    {{ $announcement->content }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-gray-500 py-8">
                            <img src="{{ asset('images/Illustrations.svg') }}" alt="No previous post"
                                class="w-24 h-24 mx-auto mb-2 opacity-80">
                            <p>No previous post</p>
                        </div>
                    @endif
                </div>
            </div>

                <!-- Recent Documents -->
                <div class="lg:col-span-2 space-y-2">
                    <h2 class="text-lg font-semibold">Recent Documents</h2>
                    <div class="bg-zinc-100 rounded-xl shadow-md p-4">
                        <div class="text-center text-gray-500 py-8">
                            <img src="{{ asset('images/recentdoc.png') }}" alt="No recent documents"
                                class="w-40 mx-auto mb-2 opacity-80">
                            <p>No recent documents at the moment</p>
                        </div>
                    </div>
                </div>

                <!-- Post New Announcements -->
                <div class="bg-white p-6 rounded-xl shadow">
                    <h2 class="text-lg font-semibold border-b pb-2 mb-4">Post New Announcements</h2>
                    <form id="announcementForm" action="{{ route('announcements.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                            <input type="text" id="titleInput" name="title" maxlength="60"
                                class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                placeholder="Enter announcement title">
                            <p id="titleError" class="text-red-500 text-sm mt-1" style="display: none;">Title is required.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                            <textarea name="content" id="contentInput" rows="4" maxlength="5000"
                                    class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                    placeholder="Enter announcement content"></textarea>
                            <p id="contentError" class="text-red-500 text-sm mt-1" style="display: none;">Content is required.</p>
                        </div>
                        <div class="text-right">
                            <button type="submit" id="submitBtn"
                                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                Post Announcement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>

    const form = document.getElementById('announcementForm');
    const titleInput = document.getElementById('titleInput');
    const contentInput = document.getElementById('contentInput');
    const titleError = document.getElementById('titleError');
    const contentError = document.getElementById('contentError');

    form.addEventListener('submit', function (e) {
        let valid = true;

        if (titleInput.value.trim() === '') {
            titleError.style.display = 'block';
            valid = false;
        } else {
            titleError.style.display = 'none';
        }

        if (contentInput.value.trim() === '') {
            contentError.style.display = 'block';
            valid = false;
        } else {
            contentError.style.display = 'none';
        }

        if (!valid) {
            e.preventDefault(); // Prevent form submission
        }
    });

    // Hide error while typing
    titleInput.addEventListener('input', () => {
        if (titleInput.value.trim() !== '') {
            titleError.style.display = 'none';
        }
    });

    contentInput.addEventListener('input', () => {
        if (contentInput.value.trim() !== '') {
            contentError.style.display = 'none';
        }
    });
        setTimeout(() => {
            const toast = document.getElementById('Toast');
            if (toast) {
                toast.style.display = 'none';
            }
        }, 5000);
    </script>
    
@endsection
