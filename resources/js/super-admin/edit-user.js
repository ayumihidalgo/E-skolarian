// Edit User Functionality
document.addEventListener('DOMContentLoaded', function () {
    // Edit User Modal Elements
    const editUserBtn = document.getElementById('editUserBtn');
    const editUserModal = document.getElementById('editUserModal');
    const closeEditModalBtn = document.getElementById('closeEditModalBtn');
    const editUserBackdrop = document.querySelector('.edit-user-backdrop');
    const editUserForm = document.getElementById('editUserForm');
    const userDetailsModal = document.getElementById('userDetailsModal');
    const successModal = document.getElementById('successModal');
    let initialFormState = {};
    const saveButton = editUserForm.querySelector('button[type="submit"]');

    // Confirmation Modal Elements - positioned right after main modal elements
    const closeEditConfirmModal = document.getElementById('closeEditConfirmModal');
    const cancelEditCloseBtn = document.getElementById('cancelEditCloseBtn');
    const confirmEditCloseBtn = document.getElementById('confirmEditCloseBtn');

    // Position the confirmation modal with proper z-index
    if (closeEditConfirmModal) {
        closeEditConfirmModal.style.zIndex = "100"; // Higher than edit modal
    }

    // Add processing flag
    let isProcessing = false;

    // We need to intercept the default modal close functionality from setupModalClose
    // by adding our own backdrop click handler
    if (editUserBackdrop) {
        editUserBackdrop.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (!isProcessing) {
                // Only show confirmation if there are unsaved changes
                if (checkFormChanged()) {
                    closeEditConfirmModal.classList.remove('hidden');
                } else {
                    // No changes, close without confirmation
                    editUserModal.classList.add('hidden');
                    setTimeout(() => {
                        userDetailsModal.classList.remove('hidden');
                    }, 100);
                }
            }
        });
    }

    // Validation feedback elements
    const usernameInput = document.getElementById('editUsername');
    const emailInput = document.getElementById('editEmail');
    const roleSelect = document.getElementById('editRoleName');
    
    // Create error elements
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

    // Function to reset the edit form
    function resetEditForm() {
        if (editUserForm) {
            // Reset form to initial state
            document.getElementById('editUsername').value = initialFormState.username || '';
            document.getElementById('editEmail').value = initialFormState.email || '';
            document.getElementById('editRoleName').value = initialFormState.roleName || '';
            
            // Reset validation states
            resetValidationState();
            
            // Reset button state
            saveButton.disabled = true;
            saveButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    // Function to reset validation state
    function resetValidationState() {
        usernameError.classList.add('hidden');
        emailError.classList.add('hidden');
        roleError.classList.add('hidden');
        usernameInput.classList.remove('border-red-500');
        emailInput.classList.remove('border-red-500');
        roleSelect.classList.remove('border-red-500');
    }

    // Username validation
    if (usernameInput) {
        let usernameCheckTimeout;
        usernameInput.addEventListener('input', function(e) {
            this.value = this.value
                .replace(/^\s+/, '')
                .replace(/[^a-zA-Z\s]/g, '')
                .replace(/\s+/g, ' ');

            clearTimeout(usernameCheckTimeout);
            usernameCheckTimeout = setTimeout(() => {
                validateUsername().then(() => validateForm());
            }, 300);
        });

        usernameInput.addEventListener('blur', function() {
            this.value = this.value.trim();
            validateUsername().then(() => validateForm());
        });
    }

    // Email validation
    if (emailInput) {
        let emailCheckTimeout;
        emailInput.addEventListener('input', function() {
            this.value = this.value.replace(/\s+/g, '');
            
            clearTimeout(emailCheckTimeout);
            emailCheckTimeout = setTimeout(() => {
                validateEmail().then(() => validateForm());
            }, 300);
        });

        emailInput.addEventListener('blur', function() {
            validateEmail().then(() => validateForm());
        });
    }

    // Function to fetch existing administrative roles
    async function fetchExistingRoles() {
        try {
            const response = await fetch('/check-roles', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!response.ok) throw new Error('Failed to fetch roles');
            const data = await response.json();
            return data.existingRoles || [];
        } catch (error) {
            console.error('Error fetching roles:', error);
            return [];
        }
    }

    // Function to update role options based on existing roles
    function updateRoleOptions(existingRoles, currentRole) {
        if (!roleSelect) return;

        const restrictedRoles = ['Student Services', 'Academic Services', 'Administrative Services', 'Campus Director'];
        const options = Array.from(roleSelect.options);

        options.forEach(option => {
            const roleName = option.value;
            // Only disable if it's a restricted role that exists AND it's not the current user's role
            const isRestricted = restrictedRoles.includes(roleName) && 
                                existingRoles.includes(roleName) && 
                                roleName !== currentRole;
            
            option.disabled = isRestricted;
            option.style.display = isRestricted ? 'none' : '';
        });
    }

    // Function to capture initial form state
    function captureInitialState() {
        initialFormState = {
            username: document.getElementById('editUsername').value.trim(),
            email: document.getElementById('editEmail').value.trim(),
            roleName: document.getElementById('editRoleName').value
        };
        // Initially disable the save button
        saveButton.disabled = true;
        saveButton.classList.add('opacity-50', 'cursor-not-allowed');
    }

    // Function to check if form has changed
    function checkFormChanged() {
        const currentValues = {
            username: document.getElementById('editUsername').value.trim(),
            email: document.getElementById('editEmail').value.trim(),
            roleName: document.getElementById('editRoleName').value
        };

        const hasChanged = 
            currentValues.username !== initialFormState.username ||
            currentValues.email !== initialFormState.email ||
            currentValues.roleName !== initialFormState.roleName;

        // Enable/disable save button based on changes
        saveButton.disabled = !hasChanged;
        if (hasChanged) {
            saveButton.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            saveButton.classList.add('opacity-50', 'cursor-not-allowed');
        }

        return hasChanged;
    }

    // Add event listener to update the actual role when role_name changes
    const editRoleName = document.getElementById('editRoleName');
    if (editRoleName) {
        editRoleName.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('editActualRole').value = selectedOption.getAttribute('data-role');
            validateRole().then(() => validateForm());
        });
    }

    // Custom close functionality for edit modal to show user details modal again
    if (closeEditModalBtn && editUserModal) {
        closeEditModalBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (!isProcessing) {
                // Only show confirmation if there are unsaved changes
                if (checkFormChanged()) {
                    // Show confirmation modal but don't close edit modal yet
                    closeEditConfirmModal.classList.remove('hidden');
                } else {
                    // No changes, close without confirmation
                    editUserModal.classList.add('hidden');
                    setTimeout(() => {
                        userDetailsModal.classList.remove('hidden');
                    }, 100);
                }
            }
        });
    }

    // Confirmation modal button handlers
    if (cancelEditCloseBtn) {
        cancelEditCloseBtn.addEventListener('click', function() {
            closeEditConfirmModal.classList.add('hidden');
        });
    }

    if (confirmEditCloseBtn) {
        confirmEditCloseBtn.addEventListener('click', function() {
            closeEditConfirmModal.classList.add('hidden');
            editUserModal.classList.add('hidden');
            userDetailsModal.classList.remove('hidden');
            resetEditForm();
            window.location.reload(); // Reload page to reset everything
        });
    }

    // Add escape key handler for both modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !isProcessing) {
            if (!closeEditConfirmModal.classList.contains('hidden')) {
                // If confirmation modal is showing, just close it
                closeEditConfirmModal.classList.add('hidden');
            } else if (!editUserModal.classList.contains('hidden')) {
                // Only show confirmation if there are unsaved changes
                if (checkFormChanged()) {
                    // Show confirmation instead of closing
                    closeEditConfirmModal.classList.remove('hidden');
                } else {
                    // No changes, close without confirmation
                    editUserModal.classList.add('hidden');
                    setTimeout(() => {
                        userDetailsModal.classList.remove('hidden');
                    }, 100);
                }
            }
        }
    });

    // Add click outside modal handler
    closeEditConfirmModal.addEventListener('click', function(e) {
        if (e.target === closeEditConfirmModal) {
            closeEditConfirmModal.classList.add('hidden');
        }
    });

    // Edit User Modal Event Listeners - Open modal when clicking edit button
    if (editUserBtn && editUserModal) {
        editUserBtn.addEventListener('click', function () {
            // Hide the user details modal
            userDetailsModal.classList.add('hidden');

            // Get current user data from the details modal
            const username = document.getElementById('userUsername').textContent;
            const email = document.getElementById('userEmail').textContent;
            const roleName = document.getElementById('userRole').textContent;

            // Populate the edit form
            document.getElementById('editUsername').value = username;
            document.getElementById('editEmail').value = email;

            // Set the role in the dropdown
            const roleSelect = document.getElementById('editRoleName');
            if (roleSelect) {
                for (let i = 0; i < roleSelect.options.length; i++) {
                    if (roleSelect.options[i].value === roleName) {
                        roleSelect.selectedIndex = i;
                        // Set the actual role (admin/student) from the data-role attribute
                        document.getElementById('editActualRole').value = roleSelect.options[i].getAttribute('data-role');
                        break;
                    }
                }
            }
            
            // Fetch available roles and update options
            fetchExistingRoles().then(existingRoles => {
                updateRoleOptions(existingRoles, roleName);
                editUserModal.classList.remove('hidden');
                captureInitialState(); // Capture initial state after form is populated
            }).catch(error => {
                console.error('Error fetching roles:', error);
                editUserModal.classList.remove('hidden');
                captureInitialState();
            });

            // Alternative for editRole if that's what's in the form
            const editRole = document.getElementById('editRole');
            if (editRole) {
                editRole.value = roleName;
            }
        });
    }

    // Add input event listeners to form fields
    if (editUserForm) {
        const formInputs = ['editUsername', 'editEmail', 'editRoleName'];
        formInputs.forEach(inputId => {
            const element = document.getElementById(inputId);
            if (element) {
                // Add input event listener with debounce
                let timeoutId;
                element.addEventListener('input', function() {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(() => {
                        checkFormChanged();
                    }, 300);
                });

                // Add blur event listener to catch paste events
                element.addEventListener('blur', function() {
                    checkFormChanged();
                });

                // Add change event for select elements
                if (element.tagName === 'SELECT') {
                    element.addEventListener('change', checkFormChanged);
                }
            }
        });
    }

    // Email existence check function
    async function checkEmailExists(email) {
        // Add current user's ID to exclude from check
        const response = await fetch('/check-email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ 
                email: email.toLowerCase(),
                exclude_id: window.currentUserId  // Exclude current user
            })
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
            body: JSON.stringify({ 
                username: username.toLowerCase(),
                exclude_id: window.currentUserId  // Exclude current user
            })
        });
        const data = await response.json();
        return data.exists;
    }

    // Validation functions
    async function validateUsername() {
        const username = usernameInput.value.trim();
        const MAX_USERNAME_LENGTH = 150;

        usernameError.classList.add('hidden');
        usernameInput.classList.remove('border-red-500');

        if (username === '') {
            showUsernameError('Name cannot be empty');
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

        if (!/^[a-zA-Z\s]+$/.test(username)) {
            showUsernameError('Name can only contain letters and spaces');
            return false;
        }

        // Only check for duplicate username if it's different from the original
        if (username.toLowerCase() !== initialFormState.username.toLowerCase()) {
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
        }

        return true;
    }

    async function validateEmail() {
        const email = emailInput.value.trim();
        const MAX_EMAIL_LENGTH = 50;

        emailError.classList.add('hidden');
        emailInput.classList.remove('border-red-500');

        if (email === '') {
            showEmailError('Email cannot be empty');
            return false;
        }

        if (email.length > MAX_EMAIL_LENGTH) {
            showEmailError(`Email must be less than ${MAX_EMAIL_LENGTH} characters`);
            return false;
        }

        if (!/^[a-zA-Z0-9._%+-]+@gmail\.com$/.test(email.toLowerCase())) {
            showEmailError('Only @gmail.com email addresses are accepted');
            return false;
        }

        // Only check for duplicate email if it's different from the original
        if (email.toLowerCase() !== initialFormState.email.toLowerCase()) {
            try {
                const exists = await checkEmailExists(email);
                if (exists) {
                    showEmailError('This email already exists');
                    return false;
                }
            } catch (error) {
                console.error('Error checking email:', error);
                showEmailError('Error checking email availability');
                return false;
            }
        }

        return true;
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

        // For edit user, we need to check if the role is being changed to a restricted role
        if (roleSelect.value !== initialFormState.roleName) {
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
        }

        return true;
    }

    // Helper functions for showing errors
    function showUsernameError(message) {
        usernameError.textContent = message;
        usernameError.classList.remove('hidden');
        usernameInput.classList.add('border-red-500');
    }

    function showEmailError(message) {
        emailError.textContent = message;
        emailError.classList.remove('hidden');
        emailInput.classList.add('border-red-500');
    }

    function showRoleError(message) {
        roleError.textContent = message;
        roleError.classList.remove('hidden');
        roleSelect.classList.add('border-red-500');
    }

    // Form validation
    async function validateForm() {
        const isUsernameValid = await validateUsername();
        const isEmailValid = await validateEmail();
        const isRoleValid = await validateRole();
        
        saveButton.disabled = !(isUsernameValid && isEmailValid && isRoleValid);
        saveButton.classList.toggle('opacity-50', !isUsernameValid || !isEmailValid || !isRoleValid);
        saveButton.classList.toggle('cursor-not-allowed', !isUsernameValid || !isEmailValid || !isRoleValid);

        return isUsernameValid && isEmailValid && isRoleValid;
    }

    // Handle Edit User Form Submission
    if (editUserForm) {
        editUserForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            // Set processing flag
            isProcessing = true;

            // Disable close button during processing
            if (closeEditModalBtn) {
                closeEditModalBtn.disabled = true;
                closeEditModalBtn.style.opacity = '0.5';
                closeEditModalBtn.style.cursor = 'not-allowed';
            }

            try {
                // Validate form before submission
                if (!await validateForm()) {
                    isProcessing = false;
                    closeEditModalBtn.disabled = false;
                    closeEditModalBtn.style.opacity = '1';
                    closeEditModalBtn.style.cursor = 'pointer';
                    return;
                }

                // Get form data
                const formData = {
                    id: window.currentUserId, // Get the current user ID from the global variable
                    username: document.getElementById('editUsername').value,
                    email: document.getElementById('editEmail').value
                };

                // Get the role data - check for both possible form structures
                if (document.getElementById('editRoleName')) {
                    formData.role_name = document.getElementById('editRoleName').value;
                    formData.role = document.getElementById('editActualRole').value;
                } else if (document.getElementById('editRole')) {
                    formData.role_name = document.getElementById('editRole').value;
                    // Get the actual role from the selected option's data-role attribute
                    const roleSelect = document.getElementById('editRole');
                    const selectedOption = roleSelect.options[roleSelect.selectedIndex];
                    if (selectedOption && selectedOption.getAttribute('data-role')) {
                        formData.role = selectedOption.getAttribute('data-role');
                    }
                }

                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Show a loading state or disable the submit button to prevent double submissions
                const submitBtn = editUserForm.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = 'Saving...';
                }

                // Send AJAX request to update user
                fetch(`/users/${formData.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => {
                    // Check response status first
                    if (response.status >= 200 && response.status < 300) {
                        // Try to parse as JSON, but don't fail if it's not JSON
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                // If it's not valid JSON, just return success
                                console.log("Response is not valid JSON, but request was successful");
                                return { success: true, message: "User updated successfully" };
                            }
                        });
                    } else {
                        // For error responses, try to get error details
                        return response.text().then(text => {
                            try {
                                return Promise.reject(JSON.parse(text));
                            } catch (e) {
                                return Promise.reject({ message: `Server error: ${response.status}` });
                            }
                        });
                    }
                })
                .then(data => {
                    // Hide the edit modal first
                    editUserModal.classList.add('hidden');

                    // Set success message content if elements exist
                    document.getElementById('successTitle').textContent = 'Account Successfully Updated!';
                    document.getElementById('successMessage').textContent = 'The user account has been updated successfully.';

                    // Show success message if modal exists
                    successModal.classList.remove('hidden');

                    // Refresh page after successful update
                    setTimeout(() => {
                        window.location.reload();
                    }, 2500);
                })
                .catch(error => {
                    console.error('Error:', error);

                    // Re-enable the submit button
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Save Changes';
                    }

                    // Extract error message
                    let errorMessage = 'An error occurred while updating the user.';

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
                })
                .finally(() => {
                    // Reset processing state
                    isProcessing = false;
                    if (closeEditModalBtn) {
                        closeEditModalBtn.disabled = false;
                        closeEditModalBtn.style.opacity = '1';
                        closeEditModalBtn.style.cursor = 'pointer';
                    }
                });
            } catch (error) {
                console.error('Error:', error);
                
                // Reset processing state
                isProcessing = false;
                if (closeEditModalBtn) {
                    closeEditModalBtn.disabled = false;
                    closeEditModalBtn.style.opacity = '1';
                    closeEditModalBtn.style.cursor = 'pointer';
                }
            }
        });
    }
});