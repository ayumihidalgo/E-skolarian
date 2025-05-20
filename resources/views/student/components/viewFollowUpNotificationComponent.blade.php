<div class="flex items-center justify-center space-x-2 py-4">
    {{-- First Page Link --}}
    <a href="{{ $paginator->url(1) }}"
       class="text-black text-sm hover:underline {{ $paginator->onFirstPage() ? 'pointer-events-none opacity-50' : '' }}">
        First
    </a>

    {{-- Page Numbers --}}
    @foreach ($elements as $element)
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span
                        class="px-3 py-1 rounded-md bg-[#410101] text-white text-sm font-medium">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}"
                       class="px-3 py-1 rounded-md bg-gray-200 text-black text-sm font-medium hover:bg-gray-300">
                        {{ $page }}
                    </a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Last Page Link --}}
    <a href="{{ $paginator->url($paginator->lastPage()) }}"
       class="text-black text-sm hover:underline {{ $paginator->currentPage() == $paginator->lastPage() ? 'pointer-events-none opacity-50' : '' }}">
        Last
    </a>
</div>
