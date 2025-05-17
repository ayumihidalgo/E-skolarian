@extends('base')
@section('content')
@include('components.studentNavBarComponent')
@include('components.studentSideBarComponent')

<div id="main-content" class="transition-all duration-300 ml-[20%]">
    <!-- Main Content -->
    <div class="flex-grow p-6">
        <div class="">
            <h1 class="text-2xl font-['Lexend'] font-semibold mb-6">Document Submission</h1>

            <form class="space-y-6 font-['Manrope']" action="{{ route('submit.document') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- Receiver, Subject, Doc Type -->
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Left Side -->
                    <div class="flex flex-col gap-4 w-full md:w-2/3 relative">
                        <!-- Receiver Button -->
                        <div class="relative w-full">
                            <button type="button" id="receiverButton" aria-expanded
                                class="w-full text-left border-b-2 border-gray-500 py-3 relative focus:outline-none flex items-center justify-between gap-2 bg-white cursor-pointer">
                                <span class="font-semibold text-gray-500">
                                    To<span class="required-indicator text-red-500"> *</span>:
                                    <span id="receiverSelected" class="font-semibold text-black"></span>
                                </span>
                                <img
                                    src="{{ asset('images/gray-arrow-down.svg') }}"
                                    alt="Dropdown Arrow"
                                    id="receiverArrow"
                                    class="w-8 h-3">
                            </button>

                            <!-- Dropdown List -->
                            <ul role="listbox" id="receiverDropdown"
                                class="hidden absolute z-10 w-full bg-white text-black border border-gray-300 rounded-[11px] shadow-md mt-1">
                                @foreach ($adminUsers as $admin)
                                <li tabindex="0" role="option" class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold"
                                    onclick="selectReceiver('{{ $admin->id }}', '{{ $admin->username }}', '{{ $admin->role_name }}')">
                                    {{ $admin->username }}
                                </li>
                                @endforeach
                            </ul>

                            <input type="hidden" name="received_by" id="receiverInput">
                        </div>

                        <!-- Subject Field -->
                        <div class="flex items-center border-b-2 border-gray-500 py-3 w-full">
                            <span class="text-gray-500 font-semibold whitespace-nowrap mr-2">Subject<span class="required-indicator text-red-500"> *</span>:</span>
                            <input
                                type="text"
                                id="subject"
                                name="subject"
                                autocomplete="off"
                                class="flex-1 font-semibold focus:outline-none"
                                maxlength="50">
                        </div>
                    </div>

                    <!-- Right Side -->
                    <div class="relative w-full md:w-1/3">
                        <!-- Document Type Button -->
                        <button type="button" id="docTypeButton" aria-expanded
                            class="relative font-semibold w-full flex justify-center items-center gap-2 bg-[#7A1212] hover:bg-[#a31515] text-white px-6 py-3 rounded-[12px] cursor-pointer transition">
                            <img
                                src="{{ asset('images/submitDocument.svg') }}"
                                alt="Submit Document"
                                id="docTypeIcon"
                                class="w-5 h-5">
                            <span id="docTypeSelected">Document Type</span>

                            <!-- Dropdown Arrow aligned to the right -->
                            <img
                                src="{{ asset('images/white-arrow-down.svg') }}"
                                alt="Dropdown Arrow"
                                id="docTypeArrow"
                                class="absolute right-4 w-8 h-3">
                        </button>

                        <!-- Dropdown List -->
                        <ul role="listbox" id="docTypeDropdown" class="hidden absolute z-10 mt-1 w-full bg-white text-black rounded-[11px] shadow-md">
                            <li tabindex="0" role="option" class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold" onclick="selectDocType('Event Proposal')">Event Proposal</li>
                            <li tabindex="0" role="option" class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold" onclick="selectDocType('General Plan of Activities')">General Plan of Activities</li>
                            <li tabindex="0" role="option" class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold" onclick="selectDocType('Calendar of Activities')">Calendar of Activities</li>
                            <li tabindex="0" role="option" class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold" onclick="selectDocType('Accomplishment Report')">Accomplishment Report</li>
                            <li tabindex="0" role="option" class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold" onclick="selectDocType('Constitution and By-Laws')">Constitution and By-Laws</li>
                            <li tabindex="0" role="option" class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold" onclick="selectDocType('Request Letter')">Request Letter</li>
                            <li tabindex="0" role="option" class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold" onclick="selectDocType('Off Campus')">Off Campus</li>
                            <li tabindex="0" role="option" class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold" onclick="selectDocType('Petition and Concern')">Petition and Concern</li>
                        </ul>

                        <!-- Hidden input for form submission -->
                        <input type="hidden" name="type" id="docTypeInput">
                    </div>
                </div>

                <!-- Summary -->
                <div class="flex flex-col gap-1">
                    <label for="summary" class="font-semibold text-gray-500">Summary<span class="required-indicator text-red-500"> *</span>:</label>

                    <textarea
                        id="summary"
                        name="summary"
                        class="w-full font-semibold h-[150px] resize-none overflow-y-visible focus:outline-none"
                        maxlength="255"
                        oninput="summaryUpdateCounter()"
                        placeholder="Write a short description or overview..."></textarea>

                    <div class="text-sm text-gray-500 text-right border-b-2 border-gray-500">
                        <span id="summary-counter">0</span>/255
                    </div>
                </div>

                <!-- Date Range (Only shows for Event Proposals) -->
                <div id="date-container" class="flex flex-col gap-2 md:flex-col w-full hidden">
                    <div class="flex flex-col md:flex-row md:items-center gap-2 w-full">
                        <input
                            type="date"
                            id="startDate"
                            name="eventStartDate"
                            class="border-b-2 border-gray-500 p-2 w-full focus:outline-none font-semibold text-gray-500">
                        <span class="hidden md:inline md:px-2">—</span>
                        <input
                            type="date"
                            id="endDate"
                            name="eventEndDate"
                            class="border-b-2 border-gray-500 p-2 w-full focus:outline-none font-semibold text-gray-500">
                    </div>
                </div>

                <!-- Event Title (Only shows for Event Proposals) -->
                <div id="event-title-container" class="flex items-center border-b-2 border-gray-500 py-3 w-full hidden">
                    <span class="text-gray-500 font-semibold whitespace-nowrap mr-2">Event Title<span class="required-indicator text-red-500"> *</span>:</span>
                    <input
                        type="text"
                        id="event-title"
                        name="event-title"
                        autocomplete="off"
                        class="flex-1 font-semibold focus:outline-none"
                        maxlength="50">
                </div>

                <!-- Event Description (Only shows for Event Proposals) -->
                <div id="event-desc-container" class="flex flex-col gap-1 hidden">
                    <label for="event-desc" class="font-semibold text-gray-500">Event Description<span class="required-indicator text-red-500"> *</span>:</label>

                    <textarea
                        id="event-desc"
                        name="event-desc"
                        class="w-full font-semibold h-[150px] resize-none overflow-y-visible focus:outline-none"
                        maxlength="255"
                        oninput="eventDescUpdateCounter()"
                        placeholder="Write a short description or overview..."></textarea>

                    <div class="text-sm text-gray-500 text-right border-b-2 border-gray-500">
                        <span id="event-desc-counter">0</span>/255
                    </div>
                </div>

                <!-- File Upload -->
                <div class="space-y-2 w-full md:w-[400px]">
                    <div class="flex items-center w-full overflow-hidden rounded-[12px] bg-white border border-gray-400">
                        <!-- Upload Button (Left side) -->
                        <label tabindex="0" for="fileUpload" class="flex items-center gap-2 bg-[#7A1212] text-white font-semibold rounded-[12px] px-4 py-2 cursor-pointer hover:bg-[#a31515]">
                            <img
                                src="{{ asset('images/upload-icon.svg') }}"
                                alt="Upload Icon"
                                id="docTypeIcon"
                                class="w-4 h-4">
                            Upload File
                        </label>

                        <!-- Hidden File Input -->
                        <input type="file" id="fileUpload" name="file_upload" class="hidden" onchange="validateFile(this)">

                        <!-- Filename Display (Right side) -->
                        <div id="fileName" class="flex-1 px-3 py-2 text-sm text-gray-500 truncate">No File Chosen</div>
                    </div>

                    <p class="text-sm text-gray-500">Choose a file up to 5MB. Valid file types: PDF, DOCX, DOC</p>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col md:flex-row gap-4 justify-end">
                    <button
                        id="mainSubmitButton"
                        type="button"
                        onclick="showConfirmPopup(event)"
                        class="order-1 md:order-2 w-full font-semibold bg-gray-500 text-white px-6 py-2 rounded-[12px] md:w-auto cursor-not-allowed transition"
                        disabled>Submit</button>

                    <button
                        type="button"
                        onclick="window.location.href='{{ route('student.dashboard') }}'"
                        class="order-2 md:order-1 w-full font-semibold border-2 hover:bg-gray-100 text-[#7A1212] px-6 py-2 rounded-[12px] md:w-auto cursor-pointer transition">Back to Home</button>
                </div>

                <!-- Confirmation Popup -->
                <div id="confirmPopup" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 hidden">
                    <div class="bg-white rounded-xl p-6 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl shadow-lg text-gray-800">
                        <div class="flex justify-between items-start mb-4">
                            <h2 class="text-lg font-semibold">Document Submission Confirmation</h2>
                            <button type="button" onclick="closeConfirmPopup()" class="text-gray-500 hover:text-gray-700 text-3xl leading-none cursor-pointer self-center">&times;</button>
                        </div>

                        <p class="mb-6">Are you sure you want to submit this document? Once submitted, you may not be able to make further changes.</p>

                        <div class="flex justify-end space-x-2">
                            <button onclick="closeConfirmPopup()" class="font-semibold px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-100 cursor-pointer" type="button">Cancel</button>
                            <button id="confirmSubmitBtn" type="submit" onclick="handleConfirmSubmit(this)" class="font-semibold px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 cursor-pointer">
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Error Toast -->
<div id="errorToast" class="hidden fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white border-l-4 border-red-300 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50">
    <div>
        <img
            src="{{ asset('images/error.svg') }}"
            alt="Error Icon"
            id="docTypeIcon"
            class="">
    </div>
    <div class="flex-1">
        <p class="font-semibold">Error</p>
        <p id="errorToastMsg" class="text-sm">Error message here</p>
    </div>
    <button type="button" onclick="hideToast('error')" class="text-gray-500 hover:text-gray-700 text-2xl leading-none cursor-pointer self-center">&times;</button>
