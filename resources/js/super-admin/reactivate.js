// Reactivate User Functionality
document.addEventListener('DOMContentLoaded', function () {
    // Reactivation Modal Elements
    const reactivateButtons = document.querySelectorAll('.reactivate-btn');
    const reactivateConfirmModal = document.getElementById('reactivateConfirmModal');
    const closeReactivateModalBtn = document.getElementById('closeReactivateConfirmBtn');
    const closeReactivateModalBtn2 = document.getElementById('closeReactivateConfirmBtn2');
    const confirmReactivateBtn = document.getElementById('confirmReactivateBtn');
    const reactivateConfirmBackdrop = document.querySelector('.reactivate-confirm-backdrop');
    
    // Email Confirmation Modal Elements
    const reactivateEmailConfirmModal = document.getElementById('reactivateEmailConfirmModal');
    const closeReactivateEmailBtn = document.getElementById('closeReactivateEmailConfirmBtn');
    const confirmReactivateEmailInput = document.getElementById('confirmReactivateEmail');
    const reactivateEmailError = document.getElementById('reactivateEmailError');
    const finalReactivateBtn = document.getElementById('finalReactivateBtn');
    const cancelReactivateEmailBtn = document.getElementById('cancelReactivateEmailBtn');
    let userEmailToReactivate = '';
    let userIdToReactivate = '';

    // Success Modal Elements
    const reactivateSuccessModal = document.getElementById('reactivateSuccessModal');
    const closeSuccessModalBtn = document.getElementById('closeSuccessModalBtn');
    const okaySuccessModalBtn = document.getElementById('okaySuccessModalBtn');

    // Deactivated User Details Modal
    const deactivatedUserDetailsModal = document.getElementById('deactivatedUserDetailsModal');
    const closeDeactivatedUserDetailsBtn = document.getElementById('closeDeactivatedUserDetailsBtn');
    const deactivatedUserDetailsBackdrop = document.querySelector('.deactivated-user-details-backdrop');

    // Set up modals functionality
    if (window.setupModalClose) {
        window.setupModalClose(reactivateConfirmModal, '#closeReactivateConfirmBtn', '.reactivate-confirm-backdrop');
        window.setupModalClose(reactivateEmailConfirmModal, '#closeReactivateEmailConfirmBtn', '.reactivate-email-confirm-backdrop');
        window.setupModalClose(reactivateSuccessModal, '#closeSuccessModalBtn');
        window.setupModalClose(deactivatedUserDetailsModal, '#closeDeactivatedUserDetailsBtn', '.deactivated-user-details-backdrop');
    }

    // Attach click event to deactivated user rows to show details
    const userRows = document.querySelectorAll('tbody tr');
    userRows.forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't open details if clicked on the reactivate button
            if (e.target.closest('.reactivate-btn') || e.target.closest('svg') || e.target.closest('path')) {
                return;
            }

            const userData = JSON.parse(this.dataset.user);
            
            // Populate user details modal
            document.getElementById('deactivatedUserUsername').textContent = userData.username;
            document.getElementById('deactivatedUserEmail').textContent = userData.email;
            document.getElementById('deactivatedUserRole').textContent = userData.role_name;
            document.getElementById('deactivatedUserDate').textContent = userData.updated_at;
            
            // Show the user details modal
            deactivatedUserDetailsModal.classList.remove('hidden');
        });
    });

    // Close deactivated user details modal
    if (closeDeactivatedUserDetailsBtn) {
        closeDeactivatedUserDetailsBtn.addEventListener('click', function() {
            deactivatedUserDetailsModal.classList.add('hidden');
        });
    }

    if (deactivatedUserDetailsBackdrop) {
        deactivatedUserDetailsBackdrop.addEventListener('click', function() {
            deactivatedUserDetailsModal.classList.add('hidden');
        });
    }

    // Reactivate Button Click Event
    reactivateButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent row click event from firing
            
            // Get user information from data attributes
            userIdToReactivate = this.dataset.userId;
            userEmailToReactivate = this.dataset.userEmail;
            
            // Show reactivate confirmation modal
            reactivateConfirmModal.classList.remove('hidden');
        });
    });

    // Close reactivate confirmation modal
    if (closeReactivateModalBtn) {
        closeReactivateModalBtn.addEventListener('click', function() {
            reactivateConfirmModal.classList.add('hidden');
        });
    }

    if (closeReactivateModalBtn2) {
        closeReactivateModalBtn2.addEventListener('click', function() {
            reactivateConfirmModal.classList.add('hidden');
        });
    }

    if (reactivateConfirmBackdrop) {
        reactivateConfirmBackdrop.addEventListener('click', function() {
            reactivateConfirmModal.classList.add('hidden');
        });
    }

    // Confirm Reactivate Button Click - Open Email Confirmation
    if (confirmReactivateBtn) {
        confirmReactivateBtn.addEventListener('click', function() {
            // Hide reactivate confirmation modal
            reactivateConfirmModal.classList.add('hidden');
            
            // Reset the email input and error message
            confirmReactivateEmailInput.value = '';
            confirmReactivateEmailInput.classList.remove('border-red-500');
            reactivateEmailError.classList.add('hidden');
            finalReactivateBtn.disabled = true;
            
            // Show email confirmation modal
            reactivateEmailConfirmModal.classList.remove('hidden');
        });
    }

    // Email input validation
    if (confirmReactivateEmailInput) {
        confirmReactivateEmailInput.addEventListener('input', function() {
            const isMatch = this.value === userEmailToReactivate;
            
            // Update input styling
            if (this.value) {
                if (!isMatch) {
                    this.classList.add('border-red-500', 'ring-red-500');
                    reactivateEmailError.classList.remove('hidden');
                    finalReactivateBtn.disabled = true;
                } else {
                    this.classList.remove('border-red-500', 'ring-red-500');
                    reactivateEmailError.classList.add('hidden');
                    finalReactivateBtn.disabled = false;
                }
            } else {
                this.classList.remove('border-red-500', 'ring-red-500');
                reactivateEmailError.classList.add('hidden');
                finalReactivateBtn.disabled = true;
            }
        });
    }

    // Cancel Email Confirmation
    if (cancelReactivateEmailBtn) {
        cancelReactivateEmailBtn.addEventListener('click', function() {
            reactivateEmailConfirmModal.classList.add('hidden');
        });
    }

    // Final Reactivation Button Click - Make API Call
    if (finalReactivateBtn) {
    finalReactivateBtn.addEventListener('click', function() {
        if (confirmReactivateEmailInput.value === userEmailToReactivate) {
            // Show loading state
            finalReactivateBtn.disabled = true;
            finalReactivateBtn.textContent = 'Processing...';

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/super-admin/reactivate-user', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userIdToReactivate,
                    email: userEmailToReactivate,
                    _token: csrfToken
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Hide the email confirmation modal
                    reactivateEmailConfirmModal.classList.add('hidden');
                    
                    // Show success modal
                    reactivateSuccessModal.classList.remove('hidden');
                } else {
                    throw new Error(data.message || 'Failed to reactivate account');
                }
            })
            .catch(error => {
                console.error('Reactivation error details:', error);
                reactivateEmailError.textContent = error.message || 'An error occurred while reactivating the user';
                reactivateEmailError.classList.remove('hidden');
                
                // Log additional details for debugging
                console.log('User ID:', userIdToReactivate);
                console.log('Email:', userEmailToReactivate);
            })
            .finally(() => {
                finalReactivateBtn.disabled = false;
                finalReactivateBtn.textContent = 'Reactivate';
            });
        }
    });
}

    // Close success modal
    if (closeSuccessModalBtn) {
        closeSuccessModalBtn.addEventListener('click', function() {
            reactivateSuccessModal.classList.add('hidden');
            window.location.reload();
        });
    }

    // "Okay" button on success modal
    if (okaySuccessModalBtn) {
        okaySuccessModalBtn.addEventListener('click', function() {
            reactivateSuccessModal.classList.add('hidden');
            window.location.reload();
        });
    }
});