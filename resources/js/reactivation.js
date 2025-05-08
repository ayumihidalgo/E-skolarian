document.addEventListener('DOMContentLoaded', function() {
    const reactivateConfirmModal = document.getElementById('reactivateConfirmModal');
    const reactivateEmailConfirmModal = document.getElementById('reactivateEmailConfirmModal');
    const confirmReactivateEmail = document.getElementById('confirmReactivateEmail');
    const reactivateEmailError = document.getElementById('reactivateEmailError');
    const finalReactivateBtn = document.getElementById('finalReactivateBtn');
    const closeReactivateEmailConfirmBtn = document.getElementById('closeReactivateEmailConfirmBtn');
    let userEmailToReactivate = '';
    let currentUserId = null;
    const reactivateSuccessModal = document.getElementById('reactivateSuccessModal');
    const okaySuccessModalBtn = document.getElementById('okaySuccessModalBtn');
    const closeSuccessModalBtn = document.getElementById('closeSuccessModalBtn');
    const cancelReactivateEmailBtn = document.getElementById('cancelReactivateEmailBtn');
    const closeReactivateConfirmBtn = document.getElementById('closeReactivateConfirmBtn');

    // Get modal elements
    const deactivatedUserDetailsModal = document.getElementById('deactivatedUserDetailsModal');

    // Get user detail elements
    const deactivatedUserUsername = document.getElementById('deactivatedUserUsername');
    const deactivatedUserEmail = document.getElementById('deactivatedUserEmail');
    const deactivatedUserRole = document.getElementById('deactivatedUserRole');
    const deactivatedUserDate = document.getElementById('deactivatedUserDate');

    // Get all close buttons
    const closeDeactivatedUserDetailsBtn = document.getElementById('closeDeactivatedUserDetailsBtn');

    // Handle table row clicks
    document.querySelectorAll('tr[data-user]').forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't show details if clicking the reactivate button
            if (e.target.closest('.reactivate-btn')) return;

            const userData = JSON.parse(this.dataset.user);
            
            // Update modal content
            deactivatedUserUsername.textContent = userData.username;
            deactivatedUserEmail.textContent = userData.email;
            deactivatedUserRole.textContent = userData.role_name;
            deactivatedUserDate.textContent = userData.updated_at;

            // Show modal
            deactivatedUserDetailsModal.classList.remove('hidden');
        });
    });

    // Close details modal when clicking the close button
    if (closeDeactivatedUserDetailsBtn) {
        closeDeactivatedUserDetailsBtn.addEventListener('click', function() {
            deactivatedUserDetailsModal.classList.add('hidden');
        });
    }

    // Close details modal when clicking outside
    document.querySelector('.deactivated-user-details-backdrop')?.addEventListener('click', function() {
        deactivatedUserDetailsModal.classList.add('hidden');
    });

    // Handle reactivate button clicks
    document.querySelectorAll('.reactivate-btn').forEach(button => {
        button.addEventListener('click', function() {
            currentUserId = this.dataset.userId;
            userEmailToReactivate = this.dataset.userEmail; // Add data-user-email attribute to your reactivate button
            reactivateConfirmModal.classList.remove('hidden');
        });
    });

    // Update confirm button handler to show email confirmation modal
    if (confirmReactivateBtn) {
        confirmReactivateBtn.addEventListener('click', function() {
            reactivateConfirmModal.classList.add('hidden');
            reactivateEmailConfirmModal.classList.remove('hidden');
            confirmReactivateEmail.value = '';
            reactivateEmailError.classList.add('hidden');
            finalReactivateBtn.disabled = true;
        });
    }

    // Email validation
    if (confirmReactivateEmail) {
        confirmReactivateEmail.addEventListener('input', function() {
            const isMatch = this.value === userEmailToReactivate;
            
            if (this.value) {
                if (!isMatch) {
                    this.classList.add('border-red-500');
                    reactivateEmailError.classList.remove('hidden');
                    finalReactivateBtn.disabled = true;
                } else {
                    this.classList.remove('border-red-500');
                    reactivateEmailError.classList.add('hidden');
                    finalReactivateBtn.disabled = false;
                }
            } else {
                this.classList.remove('border-red-500');
                reactivateEmailError.classList.add('hidden');
                finalReactivateBtn.disabled = true;
            }
        });
    }

    // Add event listener for closing email confirmation modal
    if (closeReactivateEmailConfirmBtn) {
        closeReactivateEmailConfirmBtn.addEventListener('click', function() {
            reactivateEmailConfirmModal.classList.add('hidden');
            // Reset the email input and validation states
            confirmReactivateEmail.value = '';
            confirmReactivateEmail.classList.remove('border-red-500');
            reactivateEmailError.classList.add('hidden');
        });
    }

    // Add backdrop click handler for email confirmation modal
    document.querySelector('.reactivate-email-confirm-backdrop')?.addEventListener('click', function() {
        reactivateEmailConfirmModal.classList.add('hidden');
        // Reset the email input and validation states
        confirmReactivateEmail.value = '';
        confirmReactivateEmail.classList.remove('border-red-500');
        reactivateEmailError.classList.add('hidden');
    });

    // Close email confirmation modal

    function closeEmailConfirmationModal() {
        reactivateEmailConfirmModal.classList.add('hidden');
        confirmReactivateEmail.value = '';
        confirmReactivateEmail.classList.remove('border-red-500');
        reactivateEmailError.classList.add('hidden');
    }
    // Handle 'X' button click
    if (closeReactivateEmailConfirmBtn) {
        closeReactivateEmailConfirmBtn.addEventListener('click', closeEmailConfirmationModal);
    }

    // Handle Cancel button click
    if (cancelReactivateEmailBtn) {
        cancelReactivateEmailBtn.addEventListener('click', closeEmailConfirmationModal);
    }

    // Handle backdrop click
    document.querySelector('.reactivate-email-confirm-backdrop')?.addEventListener('click', closeEmailConfirmationModal);


    if (closeReactivateConfirmBtn) {
        closeReactivateConfirmBtn.addEventListener('click', function() {
            reactivateConfirmModal.classList.add('hidden');
        });
    }

    if (closeReactivateConfirmBtn2) {
        closeReactivateConfirmBtn2.addEventListener('click', function() {
            reactivateConfirmModal.classList.add('hidden');
        });
    }
    // Add backdrop click handler for confirm modal
    document.querySelector('.reactivate-confirm-backdrop')?.addEventListener('click', function() {
        reactivateConfirmModal.classList.add('hidden');
    });

    // Final reactivation handler
    if (finalReactivateBtn) {
        finalReactivateBtn.addEventListener('click', function() {
            if (!currentUserId || confirmReactivateEmail.value !== userEmailToReactivate) return;

            // Disable button and show loading state
            this.disabled = true;
            this.textContent = 'Processing...';

            // Send reactivation request
            fetch(`/super-admin/reactivate-user/${currentUserId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide modal and refresh page
                    reactivateEmailConfirmModal.classList.add('hidden');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2500);

                    // Show success modal
                    reactivateSuccessModal.classList.remove('hidden');
                
                    // Reset the email input
                    confirmReactivateEmail.value = '';

                } else {
                    alert(data.message || 'Failed to reactivate user.');
                }
            })
            .catch(error => {
                alert('An error occurred while reactivating the user.');
            })
            .finally(() => {
                // Reset button state
                finalReactivateBtn.disabled = false;
                finalReactivateBtn.textContent = 'Reactivate Account';
                currentUserId = null;
            });
        });
    }
            // Handle success modal close button
        if (closeSuccessModalBtn) {
            closeSuccessModalBtn.addEventListener('click', function() {
                reactivateSuccessModal.classList.add('hidden');
                setTimeout(() => {
                    window.location.reload();
                }, 2500);
            });
        }

        // Handle success modal okay button
        if (okaySuccessModalBtn) {
            okaySuccessModalBtn.addEventListener('click', function() {
                reactivateSuccessModal.classList.add('hidden');
                setTimeout(() => {
                    window.location.reload();
                }, 2500);
            });
        }

            // Close modal when clicking backdrop
            document.querySelector('.reactivate-email-confirm-backdrop')?.addEventListener('click', function() {
                reactivateEmailConfirmModal.classList.add('hidden');
    });
});