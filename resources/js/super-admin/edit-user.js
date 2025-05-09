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

    // Set up modal functionality
    if (window.setupModalClose) {
        window.setupModalClose(editUserModal, '#closeEditModalBtn', '.edit-user-backdrop');
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

    // Handle Edit User Form Submission
    if (editUserForm) {
        editUserForm.addEventListener('submit', function (e) {
            e.preventDefault();

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
            });
        });
    }
});
