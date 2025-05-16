@extends('base')

@section('content')
    @include('components.studentNavBarComponent')
    @include('components.studentSideBarComponent')
    <div id="main-content" class="transition-all duration-300 ml-[20%]">
        <div class="flex-grow p-6 space-y-6">
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @foreach ([['Pending Documents', 'pendingicon.svg'], ['Under Review', 'reviewicon.svg'], ['Approved Documents', 'approvedicon.svg'], ['Total Documents', 'totaldocicon.svg']] as [$title, $icon])
                    <div class="bg-white p-4 rounded-xl shadow-md flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">{{ $title }}</p>
                            <div class="text-2xl font-bold">0</div>
                        </div>
                        <img src="{{ asset("images/$icon") }}" class="w-10 h-10" alt="{{ $title }}">
                    </div>
                @endforeach
            </div>
            <!-- Announcement and Documents Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Announcements -->
                <div class="md:col-span-2 bg-white rounded-xl shadow-md p-4">
                    <h2 class="text-lg font-semibold mb-2">📢 Announcements</h2>
                    @if ($latestAnnouncement)
                        <div class="space-y-2">
                            <h3 class="text-xl font-semibold">{{ $latestAnnouncement->title }}</h3>
                            <p class="text-sm text-gray-500">
                                Posted by {{ $latestAnnouncement->user->username }} on 
                                {{ $latestAnnouncement->created_at->format('F j, Y') }}
                            </p>
                            <p class="text-gray-700">{{ $latestAnnouncement->content }}</p>
                         </div>
                    @else
                        <div class="text-gray-500 text-center py-8">No announcement at the moment</div>
                    @endif
                </div>
                <!-- Previous Announcements -->
                <div class="bg-white rounded-xl shadow-md p-4 md:row-span-2">
                    <h2 class="text-lg font-semibold mb-2">Previous Announcements</h2>
                    <div class="text-center text-gray-500 py-8">
                        <img src="{{ asset('images/Illustrations.svg') }}" alt="No previous post"
                            class="w-24 h-24 mx-auto mb-2 opacity-80">
                        <p>No previous post</p>
                    </div>
                </div>

                <!-- Recent Documents -->
                <div class="md:col-span-2 space-y-2">
                    <h2 class="text-lg font-semibold">Recent Documents</h2>
                    <div class="bg-zinc-100 rounded-xl shadow-md p-4">
                        <div class="text-center text-gray-500 py-8">
                            <img src="{{ asset('images/recentdoc.png') }}" alt="No recent documents"
                                class="w-40 mx-auto mb-2 opacity-80">
                            <p>No recent documents at the moment</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