</div>

<!-- Document Submission Success Toast -->
<div id="successToast" class="hidden fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white border-l-4 border-green-400 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50">
    <div>
        <img
            src="{{ asset('images/successful.svg') }}"
            alt="Success Icon"
            id="docTypeIcon"
            class="">
    </div>
    <div class="flex-1">
        <p class="font-semibold">Document Successfully Submitted</p>
        <p id="successToastMsg" class="text-sm">Your document has been submitted successfully. We'll review it shortly and get back to you if anything else is needed.</p>
    </div>
    <button type="button" onclick="hideToast('success')" class="text-gray-500 hover:text-gray-700 text-2xl leading-none cursor-pointer self-center">&times;</button>
</div>

<!-- Document Submission Fail Toast -->
<div id="failToast" class="hidden fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-white border-l-4 border-red-300 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50">
    <div>
        <img
            src="{{ asset('images/error.svg') }}"
            alt="Error Icon"
            id="docTypeIcon"
            class="">
    </div>
    <div class="flex-1">
        <p class="font-semibold">Error</p>
        <p id="failToastMsg" class="text-sm">Failed to submit document. Please try again later.</p>
    </div>
    <button type="button" onclick="hideToast('fail')" class="text-gray-500 hover:text-gray-700 text-2xl leading-none cursor-pointer self-center">&times;</button>
