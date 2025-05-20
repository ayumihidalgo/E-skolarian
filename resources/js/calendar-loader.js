document.addEventListener('DOMContentLoaded', function() {
    // Find all sidebar links to the calendar
    const calendarLinks = document.querySelectorAll('a[href*="calendar"]');
    console.log('Calendar loader initialized');
    
    calendarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            console.log('Found calendar link:', link);
            e.preventDefault();
            const calendarUrl = this.getAttribute('href');
            
            // Show loading indicator
            const mainContent = document.getElementById('main-content');
            mainContent.innerHTML = '<div class="flex items-center justify-center h-96"><div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#7A1212]"></div></div>';
            
            // Fetch calendar content via AJAX
            fetch(calendarUrl)
                .then(response => response.text())
                .then(html => {
                    // Extract main content from response
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('main-content');
                    
                    if (newContent) {
                        mainContent.innerHTML = newContent.innerHTML;
                        // Initialize calendar using your existing function
                        if (typeof initCalendar === 'function') {
                            initCalendar();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading calendar:', error);
                });
        });
    });
});