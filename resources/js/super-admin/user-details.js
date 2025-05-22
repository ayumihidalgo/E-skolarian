// User Details Modal Functionality
document.addEventListener('DOMContentLoaded', function () {
    // User Details Modal Elements
    const userDetailsModal = document.getElementById('userDetailsModal');
    const userDetailsRows = document.querySelectorAll('.user-details-row');

    // Debug log to check if elements are found
    console.log('Modal element found:', userDetailsModal !== null);
    console.log('User rows found:', userDetailsRows.length);

    // Current user data for viewing/editing
    let currentUserId = null;

    // User Details Modal Event Listeners - Open modal when clicking on a user row
    if (userDetailsRows.length > 0 && userDetailsModal) {
        userDetailsRows.forEach(row => {
            row.addEventListener('click', function (e) {
                console.log('Row clicked');

                try {
                    // Get user data from the row attribute
                    const userData = JSON.parse(this.getAttribute('data-user'));
                    console.log('User data:', userData);

                    // Fill user details in the modal
                    const usernameEl = document.getElementById('userUsername');
                    const emailEl = document.getElementById('userEmail');
                    const roleEl = document.getElementById('userRole');

                    if (usernameEl) usernameEl.textContent = userData.username;
                    if (emailEl) emailEl.textContent = userData.email;
                    if (roleEl) roleEl.textContent = userData.role_name;

                    // Store user ID for edit/deactivate operations
                    currentUserId = userData.id;

                    // Store the current user ID in a global variable for other modules to access
                    window.currentUserId = currentUserId;

                    // Show the modal
                    userDetailsModal.classList.remove('hidden');
                } catch (error) {
                    console.error('Error showing user details:', error);
                }
            });

            // Make sure the cursor style is applied
            row.style.cursor = 'pointer';
        });
    }

    // Setup close button functionality
    const closeUserDetailsBtn = document.getElementById('closeUserDetailsBtn');

    if (closeUserDetailsBtn) {
        closeUserDetailsBtn.addEventListener('click', function() {
            userDetailsModal.classList.add('hidden');
        });
    }

    if (userDetailsBackdrop) {
        userDetailsBackdrop.addEventListener('click', function() {
            userDetailsModal.classList.add('hidden');
        });
    }

    // If setupModalClose is available, use it as a backup method
    if (window.setupModalClose) {
        try {
            window.setupModalClose(userDetailsModal, '#closeUserDetailsBtn');
        } catch (error) {
            console.warn('Could not setup modal close with window.setupModalClose:', error);
        }
    }
});
