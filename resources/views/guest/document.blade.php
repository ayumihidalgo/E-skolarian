<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document View</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Add WebViewer directly -->
    <script src="{{ asset('webviewer/webviewer.min.js') }}"></script>
    
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f3f4f6;
        }
        #viewer {
            height: 70vh; 
            width: 100%;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <header class="bg-[#7A1212] text-white p-4 mb-6">
        <div class="container mx-auto">
            <h1 class="text-2xl font-bold">Document View</h1>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-5xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">{{ $document->title }}</h1>
            
            <!-- Document details -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-700">Document Details</h2>
                <p class="mt-2"><strong>Type:</strong> {{ $document->type }}</p>
                <p><strong>Status:</strong> <span class="px-2 py-1 rounded {{ $document->status === 'Approved' ? 'bg-green-100 text-green-800' : ($document->status === 'Returned' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">{{ $document->status }}</span></p>
                <p><strong>Submitted:</strong> {{ $document->created_at->format('F j, Y g:i A') }}</p>
            </div>
            
            <!-- Document file -->
            @if($document->latestVersion && $document->latestVersion->document_url)
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Document Preview</h2>
                
                <div class="border rounded-lg p-4 bg-gray-50">
                    <!-- Add tabs for preview/download -->
                    <div class="flex border-b">
                        <button id="previewTab" class="px-4 py-2 text-blue-500 border-b-2 border-blue-500 font-medium">
                            Preview
                        </button>
                        <button id="downloadTab" class="px-4 py-2 text-gray-700">
                            Download
                        </button>
                    </div>
                    
                    <!-- Preview content -->
                    <div id="previewContent" class="mt-4">
                        <!-- Document viewer -->
                        <div id="viewer"></div>
                        <!-- Fallback for images if WebViewer fails -->
                        <div id="imageViewer" class="hidden h-[70vh] flex items-center justify-center"></div>
                    </div>
                    
                    <!-- Download content -->
                    <div id="downloadContent" class="mt-4 hidden">
                        <div class="flex flex-col items-center justify-center py-6 space-y-4">
                            <div class="text-center">
                                <p class="mb-4">Click below to download the document</p>
                                <p class="text-sm mb-1">Filename: <span id="downloadFileName">{{ basename($document->latestVersion->document_url) }}</span></p>
                            </div>
                            <a href="{{ asset('storage/' . $document->latestVersion->document_url) }}" 
                               download="{{ basename($document->latestVersion->document_url) }}"
                               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span>Download</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Admin message -->
            @if($document->status === 'Approved')
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h2 class="text-green-800 font-semibold text-lg mb-2">Document Approved</h2>
                <p class="text-green-700">Your document has been approved by the administrator.</p>
                
                <!-- Show the most recent approval message if available -->
                @php
                $latestApproval = $document->reviews()->where('status', 'approved')->latest()->first();
                @endphp
                
                @if($latestApproval && isset($latestApproval->message))
                <div class="mt-3 border-t border-green-200 pt-3">
                    <h3 class="font-medium text-green-800">Message from Admin:</h3>
                    <p class="mt-1">{{ $latestApproval->message }}</p>
                </div>
                @endif
            </div>
            @elseif($document->status === 'Returned')
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h2 class="text-red-800 font-semibold text-lg mb-2">Document Returned</h2>
                <p class="text-red-700">The administrator has requested changes to your document.</p>
                
                <!-- Show the most recent return reason if available -->
                @php
                $latestReturn = $document->reviews()->where('status', 'returned')->latest()->first();
                @endphp
                
                @if($latestReturn && isset($latestReturn->message))
                <div class="mt-3 border-t border-red-200 pt-3">
                    <h3 class="font-medium text-red-800">Feedback:</h3>
                    <p class="mt-1">{{ $latestReturn->message }}</p>
                </div>
                @endif
            </div>
            @endif
            
            <!-- Document timeline -->
            @if(isset($document->timeline) && count($document->timeline) > 0)
            <div class="mt-8 mb-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Document Timeline</h2>
                <div class="space-y-4">
                    @foreach($document->timeline as $entry)
                    <div class="border-l-4 border-gray-300 pl-4 py-1">
                        <p class="text-sm text-gray-500">{{ $entry->created_at->format('F j, Y g:i A') }}</p>
                        <p class="font-medium">{{ ucfirst($entry->action) }}</p>
                        @if($entry->message)
                        <p class="text-gray-700 mt-1">{{ $entry->message }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <footer class="mt-12 py-6 bg-gray-100">
        <div class="container mx-auto px-4 text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setup tab switching
            const previewTab = document.getElementById('previewTab');
            const downloadTab = document.getElementById('downloadTab');
            const previewContent = document.getElementById('previewContent');
            const downloadContent = document.getElementById('downloadContent');
            
            previewTab.addEventListener('click', function() {
                previewTab.classList.add('text-blue-500', 'border-b-2', 'border-blue-500');
                previewTab.classList.remove('text-gray-700');
                downloadTab.classList.remove('text-blue-500', 'border-b-2', 'border-blue-500');
                downloadTab.classList.add('text-gray-700');
                
                previewContent.classList.remove('hidden');
                downloadContent.classList.add('hidden');
            });
            
            downloadTab.addEventListener('click', function() {
                downloadTab.classList.add('text-blue-500', 'border-b-2', 'border-blue-500');
                downloadTab.classList.remove('text-gray-700');
                previewTab.classList.remove('text-blue-500', 'border-b-2', 'border-blue-500');
                previewTab.classList.add('text-gray-700');
                
                downloadContent.classList.remove('hidden');
                previewContent.classList.add('hidden');
            });
            
            // Initialize WebViewer for document preview
            const viewerElement = document.getElementById('viewer');
            const imageViewer = document.getElementById('imageViewer');
            
            @if($document->latestVersion && $document->latestVersion->document_url)
                const filePath = '{{ asset('storage/' . $document->latestVersion->document_url) }}';
                const fileName = '{{ basename($document->latestVersion->document_url) }}';
                const fileExt = fileName.toLowerCase().split('.').pop();
                
                // Initialize WebViewer if the document exists
                if (viewerElement && filePath && typeof WebViewer !== 'undefined') {
                    const isDocx = fileExt === 'docx' || fileExt === 'doc';
                    const isPdf = fileExt === 'pdf';
                    const isImage = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(fileExt);
                    
                    // Initialize WebViewer for PDF, DOCX, or images
                    if (isPdf || isDocx || isImage) {
                        // Initialize WebViewer with appropriate options based on file type
                        const viewerOptions = {
                            path: '/webviewer',
                            initialDoc: filePath,
                            enableFilePicker: false,
                        };
                        
                        // Add specific options based on file type
                        if (isDocx) {
                            viewerOptions.extension = 'docx';
                        } else if (isImage) {
                            viewerOptions.extension = fileExt;
                            viewerOptions.disabledElements = [
                                'leftPanel',
                                'annotationPopup',
                                'contextMenuPopup'
                            ];
                        }
                        
                        // Initialize the WebViewer
                        try {
                            WebViewer(viewerOptions, viewerElement).then(instance => {
                                // Save instance for later cleanup
                                window.currentPdfViewerInstance = instance;
                                
                                // Basic configuration - Check if properties exist before using them
                                const { UI } = instance;
                                const docViewer = instance.Core ? instance.Core.documentViewer : instance.docViewer;
                                
                                // Enable download button in WebViewer (if UI exists)
                                if (UI) {
                                    UI.enableElements(['downloadButton']);
                                    UI.disableElements(['printButton']);
                                }
                                
                                // For images, fit to screen and disable annotations
                                if (isImage && docViewer) {
                                    // Check if docViewer exists before using .on()
                                    if (docViewer.on) {
                                        docViewer.on('documentLoaded', () => {
                                            // Handle image after loading
                                            if (instance.setFitMode) {
                                                instance.setFitMode(instance.FitMode.FIT_PAGE);
                                            }
                                            if (instance.disableAnnotations) {
                                                instance.disableAnnotations();
                                            }
                                            if (UI && UI.setZoomLevel) {
                                                UI.setZoomLevel(1.0);
                                            }
                                        });
                                    } else {
                                        // Fallback if .on() is not available
                                        console.log("docViewer.on method not available, trying alternative approach");
                                        setTimeout(() => {
                                            if (instance.setFitMode) {
                                                instance.setFitMode(instance.FitMode.FIT_PAGE);
                                            }
                                            if (instance.disableAnnotations) {
                                                instance.disableAnnotations();
                                            }
                                        }, 1000); // Give document a second to load
                                    }
                                    
                                    // Set toolbar options appropriate for image viewing
                                    if (UI && UI.setToolbarGroup) {
                                        UI.setToolbarGroup('viewerGroup');
                                    }
                                }
                                
                                // For DOCX files, configure specific options
                                if (isDocx && UI && UI.setToolbarGroup) {
                                    UI.setToolbarGroup('toolbarGroup-View');
                                }
                            }).catch(error => {
                                console.error("Failed to load WebViewer:", error);
                                
                                // Fallback to traditional image viewing if WebViewer fails for images
                                if (isImage) {
                                    viewerElement.classList.add('hidden');
                                    imageViewer.classList.remove('hidden');
                                    imageViewer.innerHTML = `<img src="${filePath}" class="max-h-full max-w-full object-contain" alt="Document Preview">`;
                                } else {
                                    viewerElement.innerHTML = `
                                        <div class="p-4 text-red-500">Failed to load document viewer. Error: ${error.message}</div>
                                        <div class="p-4">
                                            <a href="${filePath}" download="${fileName}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                                                Download Document Instead
                                            </a>
                                        </div>
                                    `;
                                }
                            });
                        } catch (error) {
                            console.error("Error initializing WebViewer:", error);
                            // Provide fallback for initialization errors
                            if (isImage) {
                                viewerElement.classList.add('hidden');
                                imageViewer.classList.remove('hidden');
                                imageViewer.innerHTML = `<img src="${filePath}" class="max-h-full max-w-full object-contain" alt="Document Preview">`;
                            } else {
                                viewerElement.innerHTML = `
                                    <div class="p-4 text-red-500">Failed to initialize document viewer: ${error.message}</div>
                                    <div class="p-4">
                                        <a href="${filePath}" download="${fileName}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                                            Download Document Instead
                                        </a>
                                    </div>
                                `;
                            }
                        }
                    } else {
                        // For unsupported file types, show message
                        viewerElement.innerHTML = `
                            <div class="p-4 bg-yellow-100 text-yellow-700 rounded">
                                <p>Preview not available for this file type (${fileExt}).</p>
                                <p class="mt-2">Please use the download tab to access the document.</p>
                            </div>
                        `;
                    }
                } else {
                    // Handle missing WebViewer or other required elements
                    if (viewerElement) {
                        if (typeof WebViewer === 'undefined') {
                            viewerElement.innerHTML = `
                                <div class="p-4 bg-red-100 text-red-700 rounded">
                                    <p>Document viewer library not loaded.</p>
                                    <p class="mt-2">Please use the download tab to access the document.</p>
                                </div>
                            `;
                        } else {
                            viewerElement.innerHTML = `
                                <div class="p-4 bg-red-100 text-red-700 rounded">
                                    <p>Document file not found.</p>
                                </div>
                            `;
                        }
                    }
                }
            @else
                // No document version available
                if (viewerElement) {
                    viewerElement.innerHTML = `
                        <div class="p-4 bg-yellow-100 text-yellow-700 rounded">
                            <p>No document file available for preview.</p>
                        </div>
                    `;
                }
            @endif
        });
    </script>
</body>
</html>