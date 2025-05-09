<div>
    <nav class="w-full bg-[#4d0F0F] h-[10%] p-4 text-white flex justify-end items-center space-x-6">
        <x-general-components.notification />
        <div>
            @auth
                <a href="#" class="font-semibold">{{ Auth::user()->username }}</a>
            @else
                <a href="#" class="font-semibold">Guest</a>
            @endauth
        </div>
    </nav>
</div>