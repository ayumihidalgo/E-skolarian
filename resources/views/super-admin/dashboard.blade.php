@extends('base')<!-- Extend the base component -->
@section('content')
    <!-- Content section -->
    <!-- This is the main content area for the super admin dashboard -->
    @include('components.superAdminNavigation') <!-- Include the super admin navigation component -->
    <!-- Super admin word under the nav var -->
    <div class="max-h-screen bg-white bg-opacity-30 px-15 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-[35px] font-bold font-[Lexend] text-[#332B2B] ">SUPER ADMIN</h1>
        </div>

        <!-- Add User Button -->
        <div class="mb-4 flex justify-between items-center">
            <button id="addUserBtn"
                class="bg-[#7A1212] hover:bg-red-800 text-white text-[20px] px-4 py-2 rounded-[16px] font-semibold font-[Lexend] inline-flex items-center cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none"
                    stroke="currentColor" class="mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 5v10m5-5H5" stroke-width="2" />
                </svg>
                ADD USER
            </button>
            <div class="flex items-center gap-3">
                <a href="{{ route('deactivated.accounts') }}"
                    class="group flex items-center bg-white border border-[#4D0F0F] px-3 py-2 rounded-[10px] shadow-sm text-[18px] font-bold text-[#4D0F0F] hover:bg-red-800 hover:text-white cursor-pointer">
                    DEACTIVATED ACCOUNTS
                </a>

                <!-- Activity Log Button -->
                <button id="activityLogBtn"
                    class="group flex items-center bg-white border border-[#4D0F0F] px-3 py-2 rounded-[10px] shadow-sm text-[18px] font-bold text-[#4D0F0F] hover:bg-red-800 hover:text-white cursor-pointer">
                    ACTIVITY LOG
                    <svg width="20" height="20" viewBox="0 0 15 15" fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg"
                        class="ml-2 transition-colors duration-200 group-hover:fill-current">
                        <g id="radix-icons:activity-log">
                            <path id="Vector" fill-rule="evenodd" clip-rule="evenodd"
                                d="M0 1.5C0 1.36739 0.0526784 1.24021 0.146447 1.14645C0.240215 1.05268 0.367392 1 0.5 1H2.5C2.63261 1 2.75979 1.05268 2.85355 1.14645C2.94732 1.24021 3 1.36739 3 1.5C3 1.63261 2.94732 1.75979 2.85355 1.85355C2.75979 1.94732 2.63261 2 2.5 2H0.5C0.367392 2 0.240215 1.94732 0.146447 1.85355C0.0526784 1.75979 0 1.63261 0 1.5ZM4 1.5C4 1.36739 4.05268 1.24021 4.14645 1.14645C4.24021 1.05268 4.36739 1 4.5 1H14.5C14.6326 1 14.7598 1.05268 14.8536 1.14645C14.9473 1.24021 15 1.36739 15 1.5C15 1.63261 14.9473 1.75979 14.8536 1.85355C14.7598 1.94732 14.6326 2 14.5 2H4.5C4.36739 2 4.24021 1.94732 4.14645 1.85355C4.05268 1.75979 4 1.63261 4 1.5ZM4 4.5C4 4.36739 4.05268 4.24021 4.14645 4.14645C4.24021 4.05268 4.36739 4 4.5 4H11.5C11.6326 4 11.7598 4.05268 11.8536 4.14645C11.9473 4.24021 12 4.36739 12 4.5C12 4.63261 11.9473 4.75979 11.8536 4.85355C11.7598 4.94732 11.6326 5 11.5 5H4.5C4.36739 5 4.24021 4.94732 4.14645 4.85355C4.05268 4.75979 4 4.63261 4 4.5ZM0 7.5C0 7.36739 0.0526784 7.24021 0.146447 7.14645C0.240215 7.05268 0.367392 7 0.5 7H2.5C2.63261 7 2.75979 7.05268 2.85355 7.14645C2.94732 7.24021 3 7.36739 3 7.5C3 7.63261 2.94732 7.75979 2.85355 7.85355C2.75979 7.94732 2.63261 8 2.5 8H0.5C0.367392 8 0.240215 7.94732 0.146447 7.85355C0.0526784 7.75979 0 7.63261 0 7.5ZM4 7.5C4 7.36739 4.05268 7.24021 4.14645 7.14645C4.24021 7.05268 4.36739 7 4.5 7H14.5C14.6326 7 14.7598 7.05268 14.8536 7.14645C14.9473 7.24021 15 7.36739 15 7.5C15 7.63261 14.9473 7.75979 14.8536 7.85355C14.7598 7.94732 14.6326 8 14.5 8H4.5C4.36739 8 4.24021 7.94732 4.14645 7.85355C4.05268 7.75979 4 7.63261 4 7.5ZM4 10.5C4 10.3674 4.05268 10.2402 4.14645 10.1464C4.24021 10.0527 4.36739 10 4.5 10H11.5C11.6326 10 11.7598 10.0527 11.8536 10.1464C11.9473 10.2402 12 10.3674 12 10.5C12 10.6326 11.9473 10.7598 11.8536 10.8536C11.7598 10.9473 11.6326 11 11.5 11H4.5C4.36739 11 4.24021 10.9473 4.14645 10.8536C4.05268 10.7598 4 10.6326 4 10.5ZM0 13.5C0 13.3674 0.0526784 13.2402 0.146447 13.1464C0.240215 13.0527 0.367392 13 0.5 13H2.5C2.63261 13 2.75979 13.0527 2.85355 13.1464C2.94732 13.2402 3 13.3674 3 13.5C3 13.6326 2.94732 13.7598 2.85355 13.8536C2.75979 13.9473 2.63261 14 2.5 14H0.5C0.367392 14 0.240215 13.9473 0.146447 13.8536C0.0526784 13.7598 0 13.6326 0 13.5ZM4 13.5C4 13.3674 4.05268 13.2402 4.14645 13.1464C4.24021 13.0527 4.36739 13 4.5 13H14.5C14.6326 13 14.7598 13.0527 14.8536 13.1464C14.9473 13.2402 15 13.3674 15 13.5C15 13.6326 14.9473 13.7598 14.8536 13.8536C14.7598 13.9473 14.6326 14 14.5 14H4.5C4.36739 14 4.24021 13.9473 4.14645 13.8536C4.05268 13.7598 4 13.6326 4 13.5Z" />
                        </g>
                    </svg>
                </button>
            </div>
        </div>
