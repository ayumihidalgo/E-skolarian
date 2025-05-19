// User Adding Functionality with Enhanced Validation
document.addEventListener('DOMContentLoaded', function () {
    // Add User Modal Elements
    const addUserBtn = document.getElementById('addUserBtn');
    const addUserModal = document.getElementById('addUserModal');
    const closeAddUserBtn = document.getElementById('closeAddUserBtn');
    const closeAddUserModalBtn = document.getElementById('closeAddUserModalBtn');
    const successModal = document.getElementById('successModal');
    const submitButton = document.getElementById('addUserSubmitBtn');
    const closeConfirmModal = document.getElementById('closeConfirmModal');
    const cancelCloseBtn = document.getElementById('cancelCloseBtn');
    const confirmCloseBtn = document.getElementById('confirmCloseBtn');

    // Processing flag to track form submission state
    let isProcessing = false;

    // Form input elements
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const roleSelect = document.getElementById('role_name');
    const actualRoleInput = document.getElementById('actual_role');
    const addUserForm = document.getElementById('addUserForm');

    // Validation feedback elements
    const usernameError = document.createElement('p');
    usernameError.className = 'text-red-600 text-xs mt-1 hidden';
    const emailError = document.createElement('p');
    emailError.className = 'text-red-600 text-xs mt-1 hidden';
    const roleError = document.createElement('p');
    roleError.className = 'text-red-600 text-xs mt-1 hidden';

    // Insert error elements after inputs
    if (usernameInput) {
        usernameInput.parentNode.insertBefore(usernameError, usernameInput.nextSibling);
    }
    if (emailInput) {
        emailInput.parentNode.insertBefore(emailError, emailInput.nextSibling);
    }
    if (roleSelect) {
        roleSelect.parentNode.insertBefore(roleError, roleSelect.nextSibling);
    }

    // Function to check if form has unsaved changes
    function hasUnsavedChanges() {
        const username = usernameInput?.value.trim() || '';
        const email = emailInput?.value.trim() || '';
        const role = roleSelect?.value || '';
        
        return username !== '' || email !== '' || role !== '';
    }

    // Function to handle close attempt
    function handleCloseAttempt() {
        if (hasUnsavedChanges()) {
            closeConfirmModal.classList.remove('hidden');
        } else {
            addUserModal.classList.add('hidden');
        }
    }

    // Set up modal functionality
    if (window.setupModalClose) {
        window.setupModalClose(addUserModal, '#closeAddUserBtn');
    }

    // Open Add User Modal
    if (addUserBtn && addUserModal) {
        addUserBtn.addEventListener('click', async function () {
            // Fetch existing roles before showing the modal
            const existingRoles = await fetchExistingRoles();
            updateRoleOptions(existingRoles);
            addUserModal.classList.remove('hidden');
        });
    }

    // Close modal handlers
    if (closeAddUserModalBtn) {
        closeAddUserModalBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (!isProcessing) {
                handleCloseAttempt();
            }
        });
    }

    // Add confirmation modal button handlers
    if (cancelCloseBtn) {
        cancelCloseBtn.addEventListener('click', function() {
            closeConfirmModal.classList.add('hidden');
        });
    }

    if (confirmCloseBtn) {
        confirmCloseBtn.addEventListener('click', function() {
            closeConfirmModal.classList.add('hidden');
            addUserModal.classList.add('hidden');
            
            // Reset form
            if (addUserForm) {
                addUserForm.reset();
                resetValidationState();
            }
        });
    }

    // Add escape key handler for both modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !isProcessing) {
            if (!closeConfirmModal.classList.contains('hidden')) {
                closeConfirmModal.classList.add('hidden');
            } else if (!addUserModal.classList.contains('hidden')) {
                handleCloseAttempt();
            }
        }
    });

    // Add click outside modal handler
    closeConfirmModal.addEventListener('click', function(e) {
        if (e.target === closeConfirmModal) {
            closeConfirmModal.classList.add('hidden');
        }
    });

    // Helper functions for processing state and validation reset
    function resetProcessingState() {
        isProcessing = false;
        
        // Re-enable close button
        if (closeAddUserModalBtn) {
            closeAddUserModalBtn.disabled = false;
            closeAddUserModalBtn.style.opacity = '1';
            closeAddUserModalBtn.style.cursor = 'pointer';
        }

        // Re-enable backdrop click
        const modalBackdrop = document.querySelector('.add-user-backdrop');
        if (modalBackdrop) {
            modalBackdrop.style.pointerEvents = 'auto';
        }

        // Reset submit button
        const submitBtn = addUserForm.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Add User';
        }
    }

    function resetValidationState() {
        usernameError.classList.add('hidden');
        emailError.classList.add('hidden');
        roleError.classList.add('hidden');
        usernameInput.classList.remove('border-red-500');
        emailInput.classList.remove('border-red-500');
        roleSelect.classList.remove('border-red-500');
    }

    // Email existence check function
    async function checkEmailExists(email) {
        const response = await fetch('/check-email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ email: email.toLowerCase() })
        });
        const data = await response.json();
        return data.exists;
    }

    async function checkUsernameExists(username) {
    const response = await fetch('/check-username', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ username: username.toLowerCase() })
    });
    const data = await response.json();
    return data.exists;
}

    // Function to fetch existing administrative roles
    async function fetchExistingRoles() {
        try {
            const response = await fetch('/check-roles', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();
            return data.existingRoles;
        } catch (error) {
            console.error('Error fetching existing roles:', error);
            return [];
        }
    }

    // Function to update role options based on existing roles
    function updateRoleOptions(existingRoles) {
        if (!roleSelect) return;

        const restrictedRoles = [
            'Student Services',
            'Academic Services',
            'Administrative Services',
            'Campus Director'
        ];

        // Hide restricted roles that already exist
        Array.from(roleSelect.options).forEach(option => {
            const roleName = option.value;
            if (restrictedRoles.includes(roleName) && existingRoles.includes(roleName)) {
                option.disabled = true;
                option.style.display = 'none';
            }
        });
    }

    // Form validation functions
    async function validateForm() {
        const isUsernameValid = await validateUsername();
        const isEmailValid = await validateEmail();
        const isRoleValid = validateRole();

        // Enable/disable submit button based on validation
        if (submitButton) {
            submitButton.disabled = !(isUsernameValid && isEmailValid && isRoleValid);
            submitButton.classList.toggle('opacity-50', !isUsernameValid || !isEmailValid || !isRoleValid);
            submitButton.classList.toggle('cursor-not-allowed', !isUsernameValid || !isEmailValid || !isRoleValid);
        }

        return isUsernameValid && isEmailValid && isRoleValid;
    }

    // Username validation and space removal
    if (usernameInput) {
    let usernameCheckTimeout;
    usernameInput.addEventListener('input', function(e) {
        // Remove extra spaces and non-letter characters
        this.value = this.value
            .replace(/^\s+/, '')
            .replace(/[^a-zA-Z\s]/g, '')
            .replace(/\s+/g, ' ');

        // Clear previous timeout
        clearTimeout(usernameCheckTimeout);

        // Set new timeout to avoid too many requests
        usernameCheckTimeout = setTimeout(() => {
            validateUsername().then(() => validateForm());
        }, 300);
    });

    usernameInput.addEventListener('blur', function() {
        this.value = this.value.trim();
        validateUsername().then(() => validateForm());
    });
}

    async function validateUsername() {
    const username = usernameInput.value.trim();
    const MAX_USERNAME_LENGTH = 150;

    // Reset error state
    usernameError.classList.add('hidden');
    usernameInput.classList.remove('border-red-500');

    // Validation checks
    if (username === '') {
        showUsernameError('Name cannot be empty');
        return false;
    }

    if (username.startsWith(' ')) {
        showUsernameError('Name cannot start with a space');
        return false;
    }

    if (username.length < 3) {
        showUsernameError('Name must be at least 3 characters');
        return false;
    }

    if (username.length > MAX_USERNAME_LENGTH) {
        showUsernameError(`Name must be less than ${MAX_USERNAME_LENGTH} characters`);
        return false;
    }

    function showUsernameError(message) {
        usernameError.textContent = message;
        usernameError.classList.remove('hidden');
        usernameInput.classList.add('border-red-500');
    }

    if (!/^[a-zA-Z\s]+$/.test(username)) {
        showUsernameError('Name can only contain letters and spaces');
        return false;
    }

    // Check for duplicate username
    try {
        const exists = await checkUsernameExists(username);
        if (exists) {
            showUsernameError('This name already exists');
            return false;
        }
    } catch (error) {
        console.error('Error checking username:', error);
        showUsernameError('Error checking username availability');
        return false;
    }

    return true;
}

    // Email validation and space removal
    if (emailInput) {
        let emailCheckTimeout;
        emailInput.addEventListener('input', function(e) {
            // Remove all spaces automatically
            this.value = this.value.replace(/\s+/g, '');

            // Clear previous timeout
            clearTimeout(emailCheckTimeout);

            // Set new timeout to avoid too many requests
            emailCheckTimeout = setTimeout(() => {
                validateEmail().then(() => validateForm());
            }, 300);
        });

        emailInput.addEventListener('blur', function() {
            validateEmail().then(() => validateForm());
        });
    }

    async function validateEmail() {
        const email = emailInput.value.trim();
        const MAX_EMAIL_LENGTH = 50;

        // Reset error state
        emailError.classList.add('hidden');
        emailInput.classList.remove('border-red-500');

        // Validation checks
        if (email === '') {
            showEmailError('Email cannot be empty');
            return Promise.resolve(false);
        }

        if (email.length > MAX_EMAIL_LENGTH) {
            showEmailError(`Email must be less than ${MAX_EMAIL_LENGTH} characters`);
            return Promise.resolve(false);
        }

        // Check for valid email format
        if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email)) {
            showEmailError('Please enter a valid email address');
            return Promise.resolve(false);
        }

        // Specifically check for gmail.com
        if (!email.toLowerCase().endsWith('@gmail.com')) {
            showEmailError('Only @gmail.com email addresses are accepted');
            return Promise.resolve(false);
        }

        // Check for number instead of letter in domain (.c0m instead of .com)
        if (/\.c0m$|\.c0m@/.test(email.toLowerCase())) {
            showEmailError('Invalid domain (.c0m is not valid)');
            return Promise.resolve(false);
        }

        // Check if email already exists
        return checkEmailExists(email).then(exists => {
            if (exists) {
                showEmailError('This email already exists');
                return false;
            }
            return true;
        }).catch(() => {
            showEmailError('Error checking email availability');
            return false;
        });
    }

    function showEmailError(message) {
        emailError.textContent = message;
        emailError.classList.remove('hidden');
        emailInput.classList.add('border-red-500');
    }

    // Role validation and handling
    if (roleSelect) {
        roleSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const actualRole = selectedOption.getAttribute('data-role');
            if (actualRoleInput) {
                actualRoleInput.value = actualRole;
            }
            validateRole();
            validateForm();
        });

        // Add blur event for consistent validation behavior
        roleSelect.addEventListener('blur', function() {
            validateRole();
            validateForm();
        });
    }

    async function validateRole() {
        // Reset error state
        roleError.classList.add('hidden');
        roleSelect.classList.remove('border-red-500');

        // Validation check
        if (roleSelect.value === '') {
            showRoleError('Please select a role');
            return false;
        }

        const restrictedRoles = [
            'Student Services',
            'Academic Services',
            'Administrative Services',
            'Campus Director'
        ];

        // Check if selected role is restricted
        if (restrictedRoles.includes(roleSelect.value)) {
            // Verify against server
            const existingRoles = await fetchExistingRoles();
            if (existingRoles.includes(roleSelect.value)) {
                showRoleError('This role already exists in the system');
                return false;
            }
        }

        return true;
    }

    function showRoleError(message) {
        roleError.textContent = message;
        roleError.classList.remove('hidden');
        roleSelect.classList.add('border-red-500');
    }

    // Form Submission Handling for Add User
    if (addUserForm) {
        addUserForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            // Set processing flag
            isProcessing = true;

            // Disable close buttons and backdrop click
            if (closeAddUserModalBtn) {
                closeAddUserModalBtn.disabled = true;
                closeAddUserModalBtn.style.opacity = '0.5';
                closeAddUserModalBtn.style.cursor = 'not-allowed';
            }

            // Prevent escape key and backdrop click during processing
            const modalBackdrop = document.querySelector('.add-user-backdrop');
            if (modalBackdrop) {
                modalBackdrop.style.pointerEvents = 'none';
            }

            try {
                // Validate all fields before submission
                if (!await validateForm()) {
                    resetProcessingState();
                    return;
                }

                // Get form data
                const username = usernameInput.value.trim();
                const email = emailInput.value.trim().toLowerCase();
                const role_name = roleSelect.value;

                // Get the actual role value (admin/student)
                const selectedOption = roleSelect.options[roleSelect.selectedIndex];
                const role = selectedOption.getAttribute('data-role');

                // Create form data object
                const formData = new FormData();
                formData.append('username', username);
                formData.append('email', email);
                formData.append('role_name', role_name);
                formData.append('role', role); // Send the mapped role value

                // Get CSRF token from meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    formData.append('_token', csrfToken);
                } else {
                    console.error("CSRF token not found. Make sure you have a meta tag with name='csrf-token'");
                }

                // Disable submit button to prevent multiple submissions
                const submitBtn = addUserForm.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = 'Adding...';
                }

                // Send the form data via fetch API
                const response = await fetch('/users', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        return { success: true, message: "User added successfully" };
                    }
                });

                // Reset form fields
                addUserForm.reset();
                resetValidationState();

                // Close the add user modal
                if (addUserModal) {
                    addUserModal.classList.add('hidden');
                }

                // Show success message
                if (successModal) {
                    // Update success message if needed
                    document.getElementById('successTitle').textContent = 'Account Successfully Created!';
                    document.getElementById('successMessage').textContent = 'The user account has been added successfully.';

                    // Show success modal
                    successModal.classList.remove('hidden');

                    // Add click event listeners for both the okay button and close button
                    const okayButton = document.querySelector('#successModal button[type="button"]');
                    const closeSuccessBtn = document.querySelector('#successModal #closeSuccessModalBtn');

                    if (okayButton) {
                        okayButton.addEventListener('click', () => {
                            successModal.classList.add('hidden');
                            window.location.reload();
                        });
                    }
                    if (closeSuccessBtn) {
                        closeSuccessBtn.addEventListener('click', () => {
                            successModal.classList.add('hidden');
                            window.location.reload();
                        });
                    }
                } else {
                    // Fallback to alert if modal not found
                    alert('User added successfully!');
                    window.location.reload();
                }
            } catch (error) {
                console.error("Error:", error);

                // Extract error message
                let errorMessage = 'An error occurred while adding the user.';

                if (error && typeof error === 'object') {
                    if (error.errors && Object.keys(error.errors).length > 0) {
                        const firstErrorKey = Object.keys(error.errors)[0];
                        errorMessage = error.errors[firstErrorKey][0];
                    } else if (error.message) {
                        errorMessage = error.message;
                    }
                }

                // Display error message to user
                alert(errorMessage);
            } finally {
                // Reset processing state
                resetProcessingState();
            }
        });
    }
});