<!-- student/components/viewdocumenttypedropdown.blade.php -->
<div class="relative">
    <form method="GET" action="{{ request()->url() }}" id="documentTypeForm">
        <!-- Preserve existing filters -->
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif

        <select name="document_type"
                onchange="this.form.submit()"
                class="appearance-none border border-gray-300 rounded-full px-4 py-2 bg-[#7A1212] text-white font-bold focus:outline-none focus:ring-2 focus:ring-[#7A1212] cursor-pointer hover:bg-white hover:text-[#7A1212] transition ease-in duration-200">
            <option value="" {{ !request('document_type') ? 'selected' : '' }}>All Document Types</option>
            <option value="Event Proposal" {{ request('document_type') == 'Event Proposal' ? 'selected' : '' }} class="bg-white text-black">Event Proposal</option>
            <option value="General Plan of Activities" {{ request('document_type') == 'General Plan of Activities' ? 'selected' : '' }} class="bg-white text-black">General Plan of Activities</option>
            <option value="Calendar of Activities" {{ request('document_type') == 'Calendar of Activities' ? 'selected' : '' }} class="bg-white text-black">Calendar of Activities</option>
            <option value="Accomplishment Report" {{ request('document_type') == 'Accomplishment Report' ? 'selected' : '' }} class="bg-white text-black">Accomplishment Report</option>
            <option value="Constitution and by-Laws" {{ request('document_type') == 'Constitution and by-Laws' ? 'selected' : '' }} class="bg-white text-black">Constitution and by-Laws</option>
            <option value="Request Letter" {{ request('document_type') == 'Request Letter' ? 'selected' : '' }} class="bg-white text-black">Request Letter</option>
            <option value="Off Campus" {{ request('document_type') == 'Off Campus' ? 'selected' : '' }} class="bg-white text-black">Off-Campus</option>
            <option value="Petition and Concern" {{ request('document_type') == 'Petition and Concern' ? 'selected' : '' }} class="bg-white text-black">Petition and Concern</option>
        </select>
    </form>

    <svg width="14" height="8" viewBox="0 0 14 8" class="absolute left-[calc(195px)] top-[calc(50%-2px)] transform -translate-y-1/2 w-4 h-4 pointer-events-none" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M1.94796 0.726074H12.2754C13.204 0.726074 13.6683 1.84675 13.0131 2.50012L7.84935 7.64917C7.44146 8.0559 6.78189 8.0559 6.37834 7.64917L1.21028 2.50012C0.555057 1.84675 1.01936 0.726074 1.94796 0.726074Z" fill="white"/>
    </svg>
</div>
