@extends('base')

@section('content')
    @include('components.studentNavBarComponent')
    @include('components.studentSideBarComponent')

    <div id="main-content" class="transition-all duration-300 ml-[20%]">
        @include('student.components.viewSubmissionTrackerTitleComponent')

        <div class="flex flex-wrap gap-[15px] px-[15px]">
            <!-- Submission Details Component (Left) -->
            <div class="flex-1 min-w-[300px]">
                <div class="bg-[#4B1E1E] text-white rounded-lg shadow-lg p-6 space-y-4">
                    <div class="text-sm text-gray-300">{{ $record->created_at->format('F j, Y') }}</div>
                    <div class="text-lg font-bold">Title: <span class="text-white">{{ $record->subject }}</span></div>
                    <div class="text-sm text-gray-300">Type: <span class="font-semibold text-white">{{ $record->type }}</span>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-1">Summary</h3>
                        <div class="bg-gray-100 text-black text-sm p-3 rounded-md h-32 overflow-y-auto">
                            {{ $record->summary }}
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold mb-1">Attachment</h3>
                        @if ($record->file_path)
                            <a href="{{ asset('storage/' . $record->file_path) }}" target="_blank"
                                class="bg-white text-black text-sm px-4 py-2 rounded hover:bg-gray-200 transition inline-block">
                                {{ basename($record->file_path) }}
                            </a>
                        @else
                            <span class="text-gray-400">No attachment</span>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center flex-wrap gap-1 text-sm">
                            <span
                                class="w-3 h-3 rounded-full {{ $record->status === 'Pending' ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            <span>Pending</span>
                            <span
                                class="w-3 h-3 rounded-full {{ $record->status === 'Under Review' ? 'bg-green-600' : 'bg-gray-400' }}"></span>
                            <span>Under Review [admin]</span>
                            <span
                                class="w-3 h-3 rounded-full {{ $record->status === 'Rejected' ? 'bg-red-600' : 'bg-gray-400' }}"></span>
                            <span>Rejected</span>
                            <span
                                class="w-3 h-3 rounded-full {{ $record->status === 'Under Review of Director' ? 'bg-gray-600' : 'bg-gray-400' }}"></span>
                            <span>Under Review of Director</span>
                            <span
                                class="w-3 h-3 rounded-full {{ $record->status === 'Approved' ? 'bg-blue-500' : 'bg-gray-400' }}"></span>
                            <span>Approval</span>
                        </div>

                        <div class="bg-[#3D1515] text-xs p-3 rounded-md">
                            <div><span
                                    class="font-semibold text-pink-300">{{ $record->receiver->name ?? 'Unknown' }}</span>
                            </div>
                            @foreach ($record->reviews as $review)
                                <div class="{{ $review->status === 'Rejected' ? 'text-red-500' : 'text-red-300' }}">
                                    {{ $review->status }}, {{ $review->created_at->format('F j Y, g:i A') }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button onclick="window.history.back()"
                            class="bg-gray-600 hover:bg-gray-500 text-sm px-4 py-2 rounded">Close</button>
                        <button class="bg-green-600 hover:bg-green-500 text-sm px-4 py-2 rounded">Follow Up</button>
                    </div>
                </div>
            </div>

            <!-- Chat Component (Right) -->
            <div class="flex-1 min-w-[300px] max-w-md">
                <div class="flex flex-col h-full bg-[#4b1e1e] text-white rounded-lg overflow-hidden">
                    <!-- Header -->
                    <div class="p-4 border-b border-red-800">
                        <div class="flex items-center space-x-3">
                            <div class="bg-gray-200 rounded-full p-2 flex items-center justify-center">
                                <!-- User icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="text-gray-700">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div>
                                <h1 class="font-semibold">Jonell Espalto</h1>
                                <p class="text-xs text-gray-300">Student Services</p>
                            </div>
                        </div>
                    </div>

                    <!-- Messages Area -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4">
                        <!-- Message 1 -->
                        <div class="flex items-start space-x-3">
                            <div class="bg-gray-200 rounded-full p-2 mt-1 flex items-center justify-center">
                                <!-- User icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="text-gray-700">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <p class="font-medium">Dr. Strange</p>
                                    <span class="ml-auto text-xs text-gray-300">2 hours ago</span>
                                </div>
                                <p class="text-sm">Okay na 'to?</p>
                            </div>
                        </div>

                        <!-- Message 2 -->
                        <div class="flex items-start space-x-3">
                            <div class="bg-gray-200 rounded-full p-2 mt-1 flex items-center justify-center">
                                <!-- User icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="text-gray-700">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <p class="font-medium">Iskolarian</p>
                                    <span class="ml-auto text-xs text-gray-300">1 hour ago</span>
                                </div>
                                <p class="text-sm">Academic services approved the document and sent to Student Services</p>
                            </div>
                        </div>
                    </div>

                    <!-- Input Area -->
                    <div class="p-2 bg-red-950">
                        <div class="flex items-center bg-gray-100 rounded-full px-4 py-1">
                            <input type="text" placeholder="Message"
                                class="bg-transparent flex-1 text-gray-800 focus:outline-none text-sm py-2" />
                            <button class="p-1">
                                <!-- Send icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="text-gray-600">
                                    <path d="m22 2-7 20-4-9-9-4Z"></path>
                                    <path d="M22 2 11 13"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