<!-- Page Content Wrapper -->
<div class="space-y-6">
        <!-- Announcements Section -->
        <div class="flex flex-col lg:flex-row gap-4 mb-6">
            <!-- Announcements Card (60%) -->
            <div class="w-full lg:w-[60%] bg-white rounded-[25px] shadow p-4 flex flex-col min-h-[400px]">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold font-['Manrope']">📢 Announcements</h2>
                    <button onclick="openPostAnnouncementModal()" title="Post New Announcement"
                        class="p-2 rounded-full hover:bg-gray-100 transition">
                        <img src="{{ asset('images/add_annc.svg') }}" alt="Add Announcement" class="w-7 h-7">
                    </button>
                </div>
                <div class="flex-1 max-h-[500px] overflow-y-auto pr-1">
                    @if ($latestAnnouncements->count())
                        @foreach ($latestAnnouncements as $announcement)
                            <div class="mb-4 pb-4 border-b border-gray-300 relative">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-xl font-bold text-[#332B2B] mb-1">{{ $announcement->title }}</h3>
                                    <!-- Ellipsis Button -->
                                    <div class="relative">
                                        <button class="ml-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none transition"
                                            onclick="toggleMenu('menu-{{ $announcement->id }}')" type="button">
                                            <span class="sr-only">Open menu</span>
                                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <circle cx="4" cy="10" r="1.5" />
                                                <circle cx="10" cy="10" r="1.5" />
                                                <circle cx="16" cy="10" r="1.5" />
                                            </svg>
                                        </button>
                                        <!-- Dropdown Menu -->
                                        <div id="menu-{{ $announcement->id }}"
                                            class="hidden absolute right-0 mt-2 w-36 bg-white border border-gray-200 rounded shadow z-30">
                                            <button
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 transition whitespace-nowrap"
                                                onclick="openEditModal(
                                                    {{ $announcement->id }},
                                                    `{{ addslashes($announcement->title) }}`,
                                                    `{{ addslashes(e($announcement->content)) }}`,
                                                    `{{ $announcement->deadline ? \Carbon\Carbon::parse($announcement->deadline)->format('Y-m-d') : '' }}`,
                                                    `{{ $announcement->deadline ? \Carbon\Carbon::parse($announcement->deadline)->format('H:i') : '' }}`,
                                                    `{{ $announcement->audience ?? 'all' }}`,
                                                    '{{ htmlspecialchars(json_encode($announcement->audience_students ?? []), ENT_QUOTES, "UTF-8") }}'
                                                )"
                                                type="button">
                                                Edit
                                            </button>
                                            <form
                                                action="{{ route('super-admin.announcements.archive', $announcement->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Move this announcement to archive?');">
                                                @csrf
                                                <button type="button"
                                                    onclick="openArchiveModal({{ $announcement->id }})">
                                                    Move to Archive
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500 mb-1">
                                    Posted by {{ $announcement->user->username }} on
                                    {{ $announcement->created_at->format('F j, Y') }}
                                    @if($announcement->deadline)
                                        @php
                                            $deadline = \Carbon\Carbon::parse($announcement->deadline);
                                            $hasTime = $deadline->format('H:i:s') !== '00:00:00';
                                        @endphp
                                        <span class="ml-2 text-red-600 font-semibold">
                                            Deadline: {{ $hasTime ? $deadline->format('F j, Y g:i A') : $deadline->format('F j, Y') }}
                                        </span>
                                    @endif
                                </p>
                                <div class="text-gray-700 whitespace-pre-line">
                                    @php
                                        $maxLength = 150;
                                        $isLong = strlen($announcement->content) > $maxLength;
                                        $preview = $isLong
                                            ? mb_substr($announcement->content, 0, $maxLength) . '...'
                                            : $announcement->content;
                                        $meta = "Posted by {$announcement->user->username} on {$announcement->created_at->format('F j, Y',)}";
                                    @endphp
                                    <span class="break-words whitespace-pre-line">{{ $preview }}</span>
                                    @if ($isLong)
                                        <button class="text-indigo-600 hover:underline ml-2 text-sm"
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
                        <div class="text-gray-500 text-center py-8 font-['Manrope']">No announcement at the moment</div>
                    @endif
                </div>
            </div>

            <!-- Previous Announcements Card (40%) -->
            <div class="w-full lg:w-[40%] bg-white rounded-[25px] shadow p-4 flex flex-col min-h-[400px]">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold font-['Manrope']">
                        {{ $showArchive ? 'Archived Announcements' : 'Previous Announcements' }}
                    </h2>
                    @if ($showArchive)
                        <a href="{{ route('super-admin.dashboard') }}"
                            class="text-gray-600 hover:underline text-sm font-medium font-['Manrope']">Previous</a>
                    @else
                        <a href="{{ route('super-admin.announcementArchive') }}?archive=true"
                            class="text-gray-600 hover:underline text-sm font-medium font-['Manrope']">Archive</a>
                    @endif
                </div>
                <div class="flex-1 max-h-[500px] overflow-y-auto pr-1">
                    @php
                        $announcements = $showArchive ? $archivedAnnouncements : $previousAnnouncements;
                    @endphp
                    @if ($announcements->count())
                        @foreach ($announcements as $announcement)
                            <div class="mb-4 pb-4 border-b border-gray-300 relative">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-md font-bold text-gray-800 mb-1">{{ $announcement->title }}
                                    </h3>
                                    @if ($showArchive)
                                        <!-- Ellipsis for archived (Restore/Delete) -->
                                        <div class="relative">
                                            <button
                                                class="ml-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none transition"
                                                onclick="toggleMenu('archive-menu-{{ $announcement->id }}')"
                                                type="button" aria-haspopup="true" aria-expanded="false">
                                                <span class="sr-only">Open menu</span>
                                                <svg class="w-5 h-5 text-gray-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <circle cx="4" cy="10" r="1.5" />
                                                    <circle cx="10" cy="10" r="1.5" />
                                                    <circle cx="16" cy="10" r="1.5" />
                                                </svg>
                                            </button>
                                            <div id="archive-menu-{{ $announcement->id }}"
                                                class="hidden absolute right-0 mt-2 w-36 bg-white border border-gray-200 rounded shadow z-30">
                                                <button
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 transition whitespace-nowrap"
                                                    type="button"
                                                    onclick="openRestoreModal({{ $announcement->id }})">
                                                    Restore
                                                </button>
                                                <button
                                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition whitespace-nowrap"
                                                    type="button"
                                                    onclick="openDeleteModal({{ $announcement->id }})">
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
                                                    type="button" aria-haspopup="true" aria-expanded="false">
                                                    <span class="sr-only">Open menu</span>
                                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <circle cx="4" cy="10" r="1.5" />
                                                        <circle cx="10" cy="10" r="1.5" />
                                                        <circle cx="16" cy="10" r="1.5" />
                                                    </svg>
                                                </button>
                                                <!-- Dropdown Menu -->
                                                <div id="prev-menu-{{ $announcement->id }}"
                                                    class="hidden absolute right-0 mt-2 w-36 bg-white border border-gray-200 rounded shadow z-30">
                                                    <button
                                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50 transition whitespace-nowrap"
                                                        type="button"
                                                        onclick="openArchiveModal({{ $announcement->id }})">
                                                        Move to Archive
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 mb-2">
                                    Posted by {{ $announcement->user->username }} on
                                    {{ $announcement->created_at->format('F j, Y') }}
                                    @if($announcement->deadline)
                                        @php
                                            $deadline = \Carbon\Carbon::parse($announcement->deadline);
                                            $hasTime = $deadline->format('H:i:s') !== '00:00:00';
                                        @endphp
                                        <span class="ml-2 text-red-600 font-semibold">
                                            Deadline: {{ $hasTime ? $deadline->format('F j, Y g:i A') : $deadline->format('F j, Y') }}
                                        </span>
                                    @endif
                                </p>
                                <div class="text-gray-700 whitespace-pre-line">
                                    @php
                                        $maxLength = 100;
                                        $isLong = strlen($announcement->content) > $maxLength;
                                        $preview = $isLong
                                            ? mb_substr($announcement->content, 0, $maxLength) . '...'
                                            : $announcement->content;
                                    @endphp
                                    <span class="break-words whitespace-pre-line">{{ $preview }}</span>
                                    @if ($isLong)
                                        <button class="text-indigo-600 hover:underline ml-2 text-sm"
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
                            <p class="font-['Manrope']">No {{ $showArchive ? 'archived' : 'previous' }} announcements</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Post New Announcements Modal -->
                    <div id="postAnnouncementModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
                        <div class="absolute inset-0 bg-black opacity-20"></div>
                        <div class="relative bg-white rounded-xl shadow-lg max-w-xl w-full p-6 z-10">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-lg">Post New Announcement</span>
                                <button onclick="closePostAnnouncementModal()" class="text-2xl text-gray-500 hover:text-gray-700">&times;</button>
                            </div>
                           <form id="announcementForm" action="{{ auth()->user()->role === 'super admin' ? route('super-admin.announcements.store') : route('announcements.store') }}" method="POST">
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
                                    <textarea name="content" id="contentInput" rows="4" maxlength="1000"
                                        class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                        placeholder="Enter announcement content"></textarea>
                                    <p id="contentError" class="text-red-500 text-sm mt-1" style="display: none;">Content is required.</p>
                                </div>
                                <!-- Schedule Section -->
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" id="scheduleCheckbox" name="schedule" class="form-checkbox">
                                        <span class="ml-2">Schedule</span>
                                    </label>
                                    <div id="scheduleFields" class="mt-2 space-x-2 hidden">
                                        <input type="date" id="scheduleDate" name="schedule_date"
                                            class="border rounded px-2 py-1" min="{{ date('Y-m-d') }}">
                                        <input type="time" id="scheduleTime" name="schedule_time"
                                            class="border rounded px-2 py-1" placeholder="Time (optional)">
                                    </div>
                                </div>
                                <!-- Audience Section -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Who can see this announcement?</label>
                                    <div class="flex items-center space-x-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="audience" value="all" checked class="form-radio" id="audienceAll">
                                            <span class="ml-2">All Student</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="audience" value="custom" class="form-radio" id="audienceCustom">
                                            <span class="ml-2">Custom</span>
                                        </label>
                                    </div>
                                    <div id="customAudienceDropdown" class="mt-2 hidden border rounded-lg px-2 py-2 max-h-40 overflow-y-auto bg-white">
                                        @foreach($users as $user)
                                            @if($user->role === 'student')
                                                <label class="flex items-center py-1 border-b last:border-b-0">
                                                    <input type="checkbox" name="audience_students[]" value="{{ $user->id }}" class="form-checkbox mr-2">
                                                    <span>{{ $user->username }}</span>
                                                </label>
                                            @endif
                                        @endforeach
                                        <p class="text-xs text-gray-500 mt-1">Check students who should see the announcement.</p>
                                    </div>
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

        {{-- Modal for full announcement --}}
    <div id="announcementModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div id="modalBackdrop" class="absolute inset-0 bg-black" style="opacity:0.2;"></div>
        <div class="relative bg-white rounded-xl shadow-lg max-w-xl w-full p-6 z-10">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span class="text-2xl text-red-500">📢</span>
                    <span id="modalLabel" class="font-semibold text-lg">Announcement</span>
                </div>
                <button onclick="closeAnnouncementModal()"
                    class="text-2xl text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <h3 id="modalTitle" class="text-lg font-bold mb-1"></h3>
            <div id="modalMeta" class="text-xs text-gray-500 mb-3"></div>
            <div id="modalContent" class="text-gray-700 whitespace-pre-line break-words"></div>
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
                <input type="hidden" id="originalScheduleDate">
                <input type="hidden" id="originalScheduleTime">
                <input type="hidden" id="originalAudience">
                <input type="hidden" id="originalAudienceStudents">
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" id="editTitle" name="title" maxlength="60"
                        class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                    <textarea id="editContent" name="content" rows="4" maxlength="1000"
                        class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" required></textarea>
                </div>
                <!-- Schedule Section -->
                <div class="mb-3">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="editScheduleCheckbox" name="schedule" class="form-checkbox">
                        <span class="ml-2">Schedule</span>
                    </label>
                    <div id="editScheduleFields" class="mt-2 space-x-2 hidden">
                        <input type="date" id="editScheduleDate" name="schedule_date" class="border rounded px-2 py-1">
                        <input type="time" id="editScheduleTime" name="schedule_time" class="border rounded px-2 py-1" placeholder="Time (optional)">
                    </div>
                </div>
                <!-- Audience Section -->
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Who can see this announcement?</label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="audience" value="all" class="form-radio" id="editAudienceAll">
                            <span class="ml-2">All Student</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="audience" value="custom" class="form-radio" id="editAudienceCustom">
                            <span class="ml-2">Custom</span>
                        </label>
                    </div>
                    <div id="editCustomAudienceDropdown" class="mt-2 hidden border rounded-lg px-2 py-2 max-h-40 overflow-y-auto bg-white">
                        @foreach($users as $user)
                            @if($user->role === 'student')
                                <label class="flex items-center py-1 border-b last:border-b-0">
                                    <input type="checkbox" name="audience_students[]" value="{{ $user->id }}" class="form-checkbox mr-2 editAudienceStudent">
                                    <span>{{ $user->username }}</span>
                            </label>
                            @endif
                        @endforeach
                        <p class="text-xs text-gray-500 mt-1">Check students who should see the announcement.</p>
                    </div>
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
                Are you sure you want to archive this Announcement? Once archived, it will be removed from your list and
                will no longer be visible there.
            </div>
            <div class="flex justify-end gap-2">
                <button onclick="closeArchiveModal()"
                    class="px-4 py-2 rounded border border-gray-300 text-gray-700 bg-white hover:bg-gray-100">Cancel</button>
                <form id="archiveForm" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 rounded bg-red-700 text-white hover:bg-red-800">Archive</button>
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
                <button onclick="closeRestoreModal()"
                    class="px-4 py-2 rounded border border-gray-300 text-gray-700 bg-white hover:bg-gray-100">Cancel</button>
                <form id="restoreForm" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 rounded bg-red-700 text-white hover:bg-red-800">Restore</button>
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
                <button onclick="closeDeleteModal()"
                    class="px-4 py-2 rounded border border-gray-300 text-gray-700 bg-white hover:bg-gray-100">Cancel</button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 rounded bg-red-700 text-white hover:bg-red-800">Delete</button>
                </form>
            </div>
        </div>
    </div>

        <!-- Table Header and Container -->
        <div class="overflow-hidden rounded-[25px] shadow bg-[#D9D9D9]" style="width: 100%; height: 400px; flex-shrink:0;">
            <table class="min-w-full bg-[#DAA520] text-white rounded-t-[24px] table-fixed">
                <thead>
                    <tr>
                        <!-- New Profile Picture Column -->
                        <th class="w-[10%] px-6 py-3">
                            <!-- Empty header for profile picture -->
                        </th>
                        <th class="w-[30%] px-6 py-3 text-left font-['Manrope'] text-[25px] font-bold">
                            <div class="flex items-center">
                                <span class="whitespace-nowrap">Name</span>
                                <div class="flex flex-col ml-2">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'username', 'direction' => 'asc']) }}"
                                        class="focus:outline-none hover:bg-gray-100/20 rounded-sm p-0.5 {{ $sortField === 'username' && $sortDirection === 'asc' ? 'text-yellow-300' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 12 12" fill="none">
                                            <path d="M6 0L11.1962 9H0.803848L6 0Z" fill="white" />
                                        </svg>
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'username', 'direction' => 'desc']) }}"
                                        class="focus:outline-none hover:bg-gray-100/20 rounded-sm p-0.5 -mt-1 {{ $sortField === 'username' && $sortDirection === 'desc' ? 'text-yellow-300' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 12 12" fill="none">
                                            <path d="M6 12L0.803848 3L11.1962 3L6 12Z" fill="white" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </th>
                        <th class="w-[30%] px-6 py-3 text-center font-['Manrope'] text-[25px] font-bold">
                            <div class="flex items-center justify-center">
                                <span class="whitespace-nowrap">Role</span>
                                <div class="flex flex-col ml-2">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'role_name', 'direction' => 'asc']) }}"
                                        class="focus:outline-none hover:bg-gray-100/20 rounded-sm p-0.5 {{ $sortField === 'role_name' && $sortDirection === 'asc' ? 'text-yellow-300' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 12 12" fill="none">
                                            <path d="M6 0L11.1962 9H0.803848L6 0Z" fill="white" />
                                        </svg>
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'role_name', 'direction' => 'desc']) }}"
                                        class="focus:outline-none hover:bg-gray-100/20 rounded-sm p-0.5 -mt-1 {{ $sortField === 'role_name' && $sortDirection === 'desc' ? 'text-yellow-300' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 12 12" fill="none">
                                            <path d="M6 12L0.803848 3L11.1962 3L6 12Z" fill="white" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </th>
                        <th class="w-[30%] px-6 py-3 text-right pr-40 font-['Manrope'] text-[25px] font-bold">
                            <div class="flex items-center justify-end">
                                <span class="whitespace-nowrap">Creation Date</span>
                                <div class="flex flex-col ml-2">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => 'asc']) }}"
                                        class="focus:outline-none hover:bg-gray-100/20 rounded-sm p-0.5 {{ $sortField === 'created_at' && $sortDirection === 'asc' ? 'text-yellow-300' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 12 12" fill="none">
                                            <path d="M6 0L11.1962 9H0.803848L6 0Z" fill="white" />
                                        </svg>
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => 'desc']) }}"
                                        class="focus:outline-none hover:bg-gray-100/20 rounded-sm p-0.5 -mt-1 {{ $sortField === 'created_at' && $sortDirection === 'desc' ? 'text-yellow-300' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 12 12" fill="none">
                                            <path d="M6 12L0.803848 3L11.1962 3L6 12Z" fill="white" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
                <!-- For fetching table contents from database -->
                <tbody class="divide-y divide-[#7A1212]/70">
                    @forelse ($users as $user)
                        <tr class="border-y-[0.1px] border-[#7A1212] bg-[#d9c698] hover:bg-[#DAA520] transition duration-300 cursor-pointer user-details-row h-20"
                            data-user="{{ $user->toJson() }}">
                            <!-- Profile Picture Cell -->
                            <td class="w-[10%] px-6 py-4 pl-15">
                                <div class="flex justify-center">
                                    <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200">
                                        @if (isset($userProfilePics) && $userProfilePics->has($user->id) && $userProfilePics[$user->id])
                                            <img src="{{ asset('storage/' . $userProfilePics[$user->id]) }}"
                                                alt="Profile" class="w-full h-full object-cover">
                                        @else
                                            <img src="{{ asset('images/dprofile.svg') }}" alt="Default Profile"
                                                class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Username Cell -->
                            <td class="w-[30%] px-6 py-4 text-left pl-4">
                                <div
                                    class="max-w-[400px] overflow-hidden text-ellipsis whitespace-nowrap text-[Lexend] text-[20px] text-black text-semibold">
                                    {{ $user->role === 'admin' ? $user->role_name : $user->username }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-[Lexend] text-[20px] text-black text-semibold">
                                @if($user->role === "admin")
                                    {{ ucfirst($user->role) }}
                                @else
                                    {{ $user->role_name }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right pr-45 text-[Lexend] text-[20px] text-black text-semibold">
                                {{ $user->created_at->format('F j, Y') }}
                            </td>

                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
            <!-- This shows when there are no users to be displayed -->
            @if ($users->isEmpty())
                <div class="bg-[#D9D9D9] h-[340px] flex-grow flex items-center justify-center text-gray-600 rounded-b-[25px] px-6"
                    style="height: 100%;">
                    <span class="font-['Manrope'] text-[20px] text-[#625B5BB2]">No added user.</span>
                </div>
            @endif
        </div> <!-- This closes the table container div -->

    <!-- Pagination controls -->
    <div class="flex justify-center bg-white py-4">
        <nav>
            <ul class="inline-flex items-center space-x-2">
                <li>
                    @if ($users->currentPage() == 1)
                        <span class="pagination-btn-first px-3 py-1 rounded-lg cursor-not-allowed opacity-50">
                            <
                        </span>
                    @else
                        <a href="{{ $users->url(1) }}"
                            class="pagination-btn-first px-3 py-1 rounded-lg hover:bg-gray-100">
                            <
                        </a>
                    @endif
                </li>

                @for ($i = 1; $i <= $users->lastPage(); $i++)
                    <li>
                        @if ($users->currentPage() == $i)
                            <span class="pagination-btn px-3 py-1 rounded-lg bg-[#7A1212] text-white">
                                {{ $i }}
                            </span>
                        @else
                            <a href="{{ $users->url($i) }}"
                                class="pagination-btn px-3 py-1 rounded-lg hover:bg-gray-100">
                                {{ $i }}
                            </a>
                        @endif
                    </li>
                @endfor

                <li>
                    @if ($users->currentPage() == $users->lastPage())
                        <span class="pagination-btn-last px-3 py-1 rounded-lg cursor-not-allowed opacity-50">
                            >
                        </span>
                    @else
                        <a href="{{ $users->url($users->lastPage()) }}"
                            class="pagination-btn-last px-3 py-1 rounded-lg hover:bg-gray-100">
                            >
                        </a>
                    @endif
                </li>
            </ul>
        </nav>
    </div>
    </div>

    <!-- Modal for Add User Button -->
    <div id="addUserModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">

        <!-- Modal Backdrop -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm add-user-backdrop"></div>

        <!-- Modal Content -->
        <div class="bg-white rounded-[25px] shadow-xl w-full max-w-lg relative z-50">

            <!-- Include the Add User component -->
            @include('super-admin.super-admin-component.AddUser')
        </div>
    </div>

    <!-- User Details Modal -->
    <div id="userDetailsModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">

        <!-- Modal Backdrop -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm user-details-backdrop"></div>

        <!-- Include the View Account Details component -->
        @include('super-admin.super-admin-component.viewAccDeets')

    </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal"
        class="fixed inset-0 flex items-center justify-center z-50 {{ session()->has('success') ? '' : 'hidden' }}">

        <!-- Modal Backdrop -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm success-modal-backdrop"></div>

        <!-- Modal Content -->
        <div class="bg-white rounded-[16px] shadow-xl w-full max-w-md relative z-50 p-6">
            <!-- <button id="closeSuccessModalBtn" type="button"
                            class="absolute top-6 right-5 text-gray-500 hover:text-[#7A1212] transition-colors duration-200 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button> -->
            <!-- Success Message -->
            <div class="text-center mb-6">
                <h3 id="successTitle" class="text-xl font-semibold text-gray-800">Account Successfully Added!</h3>
                <p id="successMessage" class="text-sm text-gray-500">{{ session('success') }}</p>
            </div>

            <!-- Okay Button -->
            <div class="flex justify-center">
                <button type="button" id="closeSuccessModalBtn"
                    class="bg-[#7A1212] hover:bg-red-800 text-white px-5 py-2 rounded-[14px] font-semibold font-[Lexend] transition duration-200 cursor-pointer">
                    Okay
                </button>
            </div>
        </div>
    </div>

    <!-- Edit User Deatils Modal -->
    <div id="editUserModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">

        <!-- Modal Backdrop -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm edit-user-backdrop"></div>

        <!-- Include the Edit User Account Details component -->
        @include('super-admin.super-admin-component.editUserDeets')
    </div>
    </div>

    <!-- Deactivate Account Confirmation Modal -->
    <div id="deactivateConfirmModal" class="fixed inset-0 flex items-center justify-center z-[60] hidden">
        <!-- Modal Backdrop -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm deactivate-confirm-backdrop"></div>

        <!-- Modal Content -->
        <div class="bg-white rounded-[16px] shadow-xl w-full max-w-md relative z-[70] p-6">
            <button id="closeDeactivateModalBtn" type="button"
                class="absolute top-6 right-5 text-gray-500 hover:text-[#7A1212] transition-colors duration-200 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Confirmation Message -->
            <div class="text-left mb-6">
                <h3 class="text-lg font-semibold text-gray-900 font-[Lexend]">Deactivate Account Confirmation</h3>
                <p class="text-sm text-gray-700">Are you sure you want to deactivate this account? All data will be
                    archived for record-keeping purposes.</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancelDeactivateBtn"
                    class="px-4 py-2 bg-gray-100 text-gray-800 rounded-[10px] border border-gray-300 font-[Lexend] hover:bg-gray-200 transition duration-200 cursor-pointer">
                    Cancel
                </button>
                <button type="button" id="confirmDeactivateBtn"
                    class="bg-[#7A1212] hover:bg-red-800 text-white px-4 py-2 rounded-[10px] font-normal font-[Lexend] inline-flex items-center hover:bg-red-700 transition duration-200 cursor-pointer">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <!-- Email Confirmation Modal for Deactivation -->
    <div id="emailConfirmModal" class="fixed inset-0 flex items-center justify-center z-[70] hidden">
        <!-- Modal Backdrop -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm email-confirm-backdrop"></div>

        <!-- Modal Content -->
        <div class="bg-white rounded-[16px] shadow-xl w-full max-w-md relative z-[80] p-6">
            <button id="closeEmailConfirmBtn" type="button"
                class="absolute top-6 right-5 text-gray-500 hover:text-[#7A1212] transition-colors duration-200 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Email Confirmation Form -->
            <div class="text-left">
                <h3 class="text-lg font-semibold text-gray-900 font-[Lexend] mb-2">Deactivate Account Confirmation</h3>
                <p class="text-sm text-gray-700 mb-4">Type the account email address to confirm</p>

                <div class="mb-4">
                    <label for="confirmEmail" class="block text-sm font-medium text-gray-700 mb-1 font-[Lexend]"></label>
                    <input type="email" id="confirmEmail"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212] transition duration-200"
                        placeholder="Enter email address">
                    <p id="emailError" class="mt-1 text-sm text-red-600 hidden">*Email address does not match.</p>
                </div>

                <!-- Action Button -->
                <div class="flex justify-end">
                    <button type="button" id="finalDeactivateBtn"
                        class="bg-[#7A1212] hover:bg-red-800 text-white px-4 py-2 rounded-[10px] font-normal font-[Lexend] inline-flex items-center transition duration-200 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        Deactivate Account
                    </button>
                </div>
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

        form.addEventListener('submit', function(e) {
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

            // Schedule validation
            const scheduleCheckbox = document.getElementById('scheduleCheckbox');
            const scheduleDate = document.getElementById('scheduleDate');
            const scheduleTime = document.getElementById('scheduleTime');

            if (scheduleCheckbox.checked) {
                const selectedDate = new Date(scheduleDate.value);
                const today = new Date();
                today.setHours(0,0,0,0);

                if (!scheduleDate.value || selectedDate < today) {
                    alert('Please select today or a future date for the schedule.');
                    valid = false;
                }
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
        function openEditModal(id, title, content, scheduleDate = '', scheduleTime = '', audience = 'all', audienceStudents = '[]') {
            // Always parse audienceStudents as JSON
            try {
                audienceStudents = JSON.parse(audienceStudents) || [];
            } catch {
                audienceStudents = [];
            }

            document.getElementById('editAnnouncementId').value = id;
            document.getElementById('editTitle').value = title;
            document.getElementById('editContent').value = content;
            document.getElementById('originalTitle').value = title;
            document.getElementById('originalContent').value = content;

            // Store original schedule and audience
            document.getElementById('originalScheduleDate').value = scheduleDate || '';
            document.getElementById('originalScheduleTime').value = scheduleTime || '';
            document.getElementById('originalAudience').value = audience;
            document.getElementById('originalAudienceStudents').value = JSON.stringify(audienceStudents);

            // Schedule
            const scheduleCheckbox = document.getElementById('editScheduleCheckbox');
            const scheduleFields = document.getElementById('editScheduleFields');
            const scheduleDateInput = document.getElementById('editScheduleDate');
            const scheduleTimeInput = document.getElementById('editScheduleTime');
            if (scheduleDate) {
                scheduleCheckbox.checked = true;
                scheduleFields.classList.remove('hidden');
                scheduleDateInput.value = scheduleDate;
                scheduleTimeInput.value = scheduleTime || '';
            } else {
                scheduleCheckbox.checked = false;
                scheduleFields.classList.add('hidden');
                scheduleDateInput.value = '';
                scheduleTimeInput.value = '';
            }

            // Audience
            if (audience === 'all') {
                document.getElementById('editAudienceAll').checked = true;
                document.getElementById('editCustomAudienceDropdown').classList.add('hidden');
            } else {
                document.getElementById('editAudienceCustom').checked = true;
                document.getElementById('editCustomAudienceDropdown').classList.remove('hidden');
            }
            // Uncheck all first
            document.querySelectorAll('.editAudienceStudent').forEach(cb => cb.checked = false);
            // Check those in audienceStudents
            audienceStudents.forEach(id => {
                const cb = document.querySelector('.editAudienceStudent[value="' + id + '"]');
                if (cb) cb.checked = true;
            });

            document.getElementById('editAnnouncementModal').classList.remove('hidden');
            document.getElementById('editAnnouncementForm').action = `/announcements/${id}`; ////////etooo walang super admin
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

            const originalScheduleDate = document.getElementById('originalScheduleDate').value;
            const originalScheduleTime = document.getElementById('originalScheduleTime').value;
            const currentScheduleCheckbox = document.getElementById('editScheduleCheckbox').checked;
            const currentScheduleDate = document.getElementById('editScheduleDate').value;
            const currentScheduleTime = document.getElementById('editScheduleTime').value;

            const originalAudience = document.getElementById('originalAudience').value;
            const originalAudienceStudents = JSON.parse(document.getElementById('originalAudienceStudents').value || '[]');
            const currentAudience = document.getElementById('editAudienceAll').checked ? 'all' : 'custom';
            const currentAudienceStudents = Array.from(document.querySelectorAll('.editAudienceStudent:checked')).map(cb => cb.value);

            // Compare schedule
            let scheduleChanged = false;
            if (originalScheduleDate || currentScheduleDate) {
                scheduleChanged = (
                    (originalScheduleDate !== currentScheduleDate) ||
                    (originalScheduleTime !== currentScheduleTime) ||
                    (Boolean(originalScheduleDate) !== currentScheduleCheckbox)
                );
            }

            // Compare audience
            let audienceChanged = false;
            if (originalAudience !== currentAudience) {
                audienceChanged = true;
            } else if (currentAudience === 'custom') {
                // Compare arrays
                const orig = originalAudienceStudents.slice().sort();
                const curr = currentAudienceStudents.slice().sort();
                if (orig.length !== curr.length || !orig.every((v, i) => v == curr[i])) {
                    audienceChanged = true;
                }
            }

            if (
                originalTitle === currentTitle &&
                originalContent === currentContent &&
                !scheduleChanged &&
                !audienceChanged
            ) {
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
            form.action = `/super-admin/announcements/${announcementId}/archive`;
            document.getElementById('archiveConfirmModal').classList.remove('hidden');
        }

        function closeArchiveModal() {
            document.getElementById('archiveConfirmModal').classList.add('hidden');
        }

        // Open Restore Modal
        function openRestoreModal(announcementId) {
            const form = document.getElementById('restoreForm');
            form.action = `/super-admin/announcements/${announcementId}/restore`;
            document.getElementById('restoreConfirmModal').classList.remove('hidden');
        }
        // Close Restore Modal
        function closeRestoreModal() {
            document.getElementById('restoreConfirmModal').classList.add('hidden');
        }

        // Open Delete Modal
        function openDeleteModal(announcementId) {
            const form = document.getElementById('deleteForm');
            form.action = `/super-admin/announcements/${announcementId}/delete`;
            document.getElementById('deleteConfirmModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteConfirmModal').classList.add('hidden');
        }

        // Modal functions for Post New Announcement
        function openPostAnnouncementModal() {
            document.getElementById('postAnnouncementModal').classList.remove('hidden');
        }
        function closePostAnnouncementModal() {
            document.getElementById('postAnnouncementModal').classList.add('hidden');
        }

        // Schedule toggle
        document.getElementById('scheduleCheckbox').addEventListener('change', function() {
            document.getElementById('scheduleFields').classList.toggle('hidden', !this.checked);
        });

        // Audience toggle
        document.getElementById('audienceAll').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('customAudienceDropdown').classList.add('hidden');
            }
        });
        document.getElementById('audienceCustom').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('customAudienceDropdown').classList.remove('hidden');
            }
        });

        // Schedule toggle for edit modal
        document.getElementById('editScheduleCheckbox').addEventListener('change', function() {
            document.getElementById('editScheduleFields').classList.toggle('hidden', !this.checked);
        });

        // Audience toggle for edit modal
        document.getElementById('editAudienceAll').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('editCustomAudienceDropdown').classList.add('hidden');
            }
        });
        document.getElementById('editAudienceCustom').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('editCustomAudienceDropdown').classList.remove('hidden');
            }
        });
    </script>
    @include('super-admin.super-admin-component.activityLogModal')
    @vite(['resources/js/super-admin/modal-base.js', 'resources/js/super-admin/main.js', 'resources/js/super-admin/add-user.js', 'resources/js/super-admin/user-details.js', 'resources/js/super-admin/edit-user.js', 'resources/js/super-admin/deactivate-user.js', 'resources/js/super-admin/success-modal.js', 'resources/js/super-admin/activity-log-modal.js'])
@endsection
