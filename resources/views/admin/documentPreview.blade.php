@extends('base')

@section('content')
    @include('components.adminNavBarComponent')
    @include('components.adminSidebarComponent')

<!-- Main content area - positioned to the right of sidebar -->
<div id="main-content" class="transition-all duration-300 ml-[20%]">
    <!-- Full page container with styling -->
    <div class="w-full min-h-screen bg-[#f2f4f7] px-6 py-8 flex flex-col">
        <!-- Header section with title and back button -->
        <div class="flex justify-between items-center mb-6 mx-auto w-full max-w-[1055px]">
            <h2 class="text-2xl font-extrabold">Document Preview</h2>
            <!-- Back button to return to document history page -->
            <button type="button"
                onclick="window.history.back();"
                class="bg-[#7A1212] text-white px-4 py-2 rounded-full hover:bg-[#DAA520] w-[117px] h-[44px] flex items-center justify-center">
                Back
            </button>
        </div>

        {{-- Document Details Card --}}
        <div
            class="p-6 bg-[#4D0F0F] text-white rounded-[2rem] shadow-md space-y-6 w-full max-w-[1055px] mx-auto min-h-[450px]">
            {{-- General Information Section --}}
            <div class="space-y-3">
                <!-- Header with date and document tag -->
                <div class="flex flex-wrap justify-between items-center">
                    <!-- Document submission date - formatted for readability -->
                    <div>
                        <p class="font-semibold text-white/60">
                            {{ \Carbon\Carbon::parse($document['date'])->format('F d, Y g:i A') }}</p>
                    </div>
                    <!-- Document control tag/identifier -->
                    <div>
                        <p class="font-semibold text-white/60">{{ $document['tag'] }}</p>
                    </div>
                </div>

                <!-- Submitting organization name -->
                <p><strong class="text-white/60">From:</strong> <strong>{{ $document['organization'] }}</strong></p>
                <!-- Document title/subject -->
                <p><strong class="text-white/60">Title:</strong> <strong>{{ $document['title'] }}</strong></p>
                <!-- Document type/category -->
                <p><strong class="text-white/60">Type:</strong> <strong>{{ $document['type'] }}</strong></p>

                <!-- Document summary section -->
                <p><strong class="text-white/60">Summary:</strong></p>
                <!-- Content display area with contrasting background -->
                <div class="p-4 bg-[#f2f4f7] text-black rounded-xl">
                    <p class="text-sm">{{ $document['content'] }}</p>
                </div>

                                <!-- ATTACHMENT SECTION - Updated to match the UI design -->
                <div class="mt-4">
                    <p><strong class="text-white/60">Attachments:</strong></p>
                    <div class="mt-2">
                        @if(isset($document['attachments']) && count($document['attachments']) > 0)
                            @foreach($document['attachments'] as $attachment)
                                <a href="#" onclick="openDocumentViewer('{{ asset('storage/' . $attachment['document_url']) }}', '{{ basename($attachment['document_url']) }}'); return false;" 
                                   class="flex items-center gap-2 p-3 bg-white/10 rounded-lg hover:bg-white/20 transition w-fit mb-2 {{ isset($document['is_archived']) && $document['is_archived'] ? 'cursor-default' : '' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>View Document {{ $attachment['version'] > 1 ? '(Version ' . $attachment['version'] . ')' : '' }}</span>
                                    @if($attachment['is_latest'])
                                    <span class="ml-2 px-2 py-0.5 text-xs bg-[#10B981] text-white rounded-full">Latest</span>
                                    @endif
                                </a>
                            @endforeach
                        @elseif(isset($document['document_url']) && $document['document_url'])
                            <a href="#" onclick="openDocumentViewer('{{ asset('storage/' . $document['document_url']) }}', '{{ basename($document['document_url']) }}'); return false;" 
                               class="flex items-center gap-2 p-3 bg-white/10 rounded-lg hover:bg-white/20 transition w-fit {{ isset($document['is_archived']) && $document['is_archived'] ? 'cursor-default' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>View Document</span>
                            </a>
                        @else
                            <div class="p-3 bg-white/10 rounded-lg text-white/60">
                                No attachments available
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Document approval status section -->
                <p>
                    <strong class="text-white/60">Status:</strong><br>
                    <!-- Status pill with color coding based on document status -->
                    <span class="status-pill {{ $document['status'] === 'Approved' ? 'bg-[#10B981]' : ($document['status'] === 'Rejected' ? 'bg-[#EF4444]' : 'bg-[#F59E0B]') }} text-white px-4 py-1 rounded-full inline-block mt-1">
                        {{ $document['status'] }}
                    </span>
                </p>
                <input type="hidden" id="isArchivedDocument" value="{{ isset($document['is_archived']) && $document['is_archived'] ? 'true' : 'false' }}">
                <!-- REMARKS SECTION IF STATUS IS REJECTED - NEWLY ADDED -->
                @if($document['status'] === 'Rejected' && isset($document['remarks']))
                    <div class="mt-4">
                        <p><strong class="text-white/60">Rejection Reason:</strong></p>
                        <div class="p-4 bg-[#f2f4f7] text-black rounded-xl mt-2">
                            <p class="text-sm">{{ $document['remarks'] }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Document Preview Modal -->
<div id="documentViewerModal" class="hidden fixed inset-0 bg-black z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white w-11/12 h-5/6 rounded-lg flex flex-col">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 id="documentTitle" class="font-semibold text-lg truncate">Document Preview</h3>
            <div class="flex items-center space-x-4">
                <!-- Tabs for Preview and Download -->
                <div class="flex items-center bg-gray-100 rounded-lg p-1">
                    <button id="previewTab" class="py-1 px-4 rounded-lg bg-blue-500 text-white cursor-pointer">Preview</button>
                    <button id="downloadTab" class="py-1 px-4 rounded-lg text-gray-700 cursor-pointer">Download</button>
                </div>
                <!-- Close Button -->
                <button onclick="closeDocumentViewer()" class="text-gray-500 hover:text-gray-700 cursor-pointer">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="flex-1 overflow-hidden">
            <!-- PDF Viewer -->
            <div id="pdfViewer" class="w-full h-full overflow-auto"></div>
            
            <!-- Image Viewer -->
            <div id="imageViewer" class="hidden h-full flex items-center justify-center bg-gray-100"></div>
            
            <!-- Download View -->
            <div id="downloadView" class="hidden h-full flex items-center justify-center bg-gray-100 flex-col p-8">
                <h3 id="downloadFileName" class="text-xl font-semibold mb-4">filename.pdf</h3>
                <p class="text-gray-600 mb-8 text-center">Click the button below to download this document</p>
                <a id="downloadButton" href="#" download class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-lg flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Document
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Add JavaScript for document viewer functionality -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>
<script>
    // Set up PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.worker.min.js';
    
    // Get archived status once on page load
    const isArchived = document.getElementById('isArchivedDocument').value === 'true';
    
    // Hide download tab if document is archived
    document.addEventListener('DOMContentLoaded', function() {
        // Hide download tab if archived
        if (isArchived) {
            const downloadTab = document.getElementById('downloadTab');
            if (downloadTab) {
                downloadTab.style.display = 'none';
            }
        }
    });

    function openDocumentViewer(filePath, fileName) {
        const modal = document.getElementById('documentViewerModal');
        const pdfViewer = document.getElementById('pdfViewer');
        const imageViewer = document.getElementById('imageViewer');
        const downloadView = document.getElementById('downloadView');
        const titleElement = document.getElementById('documentTitle');
        const downloadFileName = document.getElementById('downloadFileName');
        const downloadButton = document.getElementById('downloadButton');
        
        // Set document title and download info
        titleElement.textContent = fileName;
        downloadFileName.textContent = fileName;
        downloadButton.setAttribute('href', filePath);
        
        // Clear previous content
        pdfViewer.innerHTML = '';
        imageViewer.innerHTML = '';
        
        // Determine file type
        const fileExtension = fileName.split('.').pop().toLowerCase();
        
        // Initially show PDF viewer and hide others
        pdfViewer.classList.remove('hidden');
        imageViewer.classList.add('hidden');
        downloadView.classList.add('hidden');
        
        // Reset tab styling
        document.getElementById('previewTab').classList.add('bg-blue-500', 'text-white');
        document.getElementById('previewTab').classList.remove('text-gray-700');
        document.getElementById('downloadTab').classList.remove('bg-blue-500', 'text-white');
        document.getElementById('downloadTab').classList.add('text-gray-700');
        
        // Handle different file types
        if (['pdf'].includes(fileExtension)) {
            // PDF file - use PDF.js
            const loadingTask = pdfjsLib.getDocument(filePath);
            loadingTask.promise.then(function(pdf) {
                // Get the first page
                pdf.getPage(1).then(function(page) {
                    const viewport = page.getViewport({scale: 1.5});
                    
                    // Prepare canvas
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    
                    // Add canvas to viewer
                    pdfViewer.appendChild(canvas);
                    
                    // Render PDF page
                    const renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };
                    page.render(renderContext);
                });
            }).catch(function(error) {
                console.error('Error loading PDF:', error);
                pdfViewer.innerHTML = '<div class="p-4 bg-red-100 text-red-700">Failed to load PDF. Please try downloading the file instead.</div>';
            });
        } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
            // Image file
            pdfViewer.classList.add('hidden');
            imageViewer.classList.remove('hidden');
            
            const img = document.createElement('img');
            img.src = filePath;
            img.className = 'max-h-full max-w-full';
            imageViewer.appendChild(img);
        } else {
            // Other file types - show download view directly
            pdfViewer.classList.add('hidden');
            downloadView.classList.remove('hidden');
        }
        
        // Show the modal
        modal.classList.remove('hidden');
        
        // Handle archived documents - prevent download view from showing
        if (isArchived && document.getElementById('downloadTab')) {
            document.getElementById('downloadTab').style.display = 'none';
            
            // Add indicator that document is archived
            titleElement.textContent = fileName + ' (Archived - Preview Only)';
        }
        
        // Set up tab switching (remove previous event listeners first)
        const previewTab = document.getElementById('previewTab');
        const downloadTab = document.getElementById('downloadTab');
        
        // Clone nodes to remove event listeners
        const newPreviewTab = previewTab.cloneNode(true);
        const newDownloadTab = downloadTab.cloneNode(true);
        previewTab.parentNode.replaceChild(newPreviewTab, previewTab);
        downloadTab.parentNode.replaceChild(newDownloadTab, downloadTab);
        
        document.getElementById('previewTab').addEventListener('click', function() {
            if (['pdf'].includes(fileExtension)) {
                pdfViewer.classList.remove('hidden');
                imageViewer.classList.add('hidden');
            } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                imageViewer.classList.remove('hidden');
                pdfViewer.classList.add('hidden');
            }
            downloadView.classList.add('hidden');
            
            // Update active tab styling
            this.classList.add('bg-blue-500', 'text-white');
            this.classList.remove('text-gray-700');
            document.getElementById('downloadTab').classList.remove('bg-blue-500', 'text-white');
            document.getElementById('downloadTab').classList.add('text-gray-700');
        });
        
        document.getElementById('downloadTab').addEventListener('click', function() {
            if (isArchived) {
                alert('This document is archived and cannot be downloaded.');
                return;
            }
            
            // Show download view
            pdfViewer.classList.add('hidden');
            imageViewer.classList.add('hidden');
            downloadView.classList.remove('hidden');
            
            // Update active tab styling
            this.classList.add('bg-blue-500', 'text-white');
            this.classList.remove('text-gray-700');
            document.getElementById('previewTab').classList.remove('bg-blue-500', 'text-white');
            document.getElementById('previewTab').classList.add('text-gray-700');
        });
    }

    function closeDocumentViewer() {
        const modal = document.getElementById('documentViewerModal');
        const pdfViewer = document.getElementById('pdfViewer');
        const imageViewer = document.getElementById('imageViewer');
        
        // Clear viewers
        pdfViewer.innerHTML = '';
        imageViewer.innerHTML = '';
        
        // Hide modal
        modal.classList.add('hidden');
    }
</script>
@endsection
