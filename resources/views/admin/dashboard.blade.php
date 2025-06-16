@extends('base')


@section('content')

    @include('components.adminSidebarComponent')
    <div id="main-content" class="flex flex-col min-h-screen ml-[20%] transition-all duration-300 bg-[#F2F4F7]">
        @include('components.adminNavBarComponent')
        <div class="flex-grow p-6 space-y-6">
           <h5 class="font-['Manrope'] font-extrabold text-[23px] md:text-[20px] lg:text-[30px] mb-1">
                Dashboard
            </h5>
            @if (session('success'))
                <div id="Toast"
                    class="fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white border-l-4 border-green-400 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50"
                    role="alert">
                    <div class="w-full flex justify-between">
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('images/successful.svg') }}" alt="Success Icon" id="docTypeIcon"
                                class="">
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
           <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-xl shadow-md flex justify-between items-center">
                    <div>
                        <p class="text-[10px] xs:text-xs sm:text-sm md:text-base text-gray-500">Pending Documents</p>
                        <div class="text-base xs:text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold">{{ $pendingCount }}</div>
                    </div>
                    <img src="{{ asset('images/pendingicon.svg') }}" class="w-6 h-6 xs:w-7 xs:h-7 sm:w-8 sm:h-8 md:w-10 md:h-10 max-w-full h-auto" alt="Pending Documents">
                </div>
                <div class="bg-white p-4 rounded-xl shadow-md flex justify-between items-center">
                    <div>
                        <p class="text-[10px] xs:text-xs sm:text-sm md:text-base text-gray-500">Under Review</p>
                        <div class="text-base xs:text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold">{{ $reviewCount }}</div>
                    </div>
                    <img src="{{ asset('images/reviewicon.svg') }}" class="w-6 h-6 xs:w-7 xs:h-7 sm:w-8 sm:h-8 md:w-10 md:h-10 max-w-full h-auto" alt="Under Review">
                </div>
                <div class="bg-white p-4 rounded-xl shadow-md flex justify-between items-center">
                    <div>
                        <p class="text-[10px] xs:text-xs sm:text-sm md:text-base text-gray-500">Approved Documents</p>
                        <div class="text-base xs:text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold">{{ $approvedCount }}</div>
                    </div>
                    <img src="{{ asset('images/approvedicon.svg') }}" class="w-6 h-6 xs:w-7 xs:h-7 sm:w-8 sm:h-8 md:w-10 md:h-10 max-w-full h-auto" alt="Approved Documents">
                </div>
                <div class="bg-white p-4 rounded-xl shadow-md flex justify-between items-center">
                    <div>
                        <p class="text-[10px] xs:text-xs sm:text-sm md:text-base text-gray-500">Total Documents</p>
                        <div class="text-base xs:text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold">{{ $totalCount }}</div>
                    </div>
                    <img src="{{ asset('images/totaldocicon.svg') }}" class="w-6 h-6 xs:w-7 xs:h-7 sm:w-8 sm:h-8 md:w-10 md:h-10 max-w-full h-auto" alt="Total Documents">
                </div>
            </div>

                <!-- Announcement and Documents Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <!-- Left side: Announcements + Recent Documents stacked vertically -->
                    <div class="lg:col-span-2 flex flex-col gap-4">
                        <!-- Latest Announcements -->
                        <div class="bg-white rounded-xl shadow-md p-4 flex flex-col">
                            <div class="flex items-center justify-between mb-2">
                                <h2 class="text-[15px] sm:text-[17px] md:text-[20px] lg:text-[22px] font-semibold flex items-center gap-2">
                                    <img src="{{ asset('images/annc.svg') }}" alt="Announcements" class="w-6 h-6 sm:w-7 sm:h-7 inline-block align-middle max-w-full" />
                                    Announcements
                                </h2>
                                <button onclick="openPostAnnouncementModal()" title="Post New Announcement"
                                    class="p-2 rounded-full hover:bg-indigo-50 transition">
                                    <img src="{{ asset('images/add_annc.svg') }}" alt="Add Announcement" class="w-7 h-7">
                                </button>
                            </div>
                            <div class="max-h-[220px] overflow-y-auto pr-1 flex-1">
                                @if ($latestAnnouncements->count())
                                    @foreach ($latestAnnouncements as $announcement)
                                        <div class="mb-4 pb-4 border-b border-gray-300 relative">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $announcement->title }}
                                                </h3>
                                                <!-- Ellipsis Button -->
                                                <div class="relative">
                                                    <button
                                                        class="ml-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none transition"
                                                        onclick="toggleMenu('menu-{{ $announcement->id }}')" type="button"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        <span class="sr-only">Open menu</span>
                                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor"
                                                            viewBox="0 0 20 20">
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
                                                            onclick="tryOpenEditModal(
                                                                {{ $announcement->id }},
                                                                `{{ addslashes($announcement->title) }}`,
                                                                `{{ addslashes(e($announcement->content)) }}`,
                                                                `{{ $announcement->deadline ? \Carbon\Carbon::parse($announcement->deadline)->format('Y-m-d') : '' }}`,
                                                                `{{ $announcement->deadline ? \Carbon\Carbon::parse($announcement->deadline)->format('H:i') : '' }}`,
                                                                `{{ $announcement->audience ?? 'all' }}`,
                                                                '{{ htmlspecialchars(json_encode($announcement->audience_students ?? []), ENT_QUOTES, "UTF-8") }}',
                                                                {{ $announcement->user_id }}
                                                            )"
                                                            type="button">
                                                            Edit
                                                        </button>
                                                        <form
                                                            action="{{ route('admin.announcements.archive', $announcement->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Move this announcement to archive?');">
                                                            @csrf
                                                            <button
                                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50 transition border-t border-gray-100 whitespace-nowrap"
                                                                type="button"
                                                                onclick="openArchiveModal({{ $announcement->id }}, {{ $announcement->user_id }})">
                                                                Move to Archive
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-500 mb-1">
                                                Posted by {{ $announcement->user->role_name }} on
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
                                                    $meta = "Posted by {$announcement->user->role_name} on {$announcement->created_at->format('F j, Y',)}";
                                                @endphp
                                                <span class="break-words whitespace-pre-line">{{ $preview }}</span>
                                                @if ($isLong)
                                                    <button class="text-[#7A1212] hover:underline ml-2 text-sm"
                                                        onclick="showAnnouncementModal(
                                                    `{{ addslashes($announcement->title) }}`,
                                                    `{{ addslashes(e($announcement->content)) }}`,
                                                    `Posted by {{ addslashes($announcement->user->role_name) }} on {{ $announcement->created_at->format('F j, Y') }}`,
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
                        <!-- Recent Documents -->
                        <div class="bg-white rounded-xl shadow-md p-4 flex-1 flex flex-col">
                            <h2 class="text-lg font-semibold mb-2">Recent Documents</h2>
                            @if($recentDocuments->count())
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm text-left bg-white rounded-xl">
                                        <thead>
                                            <tr class="border-b">
                                                <th class="px-3 py-2 font-semibold">Tag</th>
                                                <th class="px-3 py-2 font-semibold">Organization</th>
                                                <th class="px-3 py-2 font-semibold">Title</th>
                                                <th class="px-3 py-2 font-semibold">Date</th>
                                                <th class="px-3 py-2 font-semibold">Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentDocuments as $doc)
                                                <tr class="border-b">
                                                    <td class="px-3 py-2 font-extrabold text-black">{{ $doc->control_tag }}</td>
                                                    <td class="px-3 py-2 max-w-[180px] truncate" title="{{ $doc->user->username ?? '' }}">
                                                        @if(is_null($doc->user_id))
                                                            Guest Student
                                                        @else
                                                            {{ $doc->user->username ?? 'Unknown' }}
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-2 max-w-[200px] truncate" title="{{ $doc->subject }}">
                                                        {{ $doc->subject }}
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        {{ \Carbon\Carbon::parse($doc->created_at)->format('n/j/Y') }}
                                                    </td>
                                                    <td class="px-3 py-2">{{ $doc->type }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-gray-500 py-8">
                                    <img src="{{ asset('images/recentdoc.png') }}" alt="No recent documents"
                                        class="w-40 mx-auto mb-2 opacity-80">
                                    <p>No recent documents at the moment</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right side: Previous/Archived Announcements -->
                    <div class="bg-white rounded-xl shadow-md p-4 flex flex-col h-full"
                         style="min-height: 0; height: 100%;">
                        <div class="flex justify-between items-center mb-2">
                            <h2 class="text-lg font-semibold">
                                {{ $showArchive ? 'Archived Announcements' : 'Previous Announcements' }}
                            </h2>
                            @if ($showArchive)
                                <a href="{{ route('admin.dashboard') }}"
                                    class="text-gray-600 hover:underline text-sm font-medium">Previous</a>
                            @else
                                <a href="{{ route('admin.announcementArchive') }}"
                                    class="text-gray-600 hover:underline text-sm font-medium">Archive</a>
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
                                                            onclick="openRestoreModal({{ $announcement->id }}, {{ $announcement->user_id }})">
                                                            Restore
                                                        </button>
                                                        <button
                                                            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition whitespace-nowrap"
                                                            type="button"
                                                            onclick="openDeleteModal({{ $announcement->id }}, {{ $announcement->user_id }})">
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
                                                                onclick="openArchiveModal({{ $announcement->id }}, {{ $announcement->user_id }})">
                                                                Move to Archive
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-500 mb-2">
                                            Posted by {{ $announcement->user->role_name }} on
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
                                                <button class="text-[#7B2323] hover:underline ml-2 text-sm"
                                                    onclick="showAnnouncementModal(
                                    `{{ addslashes($announcement->title) }}`,
                                    `{{ addslashes(e($announcement->content)) }}`,
                                    `Posted by {{ addslashes($announcement->user->role_name) }} on {{ $announcement->created_at->format('F j, Y g:i A') }}`,
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

                   <!-- Post New Announcements Modal -->
                 <div id="postAnnouncementModal" class="fixed inset-0 z-50 hidden">
                <!-- Simple black overlay without blur -->
                <div class="fixed inset-0 bg-black opacity-75"></div>         
                        <!-- Modal content wrapper -->
                        <div class="relative flex items-center justify-center min-h-screen p-4">
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
                                <!-- ...existing modal header... -->
                                <div class="flex items-center mb-4 justify-between">
                                    <div class="flex items-center">
                                    <h2 class="text-lg font-semibold mr-2">Post announcement</h2>
                                    <!-- Info Icon Tooltip -->
                                    <button type="button" id="announcementInfoBtn" class="ml-1 focus:outline-none">
                                        <img src="{{ asset('images/tooltip.png') }}" alt="Info" class="w-6 h-6 inline-block align-middle" />
                                    </button>
                                </div>
                                <button onclick="closePostAnnouncementModal()" type="button" class="text-2xl text-gray-500 hover:text-gray-700 ml-2">&times;</button>
                            </div>
                            <!-- Tooltip Content -->
                            <div id="announcementInfoTooltip" class="hidden absolute top-12 left-1/2 transform -translate-x-1/2 w-80 sm:w-96 max-w-xs sm:max-w-md bg-gray-800 text-white text-sm rounded-lg shadow-lg p-4 z-50 break-words">
                                <div class="mb-2 font-semibold">You can now create and share announcements with your team or organization!</div>
                                <ol class="list-decimal list-inside mb-2">
                                    <li>
                                        <span class="font-semibold">Regular Announcements</span> – Ideal for general updates, reminders, or important messages.
                                    </li>
                                    <li>
                                        <span class="font-semibold">Announcements with Deadlines</span> – Perfect for time-sensitive updates like submission schedules, event registrations, or task deadlines. These announcements allow you to set a due date to help users stay on track.
                                    </li>
                                </ol>
                                <span>Start posting announcements to keep everyone informed and organized!</span>
                            </div>
                            <form id="announcementForm" action="{{ route('announcements.store') }}" method="POST" class="show-loader-on-submit space-y-4">
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
                                    <label class="inline-flex items-center mb-2">
                                        <input type="checkbox" id="scheduleCheckbox" name="schedule" class="form-checkbox">
                                        <span class="ml-2">Set Due Date</span>
                                    </label>
                                    <div id="scheduleFields" class="hidden">
                                        <div class="flex space-x-4">
                                            <div class="flex-1">
                                                <label for="scheduleDate" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                                <input type="date" id="scheduleDate" name="schedule_date"
                                                    class="w-full border rounded px-2 py-1" min="{{ date('Y-m-d') }}">
                                            </div>
                                            <div class="flex-1">
                                                <label for="scheduleTime" class="block text-sm font-medium text-gray-700 mb-1">Time (Optional)</label>
                                                <input type="time" id="scheduleTime" name="schedule_time"
                                                    class="w-full border rounded px-2 py-1">
                                            </div>
                                        </div>
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
                                        class="px-6 py-2 text-white rounded-lg transition hover:opacity-90"
                                        style="background-color: #7A1212;">
                                        Post Announcement
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        @include('components.footer')
    </div>
   
    {{-- Modal for full announcement --}}
<div id="announcementModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div id="modalBackdrop" class="absolute inset-0 bg-black" style="opacity:0.2;"></div>
    <div class="relative bg-white rounded-xl shadow-lg max-w-xl w-full p-6 z-10">
        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/annc.svg') }}" alt="Announcement" class="w-8 h-8 inline-block align-middle" />
                <span id="modalLabel" class="font-semibold text-lg">Announcement</span>
            </div>
            <button onclick="closeAnnouncementModal()"
                class="text-2xl text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <!-- Modal Title -->
        <h3 id="modalTitle" class="text-lg font-bold mb-1"></h3>
        <!-- Modal Meta -->
        <div id="modalMeta" class="text-xs text-gray-500 mb-3"></div>
        <!-- Modal Content -->
        <div id="modalContent" class="text-gray-700 whitespace-pre-line break-words"></div>
    </div>
</div>

    {{-- Edit Announcement Modal --}}
    <div id="editAnnouncementModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="absolute inset-0 bg-black opacity-75"></div>
        <div class="relative bg-white rounded-xl shadow-lg max-w-xl w-full p-6 z-10">
            <div class="flex items-center justify-between mb-2">
                <span class="font-semibold text-lg">Edit Announcement</span>
                <button onclick="closeEditModal()" class="text-2xl text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <form id="editAnnouncementForm" method="POST" class="show-loader-on-submit">
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
                <label class="inline-flex items-center mb-2">
                    <input type="checkbox" id="editScheduleCheckbox" name="schedule" class="form-checkbox">
                    <span class="ml-2">Set Due Date</span>
                </label>
                <div id="editScheduleFields" class="hidden">
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label for="editScheduleDate" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" id="editScheduleDate" name="schedule_date" 
                                class="w-full border rounded px-2 py-1">
                        </div>
                        <div class="flex-1">
                            <label for="editScheduleTime" class="block text-sm font-medium text-gray-700 mb-1">Time (Optional)</label>
                            <input type="time" id="editScheduleTime" name="schedule_time" 
                                class="w-full border rounded px-2 py-1">
                        </div>
                    </div>
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
                        class="px-6 py-2 text-white rounded-lg transition duration-200 hover:bg-[#a43c3c]"
                        style="background-color: #7A1212;">
                        Save
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
        <div class="absolute inset-0 bg-black opacity-75"></div>
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
        <div class="absolute inset-0 bg-black opacity-75"></div>
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
        <div class="absolute inset-0 bg-black opacity-75"></div>
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
    
    <script>
        function decodeHtmlEntities(str) {
            var txt = document.createElement('textarea');
            txt.innerHTML = str;
            return txt.value;
        }

        function showAnnouncementModal(title, content, meta = '', type = 'announcement') {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalContent').textContent = decodeHtmlEntities(content);
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

            // Schedule validation (must not be in the past, and if today, time is required and must be in the future)
            const scheduleCheckbox = document.getElementById('scheduleCheckbox');
            const scheduleDate = document.getElementById('scheduleDate');
            const scheduleTime = document.getElementById('scheduleTime');

            if (scheduleCheckbox.checked) {
                if (!scheduleDate.value) {
                    alert('Please select today or a future date for the schedule.');
                    valid = false;
                } else {
                    const now = new Date();
                    now.setSeconds(0,0); // ignore seconds/milliseconds for comparison

                    const selectedDate = new Date(scheduleDate.value);
                    selectedDate.setHours(0,0,0,0);

                    // If selected date is today, require time and it must be at least 1 hour from now
                    if (
                        selectedDate.getFullYear() === now.getFullYear() &&
                        selectedDate.getMonth() === now.getMonth() &&
                        selectedDate.getDate() === now.getDate()
                    ) {
                        if (!scheduleTime.value) {
                            alert('Please specify a time for today\'s schedule.');
                            valid = false;
                        } else {
                            // Combine date and time for accurate comparison
                            const selectedDateTime = new Date(scheduleDate.value + 'T' + scheduleTime.value);
                            const oneHourLater = new Date(now.getTime() + 60 * 60 * 1000);
                            if (selectedDateTime < oneHourLater) {
                                alert('Please select a time at least 1 hour from now.');
                                valid = false;
                            }
                        }
                    } else {
                        // Not today: must be in the future
                        if (selectedDate < now) {
                            alert('Please select today or a future date for the schedule.');
                            valid = false;
                        }
                    }
                }
            }

            // Audience validation
            const audienceCustom = document.getElementById('audienceCustom');
            if (audienceCustom.checked) {
                const checkedStudents = document.querySelectorAll('#customAudienceDropdown input[type="checkbox"]:checked');
                if (checkedStudents.length === 0) {
                    alert('Please select at least one student for the custom audience.');
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
    // Get current user info from Blade
        const currentUserId = {{ json_encode(session('currentUserId', auth()->id())) }};
        const currentUserRole = {!! json_encode(session('currentUserRole', auth()->user()->role)) !!};

        function tryOpenEditModal(id, title, content, scheduleDate = '', scheduleTime = '', audience = 'all', audienceStudents = '[]', announcementUserId = null) {
    // Only allow if current user is super admin or is the owner
    if (currentUserRole !== 'super admin' && currentUserId != announcementUserId) {
        // Show warning toast
        const toast = document.getElementById('EditNotAllowedToast');
        if (toast) {
            toast.style.display = 'flex';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        } else {
            alert('You are not authorized to edit this announcement.');
        }
        return;
    }

            // Otherwise, open the edit modal
            openEditModal(id, title, content, scheduleDate, scheduleTime, audience, audienceStudents);
        }
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
            document.getElementById('editContent').value  = decodeHtmlEntities(content);
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

           // Audience handling
            if (audience === 'all') {
                document.getElementById('editAudienceAll').checked = true;
                document.getElementById('editCustomAudienceDropdown').classList.add('hidden');
            } else {
                document.getElementById('editAudienceCustom').checked = true;
                document.getElementById('editCustomAudienceDropdown').classList.remove('hidden');
                
                // Remove any existing reminder messages first
                const existingMessages = document.querySelectorAll('.audience-reminder');
                existingMessages.forEach(msg => msg.remove());
                
                // Add new reminder message
                const customAudienceDropdown = document.getElementById('editCustomAudienceDropdown');
                const messageElement = document.createElement('div');
                messageElement.className = 'text-yellow-600 text-sm mb-2 audience-reminder'; // Added audience-reminder class
                messageElement.textContent = 'Please reselect the audience for confirmation';
                customAudienceDropdown.insertBefore(messageElement, customAudienceDropdown.firstChild);
            }
            // Uncheck all first
            document.querySelectorAll('.editAudienceStudent').forEach(cb => cb.checked = false);
            // Check those in audienceStudents
            audienceStudents.forEach(id => {
                const cb = document.querySelector('.editAudienceStudent[value="' + String(id) + '"]');
                if (cb) cb.checked = true;
            });

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

            const originalScheduleDate = document.getElementById('originalScheduleDate').value;
            const originalScheduleTime = document.getElementById('originalScheduleTime').value;
            const currentScheduleCheckbox = document.getElementById('editScheduleCheckbox').checked;
            const currentScheduleDate = document.getElementById('editScheduleDate').value;
            const currentScheduleTime = document.getElementById('editScheduleTime').value;

            const originalAudience = document.getElementById('originalAudience').value;
            const originalAudienceStudents = JSON.parse(document.getElementById('originalAudienceStudents').value || '[]');
            const currentAudience = document.getElementById('editAudienceAll').checked ? 'all' : 'custom';
            const currentAudienceStudents = Array.from(document.querySelectorAll('.editAudienceStudent:checked')).map(cb => cb.value);

    // Audience validation for edit modal
    if (document.getElementById('editAudienceCustom').checked) {
        if (currentAudienceStudents.length === 0) {
            alert('Please select at least one student for the custom audience.');
            e.preventDefault();
            return;
        }
    }

    // Validate schedule (must not be in the past, and if today, time is required and must be in the future)
    if (currentScheduleCheckbox) {
        if (!currentScheduleDate) {
            alert('Please select today or a future date for the schedule.');
            e.preventDefault();
            return;
        }
        const now = new Date();
        now.setSeconds(0,0); // ignore seconds/milliseconds for comparison

        const selectedDate = new Date(currentScheduleDate);
        selectedDate.setHours(0,0,0,0);

        // If selected date is today, require time and it must be in the future
        if (
            selectedDate.getFullYear() === now.getFullYear() &&
            selectedDate.getMonth() === now.getMonth() &&
            selectedDate.getDate() === now.getDate()
        ) {
            if (!currentScheduleTime) {
                alert('Please specify a time for today\'s schedule.');
                e.preventDefault();
                return;
            }
            // Combine date and time for accurate comparison
            const selectedDateTime = new Date(currentScheduleDate + 'T' + currentScheduleTime);
            if (selectedDateTime <= now) {
                alert('Please select a future time for today\'s schedule.');
                e.preventDefault();
                return;
            }
        } else {
            // Not today: must be in the future
            if (selectedDate < now) {
                alert('Please select today or a future date for the schedule.');
                e.preventDefault();
                return;
            }
        }
        
    }

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
        function openArchiveModal(announcementId, announcementUserId = null) {
            if (currentUserRole !== 'super admin' && currentUserId != announcementUserId) {
                const toast = document.getElementById('ArchiveNotAllowedToast');
                toast.style.display = 'flex';
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 3000);
                return;
            }
            const form = document.getElementById('archiveForm');
            form.action = `/admin/announcements/${announcementId}/archive`;
            document.getElementById('archiveConfirmModal').classList.remove('hidden');
        }
        function closeArchiveModal() {
            document.getElementById('archiveConfirmModal').classList.add('hidden');
        }

        // Open Restore Modal
        function openRestoreModal(announcementId, announcementUserId = null) {
            if (currentUserRole !== 'super admin' && currentUserId != announcementUserId) {
                const toast = document.getElementById('RestoreNotAllowedToast');
                toast.style.display = 'flex';
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 3000);
                return;
            }
            const form = document.getElementById('restoreForm');
            form.action = `/admin/announcements/${announcementId}/restore`;
            document.getElementById('restoreConfirmModal').classList.remove('hidden');
        }
        // Close Restore Modal
        function closeRestoreModal() {
            document.getElementById('restoreConfirmModal').classList.add('hidden');
        }

        // Open Delete Modal
        function openDeleteModal(announcementId, announcementUserId = null) {
            if (currentUserRole !== 'super admin' && currentUserId != announcementUserId) {
                const toast = document.getElementById('DeleteNotAllowedToast');
                toast.style.display = 'flex';
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 3000);
                return;
            }
            const form = document.getElementById('deleteForm');
            form.action = `/admin/announcements/${announcementId}/delete`;
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


// --- Draft Save/Restore Logic ---
const DRAFT_KEY_POST = 'announcementDraft';
const DRAFT_KEY_EDIT = 'editAnnouncementDraft';

function saveDraft(type) {
    if (type === 'post') {
        localStorage.setItem(DRAFT_KEY_POST, JSON.stringify({
            title: titleInput.value,
            content: contentInput.value,
            schedule: document.getElementById('scheduleCheckbox').checked,
            schedule_date: document.getElementById('scheduleDate').value,
            schedule_time: document.getElementById('scheduleTime').value,
            audience: document.getElementById('audienceAll').checked ? 'all' : 'custom',
            audience_students: Array.from(document.querySelectorAll('#customAudienceDropdown input[type="checkbox"]:checked')).map(cb => cb.value)
        }));
    } else if (type === 'edit') {
        localStorage.setItem(DRAFT_KEY_EDIT, JSON.stringify({
            id: document.getElementById('editAnnouncementId').value,
            title: document.getElementById('editTitle').value,
            content: document.getElementById('editContent').value,
            schedule: document.getElementById('editScheduleCheckbox').checked,
            schedule_date: document.getElementById('editScheduleDate').value,
            schedule_time: document.getElementById('editScheduleTime').value,
            audience: document.getElementById('editAudienceAll').checked ? 'all' : 'custom',
            audience_students: Array.from(document.querySelectorAll('.editAudienceStudent:checked')).map(cb => cb.value)
        }));
    }
}

function restoreDraft(type) {
    let draft = null;
    if (type === 'post') {
        draft = localStorage.getItem(DRAFT_KEY_POST);
        if (draft) {
            draft = JSON.parse(draft);
            titleInput.value = draft.title || '';
            contentInput.value = draft.content || '';
            document.getElementById('scheduleCheckbox').checked = !!draft.schedule;
            document.getElementById('scheduleFields').classList.toggle('hidden', !draft.schedule);
            document.getElementById('scheduleDate').value = draft.schedule_date || '';
            document.getElementById('scheduleTime').value = draft.schedule_time || '';
            if (draft.audience === 'all') {
                document.getElementById('audienceAll').checked = true;
                document.getElementById('customAudienceDropdown').classList.add('hidden');
            } else {
                document.getElementById('audienceCustom').checked = true;
                document.getElementById('customAudienceDropdown').classList.remove('hidden');
            }
            // Uncheck all first
            document.querySelectorAll('#customAudienceDropdown input[type="checkbox"]').forEach(cb => cb.checked = false);
            // Check those in audience_students
            (draft.audience_students || []).forEach(id => {
                const cb = document.querySelector('#customAudienceDropdown input[type="checkbox"][value="' + String(id) + '"]');
                if (cb) cb.checked = true;
            });
        }
    } else if (type === 'edit') {
        draft = localStorage.getItem(DRAFT_KEY_EDIT);
        if (draft) {
            draft = JSON.parse(draft);
            // Only restore if editing the same announcement
            if (draft.id == document.getElementById('editAnnouncementId').value) {
                document.getElementById('editTitle').value = draft.title || '';
                document.getElementById('editContent').value = draft.content || '';
                document.getElementById('editScheduleCheckbox').checked = !!draft.schedule;
                document.getElementById('editScheduleFields').classList.toggle('hidden', !draft.schedule);
                document.getElementById('editScheduleDate').value = draft.schedule_date || '';
                document.getElementById('editScheduleTime').value = draft.schedule_time || '';
                if (draft.audience === 'all') {
                    document.getElementById('editAudienceAll').checked = true;
                    document.getElementById('editCustomAudienceDropdown').classList.add('hidden');
                } else {
                    document.getElementById('editAudienceCustom').checked = true;
                    document.getElementById('editCustomAudienceDropdown').classList.remove('hidden');
                }
                // Uncheck all first
                document.querySelectorAll('.editAudienceStudent').forEach(cb => cb.checked = false);
                // Check those in audience_students
                (draft.audience_students || []).forEach(id => {
                    const cb = document.querySelector('.editAudienceStudent[value="' + String(id) + '"]');
                    if (cb) cb.checked = true;
                });
            }
        }
    }
}

function clearDraft(type) {
    if (type === 'post') localStorage.removeItem(DRAFT_KEY_POST);
    if (type === 'edit') localStorage.removeItem(DRAFT_KEY_EDIT);
}

// --- Modal State & Change Detection ---
let isPostModalOpen = false;
let isEditModalOpen = false;
let hasPostChanges = false;
let hasEditChanges = false;

// Watch for changes in post modal
['input', 'change'].forEach(evt => {
    form.addEventListener(evt, () => {
        hasPostChanges = true;
        saveDraft('post');
    });
});

// Watch for changes in edit modal
['input', 'change'].forEach(evt => {
    document.getElementById('editAnnouncementForm').addEventListener(evt, () => {
        hasEditChanges = true;
        saveDraft('edit');
    });
});

// Override open/close modal functions
const originalOpenPostAnnouncementModal = openPostAnnouncementModal;
openPostAnnouncementModal = function() {
    isPostModalOpen = true;
    hasPostChanges = false;
    originalOpenPostAnnouncementModal();
    restoreDraft('post');
};
const originalClosePostAnnouncementModal = closePostAnnouncementModal;
closePostAnnouncementModal = function() {
    if (hasPostChanges && (titleInput.value || contentInput.value)) {
        showDiscardChangesModal('post');
    } else {
        isPostModalOpen = false;
        hasPostChanges = false;
        clearDraft('post');
        originalClosePostAnnouncementModal();
    }
};

const originalOpenEditModal = openEditModal;
openEditModal = function(...args) {
    isEditModalOpen = true;
    hasEditChanges = false;
    originalOpenEditModal(...args);
    restoreDraft('edit');
};
const originalCloseEditModal = closeEditModal;
closeEditModal = function() {
    if (hasEditChanges && (document.getElementById('editTitle').value || document.getElementById('editContent').value)) {
        showDiscardChangesModal('edit');
    } else {
        isEditModalOpen = false;
        hasEditChanges = false;
        clearDraft('edit');
        originalCloseEditModal();
    }
};

// --- Discard Changes Modal Logic ---
let discardType = null;
function showDiscardChangesModal(type) {
    discardType = type;
    document.getElementById('discardChangesModal').classList.remove('hidden');
}
function closeDiscardChangesModal() {
    document.getElementById('discardChangesModal').classList.add('hidden');
}
function confirmDiscardChanges() {
    closeDiscardChangesModal();
    if (discardType === 'post') {
        isPostModalOpen = false;
        hasPostChanges = false;
        clearDraft('post');
        originalClosePostAnnouncementModal();
    } else if (discardType === 'edit') {
        isEditModalOpen = false;
        hasEditChanges = false;
        clearDraft('edit');
        originalCloseEditModal();
    }
    discardType = null;
}

// --- Intercept Refresh/Back/Exit ---
window.addEventListener('beforeunload', function(e) {
    if ((isPostModalOpen && hasPostChanges) || (isEditModalOpen && hasEditChanges)) {
        // Save draft before leaving
        if (isPostModalOpen) saveDraft('post');
        if (isEditModalOpen) saveDraft('edit');
        // Show browser warning (required for beforeunload)
        e.preventDefault();
        e.returnValue = '';
    }
});

// --- On Modal Submit, Clear Draft ---
form.addEventListener('submit', function() {
    clearDraft('post');
    hasPostChanges = false;
    isPostModalOpen = false;
});
document.getElementById('editAnnouncementForm').addEventListener('submit', function() {
    clearDraft('edit');
    hasEditChanges = false;
    isEditModalOpen = false;
});

// Optional: ESC key closes modal with warning
document.addEventListener('keydown', function(e) {
    if (e.key === "Escape" || e.key === "Esc") {
        if (isPostModalOpen && (titleInput.value || contentInput.value)) {
            closePostAnnouncementModal();
        }
        if (isEditModalOpen && (document.getElementById('editTitle').value || document.getElementById('editContent').value)) {
            closeEditModal();
        }
    }
});

// Set min date and min time for scheduling announcements (AnnouncementModal only)
const scheduleDate = document.getElementById('scheduleDate');
const scheduleTime = document.getElementById('scheduleTime');

function setMinScheduleTime() {
    const now = new Date();
    const todayStr = now.toISOString().slice(0, 10);

    if (scheduleDate.value === todayStr) {
        // Set min time to 1 hour from now if today is selected
        const oneHourLater = new Date(now.getTime() + 60 * 60 * 1000);
        const minTime = oneHourLater.toTimeString().slice(0, 5);
        scheduleTime.min = minTime;
    } else {
        // Allow any time for future dates
        scheduleTime.min = '';
    }
}

// Set min date to today
scheduleDate.min = new Date().toISOString().slice(0, 10);

// Listen for changes on scheduleDate
scheduleDate.addEventListener('change', setMinScheduleTime);

// Also call on page load in case today is pre-selected
setMinScheduleTime();

        // Tooltip toggle logic
const infoBtn = document.getElementById('announcementInfoBtn');
const infoTooltip = document.getElementById('announcementInfoTooltip');

infoBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    infoTooltip.classList.toggle('hidden');
});

