@extends('base')
@section('content')
    @include('components.studentSideBarComponent')
    <div id="main-content" class="flex flex-col min-h-screen ml-[20%] transition-all duration-300 bg-[#F2F4F7]">
        @include('components.studentNavBarComponent')
        <div class="flex-grow mb-10">
            <!-- Main Content -->
            <div class="flex-grow p-6">
                <h1 class="text-2xl font-['Lexend'] font-semibold mb-4">Document Submission</h1>

                <form class="font-['Manrope']" action="{{ route('submit.document') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <!-- Receiver, Subject, Doc Type, Semester A.Y. -->
                    <div class="flex flex-col md:flex-row gap-4 mb-4">
                        <!-- Left Side -->
                        <div class="flex flex-col gap-4 w-full md:w-2/3">
                            <!-- Receiver Button -->
                            <div class="relative w-full">
                                <button type="button" id="receiverButton" aria-expanded
                                    class="w-full text-left border-2 border-gray-500 p-2 rounded-[8px] relative flex items-center justify-between gap-2 bg-[#F2F4F7] cursor-pointer">
                                    <span class="font-semibold text-gray-500">
                                        To<span class="required-indicator text-red-500"> *</span>:
                                        <span id="receiverSelected" class="font-semibold text-black"></span>
                                    </span>
                                    <img src="{{ asset('images/gray-arrow-down.svg') }}" alt="Dropdown Arrow"
                                        id="receiverArrow" class="w-8 h-3">
                                </button>

                                <!-- Dropdown List -->
                                <ul role="listbox" id="receiverDropdown"
                                    class="hidden absolute z-10 w-full bg-white text-black border border-gray-300 rounded-[11px] shadow-md mt-1">
                                    @foreach ($adminUsers as $admin)
                                        <li tabindex="0" role="option"
                                            class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold"
                                            onclick="selectReceiver('{{ $admin->id }}', '{{ $admin->role_name }}')">
                                            {{ $admin->role_name }}
                                        </li>
                                    @endforeach
                                </ul>

                                <input type="hidden" name="received_by" id="receiverInput">
                            </div>

                            <!-- Subject Field -->
                            <div class="flex items-center border-2 border-gray-500 p-2 rounded-[8px] w-full">
                                <span class="text-gray-500 font-semibold whitespace-nowrap mr-2">Subject<span
                                        class="required-indicator text-red-500"> *</span>:</span>
                                <input type="text" id="subject" name="subject" autocomplete="off"
                                    class="min-w-0 w-full font-semibold focus:outline-none" maxlength="255">
                            </div>

                            <!-- Semester A.Y. Dropdown for Mobile -->
                            <div class="relative w-full md:hidden">
                                <button type="button" id="academicYearButtonMobile" aria-expanded
                                    class="w-full text-left border-2 border-gray-500 p-2 rounded-[8px] relative flex items-center justify-between gap-2 bg-[#F2F4F7] cursor-pointer">
                                    <span class="font-semibold text-gray-500">
                                        Semester A.Y.<span class="required-indicator text-red-500"> *</span>:
                                        <span id="academicYearSelectedMobile" class="font-semibold text-black"></span>
                                    </span>
                                    <img src="{{ asset('images/gray-arrow-down.svg') }}" alt="Dropdown Arrow"
                                        id="academicYearArrowMobile" class="w-8 h-3">
                                </button>

                                <ul role="listbox" id="academicYearDropdownMobile"
                                    class="hidden absolute z-10 mt-1 w-full bg-white text-black rounded-[11px] shadow-md max-h-60 overflow-y-auto">
                                </ul>
                            </div>
                        </div>

                        <!-- Right Side -->
                        <div class="flex flex-col gap-4 w-full md:w-1/3">
                            <!-- Document Type Button -->
                            <div class="relative w-full">                                    
                                <button type="button" id="docTypeButton" aria-expanded
                                    class="relative w-full bg-[#7A1212] hover:bg-[#a31515] text-white border-2 border-[#7A1212] hover:border-[#a31515] p-2 pl-4 pr-12 rounded-[8px] cursor-pointer transition font-semibold flex items-center justify-center gap-2">
                                    <img src="{{ asset('images/submitDocument.svg') }}" alt="Submit Document" id="docTypeIcon"
                                        class="w-5 h-5 flex-shrink-0">
                                    <span id="docTypeSelected" class="whitespace-nowrap truncate">Document Type</span>

                                    <!-- Dropdown Arrow absolutely positioned at right -->
                                    <img src="{{ asset('images/white-arrow-down.svg') }}" alt="Dropdown Arrow" id="docTypeArrow"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 w-6 h-3 pointer-events-none">
                                </button>

                                <!-- Dropdown List -->
                                <ul role="listbox" id="docTypeDropdown"
                                    class="hidden absolute z-10 mt-1 w-full bg-white text-black rounded-[11px] shadow-md">
                                    @foreach ([
                                        'Event Proposal',
                                        'General Plan of Activities',
                                        'Reports of Proceedings',
                                        'Constitution and By-Laws',
                                        'Fundraising Activities',
                                        'Request Letter',
                                        'Petition and Concern',
                                        'Memorandum of Agreement',
                                        'Off Campus Activities',
                                        'Other'
                                    ] as $type)
                                        <li tabindex="0" role="option"
                                            class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold"
                                            onclick="selectDocType('{{ $type }}')">
                                            {{ $type }}
                                        </li>
                                    @endforeach
                                </ul>

                                <!-- Hidden input for document type -->
                                <input type="hidden" name="type" id="docTypeInput">
                            </div>

                            <!-- Document Type Field if "Other" is Selected -->
                            <div id="othersDocTypeContainer" class="flex items-center border-2 border-gray-500 p-2 rounded-[8px] w-full" hidden>
                                <span class="text-gray-500 font-semibold whitespace-nowrap mr-2">Document Type<span
                                        class="required-indicator text-red-500"> *</span>:</span>
                                <input type="text" name="other_type" id="othersDocTypeInput" autocomplete="off"
                                    class="flex-1 font-semibold focus:outline-none" maxlength="50">
                            </div>
                            
                            <!-- Semester A.Y. Dropdown for Desktop -->
                            <div class="relative w-full hidden md:block">
                                <button type="button" id="academicYearButton" aria-expanded
                                    class="w-full text-left border-2 border-gray-500 p-2 rounded-[8px] relative flex items-center justify-between gap-2 bg-[#F2F4F7] cursor-pointer">
                                    <span class="font-semibold text-gray-500">
                                        Semester A.Y.<span class="required-indicator text-red-500"> *</span>:
                                        <span id="academicYearSelected" class="font-semibold text-black"></span>
                                    </span>
                                    <img src="{{ asset('images/gray-arrow-down.svg') }}" alt="Dropdown Arrow"
                                        id="academicYearArrow" class="w-8 h-3">
                                </button>

                                <ul role="listbox" id="academicYearDropdown"
                                    class="hidden absolute z-10 mt-1 w-full bg-white text-black rounded-[11px] shadow-md max-h-60 overflow-y-auto">
                                </ul>
                            </div>
                        </div>

                        <!-- Shared Hidden Input for Academic Year -->
                        <input type="hidden" name="academic_year" id="academicYearInput">
                    </div>

                    <!-- Overview Field -->
                    <div class="flex flex-col gap-1 border-2 border-gray-500 p-2 rounded-[8px] mb-4">
                        <label for="overview" class="font-semibold text-gray-500">Overview<span
                                class="required-indicator text-red-500"> *</span>:</label>

                        <textarea id="overview" name="overview"
                            class="w-full font-semibold h-[150px] resize-none overflow-y-visible focus:outline-none" maxlength="255"
                            oninput="overviewUpdateCounter()" placeholder="Write a short description or overview..."></textarea>

                        <div class="text-sm text-gray-500 text-right">
                            <span id="overview-counter">0</span>/255
                        </div>
                    </div>

                    <!-- Only shows for Event Proposals -->
                    <div id="event_proposal_container" class="space-y-4 mb-4 hidden">
                        <!-- Venue Field-->
                        <div class="flex items-center border-2 border-gray-500 p-2 rounded-[8px] w-full">
                            <span class="text-gray-500 font-semibold whitespace-nowrap mr-2">Venue<span
                            class="required-indicator text-red-500"> *</span>:</span>
                            <input type="text" id="venue" name="venue" autocomplete="off"
                            class="min-w-0 w-full font-semibold focus:outline-none" maxlength="100">
                        </div>
                        
                        <!-- Proposed Date & Time Field and Hours Field-->
                        <div class="flex flex-col md:flex-row gap-4">
                            <!-- Left Side -->
                            <div class="flex flex-col gap-4 w-full md:w-1/2">
                                <!-- Proposed Date & Time Field -->
                                <div class="flex items-center border-2 border-gray-500 p-2 rounded-[8px] w-full">
                                    <span class="text-gray-500 font-semibold mr-2 whitespace-nowrap">
                                        Proposed Date & Time<span class="required-indicator text-red-500"> *</span>:
                                    </span>
                                    <input type="datetime-local" id="proposed_date_time" name="proposed_date_time"
                                        class="flex-1 min-w-0 font-semibold focus:outline-none">
                                </div>
                            </div>
                            
                            <!-- Right Side -->
                            <div class="flex flex-col gap-4 w-full md:w-1/2">
                                <!-- Hours Field -->
                                <div class="flex items-center border-2 border-gray-500 p-2 rounded-[8px] w-full">
                                    <span class="text-gray-500 font-semibold whitespace-nowrap mr-2">No. of Hours<span
                                    class="required-indicator text-red-500"> *</span>:</span>
                                    <input type="number" id="hours" name="hours" min="1" step="1" autocomplete="off"
                                    class="min-w-0 w-full font-semibold focus:outline-none">
                                </div>
                            </div>
                        </div>
                    
                        <!-- Attendees Field and Attendees Range Field -->
                        <div class="flex flex-col md:flex-row gap-4">
                            <!-- Left Side -->
                            <div class="flex flex-col gap-4 w-full md:w-1/2">
                                <!-- Attendees Field -->
                                <div class="flex items-center border-2 border-gray-500 p-2 rounded-[8px] w-full">
                                    <span class="text-gray-500 font-semibold whitespace-nowrap mr-2">Attendees<span
                                    class="required-indicator text-red-500"> *</span>:</span>
                                    <input type="text" id="attendees" name="attendees" autocomplete="off"
                                    class="min-w-0 w-full font-semibold focus:outline-none" maxlength="50" placeholder="Course/Year/Section">
                                </div>
                            </div>
                            
                            <!-- Right Side -->
                            <div class="relative w-full md:w-1/2">
                                <!-- Attendees Range Button -->
                                <button type="button" id="attendeesRangeButton" aria-expanded
                                    class="w-full text-left border-2 border-gray-500 p-2 rounded-[8px] relative flex items-center justify-between gap-2 bg-[#F2F4F7] cursor-pointer">
                                    <span class="font-semibold text-gray-500">
                                        Expected No. of Attendees<span class="required-indicator text-red-500"> *</span>:
                                        <span id="attendeesRangeSelected" class="font-semibold text-black"></span>
                                    </span>
                                    <img src="{{ asset('images/gray-arrow-down.svg') }}" alt="Dropdown Arrow"
                                        id="attendeesRangeArrow" class="w-8 h-3">
                                </button>

                                <!-- Dropdown List -->
                                <ul role="listbox" id="attendeesRangeDropdown"
                                    class="hidden absolute z-10 mt-1 w-full bg-white text-black rounded-[11px] shadow-md">
                                    @foreach (['10-50', '50-100', '100-250', '250-500', 'Above 500'] as $attendees_range)
                                        <li tabindex="0" role="option"
                                            class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold"
                                            onclick="selectAttendeesRange('{{ $attendees_range }}')">
                                            {{ $attendees_range }}
                                        </li>
                                    @endforeach
                                </ul>

                                <!-- Hidden input for attendees range -->
                                <input type="hidden" name="attendees_range" id="attendeesRangeInput">
                            </div>
                        </div>                        
                    
                        <!-- Fees Field -->
                        <div class="flex flex-col md:flex-row md:items-center w-full gap-2 md:gap-4">
                            <span class="text-gray-500 font-semibold md:whitespace-nowrap">
                                Fee/Contributions per Student/Participant (if applicable):
                            </span>

                            <div class="relative w-full md:w-[150px]">
                                <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-gray-500">₱</span>
                                <input type="number" id="fees" name="fees" min="0" step="0.01" autocomplete="off"
                                    class="pl-6 w-full border-2 border-gray-500 font-semibold rounded-[8px] px-2 py-1 focus:outline-none">
                            </div>

                            <div class="flex items-center">
                                <label class="flex items-center space-x-1">
                                    <input type="checkbox" id="fee_none" name="fee_none"
                                        class="form-checkbox border-2 border-gray-500 rounded-[8px] h-5 w-5">
                                    <span class="text-gray-500 font-semibold whitespace-nowrap">None</span>
                                </label>
                            </div>
                        </div>
                    </div>
                        
                    <div id="event_attachments_container" class="text-gray-500 font-semibold mb-4 hidden">
                        <div class="flex items-center w-full md:flex md:w-[400px]">
                            <span class="text-gray-500 font-semibold whitespace-nowrap mr-2">Attachments<span
                                class="required-indicator text-red-500"> *</span>:</span>
                        </div>
                        
                        <div class="flex w-full ml-2 md:flex items-start">
                            <ul class="list-disc list-inside">
                                <li>Request Letter with Proposal Details and Program of Activities (Required)</li>
                                <li>Detailed Budgeted Expenses (if applicable)</li>
                                <li>Profile of Resource Speaker (if applicable)</li>
                            </ul>
                        </div>
                    </div>

                    <!-- File Upload Field -->
                    <div class="space-y-2 w-full mb-4">
                        <!-- Mobile Upload Button (Visible only on small screens) -->
                        <div class="block md:hidden space-y-2">
                            <label for="fileUpload" tabindex="0"
                                class="flex items-center justify-center gap-2 bg-[#7A1212] text-white font-semibold rounded-[12px] px-6 py-2 cursor-pointer hover:bg-[#a31515]">
                                <img src="{{ asset('images/upload-icon.svg') }}" alt="Upload Icon" class="w-5 h-5">
                                <span>Upload File(s)</span>
                            </label>
                            <input type="file" id="fileUpload" name="file_upload[]" class="hidden" multiple>

                            <p class="text-sm text-gray-500">
                                Choose a file up to 5MB. Valid file types: PDF, DOCX, DOC. Maximum of 30 Files
                            </p>
                        </div>

                        <!-- Dropzone Area (Visible only on medium+ screens) -->
                        <div class="hidden md:block">
                            <div id="desktopDropzone"
                                class="dropzone dz-clickable w-full border-2 border-dashed border-gray-500 rounded-lg p-6 text-center">
                                <div class="dz-message flex flex-col items-center justify-center text-gray-500">
                                    <img src="{{ asset('images/photo-upload-icon.svg') }}" alt="Upload Icon" class="w-12 h-12 mb-2">
                                    <p>
                                        <strong class="text-black">Drop your files here</strong> or
                                        <button type="button"
                                            class="text-[#7A1212] font-semibold cursor-pointer"
                                            onclick="document.getElementById('desktopFileInput').click()">
                                            browse
                                        </button>
                                    </p>
                                    <p class="text-sm mt-1">
                                        Choose a file up to 5MB. Valid file types: PDF, DOCX, DOC. Maximum of 30 Files
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Shared Preview Area -->
                        <div id="filePreviewArea" class="w-full space-y-2 mt-4"></div>

                        <!-- Custom Dropzone Preview Template -->
                        <div id="custom-preview-template" class="hidden">
                            <div class="dz-preview bg-white rounded-lg border p-3 shadow-sm overflow-hidden w-full">
                                <div class="flex items-start justify-between gap-2 mb-1 w-full flex-nowrap">
                                    <div class="flex items-start gap-2 min-w-0 w-full">
                                        <img src="{{ asset('images/uploaded-file-icon.svg') }}" alt="Uploaded File"
                                            class="w-10 h-10 flex-shrink-0" />
                                        
                                        <div class="min-w-0">
                                            <span class="dz-filename text-sm font-medium block truncate">
                                                <span data-dz-name></span>
                                            </span>
                                            <span class="dz-size text-xs text-gray-500" data-dz-size></span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 flex-shrink-0 pl-2">
                                        <img src="{{ asset('images/check-circle-icon.svg') }}" alt="Success"
                                            class="w-4 h-4 dz-success-icon hidden" />
                                        
                                        <button
                                            class="w-5 h-5 flex items-center justify-center cursor-pointer dz-remove"
                                            data-dz-remove title="Remove File"
                                            aria-label="Remove File"
                                            tabindex="0"
                                            onkeydown="if (event.key === 'Enter' || event.key === ' ') this.click();">
                                            <img src="{{ asset('images/trash-icon.svg') }}" alt="Remove" class="w-4 h-4 pointer-events-none" />
                                        </button>
                                    </div>
                                </div>

                                <div class="relative w-full bg-gray-200 h-2 rounded overflow-hidden">
                                    <div class="bg-blue-600 h-2 rounded dz-upload" data-dz-uploadprogress style="width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col md:flex-row gap-4 justify-end">
                        <button id="mainSubmitButton" type="button" onclick="showConfirmPopup(event)"
                            class="order-1 md:order-2 w-full font-semibold bg-gray-500 text-white px-6 py-2 rounded-[12px] md:w-auto cursor-not-allowed transition"
                            disabled>Submit</button>

                        <button type="button" onclick="window.location.href='{{ route('student.dashboard') }}'"
                            class="order-2 md:order-1 w-full font-semibold border-2 hover:bg-gray-100 text-[#7A1212] px-6 py-2 rounded-[12px] md:w-auto cursor-pointer transition">Back
                            to Home</button>
                    </div>

                    <!-- Confirmation Popup -->
                    <div id="confirmPopup" class="fixed inset-0 flex items-center justify-center bg-black/30 backdrop-blur-sm z-50 hidden">
                        <div
                            class="bg-white rounded-xl p-6 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl shadow-lg text-gray-800">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-lg font-semibold">Document Submission Confirmation</h2>
                                <button type="button" onclick="closeConfirmPopup()"
                                    class="text-gray-500 hover:text-gray-700 text-3xl leading-none cursor-pointer self-center">&times;</button>
                            </div>

                            <p class="mb-6">Are you sure you want to submit this document? Once submitted, you may not be
                                able to make further changes.</p>

                            <div class="flex justify-end space-x-2">
                                <button onclick="closeConfirmPopup()"
                                    class="font-semibold px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-100 cursor-pointer"
                                    type="button">Cancel</button>
                                <button id="confirmSubmitBtn" type="submit" onclick="handleConfirmSubmit(this)"
                                    class="font-semibold px-4 py-2 bg-[#7A1212] text-white rounded-md hover:bg-[#a31515] cursor-pointer">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @include('components.footer')
    </div>
    <!-- Error Toast -->
    <div id="errorToast"
        class="hidden fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white border-l-4 border-red-300 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50">
        <div>
            <img src="{{ asset('images/error.svg') }}" alt="Error Icon" id="docTypeIcon" class="">
        </div>
        <div class="flex-1">
            <p class="font-semibold">Error</p>
            <p id="errorToastMsg" class="text-sm">Error message here</p>
        </div>
        <button type="button" onclick="hideToast('error')"
            class="text-gray-500 hover:text-gray-700 text-2xl leading-none cursor-pointer self-center">&times;</button>
    </div>

    <!-- Document Submission Success Toast -->
    <div id="successToast"
        class="hidden fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white border-l-4 border-green-400 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50">
        <div>
            <img src="{{ asset('images/successful.svg') }}" alt="Success Icon" id="docTypeIcon" class="">
        </div>
        <div class="flex-1">
            <p class="font-semibold">Document Successfully Submitted</p>
            <p id="successToastMsg" class="text-sm">Your document has been submitted successfully. We'll review it shortly
                and get back to you if anything else is needed.</p>
        </div>
        <button type="button" onclick="hideToast('success')"
            class="text-gray-500 hover:text-gray-700 text-2xl leading-none cursor-pointer self-center">&times;</button>
    </div>

    <!-- Document Submission Fail Toast -->
    <div id="failToast"
        class="hidden fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white border-l-4 border-red-300 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50">
        <div>
            <img src="{{ asset('images/error.svg') }}" alt="Error Icon" id="docTypeIcon" class="">
        </div>
        <div class="flex-1">
            <p class="font-semibold">Error</p>
            <p id="failToastMsg" class="text-sm">Failed to submit document. Please try again later.</p>
        </div>
        <button type="button" onclick="hideToast('fail')"
            class="text-gray-500 hover:text-gray-700 text-2xl leading-none cursor-pointer self-center">&times;</button>
    </div>
    <!-- Display document submision success message -->
    @if (session('success'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                showToast('success');
            });
        </script>
    @endif

    <!-- Display document submission fail message -->
    @if (session('error'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                showToast('fail');
            });
        </script>
    @endif

    <script>
        // Auto-select receiver when selecting doc type disabled at start
        let receiverAutoSelected = false;
        let myDropzone;

        // Element references
        const docType = {
            button: document.getElementById('docTypeButton'),
            dropdown: document.getElementById('docTypeDropdown'),
            icon: document.getElementById('docTypeIcon'),
            selected: document.getElementById('docTypeSelected'),
            input: document.getElementById('docTypeInput')
        };

        const receiver = {
            button: document.getElementById('receiverButton'),
            dropdown: document.getElementById('receiverDropdown'),
            selected: document.getElementById('receiverSelected'),
            input: document.getElementById('receiverInput')
        };

        const academicYear = {
            button: document.getElementById('academicYearButton'),
            dropdown: document.getElementById('academicYearDropdown'),
            selected: document.getElementById('academicYearSelected'),
            input: document.getElementById('academicYearInput')
        };

        const academicYearMobile = {
            button: document.getElementById('academicYearButtonMobile'),
            dropdown: document.getElementById('academicYearDropdownMobile'),
            selected: document.getElementById('academicYearSelectedMobile'),
            input: document.getElementById('academicYearInput')
        };

        const attendeesRange = {
            button: document.getElementById('attendeesRangeButton'),
            dropdown: document.getElementById('attendeesRangeDropdown'),
            selected: document.getElementById('attendeesRangeSelected'),
            input: document.getElementById('attendeesRangeInput')
        };

        const eventProposalContainer = document.getElementById('event_proposal_container');
        const eventAttachmentsContainer = document.getElementById('event_attachments_container');
        const overviewInput = document.getElementById('overview');
        const overviewCounter = document.getElementById('overview-counter');
        const feesNoneCheckbox = document.getElementById("fee_none");

        // Arrow keys navigation for dropdowns
        function setupAccessibleDropdown(button, dropdown, onSelect) {
            const items = dropdown.querySelectorAll('li');

            // Button opens dropdown and focuses first item
            button.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    dropdown.classList.remove('hidden');
                    setTimeout(() => {
                        items[0].focus();
                    }, 0);
                }
            });

            items.forEach((item, index) => {
                item.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        const next = items[index + 1] || items[0];
                        next.focus();
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        const prev = items[index - 1] || items[items.length - 1];
                        prev.focus();
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        item.click();
                        dropdown.classList.add('hidden');
                        button.focus();
                    } else if (e.key === 'Escape' || e.key === 'Tab') {
                        dropdown.classList.add('hidden');
                        button.focus();
                    }
                });
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            setupAccessibleDropdown(docType.button, docType.dropdown, selectDocType);
            setupAccessibleDropdown(receiver.button, receiver.dropdown, selectReceiver);            
            setupAccessibleDropdown(attendeesRange.button, attendeesRange.dropdown, selectAttendeesRange);
        });

        // Prevent form submission on Enter keypress except from inside the confirmation popup
        document.querySelector('label[for="fileUpload"]').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                document.getElementById('fileUpload').click();
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("subject").addEventListener("keydown", function(e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                }
            });
            document.getElementById("othersDocTypeInput").addEventListener("keydown", function(e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                }
            });
            document.getElementById("venue").addEventListener("keydown", function(e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                }
            });
            document.getElementById("hours").addEventListener("keydown", function(e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                }
            });
            document.getElementById("attendees").addEventListener("keydown", function(e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                }
            });
            document.getElementById("fees").addEventListener("keydown", function(e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                }
            });
            if (feesNoneCheckbox) {
                document.getElementById("fee_none").addEventListener("keydown", function (e) {
                    if (e.key === "Enter" || e.key === " ") {
                        e.preventDefault();
                        feesNoneCheckbox.checked = !feesNoneCheckbox.checked;
                        feesNoneCheckbox.dispatchEvent(new Event('change', { bubbles: true })); // Fire change event manually
                    }
                });
            }
        });

        // Limits inputs for hours field to positive integers (x > 0)
        const hoursInput = document.getElementById('hours');

        hoursInput.addEventListener('keydown', function (e) {
            const invalidKeys = ["-", "e", "E", "+", "."];
            const isZero = e.key === "0" && this.value.length === 0;

            if (invalidKeys.includes(e.key) || isZero) {
                e.preventDefault();
            }
        });

        hoursInput.addEventListener('paste', function (e) {
            const pasteData = e.clipboardData.getData('text');
            if (!/^[1-9][0-9]*$/.test(pasteData)) {
                e.preventDefault();
            }
        });

        hoursInput.addEventListener('input', function (e) {
            // Immediately remove any invalid input (example: 0000)
            if (!/^[1-9][0-9]*$/.test(this.value)) {
                this.value = '';
            }
        });

        // Limits inputs for fees field to positive integers w/ 0 (x >= 0)
        const feesInput = document.getElementById('fees');

        feesInput.addEventListener('keydown', function (e) {
            const invalidKeys = ["-", "e", "E", "+"];

            if (invalidKeys.includes(e.key)) {
                e.preventDefault();
            }

            // Only one decimal point
            if (e.key === "." && this.value.includes(".")) {
                e.preventDefault();
            }
        });

        feesInput.addEventListener('paste', function (e) {
            const pasteData = e.clipboardData.getData('text');
            if (!/^\d+(\.\d{1,2})?$/.test(pasteData)) {
                e.preventDefault();
            }
        });

        // Toggle dropdown visibility
        docType.button.addEventListener('click', () => toggleDropdown(docType.dropdown));
        receiver.button.addEventListener('click', () => toggleDropdown(receiver.dropdown));
        academicYear.button.addEventListener('click', () => toggleDropdown(academicYear.dropdown));
        academicYearMobile.button.addEventListener('click', () => toggleDropdown(academicYearMobile.dropdown));
        attendeesRange.button.addEventListener('click', () => toggleDropdown(attendeesRange.dropdown));

        function toggleDropdown(dropdown) {
            dropdown.classList.toggle('hidden');
            if (!dropdown.classList.contains('hidden')) {
                const firstItem = dropdown.querySelector('li');
                if (firstItem) {
                    setTimeout(() => firstItem.focus(), 0);
                }
            }
        }

        // Dynamic Toast Message
        let errorToastTimeout = null;
        let successToastTimeout = null;
        let failToastTimeout = null;

        function showToast(type, message = '') {
            let toast, toastMsg, timeoutVar;

            if (type === 'error') {
                toast = document.getElementById("errorToast");
                toastMsg = document.getElementById("errorToastMsg");
                timeoutVar = errorToastTimeout;
            } else if (type === 'success') {
                toast = document.getElementById("successToast");
                toastMsg = document.getElementById("successToastMsg");
                timeoutVar = successToastTimeout;
            } else if (type === 'fail') {
                toast = document.getElementById("failToast");
                toastMsg = document.getElementById("failToastMsg");
                timeoutVar = failToastTimeout;
            }

            // Avoid overlapping by clearing previous timeout
            if (toast.classList.contains('hidden') === false && timeoutVar) {
                clearTimeout(timeoutVar);
            }

            if (toastMsg && message) {
                toastMsg.textContent = message;
            }

            toast.classList.remove("hidden");

            // Auto-hide this specific toast after 5 seconds
            const timeout = setTimeout(() => {
                toast.classList.add("hidden");
            }, 5000);

            // Save timeout reference for future clearing
            if (type === 'error') errorToastTimeout = timeout;
            if (type === 'success') successToastTimeout = timeout;
            if (type === 'fail') failToastTimeout = timeout;
        }

        function hideToast(type = null) {
            // Hide all if no type is provided
            if (!type || type === 'error') {
                document.getElementById("errorToast")?.classList.add("hidden");
                if (errorToastTimeout) clearTimeout(errorToastTimeout);
            }
            if (!type || type === 'success') {
                document.getElementById("successToast")?.classList.add("hidden");
                if (successToastTimeout) clearTimeout(successToastTimeout);
            }
            if (!type || type === 'fail') {
                document.getElementById("failToast")?.classList.add("hidden");
                if (failToastTimeout) clearTimeout(failToastTimeout);
            }
        }

        // Hides all toasts
        function hideAllToasts() {
            hideToast('error');
            hideToast('success');
            hideToast('fail');
        }

        // Populates the academic year field dropdown list with semesters from 1990 up to the current year
        function populateAcademicYearDropdown(dropdownId, isMobile) {
            const dropdown = document.getElementById(dropdownId);
            dropdown.innerHTML = '';

            const today = new Date();
            const currentMonth = today.getMonth(); // 0 = January, 7 = August
            let currentAcademicYearStart;

            // Academic year starts in August
            if (currentMonth >= 7) {
                currentAcademicYearStart = today.getFullYear();
            } else {
                currentAcademicYearStart = today.getFullYear() - 1;
            }

            const endYear = 1990;
            const terms = ['1st Semester', '2nd Semester', 'Midyear'];

            // Loop from current academic year down to end year (1990)
            for (let year = currentAcademicYearStart; year >= endYear; year--) {
                for (let term of terms) {
                    const label = `${year}-${year + 1} ${term}`;
                    const li = document.createElement('li');
                    li.setAttribute('tabindex', '0');
                    li.setAttribute('role', 'option');
                    li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold';
                    li.textContent = label;
                    li.onclick = () => selectAcademicYear(label, isMobile);
                    dropdown.appendChild(li);
                }
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            populateAcademicYearDropdown('academicYearDropdown', false); // desktop
            populateAcademicYearDropdown('academicYearDropdownMobile', true); // mobile

            // Setup dropdowns after population of academic year dropdown list
            setupAccessibleDropdown(academicYear.button, academicYear.dropdown, selectAcademicYear);
            setupAccessibleDropdown(academicYearMobile.button, academicYearMobile.dropdown, selectAcademicYear);
        });

        // Show receiver name in the dropdown
        window.selectReceiver = function(id, role) {
            const displayText = `${role}`;
            receiver.selected.innerHTML = displayText;
            receiver.input.value = id;
            receiver.dropdown.classList.add('hidden');
            receiverAutoSelected = true; // Disables auto-select receiver
        }

        // Show academic year in the dropdown
        window.selectAcademicYear = function (value, isMobile) {
            academicYear.input.value = value;
            academicYear.selected.textContent = value;
            academicYearMobile.selected.textContent = value;

            if (isMobile) {
                academicYearMobile.dropdown.classList.add('hidden');
            } else {
                academicYear.dropdown.classList.add('hidden');
            }

            // Fire change event once on the input
            academicYear.input.dispatchEvent(new Event('change', { bubbles: true }));
        };

        // Show attendees range in the dropdown
        window.selectAttendeesRange = function(value) {
            attendeesRange.selected.innerHTML = value;
            attendeesRange.input.value = value;
            attendeesRange.dropdown.classList.add('hidden');

            // Fire change event manually
            attendeesRange.input.dispatchEvent(new Event('change', { bubbles: true }));
        }

        // Select doc type
        window.selectDocType = function(value) {
            docType.selected.textContent = value;
            docType.input.value = value;
            docType.dropdown.classList.add('hidden');

            // Show/hide Event-specific fields
            if (value === 'Event Proposal') {
                eventProposalContainer.classList.remove('hidden');
                eventAttachmentsContainer.classList.remove('hidden');
            } else {
                eventProposalContainer.classList.add('hidden');
                eventAttachmentsContainer.classList.add('hidden');
            }

            // Show/hide 'Other' input field
            const othersDocTypeContainer = document.getElementById('othersDocTypeContainer');
            const othersDocTypeInput = document.getElementById('othersDocTypeInput');

            if (value === 'Other') {
                othersDocTypeContainer.hidden = false;
                othersDocTypeInput.name = 'type'; // Main submission field
                othersDocTypeInput.focus();
                docType.input.value = ''; // Clear main hidden input to avoid duplication
            } else {
                othersDocTypeContainer.hidden = true;
                othersDocTypeInput.name = 'other_type'; // Avoid conflicting name
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!docType.button.contains(e.target) && !docType.dropdown.contains(e.target)) {
                docType.dropdown.classList.add('hidden');
            }
            if (!receiver.button.contains(e.target) && !receiver.dropdown.contains(e.target)) {
                receiver.dropdown.classList.add('hidden');
            }
            if (!academicYear.button.contains(e.target) && !academicYear.dropdown.contains(e.target)) {
                academicYear.dropdown.classList.add('hidden');
            }
            if (!academicYearMobile.button.contains(e.target) && !academicYearMobile.dropdown.contains(e.target)) {
                academicYearMobile.dropdown.classList.add('hidden');
            }
            if (!attendeesRange.button.contains(e.target) && !attendeesRange.dropdown.contains(e.target)) {
                attendeesRange.dropdown.classList.add('hidden');
            }
        });

        // Overview character counter
        window.overviewUpdateCounter = function() {
            overviewCounter.textContent = overviewInput.value.length;
        }

        // Confirming Submission Toast Message
        function showConfirmPopup(event) {
            event.preventDefault();
            const popup = document.getElementById('confirmPopup');
            popup.classList.remove('hidden');

            const focusableElements = popup.querySelectorAll(
                'button:not([disabled]), [href], input, textarea, [tabindex]:not([tabindex="-1"])');
            const firstEl = focusableElements[0];
            const lastEl = focusableElements[focusableElements.length - 1];

            popup.addEventListener('keydown', function(e) {
                if (e.key === 'Tab') {
                    if (e.shiftKey) {
                        if (document.activeElement === firstEl) {
                            e.preventDefault();
                            lastEl.focus();
                        }
                    } else {
                        if (document.activeElement === lastEl) {
                            e.preventDefault();
                            firstEl.focus();
                        }
                    }
                }
            });

            setTimeout(() => firstEl.focus(), 0);
        }

        function closeConfirmPopup() {
            document.getElementById('confirmPopup').classList.add('hidden');
        }

        function handleConfirmSubmit(button) {
            // Disable the button to prevent multiple submissions
            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed');

            // Optionally change the text to indicate processing
            button.textContent = "Submitting...";

            // Submit the form manually if needed
            button.closest('form').submit();
        }

        // Checks if all input fields are filled before enabling the submit button
        document.addEventListener('DOMContentLoaded', () => {
            const requiredFields = {
                receiver: () => document.getElementById('receiverInput').value.trim() !== '',
                subject: () => document.getElementById('subject').value.trim() !== '',
                docType: () => {
                    const docTypeValue = document.getElementById('docTypeSelected').textContent.trim();
                    if (docTypeValue === 'Other') {
                        return requiredFields.otherType(); // ensure the user typed something
                    }
                    return document.getElementById('docTypeInput').value.trim() !== '';
                },
                otherType: () => document.getElementById('othersDocTypeInput').value.trim() !== '',
                academicYear: () => document.getElementById('academicYearInput').value.trim() !== '',
                overview: () => document.getElementById('overview').value.trim() !== '',
                venue: () => document.getElementById('venue').value.trim() !== '',
                proposed_date_time: () => document.getElementById('proposed_date_time').value.trim() !== '',
                hours: () => document.getElementById('hours').value.trim() !== '',
                attendees: () => document.getElementById('attendees').value.trim() !== '',
                attendeesRange: () => document.getElementById('attendeesRangeInput').value.trim() !== '',
                fees: () => document.getElementById('fees').value.trim() !== '',
                file: () => myDropzone && myDropzone.files && myDropzone.files.length > 0,
            };

            const submitButton = document.getElementById('mainSubmitButton');
            const docTypeInput = document.getElementById('docTypeInput');
            const feeInput = document.getElementById('fees');
            const feeNoneCheckbox = document.querySelector('input[name="fee_none"]');

            window.validateForm = function validateForm() {
                // If "Other" is selected, use the value from the text input
                const docTypeSelected = document.getElementById('docTypeSelected').textContent.trim();
                const othersInput = document.getElementById('othersDocTypeInput');

                if (docTypeSelected === 'Other') {
                    docTypeInput.value = othersInput.value.trim(); // overwrite hidden input value
                }

                const isEventProposal = docTypeInput.value === 'Event Proposal';
                const baseValid = requiredFields.receiver() && requiredFields.subject() &&
                    requiredFields.docType() && requiredFields.academicYear() && requiredFields.overview() && requiredFields.file();
                const eventValid = !isEventProposal || (
                    requiredFields.venue() && requiredFields.proposed_date_time() && requiredFields.hours()
                    && requiredFields.attendees() && requiredFields.attendeesRange() && (feeNoneCheckbox.checked || requiredFields.fees())
                );

                const allValid = baseValid && eventValid;

                submitButton.disabled = !allValid;

                // Toggle button styles
                submitButton.classList.toggle('bg-gray-500', !allValid);
                submitButton.classList.toggle('cursor-not-allowed', !allValid);
                submitButton.classList.toggle('bg-[#7A1212]', allValid);
                submitButton.classList.toggle('hover:bg-[#a31515]', allValid);
                submitButton.classList.toggle('cursor-pointer', allValid);
            }

            const inputsToWatch = [
                'receiverInput', 'subject', 'docTypeInput', 'othersDocTypeInput',
                'academicYearInput', 'overview', 'venue', 'proposed_date_time',
                'hours', 'attendees', 'attendeesRangeInput', 'fees', 'fileUpload'
            ];

            inputsToWatch.forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('input', validateForm);
                    element.addEventListener('change', validateForm);
                }
            });

            // "None" checkbox functionality for fees field
            feeNoneCheckbox.addEventListener('change', function () {
                if (this.checked) {
                    feeInput.readOnly = true;
                    feeInput.classList.add('bg-gray-200', 'text-gray-500', 'cursor-not-allowed');
                    feeInput.value = 0;
                } else {
                    feeInput.readOnly = false;
                    feeInput.classList.remove('bg-gray-200', 'text-gray-500', 'cursor-not-allowed');
                }

                validateForm();
            });

            // Re-validate when document type is changed via your selectDocType function
            const originalSelectDocType = window.selectDocType;
            window.selectDocType = function(value) {
                // Automatically select the first receiver only once if no receiver has been selected yet
                if (!receiverAutoSelected) {
                    const firstReceiver = document.querySelector('#receiverDropdown li');
                    if (firstReceiver) {
                        firstReceiver.click();
                        receiverAutoSelected = true;
                    }
                }

                originalSelectDocType(value);
                setTimeout(validateForm, 50); // slight delay to allow DOM changes
            };

            // Called on page load just in case
            validateForm();
        });
    </script>

    <!-- Dropzone JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <script>
        Dropzone.autoDiscover = false;

        const fileInput = document.getElementById("fileUpload");
        const MAX_FILES = 30;
        const MAX_FILE_SIZE_MB = 5;
        const ALLOWED_TYPES = [
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword'
        ];
        const previewTemplate = document.getElementById("custom-preview-template").innerHTML;

        myDropzone = new Dropzone("#desktopDropzone", {
            url: "#",
            autoProcessQueue: false,
            clickable: true,
            maxFiles: MAX_FILES,
            maxFilesize: MAX_FILE_SIZE_MB,
            previewsContainer: "#filePreviewArea",
            previewTemplate: previewTemplate,

            accept(file, done) {
                done(); // allow everything, we'll validate in "addedfile"
            },

            init() {
                const dz = this;

                dz.on("addedfile", function (file) {
                    // Validate
                    if (!ALLOWED_TYPES.includes(file.type)) {
                        dz.removeFile(file);
                        hideAllToasts();
                        showToast('error', "Invalid file type. Only PDF or DOCX files are allowed.");
                        return;
                    }

                    if (file.size > MAX_FILE_SIZE_MB * 1024 * 1024) {
                        dz.removeFile(file);
                        hideAllToasts();
                        showToast('error', "File size must not exceed 5 MB.");
                        return;
                    }

                    if (dz.files.length > MAX_FILES) {
                        dz.removeFile(file);
                        hideAllToasts();
                        showToast('error', "Upload limit reached. Please remove some files before uploading new ones.");
                        return;
                    }

                    // Show success icon
                    const successIcon = file.previewElement.querySelector(".dz-success-icon");
                    if (successIcon) successIcon.classList.remove("hidden");

                    // Update file input
                    const dt = new DataTransfer();
                    dz.files.forEach(f => dt.items.add(f));
                    fileInput.files = dt.files;

                    // Trigger change again to re-render previews
                    validateForm();
                });

                dz.on("removedfile", function (removedFile) {
                    const dt = new DataTransfer();
                    dz.files.forEach(f => {
                        if (f !== removedFile) {
                            dt.items.add(f);
                        }
                    });
                    fileInput.files = dt.files;

                    // Trigger change again to re-render previews
                    validateForm();
                });

                dz.on("queuecomplete", function () {
                    validateForm();
                });
            }
        });

        // Mobile input handler
        fileInput.addEventListener("change", function () {
            const files = Array.from(this.files);

            let added = 0;

            files.forEach(file => {
                if (!ALLOWED_TYPES.includes(file.type)) {
                    hideAllToasts();
                    showToast('error', "Invalid file type. Only PDF or DOCX files are allowed.");
                    return;
                }

                if (file.size > MAX_FILE_SIZE_MB * 1024 * 1024) {
                    hideAllToasts();
                    showToast('error', "File size must not exceed 5 MB.");
                    return;
                }

                if (myDropzone.files.length >= MAX_FILES) {
                    hideAllToasts();
                    showToast('error', "Upload limit reached. Please remove some files before uploading new ones.");
                    return;
                }

                myDropzone.addFile(file);
                added++;
            });

            setTimeout(() => {
                const dt = new DataTransfer();
                myDropzone.files.forEach(f => dt.items.add(f));
                fileInput.files = dt.files;

                validateForm();
            }, 100);

            // Reset so the same file can be selected again
            this.value = "";
        });

        // Override mobile trash icons via event delegation (if needed)
        document.getElementById("filePreviewArea").addEventListener("click", function (e) {
            if (e.target && e.target.matches(".dz-remove")) {
                const previewEl = e.target.closest(".dz-preview");
                if (previewEl) {
                    const file = myDropzone.files.find(f => f.previewElement === previewEl);
                    if (file) {
                        myDropzone.removeFile(file);
                    }
                }
            }
        });
    </script>
@endsection
