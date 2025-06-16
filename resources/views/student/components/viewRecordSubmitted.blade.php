@extends('base')
@section('content')
    @include('components.studentNavBarComponent')
    @include('components.studentSideBarComponent')
    <div id="main-content" class="transition-all duration-300 ml-[20%]">
        @include('student.components.viewSubmissionTrackerTitleComponent')
        <!-- Comments section -->
        @php
            $documentId = $record->id;
            $comments = \App\Models\Comment::where('document_id', $documentId)
                ->with('sender')
                ->orderBy('created_at')
                ->get();
        @endphp
        <div class="flex flex-wrap gap-[15px] px-[15px]">
            <!-- Submission Details Component (Left) -->
            <div class="flex-1 min-w-[300px]" id="record-container" data-document-id="{{ $record->id }}">
                <div class="bg-[#4D0F0F] text-white rounded-3xl shadow-lg p-6 space-y-4">
                    <div class="text-sm text-gray-300">{{ $record->created_at->format('F j, Y') }}</div>
                    <div class="text-lg font-bold">Title: <span class="text-white">{{ $record->subject }}</span></div>
                    <div class="text-sm text-gray-300">Type: <span
                            class="font-semibold text-white">{{ $record->type }}</span>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-1">Summary</h3>
                        <div class="bg-gray-100 text-black text-sm p-3 rounded-md h-32 overflow-y-auto">
                            {{ $record->overview }}
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-1">Attachment</h3>
                        @if ($record->documentVersions->count() > 0)
                            <div class="space-y-2">
                                @foreach ($record->documentVersions as $version)
                                    <div>
                                        <button type="button"
                                            class="bg-white text-black text-sm px-4 py-2 rounded hover:bg-gray-200 transition inline-block preview-document-btn cursor-pointer"
                                            data-file-url="{{ asset('storage/' . $version->document_url) }}"
                                            data-file-name="{{ basename($version->document_url) }}">
                                            Version {{ $version->version }}:
                                            {{ $version->original_file_name ?? basename($version->document_url) }}
                                        </button>
                                        @if ($version->comments)
                                            <p class="text-xs text-gray-300 mt-1">{{ $version->comments }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400">No attachments</span>
                        @endif
                    </div>
                    <!-- Document Viewer Modal -->
                    <div id="documentViewerModal"
                        class="hidden fixed inset-0 bg-black z-50 flex items-center justify-center"
                        style="background-color: rgba(0,0,0,0.3);">
                        <div class="bg-white w-11/12 h-5/6 rounded-lg flex flex-col">
                            <div class="flex justify-between items-center p-4 border-b">
                                <h3 id="documentTitle" class="font-semibold text-lg truncate">Document Preview</h3>
                                <div class="flex items-center space-x-4">
                                    <!-- Tabs for Preview and Download -->
                                    <div class="flex items-center bg-gray-100 rounded-lg p-1">
                                        <button id="previewTab"
                                            class="py-1 px-4 rounded-lg bg-blue-500 text-white cursor-pointer">Preview</button>
                                        <button id="downloadTab"
                                            class="py-1 px-4 rounded-lg text-gray-700 cursor-pointer">Download</button>
                                    </div>
                                    <!-- Close Button -->
                                    <button onclick="closeDocumentViewer()"
                                        class="text-gray-500 hover:text-gray-700 cursor-pointer">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <!-- PDF Viewer -->
                                <div id="pdfViewer" class="w-full h-full"></div>
                                <!-- Image Viewer -->
                                <div id="imageViewer" class="hidden h-full flex items-center justify-center bg-gray-100">
                                </div>
                                <!-- Download View -->
                                <div id="downloadView"
                                    class="hidden h-full flex items-center justify-center bg-gray-100 flex-col p-8">
                                    <h3 id="downloadFileName" class="text-xl font-semibold mb-4">filename.pdf</h3>
                                    <p class="text-gray-600 mb-8 text-center">Click the button below to download this
                                        document</p>
                                    <a id="downloadButton" href="#" download
                                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-lg flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Download Document
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex flex-col gap-1 text-sm">
                            <span class="text-gray-300 font-bold mb-1">Status</span>
                            <div class="relative">
                                <div class="relative pl-2">
                                    <!-- Pending/Submitted  -->
                                    <div class="flex items-start mb-1 relative">
                                        <div>
                                            <div class="flex flex-col gap-0.5 relative pl-0.5">
                                                <div class="flex items-center gap-3">
                                                    <span
                                                        class="w-4 h-4 rounded-full bg-[#D4B2B2] transition-all duration-300 border-2 border-[#D4B2B2] shadow-lg z-10">
                                                    </span>
                                                    <span
                                                        class="font-semibold text-white">{{ $record->user->organization_acronym ?? 'N/A' }}</span>
                                                </div>
                                                <span class="absolute left-[9px] top-[18px] w-0.5 h-[14px] bg-white z-0"
                                                    style="border-left: 0.2px dashed #fff; background: none;">
                                                </span>
                                                <div class="flex items-center gap-3">
                                                    <span
                                                        class=" ml-[1.2px] mt-[0.8px]
                                                            w-3 h-3 rounded-full transition-all duration-300
                                                            {{ $record->status === 'Pending' ? 'bg-yellow-400' : 'bg-[#D4B2B2]' }}
                                                            border-2 border-[#D4B2B2]  bg-[#D4B2B2] shadow-lg z-10">
                                                    </span>
                                                    <span class="text-gray-300 text-sm mt-1">
                                                        {{ $record->status === 'Pending' ? 'Pending' : 'Pending' }},
                                                        Submitted on
                                                        {{ $record->created_at->format('F j Y, g:iA') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--  Under Review Status  -->
                                    @if (in_array($record->status, ['Under Review', 'under_review', 'approved', 'rejected']))
                                        <div class="flex items-start mb-6 relative pl-0.2">
                                            <div class="gap-3">
                                                <div class="flex flex-col gap-0.5 relative pl-0.5">
                                                    <div class="flex items-center gap-3">
                                                        <span
                                                            class="w-4 h-4 rounded-full bg-[#D4B2B2] transition-all duration-300 border-2 border-[#D4B2B2] shadow-lg z-10">
                                                        </span>
                                                        <span class="font-semibold text-white">
                                                            {{ $record->reviewer->name ?? 'Office of the Student Services' }}
                                                        </span>
                                                    </div>
                                                    <!-- Connect the circle to the first circle if under review or beyond -->
                                                    @if (in_array($record->status, ['Under Review', 'under_review', 'approved', 'rejected']))
                                                        <span
                                                            class="absolute left-[9px] -top-[14px] w-0.5 h-[32px] bg-white z-0"
                                                            style="border-left: 0.2px dashed #fff; background: none;">
                                                        </span>
                                                    @endif
                                                    <span class="absolute left-[9px] top-[18px] w-0.5 h-[14px] bg-white z-0"
                                                        style="border-left: 0.2px dashed #fff; background: none;">
                                                    </span>
                                                    <div class="flex items-center gap-3">
                                                        <span
                                                            class="ml-[1.2px] mt-[0.8px]  w-3 h-3 rounded-full transition-all duration-300
            {{ $record->status === 'under_review' || $record->status === 'Under Review' ? 'bg-blue-400 ' : 'bg-[#D4B2B2] ' }}
            border-2 border-[#D4B2B2]  bg-[#D4B2B2] shadow-lg z-10">
                                                        </span>
                                                        <span>
                                                            Under Review,
                                                            {{ $record->reviewed_at ? $record->reviewed_at->format('F j Y, g:iA') : 'April 15 2025, 1:45PM' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <!-- approved/returned -->
                                    @if (in_array($record->status, ['Approved', 'rejected', 'Returned', 'returned']))
                                        <div class="flex items-start mb-6 relative pl-0.2">
                                            <div class="gap-3">
                                                <div class="flex flex-col gap-0.5 relative pl-0.5">
                                                    <div class="flex items-center gap-3">
                                                        <span
                                                            class="w-4 h-4 rounded-full bg-[#D4B2B2] transition-all duration-300 border-2 border-[#D4B2B2] shadow-lg z-10">
                                                        </span>
                                                        <span class="font-semibold text-white">
                                                            {{ $record->reviewer->name ?? 'Office of the Student Services' }}
                                                        </span>
                                                    </div>
                                                    @if (in_array($record->status, ['Approved', 'under_review', 'approved', 'rejected', 'returned', 'Returned']))
                                                        <span
                                                            class="absolute left-[9px] -top-[14px] w-0.5 h-[32px] bg-white z-0"
                                                            style="border-left: 0.2px dashed #fff; background: none;">
                                                        </span>
                                                    @endif
                                                    <span class="absolute left-[9px] top-[18px] w-0.5 h-[14px] bg-white z-0"
                                                        style="border-left: 0.2px dashed #fff; background: none;">
                                                    </span>
                                                    <div class="flex items-center gap-3">
                                                        <span
                                                            class="ml-[1.2px] mt-[0.8px] w-3 h-3 rounded-full transition-all duration-300
                                                            @if ($record->status === 'under_review' || $record->status === 'Approved') bg-green-400
                                                            @elseif ($record->status === 'Returned' || $record->status === 'returned')
                                                                bg-red-400
                                                            @else
                                                                bg-[#D4B2B2] @endif
                                                            border-2 border-[#D4B2B2] shadow-lg z-10">
                                                        </span>
                                                        @if ($record->status === 'Approved')
                                                            <span>
                                                                Approved,
                                                                {{ $record->reviewed_at ? $record->reviewed_at->format('F j Y, g:iA') : 'April 15 2025, 1:45PM' }}
                                                            </span>
                                                        @elseif ($record->status === 'Returned' || $record->status === 'returned')
                                                            <span>
                                                                Returned, Submitted on
                                                                {{ $record->returned_at ? $record->returned_at->format('F j Y, g:iA') : ($record->updated_at ? $record->updated_at->format('F j Y, g:iA') : 'N/A') }}
                                                            </span>
                                                            <div class="block text-red-400 text-l font-semibold mt-2">
                                                                <span class="">
                                                                    This document was returned on
                                                                    {{ $record->returned_at ? $record->returned_at->format('F j Y, g:iA') : ($record->updated_at ? $record->updated_at->format('F j Y, g:iA') : 'N/A') }}.
                                                                </span>
                                                            </div>
                                                            <div class="div">
                                                                @php
                                                                    $timelineMessage = \App\Models\DocumentTimeline::where(
                                                                        'document_id',
                                                                        $record->id,
                                                                    )
                                                                        ->where('status', 'Returned')
                                                                        ->orderByDesc('created_at')
                                                                        ->value('message');
                                                                @endphp
                                                                @if ($timelineMessage)
                                                                    <div class="text-red-300 text-sm mt-2">
                                                                        <strong>Return Reason:</strong>
                                                                        {{ $timelineMessage }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="div">
                                                                <button type="button" onclick="openReturnedImageModal()"
                                                                    class="mt-2 bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-semibold transition">
                                                                    Return Document
                                                                </button>
                                                            </div>
                                                        @elseif ($record->status === 'rejected')
                                                            <span>
                                                                Rejected,
                                                                {{ $record->reviewed_at ? $record->reviewed_at->format('F j Y, g:iA') : 'April 15 2025, 1:45PM' }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Returned Reason Image Modal --}}
                                        @if ($record->status === 'Returned' || $record->status === 'returned')
                                            <div id="returnedImageModal"
                                                class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                                                <div class="bg-white rounded-lg shadow-lg p-6 max-w-lg w-full relative">
                                                    <button onclick="closeReturnedImageModal()"
                                                        class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                    <h3 class="text-lg font-semibold mb-4 text-black">Return Document</h3>
                                                    <div class="mb-2">
                                                        <span class="font-semibold text-black">Title:</span>
                                                        <span class="text-gray-800">{{ $record->subject }}</span>
                                                    </div>
                                                    <div class="mb-4">
                                                        <span class="font-semibold text-black">Summary:</span>
                                                        <textarea name="returned_summary" class="w-full p-2 border rounded text-black" rows="3"
                                                            placeholder="Enter your summary here...">{{ old('returned_summary', $record->overview) }}</textarea>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label for="returned_attachment"
                                                            class="block font-semibold text-black mb-1">Upload
                                                            Document:</label>
                                                        <input type="file" name="returned_attachment"
                                                            id="returned_attachment"
                                                            class="w-full p-2 border rounded text-black"
                                                            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" />
                                                    </div>
                                                    <div class="flex justify-end gap-2 mt-6">
                                                        <button onclick="closeReturnedImageModal()"
                                                            class="bg-gray-300 hover:bg-gray-400 text-black font-semibold px-4 py-2 rounded">
                                                            Close
                                                        </button>
                                                        <button
                                                            class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded"
                                                            type="button" onclick="openFinalizeModal()">
                                                            Return
                                                        </button>

                                                        <!-- Finalize Document Modal -->
                                                        <div id="finalizeModal"
                                                            class="hidden fixed inset-0 z-60 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                                                            <div
                                                                class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full relative">
                                                                <button onclick="closeFinalizeModal()"
                                                                    class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                                                                    <svg class="w-6 h-6" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M6 18L18 6M6 6l12 12" />
                                                                    </svg>
                                                                </button>
                                                                <h3 class="text-lg font-semibold mb-4 text-black">Finalize
                                                                    Document</h3>
                                                                <p class="mb-6 text-gray-700">Are you sure you want to
                                                                    finalize the return of this document?</p>
                                                                <div class="flex justify-end gap-2">
                                                                    <button onclick="closeFinalizeModal()"
                                                                        class="bg-gray-300 hover:bg-gray-400 text-black font-semibold px-4 py-2 rounded">
                                                                        Cancel
                                                                    </button>
                                                                    <button
                                                                        class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded"
                                                                        type="submit">
                                                                        Finalize
                                                                    </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <script>
                                                            function openFinalizeModal() {
                                                                document.getElementById('finalizeModal').classList.remove('hidden');
                                                            }

                                                            function closeFinalizeModal() {
                                                                document.getElementById('finalizeModal').classList.add('hidden');
                                                            }
                                                        </script>
                                                    </div>
                                                </div>
                                            </div>
                                            <script>
                                                function openReturnedImageModal() {
                                                    document.getElementById('returnedImageModal').classList.remove('hidden');
                                                }

                                                function closeReturnedImageModal() {
                                                    document.getElementById('returnedImageModal').classList.add('hidden');
                                                }
                                            </script>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Comments Section (Right) -->
            <!-- Organization Info and Comments Section -->
            <div
                class="w-full md:w-1/3 bg-[#4D0F0F] text-white rounded-2xl p-4 md:p-6 flex flex-col justify-between mt-6 md:mt-0">
                <div class="space-y-4 md:space-y-6 flex-1 flex flex-col">
                    <div class="flex items-center space-x-3 md:space-x-4">
                        <!-- Profile Picture Placeholder -->
                        @if ($record->receiver && $record->receiver->profile_pic)
                            <div class="w-10 h-10 md:w-12 md:h-12 rounded-full overflow-hidden border-1 flex-shrink-0">
                                <img src="{{ asset('storage/' . $record->receiver->profile_pic) }}" alt="Profile"
                                    class="w-full h-full object-cover rounded-full">
                            </div>
                        @else
                            <div
                                class="w-10 h-10 md:w-12 md:h-12 bg-gray-300 rounded-full border-1 flex items-center justify-center flex-shrink-0">
                                <span class="text-gray-600 text-lg md:text-xl font-bold">
                                    {{ strtoupper(substr($record->receiver->username ?? 'O', 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <!-- Organization Details -->
                        <div class="overflow-hidden">
                            <p class="font-bold text-base md:text-lg break-words">
                                {{ $record->receiver->role_name ?? 'Organization Name' }}
                            </p>
                        </div>
                    </div>
                    <hr class="border-gray-600">
                    <!-- Comments Section (Latest at top, scrollbar starts at top) -->
                    <div id="commentCont" class="overflow-y-auto max-h-80 pr-2 flex flex-col-reverse scroll-smooth"
                        style="scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.3) transparent;">
                        @foreach ($comments->reverse() as $comment)
                            <!-- Modal for comment attachment preview -->
                        @endforeach
                        @if ($comments->isEmpty())
                            <div class="text-gray-300 text-center">No comments yet.</div>
                        @endif
                    </div>
                </div>
                <!-- Comment Input -->
                <div class="mt-4 md:mt-6">
                    <form id="commentForm" class="flex flex-col space-y-2" method="POST"
                        action="{{ route('comments.studentstore', $record->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-center bg-[#FFFFFFD6] rounded-full px-3 md:px-4 py-1">
                            <input type="hidden" name="document_id" value="{{ $record->id }}">
                            <input type="text" name="comment" id="commentInput" placeholder="Comment..."
                                class="flex-1 rounded-full py-1.5 md:py-2 px-3 md:px-4 bg-transparent text-black placeholder-gray-700 text-sm md:text-base focus:outline-none"
                                required />
                            <div class="flex items-center flex-shrink-0">
                                <label for="commentAttachment"
                                    class="text-[#4D0F0F] hover:text-[#5d0c0c] cursor-pointer mr-2">
                                    <input type="file" name="attachment" id="commentAttachment" class="hidden"
                                        accept=".jpg,.jpeg,.png,.pdf,.docx,.doc" />
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                </label>
                                <button id="submitCommentBtn" type="button"
                                    class="text-[#4D0F0F] hover:text-[#5d0c0c] cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <!-- Attachment preview -->
                        <div id="attachmentPreview" class="hidden flex items-center bg-gray-100 rounded-md p-2 mt-2">
                            <span id="attachmentName" class="text-xs text-gray-700 truncate flex-1"></span>
                            <button type="button" id="removeAttachment" class="text-gray-500 hover:text-red-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Include student comments script -->
    @vite(['resources/js/student-comments.js'])
    <style>
        /* Add fade-in animation for new comments */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }

        /* Ensure the comments container takes up proper space */
        #commentsContainer {
            scroll-behavior: smooth;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }

        #commentsContainer::-webkit-scrollbar {
            width: 6px;
        }

        #commentsContainer::-webkit-scrollbar-track {
            background: transparent;
        }

        #commentsContainer::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 6px;
        }

        .comment-container {
            overflow-x: hidden;
            word-break: break-word;
            width: 100%;
        }
    </style>

    @push('scripts')
        <script>
            function closeDocumentViewer() {
                document.getElementById('documentViewerModal').classList.add('hidden');
                document.getElementById('pdfViewer').innerHTML = '';
                document.getElementById('imageViewer').innerHTML = '';
            }

            function showTab(tab) {
                document.getElementById('previewTab').classList.toggle('bg-blue-500', tab === 'preview');
                document.getElementById('previewTab').classList.toggle('text-white', tab === 'preview');
                document.getElementById('previewTab').classList.toggle('text-gray-700', tab !== 'preview');
                document.getElementById('downloadTab').classList.toggle('bg-blue-500', tab === 'download');
                document.getElementById('downloadTab').classList.toggle('text-white', tab === 'download');
                document.getElementById('downloadTab').classList.toggle('text-gray-700', tab !== 'download');
                document.getElementById('pdfViewer').classList.toggle('hidden', tab !== 'preview');
                document.getElementById('imageViewer').classList.toggle('hidden', tab !== 'preview');
                document.getElementById('downloadView').classList.toggle('hidden', tab !== 'download');
            }
            document.addEventListener('DOMContentLoaded', function() {
                // Preview button click
                document.querySelectorAll('.preview-document-btn').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const fileUrl = btn.getAttribute('data-file-url');
                        const fileName = btn.getAttribute('data-file-name');
                        const ext = fileName.split('.').pop().toLowerCase();
                        document.getElementById('documentTitle').textContent = fileName;
                        document.getElementById('downloadFileName').textContent = fileName;
                        document.getElementById('downloadButton').href = fileUrl;
                        // Reset viewers
                        document.getElementById('pdfViewer').innerHTML = '';
                        document.getElementById('imageViewer').innerHTML = '';
                        document.getElementById('imageViewer').classList.add('hidden');
                        document.getElementById('pdfViewer').classList.remove('hidden');
                        document.getElementById('downloadView').classList.add('hidden');
                        if (['pdf'].includes(ext)) {
                            document.getElementById('pdfViewer').innerHTML =
                                `<iframe src="${fileUrl}#toolbar=0" class="w-full h-full" frameborder="0"></iframe>`;
                        } else if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(ext)) {
                            document.getElementById('pdfViewer').classList.add('hidden');
                            document.getElementById('imageViewer').classList.remove('hidden');
                            document.getElementById('imageViewer').innerHTML =
                                `<img src="${fileUrl}" alt="${fileName}" class="max-h-full max-w-full rounded shadow" />`;
                        } else {
                            document.getElementById('pdfViewer').innerHTML =
                                `<div class="flex items-center justify-center h-full text-gray-500">Preview not available for this file type.</div>`;
                        }
                        showTab('preview');
                        document.getElementById('documentViewerModal').classList.remove('hidden');
                    });
                });
                // Tab switching
                document.getElementById('previewTab').addEventListener('click', function() {
                    showTab('preview');
                });
                document.getElementById('downloadTab').addEventListener('click', function() {
                    showTab('download');
                });
            });
            // Utility to format date like 'F j Y, g:iA'
            function formatDateTime(date) {
                const options = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                };
                return date.toLocaleString('en-US', options);
            }
            // After successful approval (inside your finalize approval handler)
            document.getElementById('confirmFinalizeBtn').addEventListener('click', function() {
                // ...your AJAX or approval logic...
                // Get current date/time
                const now = new Date();
                const formatted = formatDateTime(now);
                // Find the status history container and update it
                const statusHistory = document.getElementById('statusHistory');
                if (statusHistory) {
                    statusHistory.innerHTML = `
                        <div class="relative pl-6 border-l-2 border-gray-600">
                            <div class="flex items-start mb-2 mt-2">
                                <span class="absolute left-0 mt-1.5 w-3 h-3 rounded-full bg-green-500 border-2 border-white"></span>
                                <div>
                                    <span class="font-semibold text-white">Office of the Student Services</span>
                                    <div class="text-gray-300">
                                        Approved, ${formatted}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });
        </script>
    @endpush
@endsection
