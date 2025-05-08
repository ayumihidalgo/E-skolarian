@extends('base')

@section('content')
@include('components.adminNavBarComponent')
@include('components.adminSidebarComponent')
<div id="main-content" class="transition-all duration-300 ml-[20%]">
    <div class="flex-grow bg-gray-100">
        <!-- <nav class="w-full bg-[#4d0F0F] h-[10%] p-4 text-white flex justify-end items-center space-x-6">
            <a href="#" class="hover:text-yellow-400 transition duration-200">
                <img src="{{ asset('images/mail.svg') }}" class="h-5 w-5" alt="Mail Icon">
            </a>
            <div>
                <a href="#" class="font-semibold">Organization</a>
            </div>
        </nav> -->
        
        <!-- Main Content -->
        <div id="mainContentArea" class="p-6 min-h-screen">
            <!-- Table View -->
            <div id="tableView" class="overflow-hidden h-full flex flex-col">
                <div class="text-black py-4 px-6 font-semibold text-2xl">
                    Admin Approval
                </div>

                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex items-center justify-between mb-4">
                        <!-- Search Bar -->
                        <div class="relative w-1/2 mr-2">
                            <input id="searchInput" type="text" class="w-full rounded-full border-1 border-[#9099A5] bg-white h-10 p-4" placeholder="Search...">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>
                        <!-- Filter Dropdowns -->
                        <div class="flex space-x-2">
                            <div class="relative w-40">
                                <select id="organizationFilter" class="block cursor-pointer appearance-none w-full bg-[#7A1212] hover:bg-[#DAA520] text-white py-2 px-4 pr-8 rounded-full leading-tight focus:outline-none hover:text-white transition-colors duration-200 truncate">
                                    <option class="bg-white text-black truncate" value="" disabled selected>Organization</option>
                                    <option class="bg-white text-black truncate" value="">All</option>
                                    <option class="bg-white text-black truncate" value="OSC">OSC</option>
                                    <option class="bg-white text-black truncate" value="AECES">AECES</option>
                                    <option class="bg-white text-black truncate" value="ACAP">ACAP</option>
                                    <option class="bg-white text-black truncate" value="ELITE">ELITE</option>
                                    <option class="bg-white text-black truncate" value="GIVE">GIVE</option>
                                    <option class="bg-white text-black truncate" value="JEHRA">JEHRA</option>
                                    <option class="bg-white text-black truncate" value="JMAP">JMAP</option>
                                    <option class="bg-white text-black truncate" value="JPIA">JPIA</option>
                                    <option class="bg-white text-black truncate" value="TAPNOTCH">TAPNOTCH</option>
                                    <option class="bg-white text-black truncate" value="SIGMA">SIGMA</option>
                                    <option class="bg-white text-black truncate" value="AGDS">AGDS</option>
                                    <option class="bg-white text-black truncate" value="Chorale">Chorale</option>
                                </select>
                                <div class="pointer-events-none absolute top-2 right-0 flex items-center px-3 text-white">
                                    <i class="fa-solid fa-sort-down"></i>
                                </div>
                            </div>
                            <div class="relative w-40">
                                <select id="documentTypeFilter" class="block cursor-pointer appearance-none w-48 min-w-[8rem] max-w-[10rem] bg-[#7A1212] hover:bg-[#DAA520] text-white py-2 px-4 pr-2 rounded-full leading-tight hover:text-white transition-colors duration-200 truncate">
                                    <option class="bg-white text-black truncate" value="" disabled selected>Document Type</option>
                                    <option class="bg-white text-black truncate" value="">All</option>
                                    <option class="bg-white text-black truncate" value="Event Proposal">Event Proposal</option>
                                    <option class="bg-white text-black truncate" value="General Plan">General Plan of Activities</option>
                                    <option class="bg-white text-black truncate" value="Calendar">Calendar of Activities</option>
                                    <option class="bg-white text-black truncate" value="Accomplishment Report">Accomplishment Report</option>
                                    <option class="bg-white text-black truncate" value="Constitution">Constitution and By-Laws</option>
                                    <option class="bg-white text-black truncate" value="Request Letter">Request Letter</option>
                                    <option class="bg-white text-black truncate" value="Off-Campus">Off-Campus</option>
                                    <option class="bg-white text-black truncate" value="Petition">Petition and Concern</option>
                                </select>
                                <div class="pointer-events-none absolute top-2 right-0 flex items-center px-3 text-white">
                                    <i class="fa-solid fa-sort-down"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Organization, Tag, Document Type Array -->
                    @php
                        $orgMap = [
                            'ACAP' => 'Association of Competent and Aspiring Psychologists',
                            'AECES' => 'Association of Electronics and Communications Engineering Students',
                            'ELITE' => 'Eligible League of Information Technology Enthusiasts',
                            'GIVE' => 'Guild of Imporous and Valuable Educators',
                            'JEHRA' => 'Junior Executive of Human Resource Association',
                            'JMAP' => 'Junior Marketing Association of the Philippines',
                            'JPIA' => 'Junior Philippine Institute of Accountants',
                            'PIIE' => 'Philippine Institute of Industrial Engineers',
                            'AGDS' => 'Artist Guild Dance Squad',
                            'Chorale' => 'PUP SRC Chorale',
                            'SIGMA' => 'Supreme Innovators’ Guild for Mathematics Advancements',
                            'TAPNOTCH' => 'Transformation Advocates through Purpose-driven and Noble Objectives Toward Community Holism',
                            'OSC' => 'Office of the Student Council'
                        ];
                        $orgKeys = array_keys($orgMap);
                        $tagColors = [
                            'OSC' => 'text-blue-500',
                            'ECE' => 'text-red-500',
                            'PSY' => 'text-purple-500',
                            'IT' => 'text-orange-500',
                            'HR' => 'text-pink-400',
                            'ACC' => 'text-pink-400',
                            'EDU' => 'text-blue-500',
                            'MAR' => 'text-yellow-500',
                            'IE' => 'text-green-500',
                            'TAP' => 'text-green-500',
                            'SIGMA' => 'text-yellow-900',
                            'AGDS' => 'text-yellow-900',
                            'CHO' => 'text-blue-500'
                        ];
                        $types = [
                            'Event Proposal', 'General Plan of Activities', 'Calendar of Activities',
                            'Accomplishment Report', 'Contribution and By-Laws', 'Request Letter',
                            'Off-Campus', 'Petition and Concern'
                        ];
                    @endphp

                    <!-- Document List Table -->
                    @php
                        // Test data for $documents
                        $documents = collect([
                            (object) [
                                'tag' => 'MAR-001',
                                'organization' => 'Junior Marketing Association of the Philippines',
                                'title' => 'JMAP_Request_AVR',
                                'date' => \Carbon\Carbon::parse('2024-01-15'),
                                'type' => 'Request',
                                'is_opened' => false,
                            ],
                            (object) [
                                'tag' => 'EDU-002',
                                'organization' => 'Guild of Imporous and Valuable Educators',
                                'title' => 'GIVE_Concerns',
                                'date' => \Carbon\Carbon::parse('2024-02-20'),
                                'type' => 'Request',
                                'is_opened' => false,
                            ],
                            (object) [
                                'tag' => 'IT-003',
                                'organization' => 'Eligible League of Information Technology Enthusiasts',
                                'title' => 'ELITE_IT_Week',
                                'date' => \Carbon\Carbon::parse('2024-03-10'),
                                'type' => 'Request',
                                'is_opened' => false,
                            ],
                            (object) [
                                'tag' => 'ACC-004',
                                'organization' => 'Junior Philippine Institute of Accountants',
                                'title' => 'JPIA_Seminar',
                                'date' => \Carbon\Carbon::parse('2024-04-05'),
                                'type' => 'Request',
                                'is_opened' => false,
                                ],
                            (object) [
                                'tag' => 'PSY-005',
                                'organization' => 'Association of Competent and Aspiring Psychologists',
                                'title' => 'ACAP_Seminar',
                                'date' => \Carbon\Carbon::parse('2024-05-12'),
                                'type' => 'Request',
                                'is_opened' => false,
                            ],
                        ]);

                        // Manually paginate the collection for testing
                        $page = request()->get('page', 1);
                        $perPage = 4; // Example: 3 items per page
                        $offset = ($page - 1) * $perPage;

                        $paginatedDocuments = new \Illuminate\Pagination\LengthAwarePaginator(
                            $documents->slice($offset, $perPage)->values()->all(), // Get the items for the current page
                            $documents->count(), // Total number of items
                            $perPage,
                            $page,
                            ['path' => request()->url()] // Important for generating correct URLs
                        );

                        $documents = $paginatedDocuments;

                    @endphp

                    <!-- Table Section -->
                    @if ($documents->isNotEmpty())
                        <div class="bg-gray-50 overflow-x-auto rounded-xl flex flex-col min-h-[300px]">
                            <table class="min-w-full text-sm rounded-xl">
                                <thead class="bg-[#5C0B0B] text-white">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-semibold">
                                            <div class="flex items-center cursor-pointer" onclick="sortTable(0, 'text')">
                                                <span>Tag</span>
                                                <div class="flex flex-col ml-1">
                                                    <i class="fa-solid fa-sort"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th class="px-6 py-3 text-left font-semibold">
                                            <div class="flex items-center cursor-pointer" onclick="sortTable(1, 'text')">
                                                <span>Organization</span>
                                                <div class="flex flex-col ml-1">
                                                    <i class="fa-solid fa-sort"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th class="px-6 py-3 text-left font-semibold">
                                            <div class="flex items-center cursor-pointer" onclick="sortTable(2, 'text')">
                                                <span>Title</span>
                                                <div class="flex flex-col ml-1">
                                                    <i class="fa-solid fa-sort"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th class="px-6 py-3 text-left font-semibold">
                                            <div class="flex items-center cursor-pointer" onclick="sortTable(3, 'date')">
                                                <span>Date</span>
                                                <div class="flex flex-col ml-1">
                                                    <i class="fa-solid fa-sort"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th class="px-6 py-3 text-left font-semibold">
                                            <div class="flex items-center cursor-pointer" onclick="sortTable(4, 'text')">
                                                <span>Type</span>
                                                <div class="flex flex-col ml-1">
                                                    <i class="fa-solid fa-sort"></i>
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documents as $document)
                                    <tr class="border-2 {{ !$document->is_opened ? 'border-[#7A1212] bg-white' : 'border-[#D9D9D9] bg-[#D9ACAC33]' }} cursor-pointer transition-all duration-150 hover:bg-[#DAA52080]">
                                            <!-- Tag -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    @php
                                                        // Extract organization acronym from document tag
                                                        $tagParts = preg_split('/-|_/', $document->tag);
                                                        $acronym = strtoupper($tagParts[0]);

                                                        // Map to color key
                                                        $colorKey = match($acronym) {
                                                            'PSY' => 'PSY',
                                                            'ECE' => 'ECE',
                                                            'IT' => 'IT',
                                                            'EDU' => 'EDU',
                                                            'HR' => 'HR',
                                                            'MAR' => 'MAR',
                                                            'ACC' => 'ACC',
                                                            'IE' => 'IE',
                                                            'AGDS' => 'AGDS',
                                                            'CHO' => 'CHO',
                                                            'SIGMA' => 'SIGMA',
                                                            'TAP' => 'TAP',
                                                            'OSC' => 'OSC',
                                                            default => 'text-gray-500'
                                                        };
                                                        $tagColor = $tagColors[$colorKey] ?? 'text-gray-500';
                                                    @endphp
                                                    <span class="font-bold {{ $tagColor }}">
                                                        {{ $document->tag }}
                                                    </span>
                                                </div>
                                            </td>

                                            <!-- Organization -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="truncate w-48" title="{{ $document->organization }}">{{ $document->organization }}</div>
                                            </td>

                                            <!-- Title -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $document->title }}
                                            </td>

                                            <!-- Date -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $document->date->format('n/j/Y') }}
                                            </td>

                                            <!-- Type -->
                                            <td class="px-6 py-4 whitespace-nowrap flex items-center">
                                                {{ $document->type }}
                                                @if(!$document->is_opened)
                                                    <span class="ml-2 h-2 w-2 bg-[#7A1212] rounded-full inline-block"></span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    @else
                        <div class="bg-gray-50 rounded-md p-8 flex flex-col items-center justify-center flex-grow">
                            <img src="{{ asset('images/no_entry.svg') }}" alt="No Data" class="mb-4 opacity-50 w-40 h-40">
                            <p class="text-gray-500 text-sm">No entry found at the moment</p>
                        </div>
                    @endif


                    <div class="flex justify-between items-center mt-4">
                        <!-- Previous Button -->
                        <a href="{{ $documents->previousPageUrl() }}"
                        class="bg-[#7A1212] cursor-pointer hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md disabled:bg-gray-400 disabled:cursor-not-allowed {{ $documents->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ $documents->onFirstPage() ? 'disabled' : '' }}>
                            ← Previous
                        </a>

                        <!-- Pagination Section -->
                        <div class="mt-4 flex justify-center">
                            <nav>
                                <ul class="inline-flex items-center space-x-2">
                                    <li>
                                        <a href="{{ $documents->url(1) }}"
                                        class="pagination-btn-first px-3 py-1 rounded-lg {{ $documents->onFirstPage() ? 'text-gray-600 cursor-not-allowed' : 'text-black' }}"
                                        {{ $documents->onFirstPage() ? 'disabled' : '' }}>
                                            First
                                        </a>
                                    </li>

                                    @foreach ($documents->getUrlRange(1, $documents->lastPage()) as $page => $url)
                                        <li>
                                            <a href="{{ $url }}"
                                            class="pagination-btn px-3 py-1 rounded-lg {{ $documents->currentPage() == $page ? 'bg-[#4D0F0F] text-white' : 'bg-gray-200 hover:bg-gray-300' }}">
                                                {{ $page }}
                                            </a>
                                        </li>
                                    @endforeach

                                    <li>
                                        <a href="{{ $documents->url($documents->lastPage()) }}"
                                        class="pagination-btn-last px-3 py-1 rounded-lg {{ $documents->currentPage() == $documents->lastPage() ? 'text-gray-600 cursor-not-allowed' : 'text-black' }}"
                                        {{ $documents->currentPage() == $documents->lastPage() ? 'disabled' : '' }}>
                                            Last
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>

                        <!-- Next Button -->
                        <a href="{{ $documents->nextPageUrl() }}"
                        class="bg-[#7A1212] cursor-pointer hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md disabled:bg-gray-400 disabled:cursor-not-allowed {{ !$documents->hasMorePages() ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ !$documents->hasMorePages() ? 'disabled' : '' }}>
                            Next →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Details View (initially hidden) -->
            <div id="detailsView" class="hidden h-full text-white">
                <div class="flex items-start justify-between px-6">
                    <!-- Title and Button aligned -->
                    <h2 class="font-semibold text-2xl text-black">Admin Approval</h2>
                    <button class="bg-[#7A1212] cursor-pointer text-white font-semibold rounded-full px-8 py-2 hover:bg-[#5d0c0c] focus:outline-none focus:ring-2 focus:ring-[#7A1212] focus:ring-opacity-50" onclick="closeDetailsPanel()">
                        Close
                    </button>
                </div>
                <div class="p-6 flex space-x-6 w-full max-w-7xl">
                    <!-- Left Side: Document Details -->
                    <div class="w-2/3 bg-[#4D0F0F] rounded-2xl p-6 space-y-6">
                        <!-- Header -->
                        <div class="flex justify-between items-start">
                            <div class="font-bold">
                                <p class="text-[#FFFFFF91] text-sm mb-1">April 10, 2025</p>
                                <p><span class="text-[#FFFFFF91] font-normal">From:</span> Eligible League of Information Technology</p>
                                <p><span class="text-[#FFFFFF91] font-normal">Title:</span> ELITE_IT_Week</p>
                                <p><span class="text-[#FFFFFF91]font-normal">Document Type:</span> Proposal</p>
                            </div>
                            <div class="text-right">
                                <p class="px-3 text-[#FFFFFF91] py-1 text-sm">IT_001</p>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div>
                            <h2 class="text-lg font-bold mb-2">Summary</h2>
                            <div class="bg-[#EFEFEF] text-gray-800 rounded-lg p-4">
                                <p>
                                    The ELITE organization is proposing to hold an inter-departmental IT week celebration featuring seminars, workshops, and competitions aimed at enhancing student skills and promoting collaboration. The event is scheduled for May 6–10, 2025, and will be held at the university auditorium. The proposal includes a detailed program, list of speakers, and budget breakdown.
                                </p>
                            </div>
                        </div>

                        <!-- 
                         -->
                        <div>
                            <h2 class="text-lg font-bold mb-2">Attachment</h2>
                            <div class="space-y-4">
                                <!-- Document preview button -->
                                <div class="bg-gray-200 text-gray-800 inline-flex items-center rounded-lg px-4 py-2 cursor-pointer hover:bg-gray-300" onclick="openDocumentViewer('ELITE_Event_Proposal.pdf', 'application/pdf')">
                                    <span class="mr-2">ELITE_Event_Proposal.pdf</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <h2 class="text-lg font-bold mb-2">Status</h2>
                            <div class="space-y-3 text-sm">
                                <div class="flex items-center space-x-2">
                                    <span class="h-2 w-2 bg-[#7A1212] rounded-full"></span>
                                    <div>
                                        <p><strong>Jonell Espalto</strong></p>
                                        <p>Under Review, April 15 2025, 1:45PM</p>
                                        <p>Approved, April 18 2025, 8:45PM</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="h-2 w-2 bg-[#7A1212] rounded-full"></span>
                                    <div>
                                        <p><strong>Leny Salmingo</strong></p>
                                        <p>Under Review, April 19 2025, 1:27AM</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-2 mt-4">
                            <button id="rejectButton" class="bg-[#C42E2E] hover:bg-red-700 text-white font-bold py-2 px-10 rounded-full cursor-pointer">Reject</button>
                            <button id="approveButton" class="bg-[#478642] hover:bg-green-700 text-white font-bold py-2 px-8 rounded-full cursor-pointer">Approve</button>
                        </div>
                    </div>

                    <!-- Right Side: Chat/Comments -->
                    <div class="w-1/3 bg-[#4D0F0F] text-white rounded-2xl p-6 flex flex-col justify-between">

                        <div class="space-y-6">
                            <div class="flex items-center space-x-4">
                                <!-- Profile Picture Placeholder -->
                                <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                    <span class="text-gray-600 text-xl font-bold">E</span>
                                </div>
                                <!-- Organization Details -->
                                <div>
                                    <p class="font-bold text-lg">ELITE</p>
                                    <p class="text-sm text-gray-300">Academic Organization</p>
                                </div>
                            </div>

                            <hr></hr>

                            <!-- Messages -->
                            <div class="space-y-4 text-sm overflow-y-auto max-h-[400px]" id="commentsContainer">
                                <div class="flex items-start gap-2">
                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.125h15.003m-15.003-5.5c-.023-3.423 3.454-6.125 6.911-6.125a6.907 6.907 0 016.91 6.125m-15.003 0h15.003" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 flex justify-between items-center rounded-lg px-4 py-2">
                                        <div class="text-white">
                                            <p class="font-bold text-sm">Jonell Espalto</p>
                                            <p class="text-sm mt-1">Okay na 'to?</p>
                                        </div>
                                        <p class="text-xs text-gray-300 ml-4 whitespace-nowrap">2 hours ago</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-2">
                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.125h15.003m-15.003-5.5c-.023-3.423 3.454-6.125 6.911-6.125a6.907 6.907 0 016.91 6.125m-15.003 0h15.003" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 flex justify-between items-center rounded-lg px-4 py-2">
                                        <div class="text-white">
                                            <p class="font-bold text-sm">E-Skolarian</p>
                                            <p class="text-sm mt-1">Student Services approved the document and sent to Campus Director</p>
                                        </div>
                                        <p class="text-xs text-gray-300 ml-4 whitespace-nowrap">1 hour ago</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Comment Input -->
                        <div class="flex items-center space-x-2 mt-6">
                            <input type="text"
                                id="commentInput"
                                placeholder="Comment..."
                                class="flex-1 rounded-full py-2 px-4 bg-white text-gray-700 focus:outline-none" />
                            <button onclick="submitComment()" class="text-white hover:text-gray-300 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Preview Modal -->
    <div id="documentViewerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white w-11/12 h-5/6 rounded-lg flex flex-col">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-semibold" id="documentTitle">Document Viewer</h3>
                <button onclick="closeDocumentViewer()" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-hidden">
                <!-- PDF Viewer using PDFObject -->
                <div id="pdfViewer" class="w-full h-full bg-gray-100">
                    <!-- PDF will be embedded here -->
                </div>
                
                <!-- Image Viewer -->
                <div id="imageViewer" class="hidden h-full flex items-center justify-center">
                    <img id="imageCanvas" class="max-w-full max-h-full" alt="Document Preview">
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Modal -->
    <div id="approvalModal" class="hidden fixed inset-0 bg-black bg z-50 flex items-center justify-center" style="background-color: rgba(0,0,0,0.3);">
        <div class="bg-white w-[30rem] h-[24rem] rounded-2xl shadow-xl p-6">
            <div class="bg-white rounded-t-lg">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-black">APPROVAL</h3>
                    <button id="closeApprovalModalBtn" class="text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-600 mt-2">
                    Please choose whether to forward the document for further review or to mark it as final and complete.
                </p>
            </div>

            <div class="mt-6 space-y-6">
                <div>
                    <p class="text-xs text-gray-500 mb-1 text-center">
                        Sends this document to another administrator for further review or suggested changes.
                    </p>
                    <button id="sendToAnotherAdminBtn" class="w-full bg-[#7A1212] hover:bg-[#5e0b0b] text-white py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212] focus:ring-opacity-50 cursor-pointer">
                        Send to Another Admin
                    </button>
                </div>

                <div class="mb-10">
                    <p class="text-xs text-gray-500 mb-1 text-center">
                        Final approval means the document is complete and can no longer be forwarded or edited.
                    </p>
                    <button id="finalizeApprovalBtn" class="w-full bg-[#7A1212] hover:bg-[#5e0b0b] text-white py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212] focus:ring-opacity-50 cursor-pointer">
                        Finalize Approval
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Send to Another Admin Modal -->
    <div id="sendToAdminModal" class="hidden fixed inset-0 bg-black z-50 flex items-center justify-center" style="background-color: rgba(0,0,0,0.3);">
        <div class="bg-white w-[30rem] rounded-2xl shadow-xl p-6">
            <div class="bg-white rounded-t-lg">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">APPROVAL</h3>
                    <button id="closeSendToAdminModalBtn" class="text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-600 mt-2">
                    SEND TO ANOTHER ADMIN
                </p>
            </div>

            <div class="mt-6 space-y-4">
                <div>
                    <div class="relative mt-1">
                        <label for="adminSelect" class="absolute -top-2 left-6 bg-white px-1 text-xs text-black">Send to</label>
                        <select id="adminSelect" class="mt-1 block w-full border border-black rounded-md py-2 px-3 shadow-sm focus:ring-[#7A1212] focus:border-[#7A1212] text-sm">
                            <option value="" disabled selected></option>
                            <option value="admin1">Admin 1</option>
                            <option value="admin2">Admin 2</option>
                            <option value="admin3">Admin 3</option>
                        </select>
                    </div>
                </div>

                <div>
                    <div class="relative mt-4">
                        <label for="adminMessage" class="absolute -top-2 left-6 bg-white px-1 text-xs text-black">Message</label>
                        <textarea id="adminMessage" rows="4" class="mt-1 block w-full border border-black rounded-md py-2 px-3 shadow-sm focus:ring-[#7A1212] focus:border-[#7A1212] text-sm"></textarea>
                    </div>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Send document to another admin for further review.</p>
                </div>

                <div>
                    <button id="sendToAdminSubmitBtn" class="w-full bg-[#7A1212] hover:bg-[#5e0b0b] text-white py-2.5 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212] focus:ring-opacity-50 text-sm font-semibold">
                        SEND
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Finalize Approval Confirmation Modal -->
    <div id="finalizeConfirmationModal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background-color: rgba(0,0,0,0.3);">
        <div class="bg-white w-[30rem] rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-semibold text-gray-800">Approval Confirmation</h3>
                <button id="closeFinalizeModalBtn" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <p class="text-sm text-gray-600 mb-6">
                Are you sure you want to finalize the document? Finalizing will restrict further changes or review
            </p>
            
            <div class="flex justify-end space-x-3">
                <button id="cancelFinalizeBtn" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                    Cancel
                </button>
                <button id="confirmFinalizeBtn" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 cursor-pointer">
                    Finalize Approval
                </button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background-color: rgba(0,0,0,0.3);">
        <div class="bg-white w-[30rem] rounded-2xl shadow-xl p-6">
            <div class="bg-white rounded-t-lg">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-black">REJECT</h3>
                    <button id="closeRejectModalBtn" class="text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-600 mt-2">
                    Choose Request Resubmission to ask for revisions, or Reject to decline the document and end the process.
                </p>
            </div>

            <div class="mt-6 space-y-6">
                <div>
                    <p class="text-xs text-gray-500 mb-1 text-center">
                        Returns the document to the sender with a request for changes or corrections.
                    </p>
                    <button id="requestResubmissionBtn" class="w-full bg-[#7A1212] hover:bg-[#5e0b0b] text-white py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212] focus:ring-opacity-50 cursor-pointer">
                        Request Resubmission
                    </button>
                </div>

                <div class="mb-10">
                    <p class="text-xs text-gray-500 mb-1 text-center">
                        Declines the document and ends the current review process.
                    </p>
                    <button id="confirmRejectBtn" class="w-full bg-[#7A1212] hover:bg-[#5e0b0b] text-white py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212] focus:ring-opacity-50 cursor-pointer">
                        Reject
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Request Resubmission Modal -->
    <div id="resubmissionModal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background-color: rgba(0,0,0,0.3);">
        <div class="bg-white w-[30rem] rounded-2xl shadow-xl p-6">
            <div class="bg-white rounded-t-lg">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-black">REQUEST RESUBMISSION</h3>
                    <button id="closeResubmissionModalBtn" class="text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-600 mt-2">
                    Add reason for resubmission.
                </p>
            </div>

            <div class="mt-6 space-y-4">
                <div>
                    <div class="relative mt-1">
                        <label for="resubmissionMessage" class="absolute -top-2 left-6 bg-white px-1 text-xs text-black">Message</label>
                        <textarea id="resubmissionMessage" rows="4" class="mt-1 block w-full border border-black rounded-md py-2 px-3 shadow-sm focus:ring-[#7A1212] focus:border-[#7A1212] text-sm"></textarea>
                    </div>
                </div>

                <div>
                    <p class="text-xs text-gray-500 text-center">Resend document to the organization with a request for revisions.</p>
                </div>

                <div>
                    <button id="submitResubmissionBtn" class="w-full bg-[#7A1212] hover:bg-[#5e0b0b] text-white py-2.5 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212] focus:ring-opacity-50 text-sm font-semibold uppercase">
                        Request
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Confirmation Modal -->
    <div id="rejectConfirmationModal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background-color: rgba(0,0,0,0.3);">
        <div class="bg-white w-[30rem] rounded-2xl shadow-xl p-6">
            <div class="bg-white rounded-t-lg">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-black">REJECT</h3>
                    <button id="closeRejectConfirmationModalBtn" class="text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-600 mt-2">
                    Add reason for rejection.
                </p>
            </div>

            <div class="mt-6 space-y-4">
                <div>
                    <div class="relative mt-1">
                        <label for="rejectionMessage" class="absolute -top-2 left-6 bg-white px-1 text-xs text-black">Message</label>
                        <textarea id="rejectionMessage" rows="4" class="mt-1 block w-full border border-black rounded-md py-2 px-3 shadow-sm focus:ring-[#7A1212] focus:border-[#7A1212] text-sm"></textarea>
                    </div>
                </div>

                <div>
                    <p class="text-xs text-gray-500 text-center">Marks the document as rejected ends its current processing.</p>
                </div>

                <div>
                    <button id="confirmFinalRejectBtn" class="w-full bg-[#7A1212] hover:bg-[#5e0b0b] text-white py-2.5 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212] focus:ring-opacity-50 text-sm font-semibold uppercase">
                        Reject
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Final Reject Confirmation Modal -->
    <div id="finalRejectConfirmationModal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background-color: rgba(0,0,0,0.3);">
        <div class="bg-white w-[30rem] rounded-lg shadow-xl p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-semibold text-gray-800">Reject Confirmation</h3>
                <button id="closeFinalRejectModalBtn" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <p class="text-sm text-gray-600 mb-6">
                Marks the document as rejected, stopping any further review or forwarding.
            </p>
            
            <div class="flex justify-end space-x-3">
                <button id="cancelFinalRejectBtn" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                    Cancel
                </button>
                <button id="finalizeRejectionBtn" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 cursor-pointer">
                    Finalize Rejection
                </button>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const texts = sidebar.querySelectorAll('.sidebar-text');
            const toggleBtn = document.getElementById('toggleBtn');
            const toggleIcon = document.getElementById('toggleIcon');

            sidebar.classList.toggle('w-1/4');
            sidebar.classList.toggle('w-20');

            // Move toggle button position
            if (sidebar.classList.contains('w-20')) {
                toggleBtn.classList.add('left-[4rem]');
                toggleBtn.classList.remove('left-[24%]');
            } else {
                toggleBtn.classList.remove('left-[4rem]');
                toggleBtn.classList.add('left-[24%]');
            }

            // Toggle hidden text
            texts.forEach(text => text.classList.toggle('hidden'));

            // Rotate toggle icon
            toggleIcon.classList.toggle('rotate-180');
        }

        // Filtering Function
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const organizationFilter = document.getElementById('organizationFilter');
            const documentTypeFilter = document.getElementById('documentTypeFilter');
            const tableRows = document.querySelectorAll('tbody tr');

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedOrg = organizationFilter.value.toLowerCase();
                const selectedType = documentTypeFilter.value.toLowerCase();

                tableRows.forEach(row => {
                    const tag = row.cells[0].textContent.toLowerCase();
                    const organization = row.cells[1].textContent.toLowerCase();
                    const title = row.cells[2].textContent.toLowerCase();
                    const type = row.cells[4].textContent.toLowerCase();

                    const matchesSearch = tag.includes(searchTerm) ||
                                        organization.includes(searchTerm) ||
                                        title.includes(searchTerm);

                    const matchesOrg = selectedOrg === '' || organization.includes(selectedOrg);
                    const matchesType = selectedType === '' || type.includes(selectedType);

                    if (matchesSearch && matchesOrg && matchesType) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                updateNoResultsMessage();
            }

            function updateNoResultsMessage() {
                const visibleRows = Array.from(tableRows).filter(row => row.style.display !== 'none');
                const tableContainer = document.querySelector('.overflow-x-auto');
                const noResultsDiv = document.querySelector('.no-results');

                if (visibleRows.length === 0) {
                    if (!noResultsDiv) {
                        const noResults = `
                            <div class="bg-gray-50 rounded-md p-8 flex flex-col items-center justify-center flex-grow no-results">
                                <img src="{{ asset('images/no_entry.svg') }}" alt="No Data" class="mb-4 opacity-50 w-40 h-40">
                                <p class="text-gray-500 text-sm">No matching records found</p>
                            </div>
                        `;
                        tableContainer.style.display = 'none';
                        tableContainer.insertAdjacentHTML('afterend', noResults);
                    }
                } else {
                    tableContainer.style.display = '';
                    if (noResultsDiv) {
                        noResultsDiv.remove();
                    }
                }
            }

            // Add event listeners
            searchInput.addEventListener('input', filterTable);
            organizationFilter.addEventListener('change', filterTable);
            documentTypeFilter.addEventListener('change', filterTable);
        });

        // Sorting Functionality
        let currentSort = {
            column: -1,
            direction: 'asc'
        };

        function sortTable(columnIndex, type) {
            const table = document.querySelector('table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const headers = table.querySelectorAll('th i');

            // Update sort direction
            if (currentSort.column === columnIndex) {
                currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.column = columnIndex;
                currentSort.direction = 'asc';
            }

            // Update sort icons
            headers.forEach(icon => {
                icon.className = 'fa-solid fa-sort';
            });

            const currentHeader = headers[columnIndex];
            currentHeader.className = `fa-solid fa-sort-${currentSort.direction === 'asc' ? 'up' : 'down'}`;

            // Sort rows
            rows.sort((a, b) => {
                let aValue = a.cells[columnIndex].textContent.trim();
                let bValue = b.cells[columnIndex].textContent.trim();

                if (type === 'date') {
                    // Convert date strings to Date objects
                    aValue = new Date(aValue.split('/').map((n, i) => i === 2 ? n : n.padStart(2, '0')).join('/'));
                    bValue = new Date(bValue.split('/').map((n, i) => i === 2 ? n : n.padStart(2, '0')).join('/'));
                }

                if (type === 'text') {
                    aValue = aValue.toLowerCase();
                    bValue = bValue.toLowerCase();
                }

                if (aValue < bValue) return currentSort.direction === 'asc' ? -1 : 1;
                if (aValue > bValue) return currentSort.direction === 'asc' ? 1 : -1;
                return 0;
            });

            // Reorder table rows
            rows.forEach(row => tbody.appendChild(row));

            // Update zebra striping
            rows.forEach((row, index) => {
                row.className = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
            });
        }
    </script>

    @vite('resources/js/admin-review.js')
@endsection
