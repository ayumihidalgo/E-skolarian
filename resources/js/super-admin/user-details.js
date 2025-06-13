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
                    const userData = JSON.parse(this.getAttribute('data-user'));
                    
                    const usernameEl = document.getElementById('userUsername');
                    const emailEl = document.getElementById('userEmail');
                    const recoveryEmailEl = document.getElementById('userRecoveryEmail');
                    const roleEl = document.getElementById('userRole');
                    const acronymField = document.getElementById('acronymField');
                    const acronymEl = document.getElementById('userAcronym');

                    if (userData.role === 'admin') {
                        // For admin users: show role in Role field and role_name in Name field
                        if (roleEl) roleEl.textContent = ucfirst(userData.role);
                        if (usernameEl) usernameEl.textContent = userData.role_name;
                        if (acronymField) acronymField.classList.add('hidden');
                    } else if (userData.role === 'student') {
                        // For student users: show existing behavior
                        if (acronymField) acronymField.classList.remove('hidden');
                        if (acronymEl) acronymEl.textContent = userData.organization_acronym || 'N/A';
                        if (usernameEl) usernameEl.textContent = userData.username;
                        if (roleEl) roleEl.textContent = userData.role_name;
                    } else {
                        // For other roles: show existing behavior
                        if (acronymField) acronymField.classList.add('hidden');
                        if (usernameEl) usernameEl.textContent = userData.username;
                        if (roleEl) roleEl.textContent = userData.role_name;
                    }

                    if (emailEl) emailEl.textContent = userData.email;
                    
                    // Add recovery email population with conditional styling
                    if (recoveryEmailEl) {
                        if (userData.recovery_email) {
                            recoveryEmailEl.textContent = userData.recovery_email;
                            recoveryEmailEl.classList.remove('text-gray-400', 'italic');
                            recoveryEmailEl.classList.add('underline', 'decoration-[#3f434a]');
                        } else {
                            recoveryEmailEl.textContent = 'Not set';
                            recoveryEmailEl.classList.add('text-gray-400', 'italic');
                            recoveryEmailEl.classList.remove('underline', 'decoration-[#3f434a]');
                        }
                    }

                    // Store user ID for edit/deactivate operations
                    currentUserId = userData.id;
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

// Helper function to capitalize first letter
function ucfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}
