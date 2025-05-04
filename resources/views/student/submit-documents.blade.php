<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Documents</title>
</head>
<body>
    <div > <!-- class="flex flex-col md:flex-row relative min-h-screen" -->
        
        <!-- Sidebar -->
        @include('components.studentNavigation')

        <!-- Main Content -->
        <div class="flex-grow p-8">
            <!-- Display success message -->
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Display error message -->
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Display validation errors -->
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-8">
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
                                    class="w-full text-left border-b-2 border-gray-500 py-3 relative focus:outline-none flex items-center justify-between gap-2 bg-white">
                                    <span class="font-semibold text-gray-500">
                                        To: <span id="receiverSelected" class="font-semibold text-black"></span>
                                    </span>
                                    <img 
                                        src="{{ asset('images/gray-arrow-down.svg') }}" 
                                        alt="Dropdown Arrow" 
                                        id="receiverArrow"
                                        class="w-8 h-3"
                                    >
                                </button>

                                <!-- Dropdown List (Only names shown) -->
                                <ul id="receiverDropdown"
                                    class="hidden absolute z-10 w-full bg-white text-black border border-gray-300 rounded-[11px] shadow-md mt-1">
                                    <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold"
                                        onclick="selectReceiver('Dr. Leny Salmingo', 'Campus Director')">
                                        Dr. Leny Salmingo
                                    </li>
                                    <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold"
                                        onclick="selectReceiver('Dr. Jonell John Espalto', 'Head of Student Services')">
                                        Dr. Jonell John Espalto
                                    </li>
                                    <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold"
                                        onclick="selectReceiver('Dr. Marion Laguerta', 'Head of Academic Programs')">
                                        Dr. Marion Laguerta
                                    </li>
                                    <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold"
                                        onclick="selectReceiver('Engr. Emy Lou Alinsod', 'Office of the Academic Services')">
                                        Engr. Emy Lou Alinsod
                                    </li>
                                </ul>

                                <input type="hidden" name="doc_receiver" id="receiverInput">
                            </div>

                            <!-- Subject Field -->
                            <div class="flex items-center border-b-2 border-gray-500 py-3 w-full">
                                <span class="text-gray-500 font-semibold whitespace-nowrap mr-2">Subject:</span>
                                <input
                                    type="text"
                                    id="subject"
                                    name="subject"
                                    autocomplete="off"
                                    class="flex-1 font-semibold focus:outline-none"
                                >
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
                                    class="w-5 h-5"
                                >
                                <span id="docTypeSelected">Document Type</span>
                                
                                <!-- Dropdown Arrow aligned to the right -->
                                <img 
                                    src="{{ asset('images/white-arrow-down.svg') }}" 
                                    alt="Dropdown Arrow" 
                                    id="docTypeArrow"
                                    class="absolute right-4 w-8 h-3"
                                >
                            </button>

                            <!-- Dropdown List -->
                            <ul id="docTypeDropdown" class="hidden absolute z-10 mt-1 w-full bg-white text-black rounded-[11px] shadow-md">
                                <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold" onclick="selectDocType('Event Proposal')">Event Proposal</li>
                                <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold" onclick="selectDocType('General Plan of Activities')">General Plan of Activities</li>
                                <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold" onclick="selectDocType('Calendar of Activities')">Calendar of Activities</li>
                                <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold" onclick="selectDocType('Accomplishment Report')">Accomplishment Report</li>
                                <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold" onclick="selectDocType('Constitution and By-Laws')">Constitution and By-Laws</li>
                                <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold" onclick="selectDocType('Request Letter')">Request Letter</li>
                                <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold" onclick="selectDocType('Off Campus')">Off Campus</li>
                                <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer font-semibold" onclick="selectDocType('Petition and Concern')">Petition and Concern</li>
                            </ul>

                            <!-- Hidden input for form submission -->
                            <input type="hidden" name="doc_type" id="docTypeInput">
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="flex flex-col gap-1">
                        <label for="summary" class="font-semibold text-gray-500">Summary:</label>
                        
                        <textarea
                            id="summary"
                            name="summary"
                            class="w-full font-semibold h-[180px] resize-none overflow-y-visible focus:outline-none"
                            maxlength="255"
                            oninput="updateCounter()"
                            placeholder="Write a short description or overview..."
                        ></textarea>

                        <div class="text-sm text-gray-500 text-right border-b-2 border-gray-500">
                            <span id="summary-counter">0</span>/255
                        </div>
                    </div>

                    <!-- Date Range (Only shows for Event Proposals) -->
                    <div id="date-container" class="flex flex-col gap-2 md:flex-col hidden w-full">
                        <div class="flex flex-col md:flex-row md:items-center gap-2 w-full">
                            <input 
                                type="date" 
                                id="startDate" 
                                name="eventStartDate" 
                                class="border-b-2 border-gray-500 p-2 w-full focus:outline-none font-semibold text-gray-500"
                            >
                            <span class="hidden md:inline md:px-2">—</span>
                            <input 
                                type="date" 
                                id="endDate" 
                                name="eventEndDate" 
                                class="border-b-2 border-gray-500 p-2 w-full focus:outline-none font-semibold text-gray-500"
                            >
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div class="space-y-2 w-full md:w-[400px]">
                        <div class="flex items-center w-full overflow-hidden rounded-[12px] bg-white border border-gray-400">
                            <!-- Upload Button (Left side) -->
                            <label for="fileUpload" class="flex items-center gap-2 bg-[#7A1212] text-white font-semibold rounded-[12px] px-4 py-2 cursor-pointer hover:bg-[#a31515]">
                                <img 
                                    src="{{ asset('images/upload-icon.svg') }}" 
                                    alt="Upload Icon" 
                                    id="docTypeIcon"
                                    class="w-4 h-4"
                                >
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
                            type="button"
                            onclick="showConfirmPopup(event)"
                            class="order-1 md:order-2 w-full font-semibold bg-[#7A1212] hover:bg-[#a31515] text-white px-6 py-2 rounded-[12px] md:w-auto cursor-pointer transition"
                        >Submit</button>

                        <button 
                            type="button" 
                            class="order-2 md:order-1 w-full font-semibold border-2 hover:bg-gray-100 text-[#7A1212] px-6 py-2 rounded-[12px] md:w-auto cursor-pointer transition"
                        >Back to Home</button>
                    </div>

                    <!-- Confirmation Popup -->
                    <div id="confirmPopup" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 hidden">
                        <div class="bg-white rounded-xl p-6 w-[90%] max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl shadow-lg text-gray-800">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-lg font-semibold">Document Submission Confirmation</h2>
                                <button type="button" onclick="closeConfirmPopup()" class="text-gray-500 hover:text-gray-700 text-lg cursor-pointer">&times;</button>
                            </div>

                            <p class="mb-6">Are you sure you want to submit this document? Once submitted, you may not be able to make further changes.</p>

                            <div class="flex justify-end space-x-2">
                                <button onclick="closeConfirmPopup()" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-100 cursor-pointer" type="button">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 cursor-pointer" type="button">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>

                @if(session('success'))
                <script>
                    window.onload = () => {
                        alert("{{ session('success') }}");
                    };
                </script>
                @endif
            </div>
        </div>
    </div>

    <!-- Error Toast -->
    <div id="errorToast" class="hidden fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md bg-white border-l-4 border-red-300 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50">
        <div>
            <img 
                src="{{ asset('images/error.svg') }}" 
                alt="Error Icon" 
                id="docTypeIcon"
                class=""
            >
        </div>
        <div class="flex-1">
            <p class="font-semibold">Error</p>
            <p id="errorToastMsg" class="text-sm">Error message here</p>
        </div>
        <button type="button" onclick="hideToast()" class="text-gray-500 hover:text-gray-700 text-sm cursor-pointer">&times;</button>
    </div>

    <!-- Success Toast -->
    <div id="successToast" class="hidden fixed top-5 right-5 w-[90%] max-w-sm sm:max-w-md bg-white border-l-4 border-green-400 text-gray-800 shadow-lg rounded-lg flex items-start px-5 py-2 space-x-3 z-50">
        <div>
            <img 
                src="{{ asset('images/successful.svg') }}" 
                alt="Success Icon" 
                id="docTypeIcon"
                class=""
            >
        </div>
        <div class="flex-1">
            <p class="font-semibold text-green-700">Document Successfully Submitted</p>
            <p id="successToastMsg" class="text-sm">Your document has been submitted successfully. We'll review it shortly and get back to you if anything else is needed.</p>
        </div>
        <button type="button" onclick="hideToast()" class="text-gray-500 hover:text-gray-700 text-sm cursor-pointer">&times;</button>
    </div>
