@extends('base')
@section('content')
<div id="main-content" class="flex flex-col min-h-screen bg-[#F2F4F7] font-['Lexend']">
    <div class="w-full bg-[#4d0F0F] h-[10%] p-4 text-white flex items-center relative">
        <!-- Logo -->
        <img
            class="w-[160px] md:absolute md:left-4 md:w-[160px] mx-auto md:mx-0"
            src="http://127.0.0.1:8000/images/E-SKOLARIAN LOGO.svg"
            alt="E-skolarian Document Submission" />

        <!-- Header (hidden on small screens, centered on desktop) -->
        <h1 class="hidden md:block mx-auto text-[20px] font-['Lexend']">
            Document Submission (Guest)
        </h1>
    </div>

    <div class="flex-grow flex items-center justify-center">
        <form method="POST" action="{{ route('guest.verifyOtp') }}" class="flex flex-col items-center justify-center h-full space-y-8">
            @csrf
            @include('components.guestProgress', ['step' => 2])
            <!-- OTP Form -->
            <div class="bg-white rounded-2xl shadow-md p-8 w-full max-w-[600px] space-y-4">
                <p class="text-base">
                    Please check your <strong>PUP Webmail</strong> for the One-Time Password (OTP) we've sent you,
                    and enter it in the field below to proceed.
                </p>

                <!-- OTP Inputs -->
                <div id="otp-container" class="flex justify-center gap-2">
                    @for ($i = 0; $i < 6; $i++)
                    <input
                    name="otp[]"
                    type="text"
                    maxlength="1"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    required
                    class="otp-input 
                    w-10 h-12 text-lg
                    sm:w-12 sm:h-14 sm:text-xl
                    md:w-14 md:h-16 md:text-2xl
                    text-center border border-gray-300 rounded-md
                    transition-all duration-200" />
                    @endfor
                </div>

                @error('otp')
                    <p class="text-red-600 text-sm text-center">{{ $message }}</p>
                @enderror

                <div class="text-center">
                    <a id="resend-link" href="{{ route('guest.resendOtp') }}" class="text-sm text-[#7A1212] underline hover:text-red-700">
                        Resend Code (<span id="resend-timer">60</span>s)
                    </a>
                </div>

                <!-- Verify Button -->
                <button
                    type="submit"
                    id="verifyBtn"
                    class="w-full bg-[#7A1212] text-white py-2 px-4 rounded-md hover:bg-red-700 transition cursor-pointer">
                    Verify OTP
                </button>
            </div>
        </form>
    </div>
    @include('components.footer')
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const otpInputs = document.querySelectorAll(".otp-input");

        otpInputs.forEach((input, index) => {
            input.addEventListener("input", () => {
                if (input.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener("keydown", (e) => {
                if (e.key === "Backspace" && input.value === "" && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });

            input.addEventListener("paste", (e) => {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
                paste.split('').slice(0, otpInputs.length).forEach((char, i) => {
                    otpInputs[i].value = char;
                });
                if (paste.length >= otpInputs.length) {
                    otpInputs[otpInputs.length - 1].focus();
                } else {
                    otpInputs[paste.length]?.focus();
                }
            });
        });

        const resendLink = document.getElementById("resend-link");
        const resendTimer = document.getElementById("resend-timer");
        const RESEND_DELAY = 60; // in seconds
        const STORAGE_KEY = "resend_otp_timer_start";

        function startResendCountdown(startTime) {
            resendLink.style.pointerEvents = "none";
            resendLink.style.opacity = "0.5";

            const interval = setInterval(() => {
                const elapsed = Math.floor((Date.now() - startTime) / 1000);
                const remaining = RESEND_DELAY - elapsed;

                if (remaining <= 0) {
                    clearInterval(interval);
                    resendLink.textContent = "Resend Code";
                    resendLink.style.pointerEvents = "auto";
                    resendLink.style.opacity = "1";
                    localStorage.removeItem(STORAGE_KEY);
                } else {
                    resendTimer.textContent = remaining;
                }
            }, 1000);
        }

        // On resend link click
        resendLink.addEventListener("click", () => {
            localStorage.setItem(STORAGE_KEY, Date.now());
        });

        // Initialize timer on page load if needed
        const storedStart = localStorage.getItem(STORAGE_KEY);
        if (storedStart) {
            const startTime = parseInt(storedStart);
            const elapsed = Math.floor((Date.now() - startTime) / 1000);

            if (elapsed < RESEND_DELAY) {
                startResendCountdown(startTime);
            } else {
                localStorage.removeItem(STORAGE_KEY);
            }
        } else {
            // Timer not active, show full resend link
            resendLink.textContent = "Resend Code";
            resendLink.style.pointerEvents = "auto";
            resendLink.style.opacity = "1";
        }
    });
</script>
@endsection