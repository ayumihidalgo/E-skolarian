@extends('base')
@section('content')
<div id="main-content" class="flex flex-col min-h-screen bg-[#F2F4F7] font-['Lexend']">
    <div class="w-full bg-[#4d0F0F] h-[10%] p-4 text-white flex items-center relative">
        <!-- Logo -->
        <img
        class="w-[160px] md:absolute md:left-4 md:w-[160px] mx-auto md:mx-0"
        src="http://127.0.0.1:8000/images/E-SKOLARIAN LOGO.svg"
        alt="E-skolarian Document Submission"
        />
        
        <!-- Header (hidden on small screens, centered on desktop) -->
        <h1 class="hidden md:block mx-auto text-[20px] font-['Lexend']">
            Document Submission (Class Representative)
        </h1>
    </div>

    <div class="flex-grow flex items-center justify-center">
        <!-- Webmail Form -->
        <form method="POST" action="{{ route('guest.sendOtp') }}" class="flex flex-col items-center justify-center h-full space-y-8">
            @csrf
            @include('components.guestProgress', ['step' => 1])
            <div class="bg-white rounded-2xl shadow-md p-8 w-full max-w-[550px] space-y-6">
                <div class="space-y-2">
                    <h2 class="text-xl font-semibold">Welcome, Student!</h2>
                    <p>Please ensure you use an official <strong>PUP Webmail</strong> to proceed. Kindly enter your webmail below.</p>
                </div>

                <div class="space-y-4">
                    <input
                        type="email"
                        name="email"
                        placeholder="PUP Webmail"
                        required
                        value="{{ old('email') }}"
                        class="w-full px-2 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-[#4d0F0F]
                        @error('email') border-red-500 ring-red-200 @else border-gray-300 @enderror">

                    @error('email')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    <button
                        type="submit"
                        id="submitBtn"
                        class="w-full bg-[#7A1212] text-white py-2 px-4 rounded-md hover:bg-red-700 transition cursor-pointer">
                        Submit
                    </button>
                </div>
            </div>
        </form>
    </div>
    @include('components.footer')
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
        });
    });
</script>
@endsection