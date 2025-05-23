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
                            <h6 class="font-bold font-['Manrope']">
                                {{ session('success') }}
                            </h6>
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
                            <div class="mb-4 pb-4 border-b border-gray-300 relative">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $announcement->title }}</h3>
                                    <!-- Ellipsis Button -->
                                    <div class="relative">
                                        <button 
                                            class="ml-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none transition"
                                            onclick="toggleMenu('menu-{{ $announcement->id }}')"
                                            type="button"
                                            aria-haspopup="true"
                                            aria-expanded="false"
                                        >
                                            <span class="sr-only">Open menu</span>
                                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <circle cx="4" cy="10" r="1.5"/>
                                                <circle cx="10" cy="10" r="1.5"/>
                                                <circle cx="16" cy="10" r="1.5"/>
                                            </svg>
                                        </button>
                                        <!-- Dropdown Menu -->
                                        <div id="menu-{{ $announcement->id }}" class="hidden absolute right-0 mt-2 w-36 bg-white border border-gray-200 rounded shadow z-30">
                                            <button 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 transition whitespace-nowrap"
                                                onclick="openEditModal({{ $announcement->id }}, `{{ addslashes($announcement->title) }}`, `{{ addslashes(e($announcement->content)) }}`)"
                                                type="button"
                                            >
                                                Edit
                                            </button>
                                            <form action="{{ route('admin.announcements.archive', $announcement->id) }}" method="POST" onsubmit="return confirm('Move this announcement to archive?');">
                                                @csrf
                                                <button
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50 transition border-t border-gray-100 whitespace-nowrap"
                                                    type="button"
                                                    onclick="openArchiveModal({{ $announcement->id }})"
                                                >
                                                    Move to Archive
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500 mb-1">
                                    Posted by {{ $announcement->user->username }} on {{ $announcement->created_at->format('F j, Y') }}
                                </p>
                                <div class="text-gray-700 whitespace-pre-line">
                                    @php
                                        $maxLength = 150;
                                        $isLong = strlen($announcement->content) > $maxLength;
                                        $preview = $isLong ? mb_substr($announcement->content, 0, $maxLength) . '...' : $announcement->content;
                                        $meta = "Posted by {$announcement->user->username} on {$announcement->created_at->format('F j, Y')}";
                                    @endphp
                                    <span>{{ $preview }}</span>
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
                            </div>
                        @endforeach
                    @else
                        <div class="text-gray-500 text-center py-8">No announcement at the moment</div>
                    @endif
                </div>
            </div>

            <!-- Previous Announcements and Archived Announcements-->
            <div class="bg-white rounded-xl shadow-md p-4">
    <div class="flex justify-between items-center mb-2">
        <h2 class="text-lg font-semibold">
            {{ $showArchive ? 'Archived Announcements' : 'Previous Announcements' }}
        </h2>
        @if ($showArchive)
            <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:underline text-sm font-medium">Previous</a>
        @else
            <a href="{{ route('admin.announcementArchive') }}" class="text-gray-600 hover:underline text-sm font-medium">Archive</a>
        @endif
    </div>
    <div class="max-h-[350px] overflow-y-auto pr-1">
        @php
            $announcements = $showArchive ? $archivedAnnouncements : $previousAnnouncements;
        @endphp
        @if ($announcements->count())
            @foreach ($announcements as $announcement)
                <div class="mb-4 pb-4 border-b border-gray-300 relative">
                    <div class="flex items-center justify-between">
                        <h3 class="text-md font-bold text-gray-800 mb-1">{{ $announcement->title }}</h3>
                        @if ($showArchive)
                            <!-- Ellipsis for archived (Restore/Delete) -->
                            <div class="relative">
                                <button 
                                    class="ml-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none transition"
                                    onclick="toggleMenu('archive-menu-{{ $announcement->id }}')"
                                    type="button"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                >
                                    <span class="sr-only">Open menu</span>
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <circle cx="4" cy="10" r="1.5"/>
                                        <circle cx="10" cy="10" r="1.5"/>
                                        <circle cx="16" cy="10" r="1.5"/>
                                    </svg>
                                </button>
                                <div id="archive-menu-{{ $announcement->id }}" class="hidden absolute right-0 mt-2 w-36 bg-white border border-gray-200 rounded shadow z-30">
                                    <button
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 transition whitespace-nowrap"
                                        type="button"
                                        onclick="openRestoreModal({{ $announcement->id }})"
                                    >
                                        Restore
                                    </button>
                                    <button
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition whitespace-nowrap"
                                        type="button"
                                        onclick="openDeleteModal({{ $announcement->id }})"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        @else
                            @if (!$showArchive)
                                <div class="relative">
                                    <button 
                                        class="ml-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none transition"
                                        onclick="toggleMenu('prev-menu-{{ $announcement->id }}')"
                                        type="button"
                                        aria-haspopup="true"
                                        aria-expanded="false"
                                    >
                                        <span class="sr-only">Open menu</span>
                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <circle cx="4" cy="10" r="1.5"/>
                                            <circle cx="10" cy="10" r="1.5"/>
                                            <circle cx="16" cy="10" r="1.5"/>
                                        </svg>
                                    </button>
                                    <!-- Dropdown Menu -->
                                    <div id="prev-menu-{{ $announcement->id }}" class="hidden absolute right-0 mt-2 w-36 bg-white border border-gray-200 rounded shadow z-30">
                                        <button
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50 transition whitespace-nowrap"
                                            type="button"
                                            onclick="openArchiveModal({{ $announcement->id }})"
                                        >
                                            Move to Archive
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 mb-2">
                        Posted by {{ $announcement->user->username }} on {{ $announcement->created_at->format('F j, Y') }}
                    </p>
                    <div class="text-gray-700 whitespace-pre-line">
                        @php
                            $maxLength = 100;
                            $isLong = strlen($announcement->content) > $maxLength;
                            $preview = $isLong ? mb_substr($announcement->content, 0, $maxLength) . '...' : $announcement->content;
                        @endphp
                        <span>{{ $preview }}</span>
                        @if ($isLong)
                            <button 
                                class="text-indigo-600 hover:underline ml-2 text-sm" 
                                onclick="showAnnouncementModal(
                                    `{{ addslashes($announcement->title) }}`,
                                    `{{ addslashes(e($announcement->content)) }}`,
                                    `Posted by {{ addslashes($announcement->user->username) }} on {{ $announcement->created_at->format('F j, Y g:i A') }}`,
                                    '{{ $showArchive ? 'archive' : 'previous' }}'
                                )">
                                Read More
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center text-gray-500 py-8">
                <img src="{{ asset('images/Illustrations.svg') }}" alt="No post"
                    class="w-24 h-24 mx-auto mb-2 opacity-80">
                <p>No {{ $showArchive ? 'archived' : 'previous' }} announcements</p>
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
    {{-- Modal for full announcement --}}
    <div id="announcementModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div id="modalBackdrop" class="absolute inset-0 bg-black" style="opacity:0.2;"></div>
        <div class="relative bg-white rounded-xl shadow-lg max-w-xl w-full p-6 z-10">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span class="text-2xl text-red-500">📢</span>
                    <span id="modalLabel" class="font-semibold text-lg">Announcement</span>
                </div>
                <button onclick="closeAnnouncementModal()" class="text-2xl text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <h3 id="modalTitle" class="text-lg font-bold mb-1"></h3>
            <div id="modalMeta" class="text-xs text-gray-500 mb-3"></div>
            <div id="modalContent" class="text-gray-700 whitespace-pre-line"></div>
        </div>
    </div>

    {{-- Edit Announcement Modal --}}
    <div id="editAnnouncementModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="absolute inset-0 bg-black opacity-20"></div>
        <div class="relative bg-white rounded-xl shadow-lg max-w-xl w-full p-6 z-10">
            <div class="flex items-center justify-between mb-2">
                <span class="font-semibold text-lg">Edit Announcement</span>
                <button onclick="closeEditModal()" class="text-2xl text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <form id="editAnnouncementForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="editAnnouncementId" name="id">
                <input type="hidden" id="originalTitle">
                <input type="hidden" id="originalContent">
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" id="editTitle" name="title" maxlength="60"
                        class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                    <textarea id="editContent" name="content" rows="4" maxlength="5000"
                        class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        required></textarea>
                </div>
                <div class="text-right">
                    <button type="submit" id="saveChangesBtn"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="NoChangeToast"
    class="hidden fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white border-l-4 border-yellow-400 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50"
    role="alert">
    <div class="w-full flex justify-between">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/warning.PNG') }}" alt="Warning Icon" class="w-6 h-6">
            <div>
                <h6 class="font-bold font-['Manrope']">
                    There was no change.
                </h6>
            </div>
        </div>
        <button type="button"
            class="Cursor-pointer text-gray-500 hover:text-gray-700 text-2xl leading-none cursor-pointer"
            onclick="document.getElementById('NoChangeToast').style.display='none';">&times;</button>
    </div>
