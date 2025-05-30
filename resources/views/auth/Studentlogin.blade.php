<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="icon" href="{{ asset('images/officialLogo.svg') }}" type="image/svg+xml">

    <title>E-skolarian</title>
    <style>
        input[type="password"]::-ms-reveal {
            display: none;
        }
    </style>

    @vite('resources/css/app.css')

    <script>
        // Only student logic, no role switching
        window.addEventListener('load', () => {
            setInterval(transitionBackground, 10000); // Change image every 10s
            // Unhide form container after everything is ready
            const form = document.getElementById('formContainer');
            if (form) form.classList.remove('opacity-0');
        });

        /* To carousel set of images */
        const images = [
            "{{ asset('images/PUP_Bg1.jpg') }}",
            "{{ asset('images/PUP_Bg2.jpg') }}",
            "{{ asset('images/PUP_Bg3.jpg') }}",
            "{{ asset('images/PUP_Bg4.jpg') }}",
            "{{ asset('images/PUP_Bg5.jpg') }}",
            "{{ asset('images/PUP_Bg6.jpg') }}",
        ];

        const preloadImages = images.map((src) => {
            const img = new Image();
            img.src = src;
            return img;
        });

        let currentIndex = Math.floor(Math.random() * images.length);
        let showingA = true;

        function setLayerBackground(element, url) {
            element.style.backgroundImage = `linear-gradient(var(--login-bg-color), var(--login-bg-color)), url(${url})`;
            element.style.backgroundRepeat = 'no-repeat';
            element.style.backgroundSize = 'cover';
            element.style.backgroundPosition = 'bottom';
        }

        function transitionBackground() {
            const bgA = document.getElementById('bgA');
            const bgB = document.getElementById('bgB');

            const nextIndex = (currentIndex + 1) % images.length;
            const nextImage = images[nextIndex];

            if (window.innerWidth >= 768) {
                if (showingA) {
                    setLayerBackground(bgB, nextImage);
                    bgB.classList.remove('opacity-0');
                    bgB.classList.add('opacity-100');
                    bgA.classList.remove('opacity-100');
                    bgA.classList.add('opacity-0');
                } else {
                    setLayerBackground(bgA, nextImage);
                    bgA.classList.remove('opacity-0');
                    bgA.classList.add('opacity-100');
                    bgB.classList.remove('opacity-100');
                    bgB.classList.add('opacity-0');
                }
                showingA = !showingA;
                currentIndex = nextIndex;
            }
        }

        window.addEventListener('load', () => {
            setInterval(transitionBackground, 10000); // Change image every 10s
        });

        /* Toggle Show/Hide Password */
        function togglePassword(event) {
            const input = document.getElementById('password');
            showPassIcon = document.getElementById('showPass');
            hidePassIcon = document.getElementById('hidePass');
            if (input.type === 'password') {
                input.type = 'text';
                showPassIcon.classList.add('hidden');
                hidePassIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                showPassIcon.classList.remove('hidden');
                hidePassIcon.classList.add('hidden');
            }
        }

        /* Fade Messages  */
        document.addEventListener('DOMContentLoaded', function () {
            const statusMessages = document.querySelectorAll('.status-message');
            statusMessages.forEach(function (message) {
                // Skip fading out if it's the lockout message
                if (message.id === 'lockout-message') return;
                setTimeout(function () {
                    message.classList.add('opacity-0', 'transition-opacity');
                    setTimeout(function () {
                        message.remove();
                    }, 500);
                }, 3000);
            });
        });
        document.addEventListener('DOMContentLoaded', () => {
        const reportBtn = document.getElementById('reportBtn');
        const reportModal = document.getElementById('reportModal');

        reportBtn.addEventListener('click', () => {
          reportModal.classList.remove('hidden');
        });
      });
    </script>
    @php
        $randomIndex = rand(1, 6);
        $randomImage = asset("images/PUP_Bg$randomIndex.jpg");
    @endphp
</head>

