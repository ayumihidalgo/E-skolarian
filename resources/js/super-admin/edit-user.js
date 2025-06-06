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
    const editAcronymField = document.getElementById('editAcronymField');
    const editAcronymInput = document.getElementById('editAcronym');
    
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

    // Update the updateRoleOptions function
    async function updateRoleOptions(existingRoles, currentRole) {
        if (!roleSelect) return;

        const adminRoles = [
            'Office of the Student Services',
            'Office of the Academic Services',
            'Office of the Administrative Services',
            'Office of the Campus Director'
        ];

        // Get all options
        const options = Array.from(roleSelect.options);

        options.forEach(option => {
            const roleName = option.value;
            
            // Check if it's an admin role
            if (adminRoles.includes(roleName)) {
                // Hide the option if:
                // 1. It exists in the database AND
                // 2. It's not the current user's role
                if (existingRoles.includes(roleName) && roleName !== currentRole) {
                    option.disabled = true;
                    option.style.display = 'none'; // Hide the option completely
                } else {
                    option.disabled = false;
                    option.style.display = ''; // Show the option
                }
            }
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
        editUserBtn.addEventListener('click', async function () {
            // Hide the user details modal
            userDetailsModal.classList.add('hidden');

            // Get current user data
            const username = document.getElementById('userUsername').textContent;
            const email = document.getElementById('userEmail').textContent;
            const roleName = document.getElementById('userRole').textContent;
            const acronym = document.getElementById('userAcronym')?.textContent;

            try {
                const isAdmin = roleName.toLowerCase() === 'admin' || 
                               roleName.toLowerCase().includes('services') || 
                               roleName.toLowerCase().includes('director');

                // Store the user type for later use
                window.currentUserType = isAdmin ? 'admin' : 'student';

                // Lock fields based on user type
                const usernameInput = document.getElementById('editUsername');
                const roleNameInput = document.getElementById('editRoleName');
                const emailInput = document.getElementById('editEmail');

                // For admin users, only email should be editable
                if (isAdmin) {
                    // Lock username and role
                    usernameInput.readOnly = true;
                    roleNameInput.readOnly = true;
                    usernameInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                    roleNameInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                    
                    // Keep email editable
                    emailInput.readOnly = false;
                    emailInput.classList.remove('bg-gray-100', 'cursor-not-allowed');

                    // Hide acronym field
                    if (editAcronymField) {
                        editAcronymField.classList.add('hidden');
                    }
                }

                // Set form values
                usernameInput.value = username;
                roleNameInput.value = roleName;
                emailInput.value = email;
                document.getElementById('editActualRole').value = isAdmin ? 'admin' : 'student';

                // Handle acronym field for student organizations
                if (editAcronymField && editAcronymInput && !isAdmin) {
                    if (roleName.toLowerCase().includes('organization')) {
                        editAcronymField.classList.remove('hidden');
                        editAcronymInput.value = acronym || '';
                    } else {
                        editAcronymField.classList.add('hidden');
                    }
                }

                // Show modal and capture initial state
                editUserModal.classList.remove('hidden');
                captureInitialState();

            } catch (error) {
                console.error('Error setting up edit form:', error);
                alert('Error loading user data. Please try again.');
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

    // Update the email validation function
    async function validateEmail() {
        const email = emailInput.value.trim();
        const MAX_EMAIL_LENGTH = 50;
        const ALLOWED_DOMAINS = ['gmail.com', 'yahoo.com', 'iskolarngbayan.pup.edu.ph'];

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

        // Extract domain from email
        const domain = email.split('@')[1]?.toLowerCase();
        
        if (!domain || !ALLOWED_DOMAINS.includes(domain)) {
            showEmailError('Only @gmail.com, @yahoo.com, or @iskolarngbayan.pup.edu.ph email addresses are accepted');
            return false;
        }

        // Basic email format validation
        if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email)) {
            showEmailError('Please enter a valid email address');
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
            isProcessing = true;

            try {
                const isAdmin = window.currentUserType === 'admin';
                const formData = new FormData();
                
                // Add form fields
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('_method', 'PUT');
                formData.append('id', window.currentUserId);
                formData.append('email', document.getElementById('editEmail').value.trim());
                formData.append('username', document.getElementById('editUsername').value);
                formData.append('role_name', document.getElementById('editRoleName').value);
                formData.append('role', document.getElementById('editActualRole').value);

                // Only include organization_acronym for student organizations
                if (!isAdmin && editAcronymField && !editAcronymField.classList.contains('hidden')) {
                    formData.append('organization_acronym', document.getElementById('editAcronym').value.trim());
                }

                // Disable the submit button
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = 'Saving...';
                }

                const response = await fetch(`/users/${window.currentUserId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const data = await response.json();
                        throw new Error(data.message || 'Failed to update user');
                    } else {
                        throw new Error('Server returned an invalid response');
                    }
                }

                const data = await response.json();

                // Hide edit modal and show success message
                editUserModal.classList.add('hidden');
                document.getElementById('successTitle').textContent = 'Account Updated Successfully!';
                document.getElementById('successMessage').textContent = 
                    'Your changes have been saved. The user will be notified about the update via email.';
                successModal.classList.remove('hidden');
                
                // Add reload listener to success modal close button
                const okayButton = document.querySelector('#successModal button');
                if (okayButton) {
                    okayButton.addEventListener('click', () => window.location.reload(), { once: true });
                }

            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'An error occurred while updating the user.');
            } finally {
                isProcessing = false;
                // Re-enable submit button
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Save Changes';
                }
            }
        });
    }
});