// Hide tooltip when clicking outside
document.addEventListener('click', function(e) {
    if (!infoTooltip.classList.contains('hidden')) {
        infoTooltip.classList.add('hidden');
    }
});

    </script>
    
<!-- Discard Changes Modal -->
<div id="discardChangesModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="relative bg-white rounded-xl shadow-lg max-w-md w-full p-6 z-10">
        <div class="flex items-center justify-between mb-2">
            <span class="font-semibold text-lg">Discard Changes?</span>
            <button onclick="closeDiscardChangesModal()" class="text-2xl text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <div class="mb-4 text-gray-700">
            Are you sure you want to discard your changes? All unsaved edits will be lost.
        </div>
        <div class="flex justify-end gap-2">
            <button onclick="confirmDiscardChanges()" class="px-4 py-2 rounded border border-gray-300 text-gray-700 bg-white hover:bg-gray-100">Close without saving</button>
            <button onclick="closeDiscardChangesModal()" class="px-4 py-2 rounded bg-red-700 text-white hover:bg-red-800">Keep editing</button>
        </div> 
    </div>
</div>       
    <div id="EditNotAllowedToast"
        class="hidden fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white border-l-4 border-red-400 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50"
        role="alert">
        <div class="w-full flex justify-between">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/warning.PNG') }}" alt="Warning Icon" class="w-6 h-6">
                <div>
                    <h6 class="font-bold font-['Manrope']">
                        You are not authorized to edit this announcement.
                    </h6>
                </div>
            </div>
            <button type="button"
                class="Cursor-pointer text-gray-500 hover:text-gray-700 text-2xl leading-none cursor-pointer"
                onclick="document.getElementById('EditNotAllowedToast').style.display='none';">&times;</button>
        </div>
    </div>