</body>
</html>

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
    const summaryInput = document.getElementById('summary');
    const summaryCounter = document.getElementById('summary-counter');
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

    // Toggle dropdown visibility
    docType.button.addEventListener('click', () => toggleDropdown(docType.dropdown));
    receiver.button.addEventListener('click', () => toggleDropdown(receiver.dropdown));

    function toggleDropdown(dropdown) {
        dropdown.classList.toggle('hidden');
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
            'application/msword']; // DOC

        const maxSize = 5 * 1024 * 1024;

        if (!validTypes.includes(file.type)) {
            showToast("Invalid file type. Only PDF or DOCX files are allowed.");
            input.value = "";
            fileNameDisplay.textContent = "No File Chosen";
            return;
        }

        if (file.size > maxSize) {
            showToast("File size must not exceed 5 mb.");
            input.value = "";
            fileNameDisplay.textContent = "No File Chosen";
            return;
        }

        fileNameDisplay.textContent = file.name;
    }

    // Error Toast Message
    function showToast(message) {
        const toast = document.getElementById("errorToast");
        const toastMsg = document.getElementById("errorToastMsg");
        toastMsg.textContent = message;
        toast.classList.remove("hidden");

        setTimeout(() => {
            hideToast();
        }, 5000); // Auto-hide after 5 seconds
    }

    // Hide Toast
    function hideToast() {
        const toast = document.getElementById("errorToast");
        toast.classList.add("hidden");
    }

    // selectReceiver() function
    window.selectReceiver = function(name, position) {
        const displayText = `${name} <span class="text-gray-400">&lsaquo;${position}&rsaquo;</span>`; // ‹ ›
        receiver.selected.innerHTML = displayText; // Use innerHTML to apply styling
        receiver.input.value = name;
        receiver.dropdown.classList.add('hidden');
    }

    // Select doc type
    window.selectDocType = function(value) {
        docType.selected.textContent = value;
        docType.input.value = value;
        docType.dropdown.classList.add('hidden');

        docType.icon.style.display = value !== 'Document Type' ? 'none' : 'inline';
        dateContainer.style.display = value === 'Event Proposal' ? 'block' : 'none';
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
    window.updateCounter = function() {
        summaryCounter.textContent = summaryInput.value.length;
    }

    // Show file name
    window.showFileName = function(input) {
        fileNameDisplay.textContent = input.files.length > 0 ? input.files[0].name : 'No File Chosen';
    }

    // Confirming Submission Toast Message
    function showConfirmPopup(event) {
        event.preventDefault();
        document.getElementById('confirmPopup').classList.remove('hidden');
    }

    function closeConfirmPopup() {
        document.getElementById('confirmPopup').classList.add('hidden');
    }
</script>
