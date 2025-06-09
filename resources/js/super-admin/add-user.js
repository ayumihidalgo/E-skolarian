// User Adding Functionality with Multi-Step Form and Enhanced Validation
document.addEventListener('DOMContentLoaded', function () {
    // Add User Modal Elements
    const addUserBtn = document.getElementById('addUserBtn');
    const addUserModal = document.getElementById('addUserModal');
    const closeAddUserBtn = document.getElementById('closeAddUserBtn');
    const closeAddUserModalBtn = document.getElementById('closeAddUserModalBtn');
    const successModal = document.getElementById('successModal');
    const closeConfirmModal = document.getElementById('closeConfirmModal');
    const cancelCloseBtn = document.getElementById('cancelCloseBtn');
    const confirmCloseBtn = document.getElementById('confirmCloseBtn');

    // Step navigation buttons
    const continueToNextBtn = document.getElementById('continueToNextBtn');
    const backToRoleBtn = document.getElementById('backToRoleBtn');
    const backToRoleFromAdminBtn = document.getElementById('backToRoleFromAdminBtn');
    const submitStudentBtn = document.getElementById('submitStudentBtn');
    const submitAdminBtn = document.getElementById('submitAdminBtn');

    // Step containers
    const stepRole = document.getElementById('step-role');
    const stepStudent = document.getElementById('step-student');
    const stepAdmin = document.getElementById('step-admin');
    
    // Custom role elements
    const customRoleContainer = document.getElementById('custom-role-container');

    // Processing flag to track form submission state
    let isProcessing = false;
    let currentStep = 'role'; // Initial step

    // Form input elements
    const roleSelect = document.getElementById('role_name');
    const actualRoleInput = document.getElementById('actual_role');
    const finalRoleNameInput = document.getElementById('final_role_name');
    const customRoleName = document.getElementById('custom_role_name');
    const customRoleTypeRadios = document.querySelectorAll('input[name="custom_role_type"]');
    
    // Student form elements
    const organizationNameInput = document.getElementById('organization_name');
    const organizationAcronymInput = document.getElementById('organization_acronym');
    const studentEmailInput = document.getElementById('student_email');
    
    // Admin form elements
    const usernameInput = document.getElementById('username');
    const adminEmailInput = document.getElementById('admin_email');
    
    const addUserForm = document.getElementById('addUserForm');

    // Validation feedback elements - Create error elements dynamically
    const usernameError = document.createElement('p');
    usernameError.id = 'usernameError';
    usernameError.className = 'text-red-600 text-xs mt-1 hidden';
    
    const adminEmailError = document.createElement('p');
    adminEmailError.id = 'adminEmailError';
    adminEmailError.className = 'text-red-600 text-xs mt-1 hidden';
    
    const studentEmailError = document.createElement('p');
    studentEmailError.id = 'studentEmailError';
    studentEmailError.className = 'text-red-600 text-xs mt-1 hidden';
    
    const organizationNameError = document.createElement('p');
    organizationNameError.id = 'organizationNameError';
    organizationNameError.className = 'text-red-600 text-xs mt-1 hidden';
    
    const organizationAcronymError = document.createElement('p');
    organizationAcronymError.id = 'organizationAcronymError';
    organizationAcronymError.className = 'text-red-600 text-xs mt-1 hidden';
    
    const roleError = document.createElement('p');
    roleError.id = 'roleError';
    roleError.className = 'text-red-600 text-xs mt-1 hidden';
    
    const customRoleNameError = document.createElement('p');
    customRoleNameError.id = 'customRoleNameError';
    customRoleNameError.className = 'text-red-600 text-xs mt-1 hidden';
    
    const roleTypeError = document.createElement('p');
    roleTypeError.id = 'roleTypeError';
    roleTypeError.className = 'text-red-600 text-xs mt-1 hidden';

    // Insert error elements after inputs
    if (usernameInput) {
        usernameInput.parentNode.insertBefore(usernameError, usernameInput.nextSibling);
    }
    if (adminEmailInput) {
        adminEmailInput.parentNode.insertBefore(adminEmailError, adminEmailInput.nextSibling);
    }
    if (studentEmailInput) {
        studentEmailInput.parentNode.insertBefore(studentEmailError, studentEmailInput.nextSibling);
    }
    if (organizationNameInput) {
        organizationNameInput.parentNode.insertBefore(organizationNameError, organizationNameInput.nextSibling);
    }
    if (organizationAcronymInput) {
        organizationAcronymInput.parentNode.insertBefore(organizationAcronymError, organizationAcronymInput.nextSibling);
    }
    if (roleSelect) {
        roleSelect.parentNode.insertBefore(roleError, roleSelect.nextSibling);
    }
    if (customRoleName) {
        customRoleName.parentNode.insertBefore(customRoleNameError, customRoleName.nextSibling);
    }
    
    // Insert role type error after custom role type container
    const customRoleTypeContainer = document.querySelector('input[name="custom_role_type"]')?.closest('.space-y-2');
    if (customRoleTypeContainer) {
        customRoleTypeContainer.appendChild(roleTypeError);
    }

    // Function to check if form has unsaved changes
    function hasUnsavedChanges() {
        // Check role step
        if (roleSelect?.value || customRoleName?.value) {
            return true;
        }
        
        // Check student organization step
        if (organizationNameInput?.value.trim() || 
            organizationAcronymInput?.value.trim() || 
            studentEmailInput?.value.trim()) {
            return true;
        }
        
        // Check admin step
        if (usernameInput?.value.trim() || adminEmailInput?.value.trim()) {
            return true;
        }
        
        return false;
    }

    // Function to handle close attempt
    function handleCloseAttempt() {
        if (isProcessing) {
            // Disable cursor and prevent closing if processing
            document.body.style.cursor = 'not-allowed';
            return;
        }

        if (hasUnsavedChanges()) {
            closeConfirmModal.classList.remove('hidden');
        } else {
            addUserModal.classList.add('hidden');
        }
    }

    // Function to reset the entire form
    function resetForm() {
        // Reset all form fields
        if (addUserForm) {
            addUserForm.reset();
        }
        
        // Reset validation states
        resetValidationState();
        
        // Hide custom role container
        if (customRoleContainer) {
            customRoleContainer.classList.add('hidden');
        }
        
        // Reset to first step
        showStep('role');
        
        // Set default organization dropdown to academic organizations
        updateOrganizationDropdown('academic');
        
        // Disable continue button
        if (continueToNextBtn) {
            continueToNextBtn.disabled = true;
            continueToNextBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
        
        // Disable submit buttons
        if (submitStudentBtn) {
            submitStudentBtn.disabled = true;
            submitStudentBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
        
        if (submitAdminBtn) {
            submitAdminBtn.disabled = true;
            submitAdminBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    // Set up modal functionality
    if (window.setupModalClose) {
        window.setupModalClose(addUserModal, '#closeAddUserBtn');
    }

    // Open Add User Modal
    if (addUserBtn && addUserModal) {
        addUserBtn.addEventListener('click', function () {
            // Show modal immediately
            addUserModal.classList.remove('hidden');
            
            // Reset form and validation states
            resetForm();
            
            // Set up styled dropdown for organization select
            setupStyledDropdown();
            
            // Fetch roles asynchronously without blocking modal display
            fetchExistingRoles().then(existingRoles => {
                updateRoleOptions(existingRoles);
            }).catch(error => {
                console.error('Error fetching roles:', error);
            });
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
            resetForm();
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

    // Function to show a specific step
    function showStep(step) {
        // Hide all steps
        stepRole.classList.add('hidden');
        stepStudent.classList.add('hidden');
        stepAdmin.classList.add('hidden');
        
        // Show requested step
        if (step === 'role') {
            stepRole.classList.remove('hidden');
        } else if (step === 'student') {
            stepStudent.classList.remove('hidden');
        } else if (step === 'admin') {
            stepAdmin.classList.remove('hidden');
        }
        
        currentStep = step;
    }

    // Step navigation handlers
    if (continueToNextBtn) {
        continueToNextBtn.addEventListener('click', async function() {
            // Get role type
            let roleType;
            
            // Check if custom role is selected
            if (roleSelect.value === 'custom_role') {
                const checkedRadio = document.querySelector('input[name="custom_role_type"]:checked');
                if (checkedRadio) {
                    roleType = checkedRadio.value;
                    
                    // Set the final role name to the custom role name
                    if (customRoleName) {
                        finalRoleNameInput.value = customRoleName.value.trim();
                    }
                    
                    // Show student step if student role type is selected
                    if (roleType === 'student') {
                        // Show new org fields, hide existing org fields
                        const existingOrgFields = document.getElementById('existing-org-fields');
                        const newOrgFields = document.getElementById('new-org-fields');
                        
                        if (existingOrgFields) existingOrgFields.classList.add('hidden');
                        if (newOrgFields) newOrgFields.classList.remove('hidden');
                        
                        // Enable acronym input for new organizations
                        if (organizationAcronymInput) {
                            organizationAcronymInput.value = '';
                            organizationAcronymInput.readOnly = false;
                            organizationAcronymInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
                        }
                        
                        showStep('student');
                    } else if (roleType === 'admin') {
                        showStep('admin');
                    }
                }
            } else {
                // Handle existing roles...
                const selectedOption = roleSelect.options[roleSelect.selectedIndex];
                roleType = selectedOption.getAttribute('data-role');
                finalRoleNameInput.value = roleSelect.value;
                
                if (roleType === 'student') {
                    const existingOrgFields = document.getElementById('existing-org-fields');
                    const newOrgFields = document.getElementById('new-org-fields');
                    
                    if (existingOrgFields) existingOrgFields.classList.remove('hidden');
                    if (newOrgFields) newOrgFields.classList.add('hidden');
                    
                    showStep('student');
                } else if (roleType === 'admin') {
                    showStep('admin');
                }
            }
            
            // Store role value in hidden field
            actualRoleInput.value = roleType;
        });
    }

    // Back button handlers
    if (backToRoleBtn) {
        backToRoleBtn.addEventListener('click', function() {
            showStep('role');
        });
    }
    
    if (backToRoleFromAdminBtn) {
        backToRoleFromAdminBtn.addEventListener('click', function() {
            showStep('role');
        });
    }

    // Role selection change handler
    if (roleSelect) {
    roleSelect.addEventListener('change', function () {
        // Reset errors
        roleError.classList.add('hidden');
        roleSelect.classList.remove('border-red-500');
        
        const customRoleContainer = document.getElementById('custom-role-container');
        
        // Check if custom role is selected
        if (this.value === 'custom_role') {
            if (customRoleContainer) {
                customRoleContainer.classList.remove('hidden');
            }
            
            // Enable continue button only when both name and type are filled
            updateContinueButton();
        } else {
            if (customRoleContainer) {
                customRoleContainer.classList.add('hidden');
            }
            
            // Clear custom role fields
            const customRoleName = document.getElementById('custom_role_name');
            if (customRoleName) {
                customRoleName.value = '';
            }
            
            document.querySelectorAll('input[name="custom_role_type"]').forEach(radio => {
                radio.checked = false;
            });
            
            // Update continue button state
            updateContinueButton();
        }
        
        // Update organization dropdown based on selected role
        if (this.value === 'Academic Organization') {
            updateOrganizationDropdown('academic');
        } else if (this.value === 'Non-Academic Organization') {
            updateOrganizationDropdown('non-academic');
        }
    });
}
    
    // Custom role name input handler
    if (customRoleName) {
        let customRoleTimeout;
        customRoleName.addEventListener('input', function() {
            // Update regex to allow letters, spaces and hyphens
            this.value = this.value
                .replace(/^\s+/, '')
                .replace(/[^a-zA-Z\s-]/g, '') // Changed regex to allow hyphens
                .replace(/\s+/g, ' ');

            // Clear previous timeout
            clearTimeout(customRoleTimeout);

            // Set new timeout to avoid too many requests
            customRoleTimeout = setTimeout(() => {
                validateCustomRole().then(() => {
                    updateContinueButton();
                });
            }, 300);
        });
        
        customRoleName.addEventListener('blur', function() {
            this.value = this.value.trim();
            validateCustomRole().then(() => {
                updateContinueButton();
            });
        });
    }
    
    // Custom role type radio button handlers
    customRoleTypeRadios.forEach(radio => {
    radio.addEventListener('change', function() {
        roleTypeError.classList.add('hidden');
        updateContinueButton(); // Call updateContinueButton directly
    });
});
    
    // Organization type radio button handlers (for custom student roles)
    const customOrgTypeRadios = document.querySelectorAll('input[name="custom_org_type"]');
    customOrgTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const orgTypeError = document.getElementById('orgTypeError');
            if (orgTypeError) {
                orgTypeError.classList.add('hidden');
            }
            
            // Update continue button validation
            validateCustomRole().then(() => {
                updateContinueButton();
            });
        });
    });

    // Organization name dropdown event listeners
    if (organizationNameInput) {
        organizationNameInput.addEventListener('change', function() {
            organizationNameError.classList.add('hidden');
            organizationNameInput.classList.remove('border-red-500');
            
            // Auto-fill acronym based on selected organization and make it read-only
            const selectedOrganization = this.value;
            if (selectedOrganization && organizationAcronymInput) {
                // Extract acronym from the selected option text
                const selectedOption = organizationNameInput.options[organizationNameInput.selectedIndex];
                const acronymMatch = selectedOption.text.match(/\(([A-Z]+)\)$/);
                
                if (acronymMatch && acronymMatch[1]) {
                    organizationAcronymInput.value = acronymMatch[1];
                    organizationAcronymInput.readOnly = true;
                    organizationAcronymInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                }
            }
            
            validateOrganizationName().then(() => {
                updateStudentSubmitButton();
            });
        });
        
        // Add org type toggle buttons
        const academicOrgBtn = document.getElementById('academic-org-btn');
        const nonAcademicOrgBtn = document.getElementById('non-academic-org-btn');
        
        if (academicOrgBtn && nonAcademicOrgBtn) {
            academicOrgBtn.addEventListener('click', function() {
                updateOrganizationDropdown('academic');
                academicOrgBtn.classList.add('bg-blue-600', 'text-white');
                academicOrgBtn.classList.remove('bg-gray-200', 'text-gray-700');
                nonAcademicOrgBtn.classList.add('bg-gray-200', 'text-gray-700');
                nonAcademicOrgBtn.classList.remove('bg-blue-600', 'text-white');
            });
            
            nonAcademicOrgBtn.addEventListener('click', function() {
                updateOrganizationDropdown('non-academic');
                nonAcademicOrgBtn.classList.add('bg-blue-600', 'text-white');
                nonAcademicOrgBtn.classList.remove('bg-gray-200', 'text-gray-700');
                academicOrgBtn.classList.add('bg-gray-200', 'text-gray-700');
                academicOrgBtn.classList.remove('bg-blue-600', 'text-white');
            });
        }
    }

    // Organization acronym input handler
    if (organizationAcronymInput) {
        organizationAcronymInput.addEventListener('input', function() {
            // Remove spaces and non-letter characters, convert to uppercase
            this.value = this.value.replace(/[^a-zA-Z]/g, '').toUpperCase();
            
            validateOrganizationAcronym().then(() => {
                updateStudentSubmitButton();
            });
        });
        
        organizationAcronymInput.addEventListener('blur', function() {
            validateOrganizationAcronym().then(() => {
                updateStudentSubmitButton();
            });
        });
    }

    // Student email input handler
    if (studentEmailInput) {
        let studentEmailTimeout;
        studentEmailInput.addEventListener('input', function() {
            // Remove all spaces automatically
            this.value = this.value.replace(/\s+/g, '');

            // Clear previous timeout
            clearTimeout(studentEmailTimeout);

            // Set new timeout to avoid too many requests
            studentEmailTimeout = setTimeout(() => {
                validateStudentEmail().then(() => {
                    updateStudentSubmitButton();
                });
            }, 300);
        });

        studentEmailInput.addEventListener('blur', function() {
            validateStudentEmail().then(() => {
                updateStudentSubmitButton();
            });
        });
    }

    // Username input handler
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
                validateUsername().then(() => {
                    updateAdminSubmitButton();
                });
            }, 300);
        });

        usernameInput.addEventListener('blur', function() {
            this.value = this.value.trim();
            validateUsername().then(() => {
                updateAdminSubmitButton();
            });
        });
    }

    // Admin email input handler
    if (adminEmailInput) {
        let adminEmailTimeout;
        adminEmailInput.addEventListener('input', function() {
            // Remove all spaces automatically
            this.value = this.value.replace(/\s+/g, '');

            // Clear previous timeout
            clearTimeout(adminEmailTimeout);

            // Set new timeout to avoid too many requests
            adminEmailTimeout = setTimeout(() => {
                validateAdminEmail().then(() => {
                    updateAdminSubmitButton();
                });
            }, 300);
        });

        adminEmailInput.addEventListener('blur', function() {
            validateAdminEmail().then(() => {
                updateAdminSubmitButton();
            });
        });
    }

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

        // Reset submit button states
        if (continueToNextBtn) {
            continueToNextBtn.disabled = false;
            continueToNextBtn.innerHTML = 'Continue';
        }
        
        if (submitStudentBtn) {
            submitStudentBtn.disabled = false;
            submitStudentBtn.innerHTML = 'Add User';
        }
        
        if (submitAdminBtn) {
            submitAdminBtn.disabled = false;
            submitAdminBtn.innerHTML = 'Add User';
        }
    }

    function resetValidationState() {
        // Reset all error messages
        const errorElements = document.querySelectorAll('[id$="Error"]');
        errorElements.forEach(element => {
            element.classList.add('hidden');
        });
        
        // Reset error borders
        const inputElements = document.querySelectorAll('input, select');
        inputElements.forEach(element => {
            element.classList.remove('border-red-500');
        });
    }

    // Function to update continue button state
    function updateContinueButton() {
    if (!continueToNextBtn) return;
    
    // Check if we can proceed to the next step
    if (roleSelect.value === 'custom_role') {
        // Custom role - need to check custom role fields
        const roleNameValid = customRoleName.value.trim() !== '';
        const roleTypeRadio = document.querySelector('input[name="custom_role_type"]:checked');
        const roleTypeValid = roleTypeRadio !== null;

        // Debug logs
        console.log('Role name valid:', roleNameValid);
        console.log('Role type valid:', roleTypeValid);
        console.log('Selected role type:', roleTypeRadio?.value);
        
        const allValid = roleNameValid && roleTypeValid;
        
        continueToNextBtn.disabled = !allValid;
        continueToNextBtn.classList.toggle('opacity-50', !allValid);
        continueToNextBtn.classList.toggle('cursor-not-allowed', !allValid);
        
        // Debug log
        console.log('Continue button enabled:', allValid);
    } else {
        // Standard role - just check if a valid role is selected
        const roleValid = roleSelect.value !== '';
        
        continueToNextBtn.disabled = !roleValid;
        continueToNextBtn.classList.toggle('opacity-50', !roleValid);
        continueToNextBtn.classList.toggle('cursor-not-allowed', !roleValid);
    }
}
    
    // Update the updateStudentSubmitButton function
