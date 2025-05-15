<div class="relative" style="position: relative;">
    <select
        class="appearance-none border border-gray-300 rounded-full px-4 py-2 bg-[#7A1212] text-white font-bold focus:outline-none focus:ring-2 focus:ring-[#4D0F0F] cursor-pointer hover:bg-white hover:text-[#4D0F0F] transition ease-in duration-200">
        <option value="" disabled selected hidden>Status</option>
        <option value="Approved" class="bg-white text-black">Approved</option>
        <option value="Rejected" class="bg-white text-black">Resubmit</option>
        <option value="Under Review" class="bg-white text-black">Under Review</option>
        <option value="Pending" class="bg-white text-black">Pending</option>
        <option value="Rejected" class="bg-white text-black">Rejected</option>
    </select>
    <img src="{{ asset('images/dropdownIcon.svg') }}" alt="Dropdown Icon"
        class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 pointer-events-none">
</div>
