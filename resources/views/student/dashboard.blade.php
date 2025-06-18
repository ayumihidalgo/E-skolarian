@extends('base')

@section('content')
    @include('components.studentSideBarComponent')

    <div id="main-content" class="flex flex-col min-h-screen ml-[20%] transition-all duration-300 bg-[#F2F4F7]">
        @include('components.studentNavBarComponent')
        <div class="flex-grow mb-10">
            <div class="flex-grow p-6 space-y-6">
                 <h5 class="font-['Manrope'] font-extrabold text-[23px] md:text-[20px] lg:text-[30px] mb-1">
                    Dashboard
                </h5>
                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white p-4 rounded-xl shadow-md flex justify-between items-center">
                        <div>
                            <p class="text-[10px] xs:text-xs sm:text-sm md:text-base text-gray-500">Pending Documents</p>
                            <div id="pending-count" class="text-base xs:text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold">{{ $pendingCount }}</div>
                        </div>
                        <img src="{{ asset('images/pendingicon.svg') }}" class="w-6 h-6 xs:w-7 xs:h-7 sm:w-8 sm:h-8 md:w-10 md:h-10 max-w-full h-auto" alt="Pending Documents">
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-md flex justify-between items-center">
                        <div>
                            <p class="text-[10px] xs:text-xs sm:text-sm md:text-base text-gray-500">Under Review</p>
                            <div id="review-count" class="text-base xs:text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold">{{ $reviewCount }}</div>
                        </div>
                        <img src="{{ asset('images/reviewicon.svg') }}" class="w-6 h-6 xs:w-7 xs:h-7 sm:w-8 sm:h-8 md:w-10 md:h-10 max-w-full h-auto" alt="Under Review">
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-md flex justify-between items-center">
                        <div>
                            <p class="text-[10px] xs:text-xs sm:text-sm md:text-base text-gray-500">Approved Documents</p>
                            <div id="approved-count" class="text-base xs:text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold">{{ $approvedCount }}</div>
                        </div>
                        <img src="{{ asset('images/approvedicon.svg') }}" class="w-6 h-6 xs:w-7 xs:h-7 sm:w-8 sm:h-8 md:w-10 md:h-10 max-w-full h-auto" alt="Approved Documents">
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-md flex justify-between items-center">
                        <div>
                            <p class="text-[10px] xs:text-xs sm:text-sm md:text-base text-gray-500">Total Documents</p>
                            <div id="total-count" class="text-base xs:text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold">{{ $totalCount }}</div>
                        </div>
                        <img src="{{ asset('images/totaldocicon.svg') }}" class="w-6 h-6 xs:w-7 xs:h-7 sm:w-8 sm:h-8 md:w-10 md:h-10 max-w-full h-auto" alt="Total Documents">
                    </div>
                </div>
                <!-- Announcement and Documents Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Announcements -->
                    <div class="md:col-span-2 bg-white rounded-xl shadow-md p-4">
                    <h2 class="text-[16px] sm:text-[18px] md:text-[20px] lg:text-[22px] font-semibold mb-2 flex items-center gap-2">
                        <img src="{{ asset('images/annc.svg') }}" alt="Announcements"
                            class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 inline-block align-middle" />
                        Announcements
                    </h2>
                    <div id="latest-announcements-container" class="space-y-4 h-64 overflow-y-auto pr-2">
                        @if ($latestAnnouncements->count())
                                @foreach ($latestAnnouncements as $announcement)
                                    @php
                                        $deadline = $announcement->deadline ? \Carbon\Carbon::parse($announcement->deadline) : null;
                                        $hasTime = $deadline && $deadline->format('H:i:s') !== '00:00:00';
                                        $deadlineText = $deadline
                                            ? 'Due ' . ($hasTime ? $deadline->format('F j, Y g:i A') : $deadline->format('F j, Y'))
                                            : '';
                                        $postDate = \Carbon\Carbon::parse($announcement->created_at)->format('F j, Y');
                                    @endphp
                                    <div class="mb-4 pb-4 border-b border-gray-300">
                                        <h3 class="text-lg md:text-xl font-semibold">
                                        @if (strlen($announcement->title) > 40)
                                            <!-- For desktop -->
                                            <span class="hidden md:inline cursor-help group relative">
                                                {{ Str::limit($announcement->title, 40) }}
                                                <span class="invisible group-hover:visible absolute left-0 top-full mt-1 
                                                    bg-gray-800 text-white text-sm rounded p-2 max-w-xs z-10">
                                                    {{ $announcement->title }}
                                                </span>
                                            </span>
                                            
                                            <!-- For mobile -->
                                            <span class="md:hidden">
                                                {{ $announcement->title }}
                                            </span>
                                        @else
                                            {{ $announcement->title }}
                                        @endif
                                    </h3>
                                        <p class="text-sm text-gray-500">
                                            Posted by {{ $announcement->user->role_name }} on {{ $postDate }}
                                            @if($deadlineText)
                                                <span class="ml-2 text-red-600 font-semibold">{{ $deadlineText }}</span>
                                            @endif
                                        </p>
                                        @php
                                            $maxLength = 150;
                                            $preview = mb_substr($announcement->content, 0, $maxLength) . (strlen($announcement->content) > $maxLength ? '...' : '');
                                        @endphp
                                        <span class="text-gray-700 whitespace-pre-line break-words">{{ $preview }}</span>
                                        <button class="text-[#7B2323] hover:underline ml-2 text-sm"
                                            onclick="showAnnouncementModal(
                                                `{{ addslashes($announcement->title) }}`,
                                                `{{ addslashes(e($announcement->content)) }}`,
                                                `{{ addslashes($announcement->user->role_name) }}`,
                                                `{{ $postDate }}`,
                                                `{{ $deadlineText }}`)">
                                            View Post
                                        </button>
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
    <div id="previous-announcements-container" class="space-y-4 h-[32rem] overflow-y-auto pr-2">
                        @if ($previousAnnouncements->count())
    
                                @foreach ($previousAnnouncements as $announcement)
                                    <div class="border-b pb-2 border-gray-300">
                                        <h3 class="text-sm md:text-base font-semibold">
                                        @if (strlen($announcement->title) > 40)
                                            <!-- Desktop: truncated with tooltip -->
                                            <span class="hidden md:inline cursor-help group relative">
                                                {{ Str::limit($announcement->title, 40) }}
                                                <span class="invisible group-hover:visible absolute left-0 top-full mt-1 
                                                    bg-gray-800 text-white text-sm rounded p-2 max-w-xs z-10">
                                                    {{ $announcement->title }}
                                                </span>
                                            </span>
                                            <!-- Mobile: full title, smaller text -->
                                            <span class="md:hidden">
                                                {{ $announcement->title }}
                                            </span>
                                        @else
                                            {{ $announcement->title }}
                                        @endif
                                    </h3>
                                        <p class="text-sm text-gray-500">
                                            Posted by {{ $announcement->user->role_name }} on
                                            {{ $announcement->created_at->format('F j, Y') }}
                                        </p>
                                        @php
                                            $maxLength = 100;
                                            $isLong = strlen($announcement->content) > $maxLength;
                                            $preview = $isLong
                                                ? mb_substr($announcement->content, 0, $maxLength) . '...'
                                                : $announcement->content;
                                        @endphp
                                        <span class="text-gray-700 whitespace-pre-line break-words">{{ $preview }}</span>
                                        @if ($isLong)
                                            <button class="text-[#7B2323] hover:underline ml-2 text-sm"
                                                onclick="showAnnouncementModal(
                                                `{{ addslashes($announcement->title) }}`,
                                                `{{ addslashes(e($announcement->content)) }}`,
                                                `Posted by {{ addslashes($announcement->user->role_name) }} on {{ $announcement->created_at->format('F j, Y') }}`,
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
                        <h2 class="text-lg font-semibold text-gray-800">Recent Documents</h2>
                        <div class="bg-white rounded-xl shadow p-4">
                            @if ($recentDocuments->count())
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm text-left">
                                        <thead>
                                            <tr class="border-b text-gray-500">
                                                <th class="px-3 py-2 font-semibold">Tag</th>
                                                <th class="px-3 py-2 font-semibold">Title</th>
                                                <th class="px-3 py-2 font-semibold">Date</th>
                                                <th class="px-3 py-2 font-semibold">Type</th>
                                                <th class="px-3 py-2 font-semibold">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recentDocuments as $doc)
                                                @if ($doc->archived_at !== null)
                                                    @continue
                                                @endif
                                                @php
                                                    $isUnderReview = $doc->reviews->contains(fn($review) => strtolower($review->status) === 'under review');
                                                    $displayStatus = $isUnderReview ? 'Under Review' : ucfirst($doc->status);
                                                    $statusColors = [
                                                        'Approved' => 'bg-green-100 text-green-700',
                                                        'Rejected' => 'bg-red-100 text-red-600',
                                                        'Under Review' => 'bg-yellow-100 text-yellow-700',
                                                        'Pending' => 'bg-gray-200 text-gray-600',
                                                    ];
                                                    $badgeClass = $statusColors[$displayStatus] ?? 'bg-gray-200 text-gray-600';
                                                @endphp
                                                <tr class="border-b"
                                                        onclick="showDocumentModal(
                                                            '{{ $doc->id }}',
                                                            '{{ \Carbon\Carbon::parse($doc->created_at)->format('F j, Y, g:i A') }}',
                                                            '{{ addslashes($doc->subject) }}',
                                                            '{{ addslashes($doc->type) }}',
                                                            '{{ addslashes($doc->summary) }}',
                                                            '{{ $doc->latestVersion ? addslashes($doc->latestVersion->file_path) : '' }}',
                                                            '{{ addslashes(optional($doc->receiver)->role_name ?? '') }}',
                                                            '{{ $displayStatus }}',
                                                            '{{ $doc->control_tag }}'
                                                        )">
                                                        <td class="px-3 py-2 font-extrabold text-black">{{ $doc->control_tag }}</td>
                                                        <td class="px-3 py-2 max-w-[200px] truncate" title="{{ $doc->subject }}">{{ $doc->subject }}</td>
                                                        <td class="px-3 py-2">{{ \Carbon\Carbon::parse($doc->created_at)->format('n/j/Y') }}</td>
                                                        <td class="px-3 py-2">{{ $doc->type }}</td>
                                                        <td class="px-3 py-2">
                                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">
                                                                {{ $displayStatus }}
                                                            </span>
                                                        </td>
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
                </div>
            </div>
        </div>
        @include('components.footer')
    </div>
    <!-- Modal for full announcement -->
        <div id="announcementModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
            <div id="modalBackdrop" class="absolute inset-0 bg-black" style="opacity:0.75;"></div>
            <div class="relative bg-white rounded-xl shadow-lg max-w-xl w-full p-6 z-10">
                <div class="relative mb-2 border-b pb-2">
                    <button onclick="closeAnnouncementModal()"
                        class="absolute top-3 right-4 text-2xl text-gray-500 hover:text-gray-700 z-10" style="line-height: 1;">&times;</button>
                    <!-- Modal header -->
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('images/annc.svg') }}" alt="Announcement" class="w-8 h-8 inline-block align-middle" />
                        <span id="modalLabel" class="font-semibold text-lg">Announcement</span>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <h3 id="modalTitle" class="text-lg font-bold mb-1"></h3>
                        <span id="modalDeadline" class="text-sm text-red-600 font-semibold"></span>
                    </div>
                </div>
                <h3 id="modalTitle" class="text-lg font-bold mt-3 mb-1"></h3>
                <div class="flex items-center gap-2 mb-3">
                    <span id="modalPoster" class="flex items-center bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs">
                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 10a4 4 0 100-8 4 4 0 000 8zm0 2c-4 0-8 2-8 4v2h16v-2c0-2-4-4-8-4z"/></svg>
                        <span id="modalPosterName"></span>
                    </span>
                    <span class="flex items-center bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs">
                        <!-- Clock Icon SVG -->
                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                            <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                        <span id="modalPostDate"></span>
                    </span>
                </div>
                <div id="modalContent" class="text-gray-700 whitespace-pre-line break-words mb-6"></div>
                <div class="flex justify-end">
                    <a href="{{ route('student.submit-documents') }}" id="openPortalBtn"
                        class="bg-[#7B2323] hover:bg-[#5a1818] text-white px-5 py-2 rounded-lg font-semibold transition">
                        Open Submission Portal
                    </a>
                </div>
            </div>
        </div>

    <!-- Document Details Modal -->
    <div id="documentModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div id="docModalBackdrop" class="absolute inset-0 bg-black" style="opacity:0.4;"></div>
        <div class="relative bg-[#4B2222] rounded-xl shadow-lg max-w-lg w-full p-6 z-10 text-white">
            <div class="flex items-center justify-between mb-2 border-b border-[#fff2] pb-2">
                <span class="font-semibold text-lg">Document Details</span>
                <button onclick="closeDocumentModal()" class="text-2xl text-gray-200 hover:text-white">&times;</button>
            </div>
            <div class="mb-2 text-sm" id="docDate"></div>
            <div class="mb-2">
                <span class="font-bold text-lg">Title: </span>
                <span id="docTitle" class="font-semibold"></span>
            </div>
            <div class="mb-2">
                <span class="font-semibold">Type: </span>
                <span id="docType"></span>
            </div>
            <div class="mb-2">
                <span class="font-semibold">Summary</span>
                <div id="docSummary" class="bg-gray-100 text-gray-900 rounded-md p-3 mt-1"></div>
            </div>
            <div class="mb-2">
                <span class="font-semibold">Attachment</span>
                <div id="docAttachment"></div>
            </div>
            <div class="flex gap-2 mb-2 text-xs">
                <span class="flex items-center gap-1"><span
                        class="inline-block w-3 h-3 rounded-full bg-gray-400"></span>Pending</span>
                <span class="flex items-center gap-1"><span
                        class="inline-block w-3 h-3 rounded-full bg-yellow-400"></span>Under Review</span>
                <span class="flex items-center gap-1"><span
                        class="inline-block w-3 h-3 rounded-full bg-red-500"></span>Rejected</span>
                <span class="flex items-center gap-1"><span
                        class="inline-block w-3 h-3 rounded-full bg-green-500"></span>Approved</span>
            </div>
            <div id="docMeta" class="bg-[#3a1818] rounded-md px-3 py-2 mt-2 text-xs"></div>
        </div>
    </div>

    <!-- Document Preview Modal -->
    <div id="previewModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div onclick="closePreviewModal()" class="absolute inset-0 bg-black opacity-40"></div>
        <div class="relative bg-white rounded-xl shadow-lg max-w-2xl w-full p-6 z-10 flex flex-col items-center">
            <button onclick="closePreviewModal()"
                class="absolute top-2 right-4 text-2xl text-gray-500 hover:text-gray-700">&times;</button>
            <div id="previewContent" class="w-full h-[32rem] flex items-center justify-center"></div>
        </div>
    </div>

    <script>
        function decodeHtmlEntities(str) {
            var txt = document.createElement('textarea');
            txt.innerHTML = str;
            return txt.value;
        }

        function showAnnouncementModal(title, content, poster, postDate, deadlineText) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalContent').textContent = decodeHtmlEntities(content);
            document.getElementById('modalPosterName').textContent = poster;
            document.getElementById('modalPostDate').textContent = postDate;
            document.getElementById('modalDeadline').textContent = deadlineText || '';
            document.getElementById('announcementModal').classList.remove('hidden');
        }
        function closeAnnouncementModal() {
            document.getElementById('announcementModal').classList.add('hidden');
        }


        function showPreviewModal(filePath) {
            let fileName = filePath.split('/').pop();
            let ext = fileName.split('.').pop().toLowerCase();
            let previewHtml = '';
            if (['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'].includes(ext)) {
                previewHtml =
                    `<img src="/storage/${filePath}" alt="${fileName}" class="max-h-[28rem] rounded shadow mx-auto">`;
            } else if (ext === 'pdf') {
                previewHtml =
                    `<iframe src="/storage/${filePath}" class="w-full h-full rounded shadow" frameborder="0"></iframe>`;
            } else if (['doc', 'docx'].includes(ext)) {
                // Use Google Docs Viewer for DOC/DOCX
                let url = encodeURIComponent(window.location.origin + '/storage/' + filePath);
                previewHtml =
                    `<iframe src="https://docs.google.com/gview?url=${url}&embedded=true" class="w-full h-full rounded shadow" frameborder="0"></iframe>`;
            } else {
                previewHtml = `<div class="text-gray-500">Preview not available for this file type.</div>`;
            }
            document.getElementById('previewContent').innerHTML = previewHtml;
            document.getElementById('previewModal').classList.remove('hidden');
        }

        function closePreviewModal() {
            document.getElementById('previewModal').classList.add('hidden');
            document.getElementById('previewContent').innerHTML = '';
        }

        function getPreviewUrl(filePath) {
            let fileName = filePath.split('/').pop();
            let ext = fileName.split('.').pop().toLowerCase();
            if (['doc', 'docx'].includes(ext)) {
                let url = encodeURIComponent(window.location.origin + '/storage/' + filePath);
                return `https://docs.google.com/gview?url=${url}&embedded=true`;
            }
            // For PDFs and images, just open the file directly
            return `/storage/${filePath}`;
        }

        document.addEventListener('DOMContentLoaded', function() {
    // Function to update document counts
    function updateDocumentCounts() {
        fetch('/student/document-counts')
            .then(response => response.json())
            .then(data => {
                // Store old values to check if they've changed
                const oldValues = {
                    pending: document.getElementById('pending-count').textContent,
                    review: document.getElementById('review-count').textContent,
                    approved: document.getElementById('approved-count').textContent,
                    total: document.getElementById('total-count').textContent
                };
                
                // Update with new values
                document.getElementById('pending-count').textContent = data.pendingCount;
                document.getElementById('review-count').textContent = data.reviewCount;
                document.getElementById('approved-count').textContent = data.approvedCount;
                document.getElementById('total-count').textContent = data.totalCount;
                
                // Add animation if values changed
                if (oldValues.pending != data.pendingCount) {
                    animateCountUpdate(document.getElementById('pending-count'));
                }
                if (oldValues.review != data.reviewCount) {
                    animateCountUpdate(document.getElementById('review-count'));
                }
                if (oldValues.approved != data.approvedCount) {
                    animateCountUpdate(document.getElementById('approved-count'));
                }
                if (oldValues.total != data.totalCount) {
                    animateCountUpdate(document.getElementById('total-count'));
                }
            })
            .catch(error => {
                console.error('Error fetching document counts:', error);
            });
    }
    
    // Function to animate count updates
    function animateCountUpdate(element) {
        element.classList.add('count-update');
        setTimeout(() => {
            element.classList.remove('count-update');
        }, 1000);
    }

    // Update counts every 5 seconds
    setInterval(updateDocumentCounts, 5000);
});

