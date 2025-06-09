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
        <div class="flex flex-1 gap-6 w-full max-w-6xl flex-wrap justify-around items-stretch">
            <section class="flex justify-center flex-1 min-w-[300px] w-full max-w-[500px]">
                <div class="flex flex-col gap-y-3 bg-[#A98018D9] pl-5 pr-5 pb-5 pt-10 rounded-2xl text-center w-full max-w-[500px] h-full">
                    <img class="w-[70px] mx-auto" src="{{ asset('images/landing_student.svg') }}" alt="Student Org Icon">
                    <div class="flex-1">
                        <h2 class="font-[Lexend] text-[30px]">STUDENT ORGANIZATION</h2>
                        <p class="mx-auto font-[Manrope] font-normal w-[80%]">Access your student portal or organization dashboard</p>
                    </div>
                    <a href="{{ route('student.login.form') }}"
                        class="inline-block p-4 rounded-full bg-[#A98018] border border-white py-2 text-center w-full hover:bg-[#8a6f14] transition-bg duration-75">
                        Login as Student Organization
                    </a>
                </div>
            </section>
            <section class="flex justify-center items-stretch flex-1 min-w-[300px] w-full max-w-[500px]">
                <div class="flex flex-col gap-y-3 bg-[#7A1212D9] pl-5 pr-5 pb-5 pt-10 rounded-2xl text-center w-full min-h-[310px] max-w-[500px] h-full">
                    <img class="w-[70px] mx-auto" src="{{ asset('images/landing_admin.svg') }}" alt="Admin Icon">
                    <div class="flex-1">
                        <h2 class="font-[Lexend] text-[30px]">ADMIN</h2>
                        <p class="text-center mx-auto font-[Manrope] font-normal w-[80%]">Access the administrative control panel</p>
                    </div>
                    <a href="{{ route('admin.login.form') }}"
                        class="inline-block p-4 rounded-full bg-[#7A1212] border border-white py-2 text-center w-full hover:bg-[#5a0f0f] transition-bg duration-75">
                        Login as Admin
                    </a>
                </div>
            </section>
            <div class="w-full flex justify-center items-end">
                <section class="flex justify-center items-stretch min-w-[300px] w-full max-w-[500px]">
                    <div class="flex gap-x-6 bg-[#525866CC] p-6 rounded-2xl text-center w-full h-full">
                        <div>
                            <svg width="60" height="60" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_9347_27247)">
                                    <path d="M70 34.965C70 15.6625 54.32 0 35 0C15.68 0 0 15.6625 0 34.965C0 45.5963 4.83 55.1775 12.39 61.6087C12.46 61.6787 12.53 61.6788 12.53 61.7488C13.16 62.2388 13.79 62.7287 14.49 63.2188C14.84 63.4287 15.12 63.7044 15.47 63.9844C21.2564 67.9011 28.0827 69.9962 35.07 70C42.0573 69.9962 48.8836 67.9011 54.67 63.9844C55.02 63.7744 55.3 63.4987 55.65 63.2844C56.28 62.7988 56.98 62.3088 57.61 61.8188C57.68 61.7487 57.75 61.7487 57.75 61.6787C65.17 55.1731 70 45.5963 70 34.965ZM35 65.5944C28.42 65.5944 22.4 63.4944 17.43 59.9987C17.5 59.4387 17.64 58.8831 17.78 58.3231C18.1985 56.8059 18.8102 55.3488 19.6 53.9875C20.37 52.6575 21.28 51.4675 22.4 50.4175C23.45 49.3675 24.71 48.3919 25.97 47.6219C27.3 46.8519 28.7 46.2919 30.24 45.8719C31.7925 45.4561 33.3928 45.2457 35 45.2463C39.772 45.2101 44.3692 47.0408 47.81 50.3475C49.42 51.9575 50.68 53.846 51.59 56.0131C52.08 57.2731 52.43 58.6017 52.64 59.9987C47.4739 63.6307 41.315 65.5844 35 65.5944ZM24.29 33.2194C23.6745 31.8068 23.3644 30.2801 23.38 28.7394C23.38 27.2037 23.66 25.6637 24.29 24.2638C24.92 22.8638 25.76 21.6081 26.81 20.5581C27.86 19.5081 29.12 18.6725 30.52 18.0425C31.92 17.4125 33.46 17.1325 35 17.1325C36.61 17.1325 38.08 17.4125 39.48 18.0425C40.88 18.6725 42.14 19.5125 43.19 20.5581C44.24 21.6081 45.08 22.8681 45.71 24.2638C46.34 25.6637 46.62 27.2037 46.62 28.7394C46.62 30.3494 46.34 31.8194 45.71 33.215C45.1055 34.5962 44.2512 35.8539 43.19 36.925C42.1186 37.9846 40.8609 38.8374 39.48 39.4406C36.5869 40.627 33.3431 40.627 30.45 39.4406C29.0691 38.8374 27.8114 37.9846 26.74 36.925C25.6779 35.8691 24.8441 34.6107 24.29 33.2194ZM56.77 56.4331C56.77 56.2931 56.7 56.2231 56.7 56.0831C56.0128 53.8926 54.998 51.8187 53.69 49.9319C52.381 48.0312 50.7736 46.3545 48.93 44.9662C47.5215 43.9063 45.9946 43.0136 44.38 42.3063C45.1111 41.8171 45.7912 41.2558 46.41 40.6306C47.4536 39.6003 48.3701 38.4488 49.14 37.2006C50.6956 34.6558 51.4967 31.7216 51.45 28.7394C51.4729 26.5321 51.0441 24.3435 50.19 22.3081C49.3478 20.3466 48.1357 18.5656 46.62 17.0625C45.1028 15.5796 43.3224 14.3926 41.37 13.5625C39.3316 12.7087 37.1399 12.2814 34.93 12.3069C32.7198 12.2828 30.5281 12.7116 28.49 13.5669C26.5162 14.3869 24.7299 15.6 23.24 17.1325C21.7494 18.6417 20.5613 20.4223 19.74 22.3781C18.8859 24.4135 18.4571 26.6021 18.48 28.8094C18.48 30.3494 18.69 31.8179 19.11 33.215C19.53 34.685 20.09 36.015 20.86 37.2706C21.56 38.5306 22.54 39.6506 23.59 40.7006C24.22 41.3306 24.92 41.8892 25.69 42.3763C24.0682 43.0983 22.5403 44.015 21.14 45.1063C19.32 46.5063 17.71 48.1819 16.38 50.0019C15.0599 51.8815 14.0442 53.9574 13.37 56.1531C13.3 56.2931 13.3 56.4331 13.3 56.5031C7.77 50.9075 4.34 43.3563 4.34 34.965C4.34 18.1125 18.13 4.33563 35 4.33563C51.87 4.33563 65.66 18.1125 65.66 34.965C65.6508 43.0148 62.4545 50.7335 56.77 56.4331Z" fill="white"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_9347_27247">
                                        <rect width="70" height="70" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>
                        <div class="flex flex-col flex-1">
                            <div class="flex-1 pb-4">
                                <p class="text-start mx-auto font-[Manrope] font-normal">For document submission without logging in as a Student organization.</p>
                            </div>
                            <div class="self-start">
                                <a href="{{ route('guest') }}"
                                    class="inline-block max-w-[200px] p-4 rounded-full bg-[#525866] border border-white py-2 text-center w-full hover:bg-[#404550] transition-bg duration-75">
                                    Continue as Guest
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
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
        <div id="reportModal" class="fixed px-[10px] inset-0 flex items-center z-50 backdrop-blur-sm w-full text-black hidden">
        <div class="bg-[#ffffffe8] p-6 shadow-lg mx-auto rounded-3xl max-w-[600px]">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-20 h-20 mx-auto pb-4">
                <path d="M7.757 2h8.486l5.757 5.757v8.486l-5.757 5.757H7.757L2 16.243V7.757L7.757 2z"
                    fill="transparent" stroke="black" stroke-width="2.5"/>
                <rect x="11" y="8" width="2" height="4" fill="black" />
                <rect x="11" y="14" width="2" height="2" fill="black" />
            </svg>
            <div class="mb-4 text-center mx-auto w-[90%] md:w-[75%]">
            <h1 class="text-3xl font-[Lexend] font-bold mb-2 text-[var(--secondary-color)]">Report a Problem</h1>
            <p class="text-black font-[Manrope] font-normal text-xs">
                Noticed something wrong or not working as expected? Tell us what issue you encountered so we can look into it and improve your experience.
            </p>
            </div>

            <form id="reportForm" class="mx-auto w-[90%] md:w-[75%] text-xs" method="POST" action="{{ route('report.problem.store') }}" enctype="multipart/form-data" onsubmit="submitReport(event)">
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
                    class="text-slate-500 w-full max-w-[300px] text-sm rounded-lg leading-6 file:bg-[var(--secondary-color)] file:text-white file:border-none file:px-4 file:py-1 file:mr-6 file:rounded-lg hover:file:brightness-75 border bg-[#0000000c] cursor-pointer border-gray-300 transition duration-200">
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
        isDirty = reportForm.email.value.trim().length > 0 || reportForm.description.value.trim().length > 0;
        validateInputs();
        });

        reportForm.description.addEventListener('input', () => {
        isDirty = reportForm.email.value.trim().length > 0 || reportForm.description.value.trim().length > 0;
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
