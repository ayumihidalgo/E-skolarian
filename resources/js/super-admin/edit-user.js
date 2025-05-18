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

    // Add processing flag
    let isProcessing = false;

    // Set up modal functionality
    if (window.setupModalClose) {
        window.setupModalClose(editUserModal, '#closeEditModalBtn', null);
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

    // Function to capture initial form state
    function captureInitialState() {
        initialFormState = {
            username: document.getElementById('editUsername').value,
            email: document.getElementById('editEmail').value,
            roleName: document.getElementById('editRoleName').value
        };
        // Initially disable the save button
        saveButton.disabled = true;
        saveButton.classList.add('opacity-50', 'cursor-not-allowed');
    }

    // Function to check if form has changed
    function checkFormChanged() {
        const hasChanged = 
            document.getElementById('editUsername').value !== initialFormState.username ||
            document.getElementById('editEmail').value !== initialFormState.email ||
            document.getElementById('editRoleName').value !== initialFormState.roleName;

        // Enable/disable save button based on changes
        saveButton.disabled = !hasChanged;
        if (hasChanged) {
            saveButton.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            saveButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    // Add event listener to update the actual role when role_name changes
    const editRoleName = document.getElementById('editRoleName');
    if (editRoleName) {
        editRoleName.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('editActualRole').value = selectedOption.getAttribute('data-role');
        });
    }

    // Custom close functionality for edit modal to show user details modal again
    if (closeEditModalBtn && editUserModal) {
        closeEditModalBtn.addEventListener('click', function () {
            if (!isProcessing) {
                editUserModal.classList.add('hidden');
                // Show the user details modal again after a small delay
                setTimeout(() => {
                    userDetailsModal.classList.remove('hidden');
                }, 100);
            }
        });
    }

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
            setTimeout(() => {
                editUserModal.classList.remove('hidden');
                captureInitialState(); // Capture initial state after form is populated
            }, 100);

            // Alternative for editRole if that's what's in the form
            const editRole = document.getElementById('editRole');
            if (editRole) {
                editRole.value = roleName;
            }

            // Show the edit modal after a small delay to prevent overlap
            setTimeout(() => {
                editUserModal.classList.remove('hidden');
            }, 100);
        });
    }

    // Add input event listeners to form fields
    if (editUserForm) {
        const formInputs = ['editUsername', 'editEmail', 'editRoleName'];
        formInputs.forEach(inputId => {
            const element = document.getElementById(inputId);
            if (element) {
                element.addEventListener('input', checkFormChanged);
                element.addEventListener('change', checkFormChanged);
            }
        });
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

    // Form validation
    async function validateForm() {
        const isUsernameValid = await validateUsername();
        const isEmailValid = await validateEmail();
        
        saveButton.disabled = !(isUsernameValid && isEmailValid);
        saveButton.classList.toggle('opacity-50', !isUsernameValid || !isEmailValid);
        saveButton.classList.toggle('cursor-not-allowed', !isUsernameValid || !isEmailValid);

        return isUsernameValid && isEmailValid;
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