@include('loading');
<body id="box" class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-r from-[var(--login-color-left)] to-[var(--login-color-right)] md:bg-[var(--secondary-color)] font-['Manrope'] font-bold">
    <div id="bgA" class="absolute inset-0 transition-all duration-1000 ease-in-out opacity-100 max-md:hidden" style="background: linear-gradient(var(--login-bg-color), var(--login-bg-color)), url('{{ $randomImage }}'); background-size: cover; background-repeat: no-repeat; background-position: bottom;"></div>
    <div id="bgB" class="absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0 max-md:hidden"></div>
    <div id="formWrapper" class="w-full h-full max-md:p-[20px] max-md:max-w-md  md:absolute md:right-0 md:top-0 md:bottom-0">
        <div id="formContainer" class="opacity-0 flex flex-col items-center justify-center h-full px-6 bg-[#D9D9D9]/70 p-4 rounded-3xl md:w-[50%] md:max-w-[600px] md:rounded-tl-none md:rounded-bl-none md:rounded-tr-[100px] md:rounded-br-[100px] md:backdrop-blur-xs md:bg-white/70 md:transition-all md:duration-1000">
            <div class="h-35 flex items-center">
                <img class="mx-auto h-19 md:h-22" src="{{ asset('images/e-skolarianLogo.svg') }}" alt="E-skolarian Logo">
            </div>
            <!-- Role Switch Buttons (REMOVED) -->

            <div class="w-full max-w-[400px] mx-auto pt-14 md:pt-5">
                <form method="POST" action="{{ route('student.login') }}" class="space-y-4 md:space-y-2">
                    @csrf
                    <input type="hidden" name="role" id="role" value="student">
                    <!-- Email -->
                    <div class="pb-6">
                        <label id="emailLabel" class="w-full rounded-2xl px-3 py-2 md:p-4 ring bg-white flex focus-within:ring-3 focus-within:ring-[var(--secondary-color)]">
                            <input type="email" id="emailInput" name="email" placeholder="Email Address" required
                                class="w-0 flex-grow outline-none mr-3" maxlength="100">
                            <button type="button" class="focus:outline-none" tabindex="-1">
                                <img src="{{ asset('images/email.svg') }}" alt="Email Icon" class="w-5 md:w-6" />
                            </button>
                        </label>
                        <div id="emailLengthWarning" class="text-red-600 text-sm mt-0.5 pl-[10px] font-[Lexend] font-normal hidden">
                            <p>*Email must not exceed 50 characters.</p>
                        </div>
                    </div>
                    <!-- Password -->
                    <div>
                        <label id="passwordLabel" class="w-full rounded-2xl px-3 py-2 md:p-4 bg-white flex ring focus-within:ring-3 focus-within:ring-[var(--secondary-color)]">
                            <input id="password" type="password" name="password" placeholder="Password" required
                                class="w-0 flex-grow outline-none mr-3">
                            <button type="button" onclick="togglePassword(event)" class="cursor-pointer">
                                <img id="showPass" src="{{ asset('images/show_pass.svg') }}" alt="Show Password" class="w-5 md:w-6" />
                                <img id="hidePass" src="{{ asset('images/hide_pass.svg') }}" alt="Hide Password" class="w-5 md:w-6 hidden" />
                            </button>
                        </label>
                        <div id="passwordLengthWarning" class="text-red-600 text-sm mt-0.5 pl-[10px] font-[Lexend] font-normal hidden">
                            <p>*Password must not exceed 50 characters.</p>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center mb-2">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-[var(--secondary-color)] border-gray-300 rounded focus:ring-[var(--secondary-color)]">
                        <label for="remember" class="ml-2 block text-sm text-gray-900 font-normal">
                            Remember Me
                        </label>
                    </div>

                    <!-- Error Message -->
                    @if ($errors->any() && !$errors->has('lockout_time'))
                    <div class="status-message text-red-600 text-sm mt-1 pb-1.5 font-[Lexend] font-normal">
                        <p>{{ $errors->first() }} </p>
                    </div>
                    @endif

                    <!-- Lockout Message (ADDED) -->
                    <div id="lockout-message-container" class="text-red-600 text-sm mt-1 pb-1.5 font-[Lexend] font-normal" style="display:none;">
                        <p id="lockout-message">
                            Too many login attempts. Please try again in <span id="lockout-timer"></span>.
                        </p>
                    </div>

                    <!-- Submit -->
                    <div class="pt-4 flex justify-center">
                        <button type="submit" id="signInButton"
                            class="opacity-50 w-full rounded-2xl mx-auto bg-[var(--secondary-color)] cursor-pointer text-white py-2 md:py-4  hover:bg-[var(--primary-color)] transition font-semibold">
                            Sign In
                        </button>
                    </div>
                    <div class="pb-7 flex justify-center">
                        <a href="{{ route('student.password.request') }}" class="forgot-password-link font-normal text-[14px] active:text-[var(--secondary-color)] transition-all duration-75">Forgot Password?</a>                    </div>
                </form>
            </div>
        </div>
    </div>
     <!-- Report Problem Modal -->
