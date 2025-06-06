@extends('base')

<style>
    @media (max-width: 767px) {
        #actionButtonsContainer {
            width: 100%;
            margin-top: 1rem;
            display: flex;
            justify-content: space-between;
        }
        
        #actionButtonsContainer button {
            flex: 1;
            margin: 0 0.25rem;
        }
        
        /* Ensure the status section has proper spacing */
        #statusSection {
            margin-bottom: 0.5rem;
        }

        /* This is the important part - ensure hidden state is preserved with !important */
        #actionButtonsContainer.hidden {
            display: none !important;
        }

        #main-content{
            margin-left: 0;
        }
    }
</style>

@section('content')
    @include('components.adminSidebarComponent')
    <div id="main-content" class="transition-all duration-300 ml-[20%] sm:mt-16 md:mt-0">
        @include('components.adminNavBarComponent')
        <div class="flex-grow bg-gray-100">
            <!-- Main Content -->
            <div id="mainContentArea" class="p-2 sm:p-4 md:p-6 h-auto">
                <!-- Table View -->
                <div id="tableView" class="overflow-hidden h-full flex flex-col">
                    <div class="text-black py-2 md:py-4 px-2 md:px-6 font-extrabold text-xl md:text-2xl">
                        Admin Review
                    </div>

                    <div class="p-2 md:p-6 flex flex-col flex-grow">
                        <!-- Search and Filter Section - Responsive Layout -->
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-2 md:space-y-0">
                            <!-- Search Bar - Full width on mobile -->
                            <div class="relative w-full md:w-1/2 md:mr-2">
                                <input id="searchInput" type="text" class="w-full rounded-full border-1 border-[#9099A5] bg-white h-10 p-4" placeholder="Search...">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                            </div>
                            
                            <!-- Filter Dropdowns - Side by side on all viewports including mobile -->
                            <div class="flex gap-2 w-full md:w-auto">
                                <div class="relative flex-1 md:w-40">
                                    <select id="organizationFilter" class="block cursor-pointer appearance-none w-full bg-[#7A1212] hover:bg-[#DAA520] text-white py-2 px-4 pr-8 rounded-full leading-tight focus:outline-none hover:text-white transition-colors duration-200 truncate">
                                        <option class="bg-white text-black truncate disabled:hover:bg-white disabled:hover:text-black" value="" disabled selected>Organization</option>
                                        <option class="bg-white text-black truncate" value="">All</option>
                                        @foreach($organizations as $org)
                                            <option class="bg-white text-black truncate" value="{{ $org }}" {{ $selectedOrg == $org ? 'selected' : '' }}>
                                                {{ $org }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute top-2 right-0 flex items-center px-3 text-white">
                                        <i class="fa-solid fa-sort-down"></i>
                                    </div>
                                </div>
                                <div class="relative flex-1 md:w-40">
                                    <select id="documentTypeFilter" class="block cursor-pointer appearance-none w-full bg-[#7A1212] hover:bg-[#DAA520] text-white py-2 px-4 pr-6 rounded-full leading-tight hover:text-white transition-colors duration-200 truncate">
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

                        <!-- Table Section -->
                        @if ($documents->isNotEmpty())
                            <div class="bg-white rounded-[24px] shadow-md overflow-x-auto pt-2 flex flex-col min-h-[400px]">
                                <table class="min-w-full text-sm rounded-[24px]">
                                    <thead class="bg-white text-black font-extrabold text-lg">
                                        <tr>
                                            <th class="px-6 py-3 text-left">
                                                <div class="flex items-center cursor-pointer" onclick="sortTable(0, 'text')">
                                                    <span>Tag</span>
                                                    <div class="flex flex-col ml-1">
                                                        <i class="fa-solid fa-sort text-[#9099A5]"></i>
                                                    </div>
                                                </div>
                                            </th>
                                            <th class="px-6 py-3 text-left">
                                                <div class="flex items-center cursor-pointer" onclick="sortTable(1, 'text')">
                                                    <span>Organization</span>
                                                    <div class="flex flex-col ml-1">
                                                        <i class="fa-solid fa-sort text-[#9099A5]"></i>
                                                    </div>
                                                </div>
                                            </th>
                                            <th class="px-6 py-3 text-left">
                                                <div class="flex items-center cursor-pointer" onclick="sortTable(2, 'text')">
                                                    <span>Title</span>
                                                    <div class="flex flex-col ml-1">
                                                        <i class="fa-solid fa-sort text-[#9099A5]"></i>
                                                    </div>
                                                </div>
                                            </th>
                                            <th class="px-6 py-3 text-left">
                                                <div class="flex items-center cursor-pointer" onclick="sortTable(3, 'date')">
                                                    <span>Date</span>
                                                    <div class="flex flex-col ml-1">
                                                        <i class="fa-solid fa-sort text-[#9099A5]"></i>
                                                    </div>
                                                </div>
                                            </th>
                                            <th class="px-6 py-3 text-left ">
                                                <div class="flex items-center cursor-pointer" onclick="sortTable(4, 'text')">
                                                    <span>Type</span>
                                                    <div class="flex flex-col ml-1">
                                                        <i class="fa-solid fa-sort text-[#9099A5]"></i>
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($documents as $document)
                                        <tr class="border-2 {{ !$document->is_opened ? 'border-[#7A1212] bg-white' : 'border-[#D9D9D9] bg-[#D9ACAC33]' }} cursor-pointer transition-all duration-150 hover:bg-[#DAA52080]" data-document-id="{{ $document->id }}">
                                                <!-- Tag -->
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        @php
                                                            // Extract organization acronym from document tag
                                                            $tagParts = preg_split('/-|_/', $document->tag);
                                                            $acronym = !empty($tagParts) ? strtoupper($tagParts[0]) : '';

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
                                                                'DOC' => 'DOC',
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
                                                    <div class="truncate w-64" title="{{ $document->title }}">{{ $document->title }}</div>
                                                </td>

                                                <!-- Date -->
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $document->date->format('n/j/Y') }}
                                                </td>

                                                <!-- Type -->
                                                <td class="px-6 py-4 whitespace-nowrap relative">
                                                    <div class="flex items-center justify-between w-full">
                                                        <span>{{ $document->type }}</span>
                                                        @if(!$document->is_opened)
                                                            <span class="h-2 w-2 bg-[#7A1212] rounded-full inline-block"></span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody> 
                                </table>
                            </div>

                        @else
                            <div class="bg-white rounded-[24px] shadow-md overflow-hidden p-8 flex flex-col items-center justify-center flex-grow h-auto min-h-[500px]">
                                <img src="{{ asset('images/no_entry.svg') }}" alt="No Data" class="mb-4 opacity-50 w-40 h-40">
                                <p class="text-gray-500 text-sm">No entry found at the moment</p>
                            </div>
                        @endif


                        <div class="flex justify-center mt-4">
                            <!-- Pagination Section -->
                            @if ($documents->isNotEmpty())
                                <div class="flex justify-center">
                                    <nav>
                                        <ul class="inline-flex items-center space-x-2">
                                            <li>
                                                <a href="{{ $documents->onFirstPage() ? '#' : $documents->previousPageUrl() }}"
                                                class="pagination-btn-prev px-3 py-1 rounded-lg {{ $documents->onFirstPage() ? 'text-gray-600 cursor-not-allowed bg-gray-200' : 'text-black hover:bg-gray-300' }}"
                                                @if($documents->onFirstPage()) onclick="return false;" @endif>
                                                    &lt;
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
                                                <a href="{{ $documents->hasMorePages() ? $documents->nextPageUrl() : '#' }}"
                                                class="pagination-btn-next px-3 py-1 rounded-lg {{ $documents->hasMorePages() ? 'text-black hover:bg-gray-300' : 'text-gray-600 cursor-not-allowed bg-gray-200' }}"
                                                @if(!$documents->hasMorePages()) onclick="return false;" @endif>
                                                    &gt;
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Details View (initially hidden) -->
                <div id="detailsView" class="hidden h-auto text-white">
                    <!-- Header with close button -->
                    <div class="flex items-start justify-between px-3 md:px-6 mb-2 md:mb-0">
                        <h2 class="font-extrabold text-lg md:text-2xl text-black">Admin Review</h2>
                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-2 mt-3 md:mt-4">
                            <div id="actionButtonsContainer" class="flex space-x-2 order-first md:order-none">
                                <button id="rejectButton" class="bg-[#C42E2E] hover:bg-red-700 text-white font-bold py-1.5 md:py-2 px-6 md:px-10 text-sm md:text-base rounded-full cursor-pointer">Return</button>
                                <button id="approveButton" class="bg-[#478642] hover:bg-green-700 text-white font-bold py-1.5 md:py-2 px-5 md:px-8 text-sm md:text-base rounded-full cursor-pointer">Approve</button>
                            </div>
                            <button class="bg-[#7A1212] cursor-pointer text-white font-semibold rounded-full px-4 md:px-8 py-1 md:py-2 text-sm md:text-base hover:bg-[#5d0c0c] focus:outline-none focus:ring-2 focus:ring-[#7A1212] focus:ring-opacity-50" onclick="closeDetailsPanel()">Close</button>
                        </div>
                    </div>

                    <!-- Mobile view - stacks vertically -->
                    <div class="p-3 md:p-6 flex flex-col md:flex-row md:space-x-6 space-y-4 md:space-y-0 w-full max-w-7xl">
                        <!-- Document Details - Full width on mobile -->
                        <div id="documentDetails" class="w-full md:w-2/3 bg-[#4D0F0F] rounded-2xl p-4 md:p-6 space-y-4 md:space-y-6">
                            <!-- Header -->
                            <div class="flex justify-between items-start">
                                <div class="font-bold text-sm md:text-base max-w-[70%]">
                                    <p id="documentDate" class="text-xs text-[#FFFFFF91] md:text-sm mb-1 break-words"></p>
                                    <p id="documentOrg" class="break-words"><span class="text-[#FFFFFF91] font-normal">From:</span> </p>
                                    <p id="documentTitle" class="break-words"><span class="text-[#FFFFFF91] font-normal">Title:</span> </p>
                                    <p id="documentType" class="break-words"><span class="text-[#FFFFFF91] font-normal">Document Type:</span> </p>
                                </div>
                                <div class="text-right">
                                    <p id="documentTag" class="px-2 md:px-3 text-[#FFFFFF91] py-1 text-xs md:text-sm break-words"></p>
                                </div>
                            </div>

                            <!-- Summary -->
                            <div>
                                <h2 class="text-base md:text-lg text-[#FFFFFF91] font-bold mb-1 md:mb-2">Summary</h2>
                                <div class="bg-[#EFEFEF] text-gray-800 rounded-lg p-3 md:p-4 max-h-[200px] overflow-y-auto">
                                    <p class="text-black break-words whitespace-normal text-sm md:text-base" id="documentSummary">
                                        <!-- Summary will be inserted here -->
                                    </p>
                                </div>
                            </div>

                            <!-- Added Details For Event Proposal Document Type -->
                            <div id="eventProposalDetails" class="hidden">
                                <div class="font-bold text-sm md:text-base max-w-[70%]">
                                    <p id="venueInfo" class="break-words"><span class="text-[#FFFFFF91] font-normal">Venue:</span> </p>
                                    <p id="dateTimeInfo" class="break-words"><span class="text-[#FFFFFF91] font-normal">Date & Time:</span> </p>
                                    <p id="hoursInfo" class="break-words"><span class="text-[#FFFFFF91] font-normal">No. Of Hours:</span> </p>
                                    <p id="attendeesInfo" class="break-words"><span class="text-[#FFFFFF91] font-normal">Attendees:</span> </p>
                                    <p id="numAttendeesInfo" class="break-words"><span class="text-[#FFFFFF91] font-normal">Expected No. of Attendees:</span> </p>
                                    <p id="feeInfo" class="break-words"><span class="text-[#FFFFFF91] font-normal">Fee/Contribution per Student/Participant:</span> </p>
                                </div>                         
                            </div>

                            <!-- Attachment -->
                            <div>
                                <h2 class="text-base md:text-lg text-[#FFFFFF91] font-bold mb-1 md:mb-2">Attachment</h2>
                                <div class="space-y-2 md:space-y-4">
                                    <!-- Document previews will be inserted here -->
                                    <div id="attachmentSection" class="space-y-2"></div>
                                </div>
                            </div>

                            <!-- Status section -->
                            <div id="statusSection">
                                <h2 class="text-base md:text-lg text-[#FFFFFF91] font-bold mb-1 md:mb-2">Status</h2>
                                
                                <!-- Status history with timeline style -->
                                <div class="text-xs md:text-sm overflow-y-auto max-h-[250px] pb-2" id="statusHistory">
                                    <!-- Status history will be inserted here by JavaScript -->
                                    <div class="relative pl-6 border-l-2 border-gray-600">
                                        <div class="mb-4 relative">
                                            <!-- Placeholder for empty state -->
                                            <p class="text-gray-300">Loading status updates...</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Approved status indicator - Initially hidden, will be shown by JS -->
                                <div id="processedStatusIndicator" class="hidden mt-2 p-3 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 rounded">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium">This document has already been processed and cannot be modified.</p>
                                        </div>
                                    </div>
                                </div>
                                 <!-- Returned status indicator - Initially hidden, will be shown by JS -->
                                <div id="returnedStatusIndicator" class="hidden mt-2 p-3 bg-blue-100 border-l-4 border-blue-500 text-blue-700 rounded max-h-[200px] overflow-y-auto" style="scrollbar-width: thin; scrollbar-color: rgba(0, 142, 188, 0.53) transparent;">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3 overflow-hidden">
                                            <p class="text-sm font-medium break-words">You've requested resubmission for this document on <span id="forwardedDate">loading date...</span></p>
                                            <p class="text-sm mt-1 break-words">Message: <span id="forwardedMessage" class="italic break-words">Loading message...</span></p>
                                            <p class="text-xs mt-2 font-medium break-words">You can still view this document, but you can only perform actions once the organization resubmits the document.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Organization Info and Comments - Full width on mobile -->
                        <div class="w-full md:w-1/3 bg-[#4D0F0F] text-white rounded-2xl p-4 md:p-6 flex flex-col justify-between">
                            <div class="space-y-4 md:space-y-6">
                                <div class="flex items-center space-x-3 md:space-x-4">
                                    <!-- Profile Picture Placeholder -->
                                    <div id="orgProfileContainer" class="w-12 h-12 bg-gray-300 rounded-full overflow-hidden flex items-center justify-center border border-gray-600">
                                        <span id="orgInitial" class="text-gray-600 text-lg font-bold">O</span>
                                    </div>
                                    <!-- Organization Details -->
                                    <div class="overflow-hidden">
                                        <p id="orgName" class="font-bold text-base md:text-lg truncate">Organization Name</p>
                                        <p id="orgType" class="text-xs md:text-sm text-gray-300 truncate">Academic Organization</p>
                                    </div>
                                </div>

                                <hr class="border-white"></hr>

                                <!-- Comments section -->
                                <div class="space-y-3 md:space-y-4 text-xs md:text-sm overflow-y-auto max-h-[500px]" id="commentsContainer" style="scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.3) transparent;">
                                    <!-- Comments will be loaded here -->
                                </div>
                            </div>

                            <!-- Comment Input -->
                            <div class="mt-4 md:mt-6">
                                <form id="commentForm" class="flex flex-col space-y-2">
                                    <div class="flex items-center bg-[#FFFFFFD6] rounded-full px-3 md:px-4 py-1">
                                        <input type="text"
                                            id="commentInput"
                                            placeholder="Comment..."
                                            class="flex-1 rounded-full py-1.5 md:py-2 px-3 md:px-4 bg-transparent text-black placeholder-gray-700 text-sm md:text-base focus:outline-none" />
                                        <div class="flex items-center flex-shrink-0">
                                            <label for="commentAttachment" class="text-[#4D0F0F] hover:text-[#5d0c0c] cursor-pointer mr-2">
                                                <input type="file" id="commentAttachment" class="hidden" accept=".jpg,.jpeg,.png,.pdf,.docx,.doc" />
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                            </label>
                                            <button id="submitCommentBtn" type="button" class="text-[#4D0F0F] hover:text-[#5d0c0c] cursor-pointer">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Attachment preview -->
                                    <div id="attachmentPreview" class="hidden flex items-center bg-gray-100 rounded-md p-2 mt-2">
                                        <span id="attachmentName" class="text-xs text-gray-700 truncate flex-1"></span>
                                        <button type="button" id="removeAttachment" class="text-gray-500 hover:text-red-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Preview Modal -->
        <div id="documentViewerModal" class="hidden fixed inset-0 bg-black z-50 flex items-center justify-center backdrop-blur-sm" style="background-color: rgba(0,0,0,0.3);">
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
                    <div id="pdfViewer" class="w-full h-full"></div>
                    
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

        <!-- Final Approval Message Modal -->
        <div id="finalApprovalMessageModal" class="hidden fixed inset-0 bg-black z-50 flex items-center justify-center backdrop-blur-sm" style="background-color: rgba(0,0,0,0.3);">
            <div class="bg-white w-[30rem] rounded-xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-bold text-black">APPROVAL</h3>
                    <button id="closeApprovalMessageModalBtn" class="text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-600 mb-8">FINALIZE APPROVAL</p>
                
                <div class="mb-6">
                    <textarea id="approvalMessage" rows="5" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 shadow-sm focus:ring-[#7A1212] focus:border-[#7A1212] text-sm"></textarea>
                </div>
                
                <div>
                    <p class="text-xs text-gray-500 text-center mb-4">Send message for final approval.</p>
                </div>

                <div class="text-center">
                    <button id="sendApprovalMessageBtn" class="w-full bg-[#7A1212] hover:bg-[#5e0b0b] text-white py-2.5 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212] focus:ring-opacity-50 text-sm font-semibold cursor-pointer">
                        SEND
                    </button>
                </div>
            </div>  
        </div>

        <!-- Finalize Approval Confirmation Modal -->
        <div id="finalizeConfirmationModal" class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm" style="background-color: rgba(0,0,0,0.3);">
            <div class="bg-white w-[30rem] rounded-2xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-semibold text-gray-800">Approval Confirmation</h3>
                    <button id="closeFinalizeModalBtn" class="text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
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

        <!-- Return Document Modal -->
        <div id="returnModal" class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm" style="background-color: rgba(0,0,0,0.3);">
            <div class="bg-white w-[30rem] rounded-2xl shadow-xl p-6">
                <div class="bg-white rounded-t-lg">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-black">RETURN</h3>
                        <button id="closeReturnModalBtn" class="text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">
                        Add a reason for returning the document.
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
                        <button id="submitReturnBtn" class="w-full bg-[#7A1212] hover:bg-[#5e0b0b] text-white py-2.5 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212] focus:ring-opacity-50 text-sm font-semibold uppercase cursor-pointer">
                            Request
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Final Return Confirmation Modal -->
        <div id="finalReturnConfirmationModal" class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm" style="background-color: rgba(0,0,0,0.3);">
            <div class="bg-white w-[30rem] rounded-lg shadow-xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-semibold text-gray-800">Return Confirmation</h3>
                    <button id="closeFinalReturnModalBtn" class="text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <p class="text-sm text-gray-600 mb-6">
                   Marks the document as returned, stopping any further review unless returned back by the student.
                </p>
                
                <div class="flex justify-end space-x-3">
                    <button id="cancelFinalReturnBtn" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                        Cancel
                    </button>
                    <button id="finalizeReturnBtn" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 cursor-pointer">
                        Finalize Return
                    </button>
                </div>
            </div>
        </div>

        <!-- Document Action Toast -->
        <div id="documentActionToast" class="hidden fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white border-l-4 border-gray-400 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50">
            <div>
                <img
                    src="{{ asset('images/successful.svg') }}"
                    alt="Action Icon"
                    id="actionIcon"
                    class="">
            </div>
            <div class="flex-1">
                <p class="font-semibold" id="actionTitle">Document Action</p>
                <p id="actionMessage" class="text-sm">Action performed on document.</p>
            </div>
            <button type="button" onclick="hideActionToast()" class="text-gray-500 hover:text-gray-700 text-2xl leading-none cursor-pointer self-center">&times;</button>
        </div>
            @include('components.footer')
    </div>    
    @vite('resources/js/admin-review.js')
    @vite(['resources/js/app.js'])
@endsection
