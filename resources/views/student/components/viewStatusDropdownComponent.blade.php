<!-- student/components/viewStatusDropdownComponent.blade.php -->
<div class="relative" style="position: relative;">
    <form method="GET" action="{{ request()->url() }}" id="statusForm">
        <!-- Preserve existing filters -->
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        @if(request('document_type'))
            <input type="hidden" name="document_type" value="{{ request('document_type') }}">
        @endif

        <select name="status"
                onchange="this.form.submit()"
                class="appearance-none border border-gray-300 rounded-full px-4 py-2 bg-[#7A1212] text-white font-bold focus:outline-none focus:ring-2 focus:ring-[#4D0F0F] cursor-pointer hover:bg-white hover:text-[#4D0F0F] transition ease-in duration-200">
            <option value="" {{ !request('status') ? 'selected' : '' }}>All Statuses</option>
            <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }} class="bg-white text-black">Approved</option>
            <option value="Resubmit" {{ request('status') == 'Resubmit' ? 'selected' : '' }} class="bg-white text-black">Resubmit</option>
            <option value="Under Review" {{ request('status') == 'Under Review' ? 'selected' : '' }} class="bg-white text-black">Under Review</option>
            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }} class="bg-white text-black">Pending</option>
            <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }} class="bg-white text-black">Rejected</option>
        </select>
    </form>

    <img src="{{ asset('images/dropdownIcon.svg') }}" alt="Dropdown Icon"
        class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 pointer-events-none">
</div>