<div id="reportModal" class="hidden fixed inset-0 bg-transparent flex items-center justify-center z-50 backdrop-blur-sm">
  <div class="bg-white p-6 rounded shadow-lg w-[90%] max-w-md">

    <div class="mb-4 text-center">
      <h1 class="text-2xl font-bold mb-2 text-[var(--secondary-color)]">Report a Problem</h1>
      <p class="text-gray-600 text-sm">
        Noticed something wrong or not working as expected? Tell us what issue you encountered so we can look into it and improve your experience.
      </p>
    </div>

    <form id="reportForm" method="POST" action="{{ route('report.problem.store') }}" enctype="multipart/form-data" onsubmit="submitReport(event)">
      @csrf
      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">PUP Webmail*</label>
        <input id="emailInput" type="email" name="email" placeholder="PUP Email Address" class="w-full border rounded-lg px-3 py-2" required maxlength="51" />
        <p id="webmailLengthWarning" class="text-red-600 text-sm mt-1 hidden">*Webmail must not exceed 50 characters.</p>
     </div>

    <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">Problem Description*</label>
        <textarea name="description" placeholder="Describe the problem here..." class="w-full border rounded-lg px-3 py-2" rows="4" required maxlength="251"></textarea>
        <p id="descLengthWarning" class="text-red-600 text-sm mt-1 hidden">*Description must be 250 characters or less.</p>
    </div>
      <div class="mb-4">
        <label class="block text-sm font-semibold mb-1">Attach a Screenshot</label>
        <input type="file" name="screenshot" accept="image/*" class="w-full file:cursor-pointer file:bg-[var(--secondary-color)] file:text-white file:px-4 file:py-2 file:rounded-lg file:hover:bg-[var(--primary-color)]" />
      </div>
      <div class="text-right space-x-2">
          <button type="submit" id="reportSubmitBtn" class="bg-[var(--secondary-color)] text-white px-4 py-2 rounded-lg hover:bg-[var(--primary-color)]">Submit</button>
          <button type="button" id="cancelReportBtn" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Cancel</button>
      </div>
    </form>
  </div>
</div>


<!-- Confirmation Modal -->
<div id="confirmCloseModal" class="hidden fixed inset-0 bg-transparent flex items-center justify-center z-60 backdrop-blur-sm">
  <div class="bg-white p-6 rounded shadow-lg w-[90%] max-w-sm text-center">
    <h2 class="text-xl font-semibold mb-4">Unsaved Changes</h2>
    <p class="mb-6">You have unsaved changes. Are you sure you want to close?</p>
    <div class="space-x-4">
      <button id="confirmCloseYes" class="bg-[var(--secondary-color)] text-white px-4 py-2 rounded hover:bg-[var(--primary-color)]">Yes, Close</button>
      <button id="confirmCloseNo" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">No, Keep Editing</button>
    </div>
  </div>
</div>