function updateStudentSubmitButton() {
    if (!submitStudentBtn) return;
    
    const isNewRole = roleSelect.value === 'custom_role';
    const newOrgFields = document.getElementById('new-org-fields');
    const existingOrgFields = document.getElementById('existing-org-fields');
    
    let organizationField;
    if (isNewRole && newOrgFields && !newOrgFields.classList.contains('hidden')) {
        organizationField = document.getElementById('new_organization_name');
    } else if (existingOrgFields && !existingOrgFields.classList.contains('hidden')) {
        organizationField = document.getElementById('organization_name');
    }
    
    // Debug logs
    console.log('Is new role:', isNewRole);
    console.log('Organization field:', organizationField?.value);
    console.log('Acronym:', organizationAcronymInput?.value);
    console.log('Email:', studentEmailInput?.value);
    
    const isValid = organizationField &&
                   organizationField.value.trim() !== '' &&
                   organizationAcronymInput &&
                   organizationAcronymInput.value.trim() !== '' &&
                   studentEmailInput &&
                   studentEmailInput.value.trim() !== '' &&
                   !organizationField.classList.contains('border-red-500') &&
                   !organizationAcronymInput.classList.contains('border-red-500') &&
                   !studentEmailInput.classList.contains('border-red-500');
    
    console.log('Form is valid:', isValid);
    
    if (submitStudentBtn) {
        submitStudentBtn.disabled = !isValid;
        submitStudentBtn.classList.toggle('opacity-50', !isValid);
        submitStudentBtn.classList.toggle('cursor-not-allowed', !isValid);
    }
}

