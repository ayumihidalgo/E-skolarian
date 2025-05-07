document.addEventListener('DOMContentLoaded', function () {
    // Add User Modal
    const addUserBtn = document.getElementById('addUserBtn');
    const addUserModal = document.getElementById('addUserModal');
    const closeAddUserBtn = document.getElementById('closeAddUserBtn');
    const closeAddUserModalBtn = document.getElementById('closeAddUserModalBtn');
    const addUserBackdrop = document.querySelector('.add-user-backdrop');

    // User Details Modal
    const userDetailsModal = document.getElementById('userDetailsModal');
    const closeUserDetailsBtn = document.getElementById('closeUserDetailsBtn');
    const userDetailsBackdrop = document.querySelector('.user-details-backdrop');
    const userRows = document.querySelectorAll('.user-details-row');
    const userDetailsRows = document.querySelectorAll('.user-details-row');

    // Success Modal
    const successModal = document.getElementById('successModal');
    const closeSuccessModalBtn = document.getElementById('closeSuccessModalBtn');
    const successModalBackdrop = document.querySelector('.success-modal-backdrop');

    // Edit User Modal
    const editUserBtn = document.getElementById('editUserBtn');
    const editUserModal = document.getElementById('editUserModal');
    const closeEditModalBtn = document.getElementById('closeEditModalBtn');
    const editUserBackdrop = document.querySelector('.edit-user-backdrop');
    const editUserForm = document.getElementById('editUserForm');

    // Role selection handling
    const roleSelect = document.getElementById('role_name');
    const actualRoleInput = document.getElementById('actual_role');

    // Current user data for editing
    let currentUserId = null;

    // Deactivation Confirmation
    const deactivateBtn = document.getElementById('deactivateBtn');
    const deactivateConfirmModal = document.getElementById('deactivateConfirmModal');
    const cancelDeactivateBtn = document.getElementById('cancelDeactivateBtn');
    const confirmDeactivateBtn = document.getElementById('confirmDeactivateBtn');
    const deactivateConfirmBackdrop = document.querySelector('.deactivate-confirm-backdrop');

    // Email Confirmation Modal
    const emailConfirmModal = document.getElementById('emailConfirmModal');
    const closeEmailConfirmBtn = document.getElementById('closeEmailConfirmBtn');
    const confirmEmailInput = document.getElementById('confirmEmail');
    const emailError = document.getElementById('emailError');
    const finalDeactivateBtn = document.getElementById('finalDeactivateBtn');
    let userEmailToDeactivate = '';

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

    // Add User Modal Event Listeners
    if (addUserBtn && addUserModal) {
        addUserBtn.addEventListener('click', function () {
            addUserModal.classList.remove('hidden');
        });
    }

    // Add User Modal Closing
    if (closeAddUserBtn && addUserModal) {
        closeAddUserBtn.addEventListener('click', function () {
            addUserModal.classList.add('hidden');
        });
    }

    if (closeAddUserModalBtn && addUserModal) {
        closeAddUserModalBtn.addEventListener('click', function () {
            addUserModal.classList.add('hidden');
        });
    }

    if (addUserBackdrop && addUserModal) {
        addUserBackdrop.addEventListener('click', function () {
            addUserModal.classList.add('hidden');
        });
    }

    // User Details Modal Event Listeners
    if (userRows && userDetailsModal) {
        userRows.forEach(row => {
            row.addEventListener('click', function () {
                const userData = JSON.parse(this.getAttribute('data-user'));

                // Set the user data in the modal
                document.getElementById('userUsername').textContent = userData.username;
                document.getElementById('userEmail').textContent = userData.email;
                document.getElementById('userRole').textContent = userData.role_name;

                // Store user ID for edit operation
                currentUserId = userData.id;

                userDetailsModal.classList.remove('hidden');
            });
        });
    }

    if (userDetailsRows && userDetailsModal) {
        userDetailsRows.forEach(row => {
            row.addEventListener('click', function () {
                const userData = JSON.parse(this.getAttribute('data-user'));

                // Fill user details in the modal
                document.getElementById('userUsername').textContent = userData.username;
                document.getElementById('userEmail').textContent = userData.email;
                document.getElementById('userRole').textContent = userData.role_name;

                // Store user ID for edit operation
                currentUserId = userData.id;

                // Show the modal
                userDetailsModal.classList.remove('hidden');
            });
        });
    }

    if (closeUserDetailsBtn && userDetailsModal) {
        closeUserDetailsBtn.addEventListener('click', function () {
            userDetailsModal.classList.add('hidden');
        });
    }

    if (userDetailsBackdrop && userDetailsModal) {
        userDetailsBackdrop.addEventListener('click', function () {
            userDetailsModal.classList.add('hidden');
        });
    }

    // Success Modal Event Listeners
    if (closeSuccessModalBtn && successModal) {
        closeSuccessModalBtn.addEventListener('click', function () {
            successModal.classList.add('hidden');
        });
    }

    if (successModalBackdrop && successModal) {
        successModalBackdrop.addEventListener('click', function () {
            successModal.classList.add('hidden');
        });
    }

    // Edit User Modal Event Listeners
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

    // Add event listener to update the actual role when role_name changes
    const editRoleName = document.getElementById('editRoleName');
    if (editRoleName) {
        editRoleName.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('editActualRole').value = selectedOption.getAttribute('data-role');
        });
    }

    if (closeEditModalBtn && editUserModal) {
        closeEditModalBtn.addEventListener('click', function () {
            editUserModal.classList.add('hidden');
            // Show the user details modal again after a small delay
            setTimeout(() => {
                userDetailsModal.classList.remove('hidden');
            }, 100);
        });
    }

    if (editUserBackdrop && editUserModal) {
        editUserBackdrop.addEventListener('click', function () {
            editUserModal.classList.add('hidden');
            // Show the user details modal again after a small delay
            setTimeout(() => {
                userDetailsModal.classList.remove('hidden');
            }, 100);
        });
    }

    // Handle Edit User Form Submission - FIXED VERSION
    if (editUserForm) {
        editUserForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Get form data
            const formData = {
                id: currentUserId,
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
            fetch(`/users/${currentUserId}`, {
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
                });
        });
    }

    // Form Submission Handling for Add User
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Get form data
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const role_name = document.getElementById('role_name').value;

            // Get the actual role value (admin/student)
            const roleSelect = document.getElementById('role_name');
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
                    // Close the add user modal
                    if (addUserModal) {
                        addUserModal.classList.add('hidden');
                    }

                    // Show success message
                    if (successModal) {
                        // Update success message if needed
                        const successMessageElement = successModal.querySelector('p');
                        if (successMessageElement) {
                            successMessageElement.textContent = 'User added successfully!';
                        }

                        // Show success modal
                        successModal.classList.remove('hidden');
                    } else {
                        // Fallback to alert if modal not found
                        alert('User added successfully!');
                    }

                    // Refresh the page after a delay to show the new user
                    setTimeout(() => {
                        window.location.reload();
                    }, 2500);
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

    // Close all modals when Escape key is pressed
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            if (addUserModal) addUserModal.classList.add('hidden');
            if (userDetailsModal) userDetailsModal.classList.add('hidden');
            if (editUserModal) editUserModal.classList.add('hidden');
            if (successModal) successModal.classList.add('hidden');
        }
    });

    // Deactivate User Confirmation Modal
    if (deactivateBtn) {
        deactivateBtn.addEventListener('click', function () {
            deactivateConfirmModal.classList.remove('hidden');
        });
    }

    if (cancelDeactivateBtn) {
        cancelDeactivateBtn.addEventListener('click', function () {
            deactivateConfirmModal.classList.add('hidden');
        });
    }
    if (closeDeactivateModalBtn) {
        closeDeactivateModalBtn.addEventListener('click', function () {
            deactivateConfirmModal.classList.add('hidden');
        });
    }

    if (deactivateConfirmBackdrop) {
        deactivateConfirmBackdrop.addEventListener('click', function () {
            deactivateConfirmModal.classList.add('hidden');
        });
    }

    if (confirmDeactivateBtn) {
        confirmDeactivateBtn.addEventListener('click', function () {
            // Add your deactivation logic here
            console.log('Account deactivated');
            deactivateConfirmModal.classList.add('hidden');
            // Optionally close the user details modal and show a success message
            userDetailsModal.classList.add('hidden');
            // You might want to reload the page or update the UI
        });
    }

    // Email Confirmation Modal
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
        });
    }

    // Email input validation
    if (confirmEmailInput) {
        confirmEmailInput.addEventListener('input', function () {
            const isMatch = this.value === userEmailToDeactivate;

            // Update input styling
            if (this.value) {
                if (!isMatch) {
                    this.classList.add('border-red-500', 'ring-red-500');
                    emailError.classList.remove('hidden');
                    finalDeactivateBtn.disabled = true;
                } else {
                    this.classList.remove('border-red-500', 'ring-red-500');
                    emailError.classList.add('hidden');
                    finalDeactivateBtn.disabled = false;
                }
            } else {
                this.classList.remove('border-red-500', 'ring-red-500');
                emailError.classList.add('hidden');
                finalDeactivateBtn.disabled = true;
            }
        });
    }

    // Close email confirmation modal
    if (closeEmailConfirmBtn) {
        closeEmailConfirmBtn.addEventListener('click', function () {
            emailConfirmModal.classList.add('hidden');
        });
    }

    // Final deactivation handler
    if (finalDeactivateBtn) {
        finalDeactivateBtn.addEventListener('click', function () {
            if (confirmEmailInput.value === userEmailToDeactivate) {
                // Use the currentUserId variable that's already being tracked
                // This is set when opening the user details modal

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

                            // Hide the user details modal
                            userDetailsModal.classList.add('hidden');

                            // Show success notification
                            document.getElementById('successTitle').textContent = 'Account Successfully Deactivated';
                            document.getElementById('successMessage').textContent = 'The user account has been deactivated.';

                            // Show success modal
                            successModal.classList.remove('hidden');

                            // Refresh the page after a delay to update the user list
                            setTimeout(() => {
                                window.location.reload();
                            }, 2500);
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
                    })
                    .finally(() => {
                        // Reset button state
                        finalDeactivateBtn.disabled = false;
                        finalDeactivateBtn.textContent = 'Deactivate Account';
                    });
            }
        });
    }
});