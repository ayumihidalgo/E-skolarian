<!--STUDENT NAVIGATION-->
@extends('base')
<!-- Main Content -->
<div class="flex-grow">
    <nav class="w-full bg-[#4d0F0F] h-[10%] p-4 text-white flex justify-end items-center space-x-6">
        <a href="#" class="hover:text-yellow-400 transition duration-200">
            <img src="{{ asset('images/mail.svg') }}" class="h-6 w-6" alt="Mail Icon">
        </a>
        <div>
            <img src="#" alt="Profile" class="h-10 w-10 rounded-full border-2 border-white">
        </div>
        <div>
            <a href="#" class="font-semibold">
                {{ Auth::user()->username }}
            </a>
</div>


<!-- Toggle Sidebar Script -->
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const texts = sidebar.querySelectorAll('.sidebar-text');
        const toggleBtn = document.getElementById('toggleBtn');
        const toggleIcon = document.getElementById('toggleIcon');

        sidebar.classList.toggle('w-1/4');
        sidebar.classList.toggle('w-20');

        // Move toggle button position
        if (sidebar.classList.contains('w-20')) {
            toggleBtn.classList.add('left-[4rem]');
            toggleBtn.classList.remove('left-[24%]');
        } else {
            toggleBtn.classList.remove('left-[4rem]');
            toggleBtn.classList.add('left-[24%]');
        }

        // Toggle hidden text
        texts.forEach(text => text.classList.toggle('hidden'));

        // Rotate toggle icon
        toggleIcon.classList.toggle('rotate-180');
    }
</script>
