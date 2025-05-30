// User Details Modal Functionality
document.addEventListener('DOMContentLoaded', function () {
    // User Details Modal Elements
    const userDetailsModal = document.getElementById('userDetailsModal');
    const userDetailsRows = document.querySelectorAll('.user-details-row');
    const userDetailsBackdrop = document.querySelector('.user-details-backdrop');

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
                    
                    // Fill user details in the modal
                    const usernameEl = document.getElementById('userUsername');
                    const emailEl = document.getElementById('userEmail');
                    const roleEl = document.getElementById('userRole');
                    const acronymField = document.getElementById('acronymField');
                    const acronymEl = document.getElementById('userAcronym');

                    if (userData.role === 'student') {
                        // Show acronym field for student organizations
                        if (acronymField) acronymField.classList.remove('hidden');
                        
                        // Use organization_acronym from database
                        if (acronymEl) {
                            acronymEl.textContent = userData.organization_acronym || 'N/A';
                        }
                        
                        // Display organization name
                        if (usernameEl) {
                            usernameEl.textContent = userData.username;
                        }
                    } else {
                        // Hide acronym field for non-student roles
                        if (acronymField) acronymField.classList.add('hidden');
                        if (usernameEl) usernameEl.textContent = userData.username;
                    }

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
        userDetailsBackdrop.addEventListener('click', function(e) {
            // Only close if the backdrop itself was clicked
            if (e.target === userDetailsBackdrop) {
                userDetailsModal.classList.add('hidden');
            }
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