</div>

    <!-- Archive Confirmation Modal -->
<div id="archiveConfirmModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="relative bg-white rounded-xl shadow-lg max-w-md w-full p-6 z-10">
        <div class="flex items-center justify-between mb-2">
            <span class="font-semibold text-lg">Archive Announcement Confirmation</span>
            <button onclick="closeArchiveModal()" class="text-2xl text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <div class="mb-4 text-gray-700">
            Are you sure you want to archive this Announcement? Once archived, it will be removed from your list and will no longer be visible there.
        </div>
        <div class="flex justify-end gap-2">
            <button onclick="closeArchiveModal()" class="px-4 py-2 rounded border border-gray-300 text-gray-700 bg-white hover:bg-gray-100">Cancel</button>
            <form id="archiveForm" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 rounded bg-red-700 text-white hover:bg-red-800">Archive</button>
            </form>
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div id="restoreConfirmModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="relative bg-white rounded-xl shadow-lg max-w-md w-full p-6 z-10">
        <div class="flex items-center justify-between mb-2">
            <span class="font-semibold text-lg">Restore Announcement Confirmation</span>
            <button onclick="closeRestoreModal()" class="text-2xl text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <div class="mb-4 text-gray-700">
            Are you sure you want to restore this announcement?<br>
            It will be moved back to the previous announcements list and become visible to users again.
        </div>
        <div class="flex justify-end gap-2">
            <button onclick="closeRestoreModal()" class="px-4 py-2 rounded border border-gray-300 text-gray-700 bg-white hover:bg-gray-100">Cancel</button>
            <form id="restoreForm" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 rounded bg-red-700 text-white hover:bg-red-800">Restore</button>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="relative bg-white rounded-xl shadow-lg max-w-md w-full p-6 z-10">
        <div class="flex items-center justify-between mb-2">
            <span class="font-semibold text-lg">Delete Announcement Confirmation</span>
            <button onclick="closeDeleteModal()" class="text-2xl text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <div class="mb-4 text-gray-700">
            Are you sure you want to permanently delete this announcement? This action cannot be undone.
        </div>
        <div class="flex justify-end gap-2">
            <button onclick="closeDeleteModal()" class="px-4 py-2 rounded border border-gray-300 text-gray-700 bg-white hover:bg-gray-100">Cancel</button>
            <form id="deleteForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded bg-red-700 text-white hover:bg-red-800">Delete</button>
            </form>
        </div>
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

        if (valid) {
            // Disable the button to prevent multiple clicks
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('submitBtn').classList.add('opacity-50', 'cursor-not-allowed');
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

        function toggleMenu(menuId) {
            // Hide all other menus
            document.querySelectorAll('[id$="-menu-"]').forEach(menu => {
                if (menu.id !== menuId) menu.classList.add('hidden');
            });
            // Toggle current menu
            const menu = document.getElementById(menuId);
            if (menu) menu.classList.toggle('hidden');
        }

        // Hide menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('[id^="menu-"]') && !event.target.closest('button[onclick^="toggleMenu"]')) {
                document.querySelectorAll('[id^="menu-"]').forEach(menu => menu.classList.add('hidden'));
            }
        });

        // Open Edit Modal
        function openEditModal(id, title, content) {
            document.getElementById('editAnnouncementId').value = id;
            document.getElementById('editTitle').value = title;
            document.getElementById('editContent').value = content;
            document.getElementById('originalTitle').value = title;
            document.getElementById('originalContent').value = content;
            document.getElementById('editAnnouncementModal').classList.remove('hidden');
            document.getElementById('editAnnouncementForm').action = `/announcements/${id}`;
        }

        // Close Edit Modal
        function closeEditModal() {
            document.getElementById('editAnnouncementModal').classList.add('hidden');
        }

        document.getElementById('editAnnouncementForm').addEventListener('submit', function(e) {
            const originalTitle = document.getElementById('originalTitle').value.trim();
            const originalContent = document.getElementById('originalContent').value.trim();
            const currentTitle = document.getElementById('editTitle').value.trim();
            const currentContent = document.getElementById('editContent').value.trim();

            if (originalTitle === currentTitle && originalContent === currentContent) {
                e.preventDefault();
                const toast = document.getElementById('NoChangeToast');
                toast.style.display = 'flex';
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 3000);
            } else {
                // Disable the button to prevent multiple clicks
                const saveBtn = document.getElementById('saveChangesBtn');
                saveBtn.disabled = true;
                saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        });

        // Open Archive Modal
        function openArchiveModal(announcementId) {
            const form = document.getElementById('archiveForm');
            form.action = `/admin/announcements/${announcementId}/archive`;
            document.getElementById('archiveConfirmModal').classList.remove('hidden');
        }
        function closeArchiveModal() {
            document.getElementById('archiveConfirmModal').classList.add('hidden');
        }

        // Open Restore Modal
        function openRestoreModal(announcementId) {
            const form = document.getElementById('restoreForm');
            form.action = `/admin/announcements/${announcementId}/restore`;
            document.getElementById('restoreConfirmModal').classList.remove('hidden');
        }
        // Close Restore Modal
        function closeRestoreModal() {
            document.getElementById('restoreConfirmModal').classList.add('hidden');
        }

        // Open Delete Modal
        function openDeleteModal(announcementId) {
            const form = document.getElementById('deleteForm');
            form.action = `/admin/announcements/${announcementId}/delete`;
            document.getElementById('deleteConfirmModal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            document.getElementById('deleteConfirmModal').classList.add('hidden');
        }
    </script>
    
@endsection
