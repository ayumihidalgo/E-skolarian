<!--student/components/viewSearch.blade.php-->
<!-- Search Bar Only (to be used within parent flex container) -->
<div class="relative w-full md:w-1/3">
    <form method="GET" action="{{ request()->url() }}" id="searchForm">
        <!-- Preserve all existing query parameters except 'page' -->
        @foreach(request()->except('search', 'page') as $key => $value)
            @if(is_array($value))
                @foreach($value as $arrayValue)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $arrayValue }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach

        <input type="text"
               name="search"
               value="{{ request('search') }}"
               placeholder="Search..."
               class="w-full border border-gray-300 rounded-full px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-[#7A1212]"
               onkeyup="debounceSearch(this.value)"
               id="searchInput">
    </form>
    <img src="{{ asset('images/viewMagnifier.svg') }}" alt="Search Icon"
        class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 pointer-events-none">
</div>

<script>
let searchTimeout;

function debounceSearch(searchTerm) {
    console.log('Search term:', searchTerm); // Debug log

    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        // Only search if term is 2+ characters or empty (to show all)
        if (searchTerm.length >= 2 || searchTerm.length === 0) {
            console.log('Submitting search form'); // Debug log
            document.getElementById('searchForm').submit();
        }
    }, 500);
}

// Prevent form submission on Enter key to avoid double submission
document.getElementById('searchForm').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const searchTerm = document.getElementById('searchInput').value;
        if (searchTerm.length >= 2 || searchTerm.length === 0) {
            this.submit();
        }
    }
});

// Clear search functionality
function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('searchForm').submit();
}
</script>
