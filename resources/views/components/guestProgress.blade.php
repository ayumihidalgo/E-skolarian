@php
    $step = $step ?? 1;
@endphp

<div class="flex justify-center mt-6 mb-8 px-4">
    <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4 text-[10px] sm:text-xs md:text-sm font-['Lexend']">

        <!-- Step 1 -->
        <div class="flex flex-col items-center space-y-0.5">
            <div class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 flex items-center justify-center rounded-full
                {{ $step == 1 ? 'bg-[#4D0F0F] text-white' : 'bg-[#14B8A6] text-white' }}">
                {{ $step > 1 ? '✓' : '1' }}
            </div>
            <span>Step</span>
        </div>

        <!-- Line to Step 2 -->
        <div class="w-16 sm:w-20 md:w-24 h-0.5 
            {{ $step == 1 ? 'bg-[#4D0F0F]' : ($step > 1 ? 'bg-[#14B8A6]' : 'bg-gray-300') }} self-center"></div>

        <!-- Step 2 -->
        <div class="flex flex-col items-center space-y-0.5">
            <div class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 flex items-center justify-center rounded-full
                {{ $step == 2 ? 'bg-[#4D0F0F] text-white' : ($step > 2 ? 'bg-[#14B8A6] text-white' : 'bg-gray-300 text-gray-700') }}">
                {{ $step > 2 ? '✓' : '2' }}
            </div>
            <span>Step</span>
        </div>

        <!-- Line to Step 3 -->
        <div class="w-16 sm:w-20 md:w-24 h-0.5 
            {{ $step == 2 ? 'bg-[#4D0F0F]' : ($step > 2 ? 'bg-[#14B8A6]' : 'bg-gray-300') }} self-center"></div>

        <!-- Step 3 -->
        <div class="flex flex-col items-center space-y-0.5">
            <div class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7 flex items-center justify-center rounded-full
                {{ $step == 3 ? 'bg-[#4D0F0F] text-white' : 'bg-gray-300 text-gray-700' }}">
                3
            </div>
            <span>Step</span>
        </div>

    </div>
</div>