// Add input event listeners for the new organization fields
const newOrganizationNameInput = document.getElementById('new_organization_name');
if (newOrganizationNameInput) {
    newOrganizationNameInput.addEventListener('input', () => {
        validateOrganizationName().then(() => {
            updateStudentSubmitButton();
        });
    });
}
    
    // Function to update admin submit button state
    function updateAdminSubmitButton() {
        if (!submitAdminBtn) return;
        
        const isValid = adminEmailInput.value.trim() !== '' &&
                       !adminEmailInput.classList.contains('border-red-500');
                       
        submitAdminBtn.disabled = !isValid;
        submitAdminBtn.classList.toggle('opacity-50', !isValid);
        submitAdminBtn.classList.toggle('cursor-not-allowed', !isValid);
    }

    // Function to update role options based on existing roles
    function updateRoleOptions(existingRoles) {
        if (!roleSelect) return;

        const restrictedRoles = [
            'Office of the Student Services',
            'Office of the Academic Services',
            'Office of the Administrative Services',
            'Office of the Campus Director'
        ];

        const options = Array.from(roleSelect.options);

        options.forEach(option => {
            const roleName = option.value;
            if (roleName === 'custom_role' || roleName === '') return; // Skip custom role and placeholder
            
            const isRestricted = restrictedRoles.includes(roleName) && existingRoles.includes(roleName);
            option.disabled = isRestricted;
            option.style.display = isRestricted ? 'none' : '';
        });
    }

    // Email existence check function
    async function checkEmailExists(email) {
        try {
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
        } catch (error) {
            console.error('Error checking email:', error);
            return false;
        }
    }
    
    async function checkUsernameExists(username) {
        try {
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
        } catch (error) {
            console.error('Error checking username:', error);
            return false;
        }
    }
    
    async function checkOrganizationExists(organization) {
        try {
            const response = await fetch('/check-organization', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ name: organization.toLowerCase() })
            });
            const data = await response.json();
            return data.exists;
        } catch (error) {
            console.error('Error checking organization:', error);
            return false;
        }
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

    // Function to update organization dropdown options based on type
    async function updateOrganizationDropdown(orgType) {
        if (!organizationNameInput) return;
        
        try {
            // Fetch existing organizations
            const response = await fetch('/check-organizations', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();
            const existingOrgs = data.existingOrganizations || [];

            // Clear existing options except the first one (placeholder)
            while (organizationNameInput.options.length > 1) {
                organizationNameInput.remove(1);
            }
            
            // Define academic and non-academic organizations
            const academicOrgs = [
                { name: 'Association of Competent and Aspiring Psychologists', acronym: 'ACAP' },
                { name: 'Association of Electronics and Communications Engineering Students', acronym: 'AECES' },
                { name: 'Eligible League of Information Technology Enthusiast', acronym: 'ELITE' },
                { name: 'Guild of Imporous and Valuable Educators', acronym: 'GIVE' },
                { name: 'Junior Executives of Human Resources Association', acronym: 'JEHRA' },
                { name: 'Junior Marketing Association of the Philippines', acronym: 'JMAP' },
                { name: 'Junior Philippine Institute of Accountants', acronym: 'JPIA' },
                { name: 'Philippine Institute of Industrial Engineers', acronym: 'PIIE' }
            ];
            
            const nonAcademicOrgs = [
                { name: 'Artist Guild Dance Squad', acronym: 'AGDS' },
                { name: 'Office of the Student Council', acronym: 'OSC' },
                { name: 'PUP SRC Chorale', acronym: 'CHORALE' },
                { name: "Supreme Innovators' Guild for Mathematics Advancement", acronym: 'SIGMA' },
                { name: 'Transformation Advocates through Purpose-driven and Noble Objectives Toward Community Holism', acronym: 'TAPNOTCH' }
            ];

            // Select organizations based on type
            let orgsToShow = orgType === 'academic' ? academicOrgs : nonAcademicOrgs;

            // Add filtered organizations directly to dropdown
            orgsToShow.forEach(org => {
                // Check if organization already exists
                if (!existingOrgs.includes(org.name.toLowerCase())) {
                    const option = document.createElement('option');
                    option.value = org.name; // Keep full name as value
                    option.textContent = truncateOrgName(org.name, org.acronym); // Truncated display text
                    option.title = `${org.name} (${org.acronym})`; // Full name on hover
                    organizationNameInput.appendChild(option);
                }
            });

            // Add CSS to style the dropdown options
            const style = document.createElement('style');
            style.textContent = `
                #organization_name option {
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    max-width: 100%;
                }
            `;
            document.head.appendChild(style);

            // Reset acronym field
            if (organizationAcronymInput) {
                organizationAcronymInput.value = '';
            }

        } catch (error) {
            console.error('Error updating organization dropdown:', error);
        }
    }

    // Add this helper function to truncate text
function truncateOrgName(name, acronym, maxLength = 55) {
    const suffix = ` (${acronym})`;
    if (name.length + suffix.length <= maxLength) return `${name}${suffix}`;
    return `${name.substring(0, maxLength - suffix.length - 3)}...${suffix}`;
}

    // Function to setup styled dropdown
    function setupStyledDropdown() {
        // This function can be used to set up any additional styling for dropdowns
        // Currently handled in updateOrganizationDropdown
    }

    // VALIDATION FUNCTIONS

    // Role validation
    async function validateRoleSelection() {
        roleError.classList.add('hidden');
        roleSelect.classList.remove('border-red-500');

        if (roleSelect.value === '') {
            showRoleError('Please select a role');
            return false;
        }

        const restrictedRoles = [
            'Office of the Student Services',
            'Office of the Academic Services',
            'Office of the Administrative Services',
            'Office of the Campus Director'
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

    // Custom role validation
    async function validateCustomRole() {
        customRoleNameError.classList.add('hidden');
        customRoleName.classList.remove('border-red-500');
        
        const customRole = customRoleName.value.trim();
        const MAX_ROLE_LENGTH = 50;

        if (customRole === '') {
            showCustomRoleNameError('Role name cannot be empty');
            return false;
        }

        if (customRole.startsWith(' ') || customRole.startsWith('-')) {
            showCustomRoleNameError('Role name cannot start with a space or hyphen');
            return false;
        }

        if (customRole.length < 3) {
            showCustomRoleNameError('Role name must be at least 3 characters');
            return false;
        }

        if (customRole.length > MAX_ROLE_LENGTH) {
            showCustomRoleNameError(`Role name must be less than ${MAX_ROLE_LENGTH} characters`);
            return false;
        }

        if (!/^[a-zA-Z\s-]+$/.test(customRole)) { // Updated regex to allow hyphens
            showCustomRoleNameError('Role name can only contain letters, spaces, and hyphens');
            return false;
        }

        return true;
    }

    function showCustomRoleNameError(message) {
        customRoleNameError.textContent = message;
        customRoleNameError.classList.remove('hidden');
        customRoleName.classList.add('border-red-500');
    }

    async function validateNewRoleFields() {
    let isValid = true;

    // Validate custom role name
    if (customRoleName) {
        const roleName = customRoleName.value.trim();
        if (!roleName) {
            customRoleNameError.textContent = 'Role name is required';
            customRoleNameError.classList.remove('hidden');
            customRoleName.classList.add('border-red-500');
            isValid = false;
        }
    }

    // Validate role type
    const roleTypeRadio = document.querySelector('input[name="custom_role_type"]:checked');
    if (!roleTypeRadio) {
        roleTypeError.textContent = 'Please select a role type';
        roleTypeError.classList.remove('hidden');
        isValid = false;
    }

    return isValid;
}
    
    // Organization name validation
    async function validateOrganizationName() {
        if (!organizationNameInput) return true;
        
        organizationNameError.classList.add('hidden');
        organizationNameInput.classList.remove('border-red-500');
        
        if (organizationNameInput.value === '') {
            showOrganizationNameError('Please select an organization');
            return false;
        }
        
        // Check if organization already exists in the system
        const exists = await checkOrganizationExists(organizationNameInput.value);
        if (exists) {
            showOrganizationNameError('This organization already exists in the system');
            return false;
        }
        
        return true;
    }
    
    function showOrganizationNameError(message) {
        organizationNameError.textContent = message;
        organizationNameError.classList.remove('hidden');
        organizationNameInput.classList.add('border-red-500');
    }

    // Organization acronym validation
    async function validateOrganizationAcronym() {
        if (!organizationAcronymInput) return true;
        
        organizationAcronymError.classList.add('hidden');
        organizationAcronymInput.classList.remove('border-red-500');
        
        const acronym = organizationAcronymInput.value.trim();
        
        if (acronym === '') {
            showOrganizationAcronymError('Organization acronym cannot be empty');
            return false;
        }
        
        if (acronym.length < 2) {
            showOrganizationAcronymError('Acronym must be at least 2 characters');
            return false;
        }
        
        if (acronym.length > 10) {
            showOrganizationAcronymError('Acronym must be less than 10 characters');
            return false;
        }
        
        if (!/^[A-Z]+$/.test(acronym)) {
            showOrganizationAcronymError('Acronym can only contain uppercase letters');
            return false;
        }
        
        return true;
    }
    
    function showOrganizationAcronymError(message) {
        organizationAcronymError.textContent = message;
        organizationAcronymError.classList.remove('hidden');
        organizationAcronymInput.classList.add('border-red-500');
    }

    // Student email validation
    async function validateStudentEmail() {
        const email = studentEmailInput.value.trim();
        const MAX_EMAIL_LENGTH = 50;
        const ALLOWED_DOMAINS = ['@gmail.com', '@yahoo.com', '@iskolarngbayan.pup.edu.ph'];

        // Reset error state
        studentEmailError.classList.add('hidden');
        studentEmailInput.classList.remove('border-red-500');

        // Validation checks
        if (email === '') {
            showStudentEmailError('Email cannot be empty');
            return false;
        }

        if (email.length > MAX_EMAIL_LENGTH) {
            showStudentEmailError(`Email must be less than ${MAX_EMAIL_LENGTH} characters`);
            return false;
        }

        // Check for valid email format
        if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email)) {
            showStudentEmailError('Please enter a valid email address');
            return false;
        }

        // Check for allowed domains
        const hasValidDomain = ALLOWED_DOMAINS.some(domain => 
            email.toLowerCase().endsWith(domain)
        );
        
        if (!hasValidDomain) {
            showStudentEmailError('Please use a valid @gmail.com, @yahoo.com, or @iskolarngbayan.pup.edu.ph email address');
            return false;
        }

        // Check for number instead of letter in domain (.c0m instead of .com)
        if (/\.c0m$|\.c0m@/.test(email.toLowerCase())) {
            showStudentEmailError('Invalid domain format');
            return false;
        }

        // Check if email already exists
        try {
            const exists = await checkEmailExists(email);
            if (exists) {
                showStudentEmailError('This email already exists');
                return false;
            }
        } catch (error) {
            showStudentEmailError('Error checking email availability');
            return false;
        }

        return true;
    }

    function showStudentEmailError(message) {
        studentEmailError.textContent = message;
        studentEmailError.classList.remove('hidden');
        studentEmailInput.classList.add('border-red-500');
    }

    // Username validation
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

    function showUsernameError(message) {
        usernameError.textContent = message;
        usernameError.classList.remove('hidden');
        usernameInput.classList.add('border-red-500');
    }

    // Admin email validation
    async function validateAdminEmail() {
        const email = adminEmailInput.value.trim();
        const MAX_EMAIL_LENGTH = 100;
        const ALLOWED_DOMAINS = ['@gmail.com', '@yahoo.com', '@iskolarngbayan.pup.edu.ph'];

        // Reset error state
        adminEmailError.classList.add('hidden');
        adminEmailInput.classList.remove('border-red-500');

        // Validation checks
        if (email === '') {
            showAdminEmailError('Email cannot be empty');
            return false;
        }

        if (email.length > MAX_EMAIL_LENGTH) {
            showAdminEmailError(`Email must be less than ${MAX_EMAIL_LENGTH} characters`);
            return false;
        }

        // Check for valid email format
        if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email)) {
            showAdminEmailError('Please enter a valid email address');
            return false;
        }

        // Check for allowed domains
        const hasValidDomain = ALLOWED_DOMAINS.some(domain => 
            email.toLowerCase().endsWith(domain)
        );
        
        if (!hasValidDomain) {
            showAdminEmailError('Please use a valid @gmail.com, @yahoo.com, or @iskolarngbayan.pup.edu.ph email address');
            return false;
        }

        // Check for number instead of letter in domain (.c0m instead of .com)
        if (/\.c0m$|\.c0m@/.test(email.toLowerCase())) {
            showAdminEmailError('Invalid domain format');
            return false;
        }

        // Check if email already exists
        try {
            const exists = await checkEmailExists(email);
            if (exists) {
                showAdminEmailError('This email already exists');
                return false;
            }
        } catch (error) {
            showAdminEmailError('Error checking email availability');
            return false;
        }

        return true;
    }

    function showAdminEmailError(message) {
        adminEmailError.textContent = message;
        adminEmailError.classList.remove('hidden');
        adminEmailInput.classList.add('border-red-500');
    }

    // FORM SUBMISSION HANDLERS

    // Student form submission
    if (submitStudentBtn) {
        submitStudentBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            if (isProcessing) return;

            try {
                isProcessing = true;
                document.body.style.cursor = 'not-allowed';
                    disableFormButtons(); // Disable all buttons
                
                // Determine which organization name field to use
                const isNewRole = roleSelect.value === 'custom_role';
                const organizationName = isNewRole ? 
                    document.getElementById('new_organization_name').value.trim() : 
                    organizationNameInput.value.trim();

                const formData = {
                    username: organizationName, // Use the correct organization name based on role type
                    email: studentEmailInput.value.trim().toLowerCase(),
                    password: Math.random().toString(36).slice(-8),
                    role: 'student',
                    role_name: finalRoleNameInput.value,
                    organization_acronym: organizationAcronymInput.value.trim(),
                    active: true,
                    is_new_organization: isNewRole, // Add flag for new organization
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                };

                console.log('Sending data:', formData);

                // Validate required fields
                if (!formData.username) {
                    throw new Error('Organization name is required');
                }
                if (!formData.email) {
                    throw new Error('Email is required');
                }
                if (!formData.organization_acronym) {
                    throw new Error('Organization acronym is required');
                }

                const response = await fetch('/users', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();
                console.log('Response:', data);

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to add user');
                }

                if (data.success) {
                    resetForm();
                    addUserModal.classList.add('hidden');
                    showSuccessModal();
                }

            } catch (error) {
                console.error('Error details:', error);
                handleSubmissionError(error);
            } finally {
                isProcessing = false;
                document.body.style.cursor = 'default';
                enableFormButtons(); // Re-enable all buttons
            }
        });
    }

    // Admin form submission
    if (submitAdminBtn) {
        submitAdminBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            if (isProcessing) return;

            try {
                isProcessing = true;
                submitAdminBtn.disabled = true;
                submitAdminBtn.innerHTML = 'Adding...';
                
                const email = adminEmailInput.value.trim().toLowerCase();
                // Extract username from email (remove @gmail.com and capitalize first letter)
                const username = email.split('@')[0]
                    .split(/[._-]/)
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
                
                const formData = {
                    username: username, // Use extracted username
                    email: email,
                    password: Math.random().toString(36).slice(-10),
                    role: 'admin',
                    role_name: finalRoleNameInput.value,
                    active: true,
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                };

                const response = await fetch('/users', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': formData._token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Failed to create user');
                }

                // Show success modal
                resetForm();
                addUserModal.classList.add('hidden');
                showSuccessModal();

            } catch (error) {
                console.error('Error:', error);
                const adminEmailError = document.getElementById('adminEmailError');
                if (adminEmailError) {
                    adminEmailError.textContent = error.message;
                    adminEmailError.classList.remove('hidden');
                }
            } finally {
                isProcessing = false;
                submitAdminBtn.disabled = false;
                submitAdminBtn.innerHTML = 'Add User';
            }
        });
    }

    // Helper functions for form submission
    function showSuccessModal() {
        if (successModal) {
            // Update success message if needed
            const successTitle = document.getElementById('successTitle');
            const successMessage = document.getElementById('successMessage');
            
            if (successTitle) {
                successTitle.textContent = 'Account Successfully Created!';
            }
            if (successMessage) {
                successMessage.textContent = 'The user account has been added successfully.';
            }

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
    }

    function handleSubmissionError(error) {
    let errorMessage = 'Failed to add user. ';

    if (error.response) {
        // Server responded with error
        errorMessage += error.response.data?.message || '';
    } else if (error.message) {
        // Network or other error
        errorMessage += error.message;
    }

    // Show error in form
    const formError = document.getElementById('studentFormError') || document.getElementById('adminFormError');
    if (formError) {
        formError.textContent = errorMessage;
        formError.classList.remove('hidden');
    } else {
        // Fallback to alert if error element not found
        alert(errorMessage);
    }
}

// Add these helper functions to manage button states
function disableFormButtons() {
    // Disable submit buttons
    if (submitStudentBtn) {
        submitStudentBtn.disabled = true;
        submitStudentBtn.innerHTML = 'Adding...';
        submitStudentBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
    if (submitAdminBtn) {
        submitAdminBtn.disabled = true;
        submitAdminBtn.innerHTML = 'Adding...';
        submitAdminBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
    
    // Disable back buttons
    if (backToRoleBtn) {
        backToRoleBtn.disabled = true;
        backToRoleBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
    if (backToRoleFromAdminBtn) {
        backToRoleFromAdminBtn.disabled = true;
        backToRoleFromAdminBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
    
    // Disable close button
    if (closeAddUserModalBtn) {
        closeAddUserModalBtn.disabled = true;
        closeAddUserModalBtn.style.opacity = '0.5';
        closeAddUserModalBtn.style.cursor = 'not-allowed';
    }
}

function enableFormButtons() {
    // Enable submit buttons
    if (submitStudentBtn) {
        submitStudentBtn.disabled = false;
        submitStudentBtn.innerHTML = 'Add User';
        submitStudentBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
    if (submitAdminBtn) {
        submitAdminBtn.disabled = false;
        submitAdminBtn.innerHTML = 'Add User';
        submitAdminBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
    
    // Enable back buttons
    if (backToRoleBtn) {
        backToRoleBtn.disabled = false;
        backToRoleBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
    if (backToRoleFromAdminBtn) {
        backToRoleFromAdminBtn.disabled = false;
        backToRoleFromAdminBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
    
    // Enable close button
    if (closeAddUserModalBtn) {
        closeAddUserModalBtn.disabled = false;
        closeAddUserModalBtn.style.opacity = '1';
        closeAddUserModalBtn.style.cursor = 'pointer';
    }
}
});