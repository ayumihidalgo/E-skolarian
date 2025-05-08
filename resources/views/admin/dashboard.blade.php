@extends('base')

@include('components.adminNavBarComponent')
@include('components.adminSidebarComponent')
@section('content')
    <div id="main-content" class="transition-all duration-300 ml-[20%]">
        <div class="flex-grow p-6 space-y-6">
            <!-- Stats Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Announcements -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-4">
                    <h2 class="text-lg font-semibold mb-2">📢 Announcements</h2>
                    <div class="text-gray-500 text-center py-8">No announcement at the moment</div>
                </div>

                <!-- Previous Announcements -->
                <div class="bg-white rounded-xl shadow-md p-4">
                    <h2 class="text-lg font-semibold mb-2">Previous Announcements</h2>
                    <div class="text-center text-gray-500 py-8">
                        <img src="{{ asset('images/Illustrations.svg') }}" alt="No previous post"
                            class="w-24 h-24 mx-auto mb-2 opacity-80">
                        <p>No previous post</p>
                    </div>
                </div>

                <!-- Recent Documents -->
                <div class="lg:col-span-2 space-y-2">
                    <h2 class="text-lg font-semibold">Recent Documents</h2>
                    <div class="bg-zinc-100 rounded-xl shadow-md p-4">
                        <div class="text-center text-gray-500 py-8">
                            <img src="{{ asset('images/recentdoc.png') }}" alt="No recent documents"
                                class="w-40 mx-auto mb-2 opacity-80">
                            <p>No recent documents at the moment</p>
                        </div>
                    </div>
                </div>

                <!-- Post New Announcements -->
                <div class="bg-white p-6 rounded-xl shadow">
                    <h2 class="text-lg font-semibold border-b pb-2 mb-4">Post New Announcements</h2>
                    <form {{-- action="{{ route('announcements.store') }}" --}} method="POST" class="space-y-4">
                        {{-- @csrf --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                            <input type="text" name="title"
                                class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                placeholder="Enter announcement title">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                            <textarea name="content" rows="4"
                                class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                placeholder="Enter announcement content"></textarea>
                        </div>
                        <div class="text-right">
                            <button type="submit"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                Post Announcement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
