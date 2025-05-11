@extends('base')

@section('content')
    @include('components.studentNavBarComponent')
    @include('components.studentSideBarComponent')

    <div id="main-content" class="transition-all duration-300 ml-[20%]">
        <div
            class="flex flex-col lg:flex-row bg-[#3D0E0E] text-white rounded-xl p-6 space-y-6 lg:space-y-0 lg:space-x-6 max-w-5xl mx-auto">

            <!-- Left Panel -->
            <div class="w-full lg:w-2/3 space-y-4">
                <div class="text-sm text-gray-300">April 10, 2025</div>
                <div class="text-xl font-bold">Title: <span class="text-white">ELITE_Event_Proposal</span></div>
                <div class="text-md font-semibold">Type: <span class="text-white">Proposal</span></div>

                <!-- Summary -->
                <div>
                    <h3 class="font-semibold mb-2">Summary</h3>
                    <div class="bg-white text-black p-4 rounded-md max-h-48 overflow-y-auto">
                        The ELITE organization is proposing to hold an inter-departmental IT week celebration featuring
                        seminars, workshops, and competitions aimed at enhancing student skills and promoting collaboration.
                        The event is scheduled for May 6–10, 2025, and will be held at the university auditorium. The
                        proposal includes a detailed program, list of speakers, and budget breakdown.
                    </div>
                </div>

                <!-- Attachment -->
                <div>
                    <h3 class="font-semibold mb-2">Attachment</h3>
                    <button
                        class="bg-[#5A1F1F] text-white px-4 py-2 rounded hover:bg-[#6a2626]">ELITE_Event_Proposal.docx</button>
                </div>

                <!-- Status Tracker -->
                <div class="flex items-center justify-between text-center mt-4">
                    <div class="flex flex-col items-center">
                        <div class="w-4 h-4 rounded-full bg-green-600 mb-1"></div>
                        <span class="text-xs">Pending</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-4 h-4 rounded-full bg-green-600 mb-1"></div>
                        <span class="text-xs">Under Review<br>[admin]</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-4 h-4 rounded-full bg-red-600 mb-1"></div>
                        <span class="text-xs">Rejected</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-4 h-4 rounded-full bg-gray-400 mb-1"></div>
                        <span class="text-xs">Under Review<br>of Director</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-4 h-4 rounded-full bg-gray-400 mb-1"></div>
                        <span class="text-xs">Approval</span>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-[#5A1F1F] p-3 rounded mt-2 text-sm">
                    <p><span class="font-semibold text-pink-300">Jonell Espalto</span></p>
                    <p>Under Review, April 15 2025, 1:45PM</p>
                    <p>Rejected, April 18 2025, 8:45PM</p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 mt-4">
                    <button class="bg-[#8B2C2C] text-white px-4 py-2 rounded hover:bg-[#a03d3d]">Close</button>
                    <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Follow Up</button>
                </div>
            </div>

            <!-- Right Panel -->
            <div class="w-full lg:w-1/3 bg-[#290808] rounded-xl p-4 space-y-4">
                <div class="font-bold text-lg">Jonell Espalto</div>
                <div class="text-sm text-gray-400">Student Services</div>

                <div class="mt-4 space-y-3">
                    <div>
                        <span class="font-semibold">Dr. Strange</span>
                        <p class="text-sm text-gray-300">Okay na ‘to?</p>
                        <span class="text-xs text-gray-400">2 hours ago</span>
                    </div>
                    <div>
                        <span class="font-semibold">Iskolarian</span>
                        <p class="text-sm text-gray-300">Academic services approved the document and sent to Student
                            Services</p>
                        <span class="text-xs text-gray-400">1 hour ago</span>
                    </div>
                </div>

                <!-- Comment Box -->
                <div class="flex items-center space-x-2 mt-4">
                    <input type="text" placeholder="Comment..."
                        class="flex-grow px-3 py-2 rounded-md bg-[#3D0E0E] text-white border border-gray-600 focus:outline-none">
                    <button class="text-white"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M5 13l4 4L19 7" />
                        </svg></button>
                </div>
            </div>
        </div>

    </div>
@endsection