<!-- Report Button -->
<button id="reportBtn"
  class="fixed bottom-4 left-4 bg-transparent rounded-full w-9 h-9 shadow-none focus:outline-none z-50 flex items-center justify-center"
  title="Report a Problem">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-full h-full">
    <path d="M7.757 2h8.486l5.757 5.757v8.486l-5.757 5.757H7.757L2 16.243V7.757L7.757 2z"
          fill="transparent" stroke="black" stroke-width="2.5"/>
    <rect x="11" y="8" width="2" height="4" fill="black" />
    <rect x="11" y="14" width="2" height="2" fill="black" />
  </svg>
</button>


    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const hasFormErrors = {!! json_encode($errors->any()) !!};

        const emailInput = document.getElementById('emailInput');
        const passwordInput = document.getElementById('password');

        const emailLabel = document.getElementById('emailLabel');
        const emailWarning = document.getElementById('emailLengthWarning');

        const passwordLabel = document.getElementById('passwordLabel');
        const passwordWarning = document.getElementById('passwordLengthWarning');

        const signInButton = document.getElementById('signInButton');
        const form = emailInput.closest('form');

        let serverErrorEmail = hasFormErrors;
        let serverErrorPassword = hasFormErrors;

        function validateInputs() {
            const email = emailInput.value.trim();
            const password = passwordInput.value.trim();

            const isEmailTooLong = email.length > 50;
            const isPasswordTooLong = password.length > 50;

            const isEmailValid = email.length > 0 && !isEmailTooLong;
            const isPasswordValid = password.length > 0 && !isPasswordTooLong;

            if (!serverErrorEmail) {
                if (email.length > 0 && isEmailTooLong) {
                    emailLabel.classList.add('ring-3', '!ring-red-600');
                    emailWarning.classList.remove('hidden');
                } else {
                    emailLabel.classList.remove('ring-3', '!ring-red-600');
                    emailWarning.classList.add('hidden');
                }
            }

            if (!serverErrorPassword) {
                if (password.length > 0 && isPasswordTooLong) {
                    passwordLabel.classList.add('ring-3', '!ring-red-600');
                    passwordWarning.classList.remove('hidden');
                } else {
                    passwordLabel.classList.remove('ring-3', '!ring-red-600');
                    passwordWarning.classList.add('hidden');
                }
            }

            // Only enable Sign In button if both fields are filled and valid
            const shouldEnableButton = isEmailValid && isPasswordValid;
            signInButton.disabled = !shouldEnableButton;
            signInButton.classList.toggle('opacity-50', !shouldEnableButton);
            signInButton.classList.toggle('cursor-not-allowed', !shouldEnableButton);
        }

        // Prevent spaces in email
        emailInput.addEventListener('keydown', function (e) {
            if (e.key === ' ') e.preventDefault();
        });

        emailInput.addEventListener('paste', function (e) {
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            if (/\s/.test(pastedText)) {
                e.preventDefault();
                alert('Spaces are not allowed in the email address.');
            }
        });

        // Input event handlers
        emailInput.addEventListener('input', function () {
            if (/\s/.test(emailInput.value)) {
                emailInput.value = emailInput.value.replace(/\s/g, '');
            }

            if (serverErrorEmail) {
                emailLabel.classList.remove('ring-3', '!ring-red-600');
                serverErrorEmail = false;
            }

            validateInputs();
        });

        emailInput.addEventListener('focus', function () {
            if (serverErrorEmail) {
                emailLabel.classList.remove('ring-3', '!ring-red-600');
                serverErrorEmail = false;
            }
        });

        passwordInput.addEventListener('input', function () {
            if (serverErrorPassword) {
                passwordLabel.classList.remove('ring-3', '!ring-red-600');
                serverErrorPassword = false;
            }

            validateInputs();
        });

        form.addEventListener('submit', function (e) {
            if (emailInput.value.length > 50 || passwordInput.value.length > 50) {
                e.preventDefault();
                alert('Email or password exceeds the allowed length.');
            }
            // Disable button to prevent multiple submissions
            signInButton.disabled = true;
            signInButton.innerText = 'Signing in...'; // Optional: change button text

            // Show loader
            document.getElementById('loader').classList.toggle('hidden');
            document.getElementById('loader').classList.toggle('flex');
        });

        passwordInput.addEventListener('focus', function () {
            if (serverErrorPassword) {
                passwordLabel.classList.remove('ring-3', '!ring-red-600');
                serverErrorPassword = false;
            }
        });

        // Initial server-side red rings
        if (hasFormErrors) {
            emailLabel.classList.add('ring-3', '!ring-red-600');
            passwordLabel.classList.add('ring-3', '!ring-red-600');
        }

        // Initial validation on page load
        validateInputs();
    });
    </script>
    <script>