document.addEventListener('DOMContentLoaded', function() {
    // Cache the DOM elements
    const latestContainer = document.getElementById('latest-announcements-container');
    const previousContainer = document.getElementById('previous-announcements-container');
    const latestEmptyMessage = document.getElementById('latest-empty-message');
    const previousEmptyMessage = document.getElementById('previous-empty-message');
    
    // Function to escape HTML to prevent XSS
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    
    // Function to update announcements
    function updateAnnouncements() {
        console.log('Fetching latest announcements...');
        
        fetch('/student/announcements')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Announcements data received');
                
                // Update latest announcements
                if (data.latestAnnouncements.length > 0) {
                    let latestHtml = '';
                    
                    data.latestAnnouncements.forEach(announcement => {
                        const previewContent = announcement.isLong 
                            ? announcement.content.substring(0, 150) + '...' 
                            : announcement.content;
                            
                        latestHtml += `
                            <div class="mb-4 pb-4 border-b border-gray-300">
                                <h3 class="text-lg md:text-xl font-semibold">
                                    ${announcement.title.length > 40 
                                        ? `<span class="hidden md:inline cursor-help group relative">
                                            ${escapeHtml(announcement.title.substring(0, 40))}...
                                            <span class="invisible group-hover:visible absolute left-0 top-full mt-1 
                                                bg-gray-800 text-white text-sm rounded p-2 max-w-xs z-10">
                                                ${escapeHtml(announcement.title)}
                                            </span>
                                          </span>
                                          <span class="md:hidden">
                                            ${escapeHtml(announcement.title)}
                                          </span>`
                                        : escapeHtml(announcement.title)
                                    }
                                </h3>
                                <p class="text-sm text-gray-500">
                                    Posted by ${escapeHtml(announcement.author)} on ${announcement.postDate}
                                    ${announcement.deadlineText 
                                        ? `<span class="ml-2 text-red-600 font-semibold">${escapeHtml(announcement.deadlineText)}</span>` 
                                        : ''}
                                </p>
                                <span class="text-gray-700 whitespace-pre-line break-words">${escapeHtml(previewContent)}</span>
                                <button class="text-[#7B2323] hover:underline ml-2 text-sm"
                                    onclick="showAnnouncementModal(
                                        \`${escapeHtml(announcement.title).replace(/`/g, '\\`')}\`,
                                        \`${escapeHtml(announcement.content).replace(/`/g, '\\`')}\`,
                                        \`${escapeHtml(announcement.author).replace(/`/g, '\\`')}\`,
                                        \`${announcement.postDate}\`,
                                        \`${announcement.deadlineText ? announcement.deadlineText : ''}\`)">
                                    View Post
                                </button>
                            </div>
                        `;
                    });
                    
                    latestContainer.innerHTML = latestHtml;
                    if (latestEmptyMessage) {
                        latestEmptyMessage.style.display = 'none';
                    }
                } else if (latestContainer.children.length === 0) {
                    latestContainer.innerHTML = '<div class="text-gray-500 text-center py-8">No announcements at the moment</div>';
                }
                
                // Update previous announcements
                if (data.previousAnnouncements.length > 0) {
                    let previousHtml = '';
                    
                    data.previousAnnouncements.forEach(announcement => {
                        const maxLength = 100;
                        const isLong = announcement.content.length > maxLength;
                        const previewContent = isLong 
                            ? announcement.content.substring(0, maxLength) + '...' 
                            : announcement.content;
                            
                        previousHtml += `
                            <div class="border-b pb-2 border-gray-300">
                                <h3 class="text-sm md:text-base font-semibold">
                                    ${announcement.title.length > 40 
                                        ? `<span class="hidden md:inline cursor-help group relative">
                                            ${escapeHtml(announcement.title.substring(0, 40))}...
                                            <span class="invisible group-hover:visible absolute left-0 top-full mt-1 
                                                bg-gray-800 text-white text-sm rounded p-2 max-w-xs z-10">
                                                ${escapeHtml(announcement.title)}
                                            </span>
                                          </span>
                                          <span class="md:hidden">
                                            ${escapeHtml(announcement.title)}
                                          </span>`
                                        : escapeHtml(announcement.title)
                                    }
                                </h3>
                                <p class="text-sm text-gray-500">
                                    Posted by ${escapeHtml(announcement.author)} on ${announcement.postDate}
                                </p>
                                <span class="text-gray-700 whitespace-pre-line break-words">${escapeHtml(previewContent)}</span>
                                ${isLong ? `
                                    <button class="text-[#7B2323] hover:underline ml-2 text-sm"
                                        onclick="showAnnouncementModal(
                                            \`${escapeHtml(announcement.title).replace(/`/g, '\\`')}\`,
                                            \`${escapeHtml(announcement.content).replace(/`/g, '\\`')}\`,
                                            \`Posted by ${escapeHtml(announcement.author).replace(/`/g, '\\`')} on ${announcement.postDate}\`,
                                            'previous'
                                        )">
                                        Read More
                                    </button>
                                ` : ''}
                            </div>
                        `;
                    });
                    
                    previousContainer.innerHTML = previousHtml;
                    if (previousEmptyMessage) {
                        previousEmptyMessage.style.display = 'none';
                    }
                } else if (previousContainer.children.length === 0) {
                    previousContainer.innerHTML = `
                        <div class="text-center text-gray-500 py-8">
                            <img src="{{ asset('images/Illustrations.svg') }}" alt="No previous post"
                                class="w-24 h-24 mx-auto mb-2 opacity-80">
                            <p>No previous post</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching announcements:', error);
            });
    }
    
    // Initially update immediately in case there were new announcements since page load
    setTimeout(updateAnnouncements, 2000);

    // Update announcements every 15 seconds
    setInterval(updateAnnouncements, 15000);
    
    // Add event listener for visibility changes to optimize performance
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            // Update immediately when page becomes visible again
            updateAnnouncements();
        }
    });
});
    </script>
@endsection
