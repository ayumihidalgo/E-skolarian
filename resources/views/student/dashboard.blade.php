@extends('base')

@section('content')
    @include('components.studentNavBarComponent')
    @include('components.studentSideBarComponent')
    <div id="main-content" class="transition-all duration-300 ml-[20%]">
        <div class="flex-grow p-6 space-y-6">
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
               <!-- Announcements -->
                <div class="md:col-span-2 bg-white rounded-xl shadow-md p-4">
                    <h2 class="text-lg font-semibold mb-2">📢 Announcements</h2>
                    @if ($latestAnnouncements->count())
                        <div class="space-y-4 h-64 overflow-y-auto pr-2">
                            @foreach ($latestAnnouncements as $announcement)
                                <div class="mb-4 pb-4 border-b border-gray-300">
                                    <h3 class="text-xl font-semibold">{{ $announcement->title }}</h3>
                                    <p class="text-sm text-gray-500">
                                        Posted by {{ $announcement->user->username }} on 
                                        {{ $announcement->created_at->format('F j, Y') }}
                                    </p>
                                    @php
                                        $maxLength = 150;
                                        $isLong = strlen($announcement->content) > $maxLength;
                                        $preview = $isLong ? mb_substr($announcement->content, 0, $maxLength) . '...' : $announcement->content;
                                    @endphp
                                    <span class="text-gray-700 whitespace-pre-line">{{ $preview }}</span>
                                    @if ($isLong)
                                        <button 
                                            class="text-indigo-600 hover:underline ml-2 text-sm"
                                            onclick="showAnnouncementModal(
                                                `{{ addslashes($announcement->title) }}`,
                                                `{{ addslashes(e($announcement->content)) }}`,
                                                `Posted by {{ addslashes($announcement->user->username) }} on {{ $announcement->created_at->format('F j, Y') }}`,
                                                'announcement'
                                            )">
                                            Read More
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-gray-500 text-center py-8">No announcements at the moment</div>
                    @endif
                </div>

               <!-- Previous Announcements -->
                <div class="bg-white rounded-xl shadow-md p-4 md:row-span-2">
                    <h2 class="text-lg font-semibold mb-2">Previous Announcements</h2>
                    @if ($previousAnnouncements->count())
                        <div class="space-y-4 h-[32rem] overflow-y-auto pr-2">
                            @foreach ($previousAnnouncements as $announcement)
                                <div class="border-b pb-2 border-gray-300">
                                    <h3 class="text-base font-semibold">{{ $announcement->title }}</h3>
                                    <p class="text-sm text-gray-500">
                                        Posted by {{ $announcement->user->username }} on 
                                        {{ $announcement->created_at->format('F j, Y') }}
                                    </p>
                                    @php
                                        $maxLength = 100;
                                        $isLong = strlen($announcement->content) > $maxLength;
                                        $preview = $isLong ? mb_substr($announcement->content, 0, $maxLength) . '...' : $announcement->content;
                                    @endphp
                                    <span class="text-gray-700 whitespace-pre-line">{{ $preview }}</span>
                                    @if ($isLong)
                                        <button 
                                            class="text-indigo-600 hover:underline ml-2 text-sm"
                                            onclick="showAnnouncementModal(
                                                `{{ addslashes($announcement->title) }}`,
                                                `{{ addslashes(e($announcement->content)) }}`,
                                                `Posted by {{ addslashes($announcement->user->username) }} on {{ $announcement->created_at->format('F j, Y') }}`,
                                                'previous'
                                            )">
                                            Read More
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-8">
                            <img src="{{ asset('images/Illustrations.svg') }}" alt="No previous post"
                                class="w-24 h-24 mx-auto mb-2 opacity-80">
                            <p>No previous post</p>
                        </div>
                    @endif
                </div>
                <!-- Recent Documents -->
                <div class="md:col-span-2 space-y-2">
                    <h2 class="text-lg font-semibold">Recent Documents</h2>
                    <div class="bg-zinc-100 rounded-xl shadow-md p-4">
                        <div class="text-center text-gray-500 py-8">
                            <img src="{{ asset('images/recentdoc.png') }}" alt="No recent documents"
                                class="w-40 mx-auto mb-2 opacity-80">
                            <p>No recent documents at the moment</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for full announcement -->
    <div id="announcementModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div id="modalBackdrop" class="absolute inset-0 bg-black" style="opacity:0.2;"></div>
        <div class="relative bg-white rounded-xl shadow-lg max-w-xl w-full p-6 z-10">
            <div class="flex items-center justify-between mb-2 border-b pb-2">
                <div class="flex items-center gap-2">
                    <span class="text-2xl text-red-500">📢</span>
                    <span id="modalLabel" class="font-semibold text-lg">Announcement</span>
                </div>
                <button onclick="closeAnnouncementModal()" class="text-2xl text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <h3 id="modalTitle" class="text-lg font-bold mt-3 mb-1"></h3>
            <div id="modalMeta" class="text-xs text-gray-500 mb-3"></div>
            <div id="modalContent" class="text-gray-700 whitespace-pre-line"></div>
        </div>
    </div>

    <script>
    function showAnnouncementModal(title, content, meta = '', type = 'announcement') {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalContent').textContent = content;
        document.getElementById('modalMeta').innerHTML = meta;
        document.getElementById('modalLabel').textContent = 
            type === 'previous' ? 'Previous Announcement' : 'Announcement';
        document.getElementById('announcementModal').classList.remove('hidden');
    }
    function closeAnnouncementModal() {
        document.getElementById('announcementModal').classList.add('hidden');
    }
    </script>
@endsection
