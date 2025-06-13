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
        window.setupModalClose(reactivateConfirmModal, '#closeReactivateConfirmBtn');
        window.setupModalClose(reactivateEmailConfirmModal, '#closeReactivateEmailConfirmBtn');
        window.setupModalClose(deactivatedUserDetailsModal, '#closeDeactivatedUserDetailsBtn');
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

    // Update the row click event handler
    const userDetailsRows = document.querySelectorAll('tbody tr');
    if (userDetailsRows.length > 0) {
        userDetailsRows.forEach(row => {
            row.addEventListener('click', function(e) {
                // Don't open details if clicked on the reactivate button
                if (e.target.closest('.reactivate-btn') || e.target.closest('svg') || e.target.closest('path')) {
                    return;
                }

                const userData = JSON.parse(this.getAttribute('data-user'));
                
                // Populate user details
                document.getElementById('deactivatedUserUsername').textContent = userData.username;
                document.getElementById('deactivatedUserEmail').textContent = userData.email;
                document.getElementById('deactivatedUserRole').textContent = userData.role_name;

                // Handle acronym field visibility
                const acronymField = document.getElementById('deactivatedAcronymField');
                const acronymEl = document.getElementById('deactivatedUserAcronym');
                
                if (!userData.role_name.toLowerCase().includes('admin')) {
                    if (acronymField) {
                        acronymField.classList.remove('hidden');
                    }
                    if (acronymEl) {
                        acronymEl.textContent = userData.organization_acronym || 'N/A';
                    }
                } else {
                    if (acronymField) {
                        acronymField.classList.add('hidden');
                    }
                }
                
                // Show the modal
                deactivatedUserDetailsModal.classList.remove('hidden');
            });
        });
    }

    // Close deactivated user details modal
    if (closeDeactivatedUserDetailsBtn) {
        closeDeactivatedUserDetailsBtn.addEventListener('click', function() {
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

    if (closeReactivateEmailBtn) {
    closeReactivateEmailBtn.addEventListener('click', function() {
        // Hide the email confirmation modal
        reactivateEmailConfirmModal.classList.add('hidden');
        
        // Reset form state
        confirmReactivateEmailInput.value = '';
        confirmReactivateEmailInput.classList.remove('border-red-500', 'ring-red-500');
        reactivateEmailError.classList.add('hidden');
        finalReactivateBtn.disabled = true;
    });
}

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

    // Email input validation with format/domain checks and input prevention for invalid characters
    if (confirmReactivateEmailInput) {
        // Prevent invalid characters as user types
        confirmReactivateEmailInput.addEventListener('keypress', function (e) {
            const char = String.fromCharCode(e.which);
            // Allow: a-z, A-Z, 0-9, @, ., _, -, +
            if (!/[a-zA-Z0-9@._\-+]/.test(char)) {
                e.preventDefault();
            }
        });

        // Prevent pasting invalid content
        confirmReactivateEmailInput.addEventListener('paste', function (e) {
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            if (!/^[a-zA-Z0-9@._\-+]+$/.test(paste)) {
                e.preventDefault();
            }
        });

        confirmReactivateEmailInput.addEventListener('input', function () {
            const email = this.value.trim();
            const allowedDomains = ['@gmail.com', '@yahoo.com', '@iskolarngbayan.pup.edu.ph'];
            const maxEmailLength = 100;

            // Reset error state
            this.classList.remove('border-red-500', 'ring-red-500');
            reactivateEmailError.classList.add('hidden');
            finalReactivateBtn.disabled = true;

            // Remove any invalid characters that slipped through
            this.value = this.value.replace(/[^a-zA-Z0-9@._\-+]/g, '');

            // Validation checks
            if (email === '') {
                reactivateEmailError.textContent = 'Email cannot be empty';
                reactivateEmailError.classList.remove('hidden');
                this.classList.add('border-red-500', 'ring-red-500');
                return;
            }

            if (email.length > maxEmailLength) {
                reactivateEmailError.textContent = `Email must be less than ${maxEmailLength} characters`;
                reactivateEmailError.classList.remove('hidden');
                this.classList.add('border-red-500', 'ring-red-500');
                return;
            }

            // Email format
            if (!/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/.test(email)) {
                reactivateEmailError.textContent = 'Please enter a valid email address';
                reactivateEmailError.classList.remove('hidden');
                this.classList.add('border-red-500', 'ring-red-500');
                return;
            }

            // Allowed domains
            const hasValidDomain = allowedDomains.some(domain => email.toLowerCase().endsWith(domain));
            if (!hasValidDomain) {
                reactivateEmailError.textContent = 'Please use a valid @gmail.com, @yahoo.com, or @iskolarngbayan.pup.edu.ph email address';
                reactivateEmailError.classList.remove('hidden');
                this.classList.add('border-red-500', 'ring-red-500');
                return;
            }

            // Check for number instead of letter in domain (.c0m instead of .com)
            if (/\.c0m$|\.c0m@/.test(email.toLowerCase())) {
                reactivateEmailError.textContent = 'Invalid domain format';
                reactivateEmailError.classList.remove('hidden');
                this.classList.add('border-red-500', 'ring-red-500');
                return;
            }

            // Check if email matches the user's email
            if (email !== userEmailToReactivate) {
                reactivateEmailError.textContent = '*Email address does not match.';
                reactivateEmailError.classList.remove('hidden');
                this.classList.add('border-red-500', 'ring-red-500');
                finalReactivateBtn.disabled = true;
                return;
            }

            // All checks passed
            reactivateEmailError.classList.add('hidden');
            this.classList.remove('border-red-500', 'ring-red-500');
            finalReactivateBtn.disabled = false;
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

            // Disable close and cancel buttons
            if (closeReactivateEmailBtn) {
                closeReactivateEmailBtn.disabled = true;
                closeReactivateEmailBtn.style.opacity = '0.5';
                closeReactivateEmailBtn.style.cursor = 'not-allowed';
            }
            if (cancelReactivateEmailBtn) {
                cancelReactivateEmailBtn.disabled = true;
                cancelReactivateEmailBtn.style.opacity = '0.5';
                cancelReactivateEmailBtn.style.cursor = 'not-allowed';
            }

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
                
                // Re-enable buttons on error
                if (closeReactivateEmailBtn) {
                    closeReactivateEmailBtn.disabled = false;
                    closeReactivateEmailBtn.style.opacity = '1';
                    closeReactivateEmailBtn.style.cursor = 'pointer';
                }
                if (cancelReactivateEmailBtn) {
                    cancelReactivateEmailBtn.disabled = false;
                    cancelReactivateEmailBtn.style.opacity = '1';
                    cancelReactivateEmailBtn.style.cursor = 'pointer';
                }

                // Log additional details for debugging
                console.log('User ID:', userIdToReactivate);
                console.log('Email:', userEmailToReactivate);
            })
            .finally(() => {
                // Only re-enable buttons if the modal is still visible
                if (!data.success && !reactivateEmailConfirmModal.classList.contains('hidden')) {
                    if (closeReactivateEmailBtn) {
                        closeReactivateEmailBtn.disabled = false;
                        closeReactivateEmailBtn.style.opacity = '1';
                        closeReactivateEmailBtn.style.cursor = 'pointer';
                    }
                    if (cancelReactivateEmailBtn) {
                        cancelReactivateEmailBtn.disabled = false;
                        cancelReactivateEmailBtn.style.opacity = '1';
                        cancelReactivateEmailBtn.style.cursor = 'pointer';
                    }
                }
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