@extends('base')

@section('content')
    @include('components.studentNavBarComponent')
    @include('components.studentSideBarComponent')
    <div id="main-content" class="transition-all duration-300 ml-[20%]">

        <div class="p-8">
            <!-- Profile Settings Heading -->
            <h2 class="text-2xl font-bold mb-6 font-['Lexend']">Profile Settings</h2>

            <!-- Profile Card -->
            <div class="flex items-center gap-8 mb-8">
                <div class="relative  w-36 h-36 rounded-full ">
                    @if ($user->profile_pic)
                        <!-- Show uploaded profile image -->
                        <img src="{{ asset('storage/' . $user->profile_pic) }}" alt="Profile"
                            class="w-24 h-24 rounded-full object-cover">
                    @else
                        <!-- Default profile with initials -->
                        <div
                            class="w-full h-full rounded-full bg-maroon-700 flex items-center justify-center text-white text-3xl font-bold">
                            <img src="{{ asset('images/dprofile.svg') }}" class="w-36 h-36" alt="camera icon">
                        </div>
                    @endif

                    <!-- Camera icon overlay -->
                    <label class="absolute bottom-[-5px] right-2 bg-yellow-500 p-[5px] rounded-full cursor-pointer">
                        <img src="{{ asset('images/camera.svg') }}" class="w-6 h-6" alt="camera icon">
                        <form action="{{ route('student.settings.update-profile-picture') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="profile_image" class="hidden" onchange="this.form.submit()">
                        </form>
                    </label>
                </div>
                <div>
                    <h3 class="text-2xl font-black tracking-wider font-['Lexend']">ELITE</h3>
                    <p class="uppercase text-lg tracking-wider font-semibold font-['Lexend']">Academic Organization</p>
                    <div id="" class="mt-2 text-sm relative flex items-center gap-20">
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/Smail.svg') }}" class="w-6 h-6" alt="email icon">
                            <div>
                                <p class="font-extrabold text-[11px]">Email</p>
                                <p class="font-extrabold text-[12px]">elite@pup.edu.ph</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/department.svg') }}" class="w-6 h-6" alt="department icon">
                            <div>
                                <p class="font-extrabold text-[11px]">Department</p>
                                <p class="font-extrabold text-[12px]">BSIT</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Info -->
            <div class="bg-white w-full [box-shadow:1px_2px_7px_rgba(0,0,0,0.3)] rounded-3xl ">
                <div class="border-b w-full px-4 py-3">
                    <h4 class="text-2xl font-bold mb-2 mt-1 font-['Lexend']">SECURITY INFO</h4>
                    <p class="text-sm text-gray-600">Manage your password settings here to reset your password and
                        enhance
                        your account security.</p>
                </div>
                <div class="border-t-[0] w-full flex justify-between items-center px-6 py-5 ">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('images/dpassword.svg') }}" class="w-6 h-6" alt="password icon">
                        <p class="font-['Lexend'] text-sm">Password</p>
                    </div>
                    <div class="flex flex-col items-center text-center">
                        <p class="font-['Lexend'] text-sm">Password</p>
                        <p class="text-gray-400 text-xs">Last Updated: 6 months ago</p>
                    </div>
                    <button
                        class="text-blue-600 font-bold bg-transparent border-none cursor-pointer text-sm">Change</button>
                </div>
            </div>
        </div>
    </div>
@endsection
