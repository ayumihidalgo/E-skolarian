// Deactivate User Functionality
document.addEventListener('DOMContentLoaded', function () {
    // Deactivation Modal Elements
    const deactivateBtn = document.getElementById('deactivateBtn');
    const deactivateConfirmModal = document.getElementById('deactivateConfirmModal');
    const closeDeactivateModalBtn = document.getElementById('closeDeactivateModalBtn');
    const cancelDeactivateBtn = document.getElementById('cancelDeactivateBtn');
    const confirmDeactivateBtn = document.getElementById('confirmDeactivateBtn');
    const deactivateConfirmBackdrop = document.querySelector('.deactivate-confirm-backdrop');
    const userDetailsModal = document.getElementById('userDetailsModal');
    const successModal = document.getElementById('successModal');
    const closeEmailModalBtn = document.getElementById('closeEmailConfirmBtn');

    // Email Confirmation Modal Elements
    const emailConfirmModal = document.getElementById('emailConfirmModal');
    const closeEmailConfirmBtn = document.getElementById('closeEmailConfirmBtn');
    const confirmEmailInput = document.getElementById('confirmEmail');
    const emailError = document.getElementById('emailError');
    const finalDeactivateBtn = document.getElementById('finalDeactivateBtn');
    let userEmailToDeactivate = '';

    // Set up modals functionality
    if (window.setupModalClose) {
        window.setupModalClose(deactivateConfirmModal, '#closeDeactivateModalBtn');
        window.setupModalClose(emailConfirmModal, '#closeEmailConfirmBtn');
    }

    // Cancel deactivation
    if (cancelDeactivateBtn) {
        cancelDeactivateBtn.addEventListener('click', function () {
            deactivateConfirmModal.classList.add('hidden');
        });
    }

    // Deactivate User Modal - Open modal when clicking deactivate button
    if (deactivateBtn) {
        deactivateBtn.addEventListener('click', function () {
            deactivateConfirmModal.classList.remove('hidden');
        });
    }

    // Email Confirmation Modal - Open after deactivation confirmation
    if (confirmDeactivateBtn) {
        confirmDeactivateBtn.addEventListener('click', function () {
            // Get the email from the user details modal
            userEmailToDeactivate = document.getElementById('userEmail').textContent;

            // Hide deactivate confirmation modal
            deactivateConfirmModal.classList.add('hidden');

            // Show email confirmation modal
            emailConfirmModal.classList.remove('hidden');

            // Reset the email input and error message
            confirmEmailInput.value = '';
            confirmEmailInput.classList.remove('border-red-500');
            emailError.classList.add('hidden');
            finalDeactivateBtn.disabled = true;
        });
    }

    // Email input validation with format/domain checks and input prevention for invalid characters
    if (confirmEmailInput) {
        // Prevent invalid characters as user types
        confirmEmailInput.addEventListener('keypress', function (e) {
            // Allow only valid email characters
            const char = String.fromCharCode(e.which);
            // Allow: a-z, A-Z, 0-9, @, ., _, -, +
            if (!/[a-zA-Z0-9@._\-+]/.test(char)) {
                e.preventDefault();
            }
        });

        // Prevent pasting invalid content
        confirmEmailInput.addEventListener('paste', function (e) {
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            // Allow only valid email characters in the pasted string
            if (!/^[a-zA-Z0-9@._\-+]+$/.test(paste)) {
                e.preventDefault();
            }
        });

        confirmEmailInput.addEventListener('input', function () {
            const email = this.value.trim();
            const allowedDomains = ['@gmail.com', '@yahoo.com', '@iskolarngbayan.pup.edu.ph', '@pup.edu.ph']; // Added @pup.edu.ph
            const maxEmailLength = 100;

            // Reset error state
            this.classList.remove('border-red-500', 'ring-red-500');
            emailError.classList.add('hidden');
            finalDeactivateBtn.disabled = true;

            // Remove any invalid characters that slipped through
            this.value = this.value.replace(/[^a-zA-Z0-9@._\-+]/g, '');

            // Validation checks
            if (email === '') {
                emailError.textContent = 'Email cannot be empty';
                emailError.classList.remove('hidden');
                this.classList.add('border-red-500', 'ring-red-500');
                return;
            }

            if (email.length > maxEmailLength) {
                emailError.textContent = `Email must be less than ${maxEmailLength} characters`;
                emailError.classList.remove('hidden');
                this.classList.add('border-red-500', 'ring-red-500');
                return;
            }

            // Email format
            if (!/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/.test(email)) {
                emailError.textContent = 'Please enter a valid email address';
                emailError.classList.remove('hidden');
                this.classList.add('border-red-500', 'ring-red-500');
                return;
            }

            // Allowed domains
            const hasValidDomain = allowedDomains.some(domain => email.toLowerCase().endsWith(domain));
            if (!hasValidDomain) {
                emailError.textContent = 'Please use a valid @gmail.com, @yahoo.com, @iskolarngbayan.pup.edu.ph, or @pup.edu.ph email address'; // Updated error message
                emailError.classList.remove('hidden');
                this.classList.add('border-red-500', 'ring-red-500');
                return;
            }

            // Check for number instead of letter in domain (.c0m instead of .com)
            if (/\.c0m$|\.c0m@/.test(email.toLowerCase())) {
                emailError.textContent = 'Invalid domain format';
                emailError.classList.remove('hidden');
                this.classList.add('border-red-500', 'ring-red-500');
                return;
            }

            // Check if email matches the user's email
            if (email !== userEmailToDeactivate) {
                emailError.textContent = '*Email address does not match.';
                emailError.classList.remove('hidden');
                this.classList.add('border-red-500', 'ring-red-500');
                finalDeactivateBtn.disabled = true;
                return;
            }

            // All checks passed
            emailError.classList.add('hidden');
            this.classList.remove('border-red-500', 'ring-red-500');
            finalDeactivateBtn.disabled = false;
        });
    }

    // Final deactivation handler
    if (finalDeactivateBtn) {
        finalDeactivateBtn.addEventListener('click', function () {
            if (confirmEmailInput.value === userEmailToDeactivate) {
                // Disable close button and show processing state
                if (closeEmailModalBtn) {
                    closeEmailModalBtn.disabled = true;
                    closeEmailModalBtn.style.opacity = '0.5';
                    closeEmailModalBtn.style.cursor = 'not-allowed';
                }

                // Show loading state on deactivate button
                finalDeactivateBtn.disabled = true;
                finalDeactivateBtn.textContent = 'Processing...';
                // Use the currentUserId variable that's already being tracked globally
                const currentUserId = window.currentUserId;

                // Create a form data object
                const formData = new FormData();
                formData.append('user_id', currentUserId);
                formData.append('email', userEmailToDeactivate);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                // Show loading state
                finalDeactivateBtn.disabled = true;
                finalDeactivateBtn.textContent = 'Processing...';

                // Make API call to deactivate the user
                fetch('/super-admin/deactivate-user', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    // First check if the response is OK
                    if (!response.ok) {
                        // If not OK, get the response text to help with debugging
                        return response.text().then(text => {
                            console.error('Server Error Response:', text);
                            throw new Error(`Server error: ${response.status}`);
                        });
                    }

                    // If OK, try to parse as JSON, but handle non-JSON responses gracefully
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('JSON Parse Error:', e);
                            console.log('Raw server response:', text);
                            throw new Error('Invalid JSON response from server');
                        }
                    });
                })
                .then(data => {
                    if (data.success) {
                        // Hide the email confirmation modal
                        emailConfirmModal.classList.add('hidden');
                        userDetailsModal.classList.add('hidden');

                        // Show success notification
                        document.getElementById('successTitle').textContent = 'Account Deactivated Successfully!';
                        document.getElementById('successMessage').textContent = 'Your changes have been saved. Account has been deactivated and the user can no longer use it.';

                        // Show success modal
                        successModal.classList.remove('hidden');

                        // Refresh the page after a delay to update the user list
                        const okayButton = document.querySelector('#successModal button');
                            if (okayButton) {
                                okayButton.addEventListener('click', function() {
                                    window.location.reload();
                                }, { once: true }); // Use once:true to prevent multiple handlers
                            }
                    } else {
                        // Show error
                        emailError.textContent = data.message || 'Failed to deactivate account';
                        emailError.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    emailError.textContent = 'An error occurred. Please try again.';
                    emailError.classList.remove('hidden');

                // Re-enable close button on error
                if (closeEmailModalBtn) {
                    closeEmailModalBtn.disabled = false;
                    closeEmailModalBtn.style.opacity = '1';
                    closeEmailModalBtn.style.cursor = 'pointer';
                }
                })
                .finally(() => {
                    // Reset button state
                    finalDeactivateBtn.disabled = false;
                    finalDeactivateBtn.textContent = 'Deactivate Account';

                    // Only re-enable close button if there was an error
                // Success case will close the modal anyway
                if (!emailConfirmModal.classList.contains('hidden')) {
                    if (closeEmailModalBtn) {
                        closeEmailModalBtn.disabled = false;
                        closeEmailModalBtn.style.opacity = '1';
                        closeEmailModalBtn.style.cursor = 'pointer';
                    }
                }
                });
            }
        });
    }
});