</div>

<!-- Display document submision success message -->
@if(session('success'))
<script>
    window.addEventListener('DOMContentLoaded', () => {
        showToast('success');
    });
</script>
@endif

<!-- Display document submission fail message -->
@if(session('error'))
<script>
    window.addEventListener('DOMContentLoaded', () => {
        showToast('fail');
    });
</script>
@endif

<script>
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

    const dateContainer = document.getElementById('date-container');
    const eventTitleContainer = document.getElementById('event-title-container');
    const eventDescContainer = document.getElementById('event-desc-container');
    const summaryInput = document.getElementById('summary');
    const summaryCounter = document.getElementById('summary-counter');
    const eventDescInput = document.getElementById('event-desc');
    const eventDescCounter = document.getElementById('event-desc-counter');
    const fileNameDisplay = document.getElementById('fileName');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');

    // Set today's date as the minimum for both fields
    const today = new Date().toISOString().split('T')[0];
    startDateInput.min = today;
    endDateInput.min = today;

    // Ensure start date is not after end date
    startDateInput.addEventListener('change', () => {
        if (startDateInput.value > endDateInput.value) {
            endDateInput.value = startDateInput.value;
        }
        endDateInput.min = startDateInput.value;
    });

    endDateInput.addEventListener('change', () => {
        if (endDateInput.value < startDateInput.value) {
            startDateInput.value = endDateInput.value;
        }
    });

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


    document.querySelector('label[for="fileUpload"]').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            document.getElementById('fileUpload').click();
        }
    });

    // Prevent form submission on Enter keypress except from inside the confirmation popup
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("subject").addEventListener("keydown", function(e) {
            if (e.key === "Enter") {
                e.preventDefault(); // Prevent form submission
            }
        });
        document.getElementById("event-title").addEventListener("keydown", function(e) {
            if (e.key === "Enter") {
                e.preventDefault(); // Prevent form submission
            }
        });

        setupAccessibleDropdown(docType.button, docType.dropdown, selectDocType);
        setupAccessibleDropdown(receiver.button, receiver.dropdown, selectReceiver);
    });

    // Toggle dropdown visibility
    docType.button.addEventListener('click', () => toggleDropdown(docType.dropdown));
    receiver.button.addEventListener('click', () => toggleDropdown(receiver.dropdown));

    function toggleDropdown(dropdown) {
        dropdown.classList.toggle('hidden');
        if (!dropdown.classList.contains('hidden')) {
            const firstItem = dropdown.querySelector('li');
            if (firstItem) {
                setTimeout(() => firstItem.focus(), 0);
            }
        }
    }

    // File Upload Validation
    function validateFile(input) {
        const file = input.files[0];
        const fileNameDisplay = document.getElementById('fileName');

        if (!file) {
            fileNameDisplay.textContent = "No File Chosen";
            return;
        }

        const validTypes = ['application/pdf', //PDF
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // DOCX
            'application/msword' // DOC
        ];

        const maxSize = 5 * 1024 * 1024;

        if (!validTypes.includes(file.type)) {
            hideAllToasts();
            showToast('error', "Invalid file type. Only PDF or DOCX files are allowed.");
            input.value = "";
            fileNameDisplay.textContent = "No File Chosen";
            return;
        }

        if (file.size > maxSize) {
            hideAllToasts();
            showToast('error', "File size must not exceed 5 mb.");
            input.value = "";
            fileNameDisplay.textContent = "No File Chosen";
            return;
        }

        fileNameDisplay.textContent = file.name;
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

    // selectReceiver() function
    window.selectReceiver = function(id, name, role) {
        const displayText = `${name} <span class="text-gray-400">&lsaquo;${role}&rsaquo;</span>`; // ‹ ›
        receiver.selected.innerHTML = displayText; // Use innerHTML to apply styling
        receiver.input.value = id;
        receiver.dropdown.classList.add('hidden');
    }

    // Select doc type
    window.selectDocType = function(value) {
        docType.selected.textContent = value;
        docType.input.value = value;
        docType.dropdown.classList.add('hidden');

        // Show/hide Event-specific fields
        if (value === 'Event Proposal') {
            eventTitleContainer.classList.remove('hidden');
            eventDescContainer.classList.remove('hidden');
            dateContainer.classList.remove('hidden');
        } else {
            eventTitleContainer.classList.add('hidden');
            eventDescContainer.classList.add('hidden');
            dateContainer.classList.add('hidden');
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
    });

    // Summary character counter
    window.summaryUpdateCounter = function() {
        summaryCounter.textContent = summaryInput.value.length;
    }

    // Event description character counter
    window.eventDescUpdateCounter = function() {
        eventDescCounter.textContent = eventDescInput.value.length;
    }

    // Show file name
    window.showFileName = function(input) {
        fileNameDisplay.textContent = input.files.length > 0 ? input.files[0].name : 'No File Chosen';
    }

    // Confirming Submission Toast Message
    function showConfirmPopup(event) {
        event.preventDefault();
        const popup = document.getElementById('confirmPopup');
        popup.classList.remove('hidden');

        const focusableElements = popup.querySelectorAll('button:not([disabled]), [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
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
            docType: () => document.getElementById('docTypeInput').value.trim() !== '',
            summary: () => document.getElementById('summary').value.trim() !== '',
            file: () => document.getElementById('fileUpload').files.length > 0,
            eventTitle: () => document.getElementById('event-title').value.trim() !== '',
            eventDesc: () => document.getElementById('event-desc').value.trim() !== '',
            startDate: () => document.getElementById('startDate').value.trim() !== '',
            endDate: () => document.getElementById('endDate').value.trim() !== '',
        };

        const submitButton = document.getElementById('mainSubmitButton');
        const docTypeInput = document.getElementById('docTypeInput');

        function validateForm() {
            const isEventProposal = docTypeInput.value === 'Event Proposal';
            const baseValid = requiredFields.receiver() && requiredFields.subject() &&
                requiredFields.docType() && requiredFields.summary() && requiredFields.file();
            const eventValid = !isEventProposal || (
                requiredFields.eventTitle() && requiredFields.eventDesc() &&
                requiredFields.startDate() && requiredFields.endDate()
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
            'receiverInput', 'subject', 'docTypeInput', 'summary',
            'event-title', 'event-desc', 'startDate', 'endDate', 'fileUpload'
        ];

        inputsToWatch.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('input', validateForm);
                element.addEventListener('change', validateForm);
            }
        });

        // Re-validate when document type is changed via your selectDocType function
        const originalSelectDocType = window.selectDocType;
        window.selectDocType = function(value) {
            originalSelectDocType(value);
            setTimeout(validateForm, 50); // slight delay to allow DOM changes
        };

        // Called on page load just in case
        validateForm();
    });
</script>

@endsection