@extends('base')

@section('content')
    @include('components.studentSideBarComponent')

    <div id="main-content" class="flex flex-col min-h-screen ml-[20%] transition-all duration-300 bg-[#F2F4F7]">
        @include('components.studentNavBarComponent')
        <div class="flex-grow mb-10">
            <div class="flex-grow p-6 space-y-6">
                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white p-4 rounded-xl shadow-md flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Pending Documents</p>
                            <div class="text-2xl font-bold">{{ $pendingCount }}</div>
                        </div>
                        <img src="{{ asset('images/pendingicon.svg') }}" class="w-10 h-10" alt="Pending Documents">
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-md flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Under Review</p>
                            <div class="text-2xl font-bold">{{ $reviewCount }}</div>
                        </div>
                        <img src="{{ asset('images/reviewicon.svg') }}" class="w-10 h-10" alt="Under Review">
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-md flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Approved Documents</p>
                            <div class="text-2xl font-bold">{{ $approvedCount }}</div>
                        </div>
                        <img src="{{ asset('images/approvedicon.svg') }}" class="w-10 h-10" alt="Approved Documents">
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-md flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Total Documents</p>
                            <div class="text-2xl font-bold">{{ $totalCount }}</div>
                        </div>
                        <img src="{{ asset('images/totaldocicon.svg') }}" class="w-10 h-10" alt="Total Documents">
                    </div>
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
                                            $preview = $isLong
                                                ? mb_substr($announcement->content, 0, $maxLength) . '...'
                                                : $announcement->content;
                                        @endphp
                                        <span class="text-gray-700 whitespace-pre-line break-words">{{ $preview }}</span>
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
                                            $preview = $isLong
                                                ? mb_substr($announcement->content, 0, $maxLength) . '...'
                                                : $announcement->content;
                                        @endphp
                                        <span class="text-gray-700 whitespace-pre-line break-words">{{ $preview }}</span>
                                        @if ($isLong)
                                            <button class="text-indigo-600 hover:underline ml-2 text-sm"
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
                            @if ($recentDocuments->count())
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm text-left">
                                        <thead>
                                            <tr class="border-b">
                                                <th class="px-3 py-2 font-semibold">Tag</th>
                                                <th class="px-3 py-2 font-semibold">Title</th>
                                                <th class="px-3 py-2 font-semibold">Date</th>
                                                <th class="px-3 py-2 font-semibold">Type</th>
                                                <th class="px-3 py-2 font-semibold">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recentDocuments as $doc)
                                                @php
                                                    // Check if the document has any review with status 'Under Review'
                                                    $isUnderReview = $doc->reviews->contains(function ($review) {
                                                        return strtolower($review->status) === 'under review';
                                                    });
                                                    $displayStatus = $isUnderReview
                                                        ? 'Under Review'
                                                        : ucfirst($doc->status);
                                                    $statusColors = [
                                                        'Approved' => 'bg-green-500 text-white',
                                                        'Rejected' => 'bg-red-500 text-white',
                                                        'Under Review' => 'bg-yellow-400 text-gray-800',
                                                        'Pending' => 'bg-gray-400 text-white',
                                                    ];
                                                    $badgeClass =
                                                        $statusColors[$displayStatus] ?? 'bg-gray-400 text-white';
                                                @endphp
                                                <tr class="border-b hover:bg-zinc-200 cursor-pointer"
                                                    onclick="showDocumentModal(
                                                    '{{ $doc->id }}',
                                                    '{{ \Carbon\Carbon::parse($doc->created_at)->format('F j, Y, g:i A') }}',
                                                    '{{ addslashes($doc->subject) }}',
                                                    '{{ addslashes($doc->type) }}',
                                                    '{{ addslashes($doc->summary) }}',
                                                    '{{ $doc->latestVersion ? addslashes($doc->latestVersion->file_path) : '' }}',
                                                    '{{ addslashes(optional($doc->receiver)->username ?? '') }}',
                                                    '{{ $displayStatus }}',
                                                    '{{ $doc->control_tag }}'
                                                )">
                                                    <td class="px-3 py-2 font-bold text-orange-500">{{ $doc->control_tag }}
                                                    </td>
                                                    <td class="px-3 py-2 max-w-[200px] truncate"
                                                        title="{{ $doc->subject }}">{{ $doc->subject }}</td>
                                                    <td class="px-3 py-2">
                                                        {{ \Carbon\Carbon::parse($doc->created_at)->format('n/j/Y') }}</td>
                                                    <td class="px-3 py-2">{{ $doc->type }}</td>
                                                    <td class="px-3 py-2">
                                                        <span
                                                            class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">
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
        <div id="modalBackdrop" class="absolute inset-0 bg-black" style="opacity:0.2;"></div>
        <div class="relative bg-white rounded-xl shadow-lg max-w-xl w-full p-6 z-10">
            <div class="flex items-center justify-between mb-2 border-b pb-2">
                <div class="flex items-center gap-2">
                    <span class="text-2xl text-red-500">📢</span>
                    <span id="modalLabel" class="font-semibold text-lg">Announcement</span>
                </div>
                <button onclick="closeAnnouncementModal()"
                    class="text-2xl text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <h3 id="modalTitle" class="text-lg font-bold mt-3 mb-1"></h3>
            <div id="modalMeta" class="text-xs text-gray-500 mb-3"></div>
            <div id="modalContent" class="text-gray-700 whitespace-pre-line break-words"></div>
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

        function showDocumentModal(id, date, title, type, summary, filePath, reviewer, status, tag) {
            document.getElementById('docDate').textContent = date;
            document.getElementById('docTitle').textContent = title;
            document.getElementById('docType').textContent = type;
            document.getElementById('docSummary').textContent = summary || 'No summary';
            if (filePath) {
                let fileName = filePath.split('/').pop();
                document.getElementById('docAttachment').innerHTML =
                    `<a href="${getPreviewUrl(filePath)}" target="_blank" class="bg-white text-gray-900 px-3 py-1 rounded shadow text-xs inline-block hover:bg-gray-200 transition">View: ${fileName}</a>`;
            } else {
                document.getElementById('docAttachment').innerHTML = '<span class="text-gray-300">No attachment</span>';
            }
            // Status badge
            let badge = '';
            if (status === 'Approved') badge = `<span class="inline-block w-3 h-3 rounded-full bg-green-500 mr-1"></span>`;
            else if (status === 'Rejected') badge =
                `<span class="inline-block w-3 h-3 rounded-full bg-red-500 mr-1"></span>`;
            else if (status === 'Under Review') badge =
                `<span class="inline-block w-3 h-3 rounded-full bg-yellow-400 mr-1"></span>`;
            else badge = `<span class="inline-block w-3 h-3 rounded-full bg-gray-400 mr-1"></span>`;
            document.getElementById('docMeta').innerHTML =
                `<span class="font-semibold">${reviewer || ''}</span> <br>${badge}${status}, ${date}`;
            document.getElementById('documentModal').classList.remove('hidden');
        }

        function closeDocumentModal() {
            document.getElementById('documentModal').classList.add('hidden');
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
    </script>
@endsection