<!-- Archive Not Allowed Toast -->
<div id="ArchiveNotAllowedToast"
    class="hidden fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white border-l-4 border-red-400 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50"
    role="alert">
    <div class="w-full flex justify-between">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/warning.PNG') }}" alt="Warning Icon" class="w-6 h-6">
            <div>
                <h6 class="font-bold font-['Manrope']">
                    You are not authorized to archive this announcement.
                </h6>
            </div>
        </div>
        <button type="button"
            class="Cursor-pointer text-gray-500 hover:text-gray-700 text-2xl leading-none cursor-pointer"
            onclick="document.getElementById('ArchiveNotAllowedToast').style.display='none';">&times;</button>
    </div>
</div>
<!-- Restore Not Allowed Toast -->
<div id="RestoreNotAllowedToast"
    class="hidden fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white border-l-4 border-red-400 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50"
    role="alert">
    <div class="w-full flex justify-between">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/warning.PNG') }}" alt="Warning Icon" class="w-6 h-6">
            <div>
                <h6 class="font-bold font-['Manrope']">
                    You are not authorized to restore this announcement.
                </h6>
            </div>
        </div>
        <button type="button"
            class="Cursor-pointer text-gray-500 hover:text-gray-700 text-2xl leading-none cursor-pointer"
            onclick="document.getElementById('RestoreNotAllowedToast').style.display='none';">&times;</button>
    </div>
</div>
<!-- Delete Not Allowed Toast -->
<div id="DeleteNotAllowedToast"
    class="hidden fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white border-l-4 border-red-400 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50"
    role="alert">
    <div class="w-full flex justify-between">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/warning.PNG') }}" alt="Warning Icon" class="w-6 h-6">
            <div>
                <h6 class="font-bold font-['Manrope']">
                    You are not authorized to delete this announcement.
                </h6>
            </div>
        </div>
        <button type="button"
            class="Cursor-pointer text-gray-500 hover:text-gray-700 text-2xl leading-none cursor-pointer"
            onclick="document.getElementById('DeleteNotAllowedToast').style.display='none';">&times;</button>
    </div>
</div>



@endsection
