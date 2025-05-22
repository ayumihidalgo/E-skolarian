@extends('base')

@section('content')
@include('components.studentNavBarComponent')
@include('components.studentSidebarComponent')

<div id="main-content" class="transition-all duration-300 ml-[20%]">
    <div class="w-full min-h-screen bg-[#f2f4f7] px-6 py-8 flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-extrabold">Document History Preview</h2>
            <button type="button"
                onclick="window.history.back();"
                class="bg-[#7A1212] text-white px-4 py-2 rounded-full hover:bg-[#DAA520] w-[117px] h-[44px] flex items-center justify-center">
                Back
            </button>
        </div>


        {{-- Document Details --}}
        <div class="p-6 bg-[#4D0F0F] text-white rounded-[2rem] shadow-md space-y-6 w-full max-w-[1055px] mx-auto min-h-[450px]">
            {{-- General Information --}}
            <div class="space-y-3">
                <div class="flex flex-wrap justify-between items-center">
                    <!-- Document submission date - formatted for readability -->
                    <div>
                        <p class="font-semibold text-white/60">{{ \Carbon\Carbon::parse($document['date'])->format('F d, Y') }}</p>
                    </div>
                    <!-- Document control tag/identifier -->
                    <div>
                        <p class="font-semibold text-white/60">{{ $document['tag'] }}</p>
                    </div>
                </div>
                <p><strong class="text-white/60">Title:</strong> <strong>{{ $document['title'] }}</strong></p>
                <p><strong class="text-white/60">Type:</strong> <strong>{{ $document['type'] }}</strong></p>

                <p><strong class="text-white/60">Summary:</strong></p>
                <div class="p-4 bg-[#f2f4f7] text-black rounded-xl">
                    <p class="text-sm">{{ $document['content'] }}</p>
                </div>

                <p><strong class="text-white/60">Attachment:</strong></p>
                <a href="{{ asset('storage/'.$document['file_path']) }}" target="_blank" class="block w-[200px]">
                    <div class="p-2 bg-[#f2f4f7] text-black rounded-xl hover:bg-gray-200 cursor-pointer transition-colors duration-200">
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/Download.svg') }}" alt="Download" class="w-4 h-4">
                            <p class="text-sm">View Attachment</p>
                        </div>
                    </div>
                </a>

                <p>
                    <strong class="text-white/60">Status:</strong><br>
                    <span class="status-pill {{ $document['status'] === 'Approved' ? 'bg-[#10B981]' : 'bg-[#EF4444]' }} text-white px-4 py-1 rounded-full inline-block mt-1">
                        {{ $document['status'] }}
                    </span>
                </p>
            </div>
        </div>

    </div>
    @endsection