function formatTime(seconds) {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${m}:${s.toString().padStart(2, '0')}`;
}

function removeLockoutMsg(labelId) {
    const lockoutMessage = document.getElementById('lockout-message');
    const label = document.getElementById(labelId);
    if (lockoutMessage && label) {
        const parent = lockoutMessage.closest('div');
        if (parent) {
            parent.classList.add('opacity-0', 'transition-opacity');
            setTimeout(() => parent.remove(), 500);
        }
        label.classList.remove('ring-3', '!ring-red-600');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const emailInput = document.getElementById('emailInput');
    const passwordInput = document.getElementById('password');
    const emailLabel = document.getElementById('emailLabel');
    const passwordLabel = document.getElementById('passwordLabel');
    const lockoutMessage = document.getElementById('lockout-message');
    const lockoutMessageContainer = document.getElementById('lockout-message-container');
    const lockoutTimer = document.getElementById('lockout-timer');

    const formInputs = Array.from(document.querySelectorAll('input, button[type="submit"]'))
        .filter(input => !(input.name === '_token' || input.id === 'role'));

    const now = Math.floor(Date.now() / 1000);

    // Always check localStorage for lockoutEnd
    let storedEnd = parseInt(localStorage.getItem('lockoutEnd_student')) || 0;
    let backendLockoutTime = parseInt({{ $errors->first('lockout_time') ?? '0' }});
    let lockoutEnd = 0;
    let lockoutTime = 0;

    if (storedEnd > now) {
        // Use lockout from storage
        lockoutEnd = storedEnd;
        lockoutTime = lockoutEnd - now;
    } else if (backendLockoutTime > 0) {
        // Use backend lockout if present
        lockoutEnd = now + backendLockoutTime;
        lockoutTime = backendLockoutTime;
        localStorage.setItem('lockoutEnd_student', lockoutEnd);
    } else {
        // No lockout, remove any previous lockoutEnd and hide message
        localStorage.removeItem('lockoutEnd_student');
        if (lockoutMessageContainer) lockoutMessageContainer.style.display = 'none';
        return;
    }

    // Show lockout message and timer
    if (lockoutMessageContainer && lockoutTimer) {
        lockoutMessageContainer.style.display = '';
        lockoutTimer.innerText = formatTime(lockoutTime);
    }

    // Disable form inputs
    formInputs.forEach(input => input.disabled = true);

    const timerInterval = setInterval(() => {
        const current = Math.floor(Date.now() / 1000);
        const remaining = lockoutEnd - current;

        if (remaining <= 0) {
            clearInterval(timerInterval);
            if (lockoutMessage) lockoutMessage.innerText = "You can now try logging in again.";
            formInputs.forEach(input => input.disabled = false);
            localStorage.removeItem('lockoutEnd_student');

            if (emailInput) {
                emailInput.addEventListener('focus', () => {
                    removeLockoutMsg('emailLabel');
                }, { once: true });
            }

            if (passwordInput) {
                passwordInput.addEventListener('focus', () => {
                    removeLockoutMsg('passwordLabel');
                }, { once: true });
            }
        } else {
            if (lockoutTimer) lockoutTimer.innerText = formatTime(remaining);
        }
    }, 1000);
});

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
  const maxDescLength = 250;

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
      alert('Report submitted successfully!');
      closeReportModal();
      resetFormState();
    })
    .catch(error => {
      alert('There was an error submitting your report.');
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

</script>
</body>
</html>
