{{-- filepath: c:\Users\Ayumi Hidalgo\E-skolarian\resources\views\admin\documentPreview.blade.php --}}
@extends('components.adminNavigation')

@section('adminContent')
    <div class="w-full min-h-screen bg-[#f2f4f7] px-6 py-8">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-extrabold">Document Archive Preview</h2>
            {{-- Back Button --}}
            <a href="{{ url('/') }}"
               class="bg-[#7A1212] text-white px-4 py-2 rounded-full hover:bg-[#DAA520]">
                Back
            </a>
        </div>

        {{-- Document Details --}}
        <div class="p-6 bg-[#4D0F0F] text-white rounded-[2rem] shadow-md space-y-6">
            {{-- General Information --}}
            <div class="space-y-2">
                <p class="text-white/60"> <strong>{{ \Carbon\Carbon::parse($document['date'])->format('F d, Y') }}</strong></p>
                <p><strong class="text-white/60">From:</strong> <strong>{{ $document['organization'] }}</strong></p>
                <p><strong class="text-white/60">Title:</strong> <strong>{{ $document['title'] }}</strong></p>
                <p><strong class="text-white/60">Type:</strong> <strong>{{ $document['type'] }}</strong></p>

                <p><strong class="text-white/60">Summary:</strong></p>
                <div class="p-4 bg-[#f2f4f7] text-black rounded-xl">
                    <p class="text-sm">{{ $document['content'] }}</p>
                </div>

                <p><strong class="text-white/60">Attachment:</strong></p>
                <div class="p-4 bg-[#f2f4f7] text-black rounded-xl">
                    <p class="text-sm">Attachment content goes here.</p>
                </div>

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
