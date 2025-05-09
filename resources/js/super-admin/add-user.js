// User Adding Functionality with Enhanced Validation
document.addEventListener('DOMContentLoaded', function () {
    // Add User Modal Elements
    const addUserBtn = document.getElementById('addUserBtn');
    const addUserModal = document.getElementById('addUserModal');
    const closeAddUserBtn = document.getElementById('closeAddUserBtn');
    const closeAddUserModalBtn = document.getElementById('closeAddUserModalBtn');
    const addUserBackdrop = document.querySelector('.add-user-backdrop');
    const successModal = document.getElementById('successModal');

    // Form input elements
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const roleSelect = document.getElementById('role_name');
    const actualRoleInput = document.getElementById('actual_role');

    // Validation feedback elements
    const usernameError = document.createElement('p');
    usernameError.className = 'text-red-600 text-xs mt-1 hidden';
    const emailError = document.createElement('p');
    emailError.className = 'text-red-600 text-xs mt-1 hidden';

    // Insert error elements after inputs
    if (usernameInput) {
        usernameInput.parentNode.insertBefore(usernameError, usernameInput.nextSibling);
    }
    if (emailInput) {
        emailInput.parentNode.insertBefore(emailError, emailInput.nextSibling);
    }

    // Set up modal functionality
    if (window.setupModalClose) {
        window.setupModalClose(addUserModal, '#closeAddUserBtn', '.add-user-backdrop');

        if (closeAddUserModalBtn) {
            closeAddUserModalBtn.addEventListener('click', function () {
                addUserModal.classList.add('hidden');
            });
        }
    }

    // Open Add User Modal
    if (addUserBtn && addUserModal) {
        addUserBtn.addEventListener('click', function () {
            addUserModal.classList.remove('hidden');
        });
    }

    // Username validation and space removal
    if (usernameInput) {
        usernameInput.addEventListener('input', function(e) {
            // Remove spaces automatically
            this.value = this.value.replace(/\s+/g, ' ');

            validateUsername();
        });

        usernameInput.addEventListener('blur', validateUsername);
    }

    function validateUsername() {
        const username = usernameInput.value.trim();

        // Reset error state
        usernameError.classList.add('hidden');
        usernameInput.classList.remove('border-red-500');

        // Validation checks
        if (username === '') {
            showUsernameError('Username cannot be empty');
            return false;
        }

        if (username.length < 3) {
            showUsernameError('Username must be at least 3 characters');
            return false;
        }

        if (username.length > 100) {
            showUsernameError('Username must be less than 100 characters');
            return false;
        }

        if (!/^[a-zA-Z0-9\s\-_.]+$/.test(username)) {
            showUsernameError('Username can only contain letters, numbers, spaces, hyphens, underscores, and periods');
            return false;
        }

        return true;
    }

    function showUsernameError(message) {
        usernameError.textContent = message;
        usernameError.classList.remove('hidden');
        usernameInput.classList.add('border-red-500');
    }

    // Email validation and space removal
    if (emailInput) {
        emailInput.addEventListener('input', function(e) {
            // Remove all spaces automatically
            this.value = this.value.replace(/\s+/g, '');

            validateEmail();
        });

        emailInput.addEventListener('blur', validateEmail);
    }

    function validateEmail() {
        const email = emailInput.value.trim();

        // Reset error state
        emailError.classList.add('hidden');
        emailInput.classList.remove('border-red-500');

        // Validation checks
        if (email === '') {
            showEmailError('Email cannot contain spaces');
            return false;
        }

        // Check for valid email format
        if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email)) {
            showEmailError('Please enter a valid email address');
            return false;
        }

        // Specifically check for gmail.com
        if (!email.toLowerCase().endsWith('@gmail.com')) {
            showEmailError('Only @gmail.com email addresses are accepted');
            return false;
        }

        // Check for number instead of letter in domain (.c0m instead of .com)
        if (/\.c0m$|\.c0m@/.test(email.toLowerCase())) {
            showEmailError('Invalid domain (.c0m is not valid)');
            return false;
        }

        return true;
    }

    function showEmailError(message) {
        emailError.textContent = message;
        emailError.classList.remove('hidden');
        emailInput.classList.add('border-red-500');
    }

    // Update the hidden role value when role_name changes
    if (roleSelect) {
        roleSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const actualRole = selectedOption.getAttribute('data-role');
            if (actualRoleInput) {
                actualRoleInput.value = actualRole;
            }
        });
    }

    // Form Submission Handling for Add User
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Validate both fields before submission
            const isUsernameValid = validateUsername();
            const isEmailValid = validateEmail();

            // Stop submission if validation fails
            if (!isUsernameValid || !isEmailValid) {
                return;
            }

            // Get form data
            const username = usernameInput.value.trim();
            const email = emailInput.value.trim();
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
            fetch('/users', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (response.status >= 200 && response.status < 300) {
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                return { success: true, message: "User added successfully" };
                            }
                        });
                    } else {
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
                    // Reset form fields
                    addUserForm.reset();
                    usernameError.classList.add('hidden');
                    emailError.classList.add('hidden');
                    usernameInput.classList.remove('border-red-500');
                    emailInput.classList.remove('border-red-500');

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
                    } else {
                        // Fallback to alert if modal not found
                        alert('User added successfully!');
                    }

                    // Refresh the page after a delay to show the new user
                    // setTimeout(() => {
                    //     window.location.reload();
                    // }, 2500);
                })
                .catch(error => {
                    console.error("Error:", error);

                    // Re-enable the submit button
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Add User';
                    }

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
                });
        });
    }
});
