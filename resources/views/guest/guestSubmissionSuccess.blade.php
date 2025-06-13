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
        <div class="flex flex-col items-center justify-center h-full space-y-8">
            <div class="bg-white rounded-2xl shadow-md p-8 w-full max-w-[550px] space-y-4 text-center">                
                <p>Your document has been received and is now being processed. Please check your email for updates.</p>

                <img
                    class="mx-auto w-[333px]" 
                    src="http://127.0.0.1:8000/images/guest-doc-submission-success.svg"
                    alt="Guest Document Submission Successful" />

                <p>Where would you like to go next?</p>

                <div class="flex flex-col md:flex-row justify-between gap-4 w-full">
                    <a href="{{ route('landing') }}"
                        class="w-full md:w-auto text-center font-semibold border-2 hover:bg-gray-100 text-[#7A1212] px-6 py-2 rounded-[12px] cursor-pointer transition">
                        Back to Login Page
                    </a>

                    <a href="{{ route('guest.submissionForm') }}"
                        class="w-full md:w-auto text-center font-semibold px-6 py-2 bg-[#7A1212] text-white rounded-[12px] hover:bg-[#a31515] cursor-pointer transition">
                        Submit Another Document
                    </a>
                </div>
            </div>
        </div>
    </div>
    @include('components.footer')
</div>
@endsection