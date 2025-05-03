// This script handles the functionality of the user management system for the super admin.
// It includes modals for adding, editing, and viewing user details, as well as form submission handling.
// Execute when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Add User Modal
    const addUserModal = document.getElementById('addUserModal');
    const addUserBtn = document.getElementById('addUserBtn');
    const closeAddUserModalBtn = document.getElementById('closeAddUserModalBtn');
    const addUserBackdrop = document.querySelector('.add-user-backdrop');

    // Role selection handling
    const roleSelect = document.getElementById('role_name');
    const actualRoleInput = document.getElementById('actual_role');
    
    // Update the hidden role value when role_name changes
    if (roleSelect) {
        roleSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const actualRole = selectedOption.getAttribute('data-role');
            if (actualRoleInput) {
                actualRoleInput.value = actualRole;
            }
        });
    }

    // User Details Modal
    const userDetailsModal = document.getElementById('userDetailsModal');
    const userDetailsRows = document.querySelectorAll('.user-details-row');
    const closeUserDetailsBtn = document.getElementById('closeUserDetailsBtn');
    const userDetailsBackdrop = document.querySelector('.user-details-backdrop');

    // Edit User Modal
    const editUserModal = document.getElementById('editUserModal');
    const editUserBtn = document.getElementById('editUserBtn');
    const closeEditModalBtn = document.getElementById('closeEditModalBtn');
    const editUserBackdrop = document.querySelector('.edit-user-backdrop');

    // Success Modal
    const successModal = document.getElementById('successModal');
    const closeSuccessModalBtn = document.getElementById('closeSuccessModalBtn');
    const successModalBackdrop = document.querySelector('.success-modal-backdrop');

    // Add User Modal Opening
    if (addUserBtn) {
        addUserBtn.addEventListener('click', function() {
            addUserModal.classList.remove('hidden');
        });
    }

    // Add User Modal Closing
    if (closeAddUserModalBtn) {
        closeAddUserModalBtn.addEventListener('click', function() {
            addUserModal.classList.add('hidden');
        });
    }

    if (addUserBackdrop) {
        addUserBackdrop.addEventListener('click', function() {
            addUserModal.classList.add('hidden');
        });
    }

    // Form Submission Handling
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const role_name = document.getElementById('role_name').value;
            
            // Get the actual role value (admin/student)
            const roleSelect = document.getElementById('role_name');
            const selectedOption = roleSelect.options[roleSelect.selectedIndex];
            const role = selectedOption.getAttribute('data-role');
            
            console.log("Form submission values:", { 
                username, 
                email, 
                role_name, 
                role
            });

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

            // Send the form data via fetch API
            fetch('/users', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(JSON.stringify(data));
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log("Success response:", data);
                
                // Close the add user modal
                addUserModal.classList.add('hidden');

                // Show success message
                if (successModal) {
                    // Update success message if needed
                    const successMessageElement = successModal.querySelector('p');
                    if (successMessageElement) {
                        successMessageElement.textContent = 'User added successfully!';
                    }

                    // Show success modal
                    successModal.classList.remove('hidden');

                    // Refresh the page after a delay to show the new user
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                
                let errorData;
                try {
                    errorData = JSON.parse(error.message);
                } catch (e) {
                    errorData = { message: 'An error occurred while adding the user.' };
                }

                // Handle validation errors
                if (errorData.errors) {
                    // Display the first error
                    const firstErrorKey = Object.keys(errorData.errors)[0];
                    alert(errorData.errors[firstErrorKey][0]);
                } else {
                    // Display general error
                    alert(errorData.message || 'An error occurred while adding the user.');
                }
            });
        });
    }

    // User Details Modal
    userDetailsRows.forEach(row => {
        row.addEventListener('click', function() {
            const userData = JSON.parse(this.getAttribute('data-user'));

            // Fill user details in the modal
            document.getElementById('userUsername').textContent = userData.username;
            document.getElementById('userEmail').textContent = userData.email;
            document.getElementById('userRole').textContent = userData.role_name;
            
            // Show the modal
            userDetailsModal.classList.remove('hidden');
        });
    });

    // Close User Details Modal
    if (closeUserDetailsBtn) {
        closeUserDetailsBtn.addEventListener('click', function() {
            userDetailsModal.classList.add('hidden');
        });
    }

    if (userDetailsBackdrop) {
        userDetailsBackdrop.addEventListener('click', function() {
            userDetailsModal.classList.add('hidden');
        });
    }

    // Edit User Modal
    if (editUserBtn) {
        editUserBtn.addEventListener('click', function() {
            // Get current user data
            const username = document.getElementById('userUsername').textContent;
            const email = document.getElementById('userEmail').textContent;
            const role = document.getElementById('userRole').textContent;
            
            // Fill the edit form
            document.getElementById('editUsername').value = username;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRole').value = role;
            
            // Hide user details modal first
            userDetailsModal.classList.add('hidden');
            
            // Show edit modal after a small delay to prevent overlap
            setTimeout(() => {
                editUserModal.classList.remove('hidden');
            }, 100);
        });
    }

    // Close Edit Modal
    if (closeEditModalBtn) {
        closeEditModalBtn.addEventListener('click', function() {
            editUserModal.classList.add('hidden');
            setTimeout(() => {
                userDetailsModal.classList.remove('hidden');
            }, 100);
        });
    }

    if (editUserBackdrop) {
        editUserBackdrop.addEventListener('click', function() {
            editUserModal.classList.add('hidden');
            setTimeout(() => {
                userDetailsModal.classList.remove('hidden');
            }, 100);
        });
    }

    // Success Modal
    if (closeSuccessModalBtn) {
        closeSuccessModalBtn.addEventListener('click', function() {
            successModal.classList.add('hidden');
        });
    }

    if (successModalBackdrop) {
        successModalBackdrop.addEventListener('click', function() {
            successModal.classList.add('hidden');
        });
    }

    // Close all modals when Escape key is pressed
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            addUserModal.classList.add('hidden');
            userDetailsModal.classList.add('hidden');
            editUserModal.classList.add('hidden');
            successModal.classList.add('hidden');
        }
    });
});