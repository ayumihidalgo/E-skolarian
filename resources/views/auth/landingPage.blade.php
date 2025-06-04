<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="admin">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="icon" href="{{ asset('images/officialLogo.svg') }}" type="image/svg+xml">

    <title>E-skolarian | Login Selection</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen flex flex-col font-['Manrope'] font-bold text-white" style="background: linear-gradient(rgba(148, 125, 125, 0.6), rgba(148, 125, 125, 0.6)), url('{{ asset('images/landing-page-bg.jpg') }}'); background-size: cover; background-repeat: no-repeat; background-position: bottom;">
    <header class="bg-[#7A1212] text-center pb-3">
        <div class="flex gap-x-5 justify-center">
            <img class="w-[75px]" src="{{ asset('images/e-skolarianIcon.svg') }}" alt="E-skolarian Logo">
            <img class="w-[120px]" src="{{ asset('images/E-skolarianWhite.svg') }}" alt="E-skolarian Document Management Text">
        </div>
        <h1 class="font-[Lexend] text-[28px]/none">PUPSRC Document Management System</h1>
        <p class="font-[Manrope] font-normal text-xl">You are Logging In as?</p>
    </header>
    <main class="flex flex-1 justify-center items-center gap-6 p-5 flex-wrap">
        <section class="flex justify-center items-center flex-1">
            <div class="flex flex-col gap-y-3 bg-[#A98018D9] pl-5 pr-5 pb-5 pt-10 rounded-2xl text-center w-full min-h-[310px] max-w-[500px]">
                <img class="w-[80px] mx-auto" src="{{ asset('images/landing_student.svg') }}" alt="Student Org Icon">
                <div class="flex-1 pb-4">
                    <h2 class="font-[Lexend] text-[27px]">STUDENT ORGANIZATION</h2>
                    <p class="mx-auto font-[Manrope] font-normal w-[80%]">Access your student portal or organization dashboard</p>
                </div>
                <a href="{{ route('student.login.form') }}"
                    class="inline-block p-4 rounded-full bg-[#A98018] border border-white py-2 text-center w-full hover:bg-[#8a6f14] transition-bg duration-75">
                    Login as Student Organization
                </a>
            </div>
        </section>
          <section class="flex justify-center items-center flex-1">
            <div class="flex flex-col gap-y-3 bg-[#7A1212D9] pl-5 pr-5 pb-5 pt-10 rounded-2xl text-center w-full min-h-[310px] max-w-[500px]">
                <img class="w-[80px] mx-auto" src="{{ asset('images/landing_admin.svg') }}" alt="Admin Icon">
                <div class="flex-1 pb-4">
                    <h2 class="font-[Lexend] text-[27px]">ADMIN</h2>
                    <p class="text-center mx-auto font-[Manrope] font-normal w-[80%]">Access the administrative control panel</p>
                </div>
                <a href="{{ route('admin.login.form') }}"
                    class="inline-block p-4 rounded-full bg-[#7A1212] border border-white py-2 text-center w-full hover:bg-[#5a0f0f] transition-bg duration-75">
                    Login as Admin
                </a>
            </div>
        </section>
    </main>
    <footer class="flex text-center justify-center items-center gap-2 bg-[#4D0F0F] min-h-[70px] flex-wrap p-2">
        <p>Need help? Contact <button onclick="document.getElementById('reportBtn').click();" class="underline cursor-pointer">PUPSRC SUPERADMIN</button> for assistance with your login.</p>
        <!-- Report Button -->
        <button id="reportBtn"
            class="bg-transparent rounded-full shadow-none focus:outline-none z-50 flex items-center justify-center cursor-pointer"
            title="Report a Problem">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-[26px]">
                <path d="M7.757 2h8.486l5.757 5.757v8.486l-5.757 5.757H7.757L2 16.243V7.757L7.757 2z"
                    fill="transparent" stroke="white" stroke-width="2.5"/>
                <rect x="11" y="8" width="2" height="4" fill="white" />
                <rect x="11" y="14" width="2" height="2" fill="white" />
            </svg>
        </button>
    </footer>
         <!-- Report Problem Modal -->
        <div id="reportModal" class="fixed inset-0 flex items-center z-50 backdrop-blur-sm w-full text-black hidden">
        <div class="bg-[#ffffffe8] p-6 shadow-lg mx-auto rounded-3xl max-w-[600px]">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-20 h-20 mx-auto pb-4">
                <path d="M7.757 2h8.486l5.757 5.757v8.486l-5.757 5.757H7.757L2 16.243V7.757L7.757 2z"
                    fill="transparent" stroke="black" stroke-width="2.5"/>
                <rect x="11" y="8" width="2" height="4" fill="black" />
                <rect x="11" y="14" width="2" height="2" fill="black" />
            </svg>
            <div class="mb-4 text-center mx-auto w-[75%]">
            <h1 class="text-3xl font-[Lexend] font-bold mb-2 text-[var(--secondary-color)]">Report a Problem</h1>
            <p class="text-black font-[Manrope] font-normal text-xs">
                Noticed something wrong or not working as expected? Tell us what issue you encountered so we can look into it and improve your experience.
            </p>
            </div>

            <form id="reportForm" class="mx-auto w-[75%] text-xs" method="POST" action="{{ route('report.problem.store') }}" enctype="multipart/form-data" onsubmit="submitReport(event)">
            @csrf
            <div class="mb-7">
                <label class="pl-[9px] block font-semibold mb-1">PUP Webmail<span class="text-red-600">*</span></label>
                <input id="webmailInput" type="email" name="email" placeholder="PUP Email Address" class="w-full border bg-white rounded-lg px-3 py-2" required maxlength="51" />
                <p id="webmailLengthWarning" class="text-red-600 mt-1 hidden">*Webmail must not exceed 50 characters.</p>
            </div>

            <div class="mb-3 relative">
            <label class="pl-[9px] block font-semibold mb-1">Problem Description<span class="text-red-600">*</span></label>
            <textarea
                id="descriptionInput"
                name="description"
                placeholder="Describe the problem here..."
                class="w-full border rounded-lg px-3 py-2 bg-white resize-none"
                rows="4"
                maxlength="255"
                required
            ></textarea>

            <span
                id="descCounter"
                class="absolute bottom-2 right-3 text-xs text-gray-500 select-none pointer-events-none"
            >0 / 255</span>
            <p id="descLengthWarning" class="text-red-600 mt-1 hidden">*Description must be 255 characters or less.</p>
            </div>

            <div class="mb-7">
                <label class="pl-[9px] block font-semibold mb-1">Attach a File (optional)</label>
                <input id="fileInput" type="file" name="screenshot" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg"
                    class="text-slate-500 text-sm rounded-lg leading-6 file:bg-[var(--secondary-color)] file:text-white file:border-none file:px-4 file:py-1 file:mr-6 file:rounded-lg hover:file:brightness-75 border bg-[#0000000c] cursor-pointer border-gray-300 transition duration-200">
                <p class="pl-[5px] text-[8px]">Choose a file up to 5MB. Valid file types: PDF, DOCX, DOC, PNG, JPG</p>
            </div>

            <div class="text-[16px]">
                <button type="submit" id="reportSubmitBtn" class="block w-full bg-[var(--secondary-color)] text-white px-4 py-2 mb-4 rounded-full hover:brightness-75">Submit</button>
                <button type="button" id="cancelReportBtn" class="block w-full bg-gray-300 text-gray-700 px-4 py-2 rounded-full hover:bg-gray-400">Cancel</button>
            </div>
            </form>
        </div>
        </div>

    <!-- Confirmation Modal -->
    <div id="confirmCloseModal" class="hidden font-[Manrope] fixed inset-0 bg-transparent flex items-center justify-center z-60 backdrop-blur-sm">
        <div class="bg-white/90 font-[Manrope] rounded-4xl shadow-lg py-6 px-4 max-w-lg w-full text-center">
            <div class="w-[80%] mx-auto">
                <h1 class="text-xl mb-4 text-[var(--secondary-color)]">Are you sure you want to cancel?</h1>
                <p class="mb-6 font-normal text-black">You have unsaved changes. Are you sure you want to close?</p>
                <div class="flex justify-around gap-5">
                    <button id="confirmCloseYes" class="bg-[var(--secondary-color)] text-white px-4 py-2 w-full max-w-md rounded-full hover:brightness-75">Yes</button>
                    <button id="confirmCloseNo" class="bg-[#D9D9D9CC] text-black px-4 py-2 rounded-full w-full max-w-md hover:bg-gray-400">Go Back</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="hidden font-[Manrope] fixed inset-0 bg-transparent flex items-center justify-center z-60 backdrop-blur-sm">
        <div class="bg-white/90 font-[Manrope] rounded-4xl shadow-lg py-6 px-4 max-w-lg w-full text-center">
            <div class="w-[80%] mx-auto">
                <h1 class="text-[20px] font-[Lexend] mb-3 text-[var(--secondary-color)]">✅ Report Submitted Successfully!</h1>
                <div class="text-center text-md">
                    <p class="mb-3 font-normal text-black">Thank you for your feedback.</p>
                    <p class="mb-6 font-normal text-black">Our team will review your report and get back to you if necessary. You may now close this window or return to the previous page.</p>
                </div>
                <div class="flex justify-center">
                    <button onclick="closeSuccessModal()" class="bg-[var(--secondary-color)] text-white px-6 py-2 w-full max-w-md rounded-full hover:brightness-75 transition-colors duration-200">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="hidden font-[Manrope] fixed inset-0 bg-transparent flex items-center justify-center z-60 backdrop-blur-sm">
        <div class="bg-white/90 font-[Manrope] rounded-4xl shadow-lg py-6 px-4 max-w-lg w-full text-center">
            <div class="w-[80%] mx-auto">
                <h1 class="text-[20px] font-[Lexend] mb-3 text-[var(--secondary-color)]">❌ Report Submitted Failed!</h1>
                <div class="text-center text-md">
                    <p class="mb-3 font-normal text-black">Sorry for the inconvenience.</p>
                    <p class="mb-6 font-normal text-black">Please try to submit again or provide a valid format.</p>
                </div>
                <div class="flex justify-center">
                    <button onclick="closeErrorModal()" class="bg-[var(--secondary-color)] text-white px-6 py-2 w-full max-w-md rounded-full hover:brightness-75 transition-colors duration-200">Close</button>
                </div>
            </div>
        </div>
    </div>


    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const reportBtn = document.getElementById('reportBtn');
        const reportModal = document.getElementById('reportModal');
        const reportForm = document.getElementById('reportForm');
        const cancelReportBtn = document.getElementById('cancelReportBtn');
        const submitBtn = document.getElementById('reportSubmitBtn');

        let isDirty = false;

        // Disable submit button initially with styles
        function disableSubmit() {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }

        // Enable submit button with styles removed
        function enableSubmit() {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }

        // Validate inputs and enable/disable submit button accordingly
        function validateInputs() {
        const email = reportForm.email.value.trim();
        const desc = reportForm.description.value.trim();

        const maxEmailLength = 50;
        const maxDescLength = 255;

        const isEmailTooLong = email.length > maxEmailLength;
        const isDescTooLong = desc.length > maxDescLength;

        const isEmailValid = email.length > 0 && !isEmailTooLong;
        const isDescValid = desc.length > 0 && !isDescTooLong;

        // Show warnings only if too long
        document.getElementById('webmailLengthWarning').classList.toggle('hidden', !isEmailTooLong);
        document.getElementById('descLengthWarning').classList.toggle('hidden', !isDescTooLong);

        const shouldEnableSubmit = isEmailValid && isDescValid;

        if (shouldEnableSubmit) {
            enableSubmit();
        } else {
            disableSubmit();
        }
        }
        const webmailInput = document.getElementById('webmailInput');

         // Prevent spaces in email
        webmailInput.addEventListener('keydown', function (e) {
            if (e.key === ' ') e.preventDefault();
            });

        webmailInput.addEventListener('paste', function (e) {
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            if (/\s/.test(pastedText)) {
                e.preventDefault();
                alert('Spaces are not allowed in the email address.');
            }
        });

         // Update description counter
        const descriptionInput = document.getElementById('descriptionInput');
        const descCounter = document.getElementById('descCounter');
        const descWarning = document.getElementById('descLengthWarning');

        descriptionInput.addEventListener('input', () => {
            const currentLength = descriptionInput.value.length;
            descCounter.textContent = `${currentLength} / 255`;

            if (currentLength > 255) {
                descWarning.classList.remove('hidden');
            } else {
                descWarning.classList.add('hidden');
            }
        });

        // Initialize counter on load if there's pre-filled text
        descCounter.textContent = `${descriptionInput.value.length} / 255`;



        // On modal open: reset form, reset flags, disable submit and validate inputs
        reportBtn.addEventListener('click', () => {
            reportModal.classList.remove('hidden');
            reportForm.reset();
            isDirty = false;
            disableSubmit();
            validateInputs();
        });

        // Listen for input on required fields
        reportForm.email.addEventListener('input', () => {
            isDirty = true;
            validateInputs();
        });

        reportForm.description.addEventListener('input', () => {
            isDirty = true;
            validateInputs();
        });

        // Cancel button logic with confirmation if dirty
        cancelReportBtn.addEventListener('click', () => {
            if (isDirty) {
            document.getElementById('confirmCloseModal').classList.remove('hidden');
            } else {
            closeReportModal();
            }
        });

        // Confirmation modal buttons
        document.getElementById('confirmCloseYes').addEventListener('click', () => {
            closeReportModal();
            document.getElementById('confirmCloseModal').classList.add('hidden');
            resetFormState();
        });

        document.getElementById('confirmCloseNo').addEventListener('click', () => {
            document.getElementById('confirmCloseModal').classList.add('hidden');
        });

        function closeReportModal() {
            reportModal.classList.add('hidden');
        }

        function resetFormState() {
            reportForm.reset();
            isDirty = false;
            disableSubmit();
        }

        // Submit report function with fetch
        window.submitReport = function(event) {
            event.preventDefault();

            disableSubmit();
            submitBtn.innerText = 'Submitting...';

            const formData = new FormData(reportForm);

            fetch(reportForm.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
            })
            .then(response => {
            const contentType = response.headers.get("content-type") || "";
            if (contentType.includes("application/json")) {
                return response.json();
            } else {
                return response.text();
            }
            })
            .then(data => {
            showSuccessModal();
            closeReportModal();
            resetFormState();
            })
            .catch(error => {
            showErrorModal();
            console.error('Error:', error);
            })
            .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Submit';
            validateInputs();
            });
        };

        // Initial state
        disableSubmit();
    });

    const fileInput = document.getElementById('fileInput');

    fileInput.addEventListener('change', function () {
        if (fileInput.files.length > 0) {
        fileInput.classList.remove('bg-gray-300');
        fileInput.classList.add('bg-white');
        } else {
        fileInput.classList.remove('bg-white');
        fileInput.classList.add('bg-gray-300');
        }
    });

    function showSuccessModal() {
        document.getElementById('successModal').classList.remove('hidden');
    }

    function closeSuccessModal() {
        document.getElementById('successModal').classList.add('hidden');
    }

    function showErrorModal() {
        document.getElementById('errorModal').classList.remove('hidden');
    }

    function closeErrorModal() {
        document.getElementById('errorModal').classList.add('hidden');
    }
</script>
</body>
</html>
