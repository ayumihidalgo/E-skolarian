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
        continueToNextBtn.addEventListener('click', function() {
            // Get role type
            let roleType;
            let organizationType = 'academic'; // Default to academic
            
            // Check if custom role is selected
            if (roleSelect.value === 'custom_role') {
                // Get the role type from radio buttons
                const checkedRadio = document.querySelector('input[name="custom_role_type"]:checked');
                if (checkedRadio) {
                    roleType = checkedRadio.value;
                    
                    // Set the final role name to the custom role name
                    finalRoleNameInput.value = customRoleName.value.trim();
                    
                    // Get organization type if this is a student custom role
                    if (roleType === 'student') {
                        // Update organization dropdown based on selected role
                        const selectedRole = roleSelect.value;
                        if (selectedRole === 'Academic Organization' || selectedRole === 'Non-Academic Organization') {
                            updateOrganizationDropdown(selectedRole);
                        }
                        showStep('student');
                    }
                } else {
                    // Show error message if no role type is selected
                    roleTypeError.textContent = 'Please select a role type';
                    roleTypeError.classList.remove('hidden');
                    return;
                }
            } else {
                // Get role type from selected option
                const selectedOption = roleSelect.options[roleSelect.selectedIndex];
                roleType = selectedOption.getAttribute('data-role');
                
                // Set the final role name to the selected role
                finalRoleNameInput.value = roleSelect.value;
                
                // For predefined student roles, determine org type based on role name
                if (roleType === 'student') {
                    // Map predefined student roles to organization types
                    const roleToOrgTypeMap = {
                        'Student Services': 'academic',
                        'Academic Services': 'academic',
                        // Add other predefined student roles here if needed
                    };
                    
                    organizationType = roleToOrgTypeMap[roleSelect.value] || 'academic';
                }
            }
            
            // Store role value in hidden field
            actualRoleInput.value = roleType;
            
            // Navigate to appropriate step
            if (roleType === 'student') {
                // Update organization dropdown based on org type
                updateOrganizationDropdown(organizationType);
                showStep('student');
            } else if (roleType === 'admin') {
                showStep('admin');
            }
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
            
            // Check if custom role is selected
            if (this.value === 'custom_role') {
                customRoleContainer.classList.remove('hidden');
                
                // Validate role selection and custom role fields
                validateRoleSelection().then(() => {
                    validateCustomRole().then(() => {
                        updateContinueButton();
                    });
                });
            } else {
                customRoleContainer.classList.add('hidden');
                
                // Clear custom role fields
                customRoleName.value = '';
                document.querySelectorAll('input[name="custom_role_type"]').forEach(radio => {
                    radio.checked = false;
                });
                
                // Validate role selection
                validateRoleSelection().then(() => {
                    updateContinueButton();
                });
            }
        });
    }
    
    // Custom role name input handler
    if (customRoleName) {
        let customRoleTimeout;
        customRoleName.addEventListener('input', function() {
            // Remove extra spaces and special characters
            this.value = this.value
                .replace(/^\s+/, '')
                .replace(/[^a-zA-Z\s]/g, '')
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
            
            // If this is a student role type, we need organization type radios
            if (this.value === 'student') {
                // Check if the container for org type exists
                const orgTypeContainer = document.getElementById('custom-org-type-container');
                if (orgTypeContainer) {
                    orgTypeContainer.classList.remove('hidden');
                }
            } else {
                // Hide org type container if it exists
                const orgTypeContainer = document.getElementById('custom-org-type-container');
                if (orgTypeContainer) {
                    orgTypeContainer.classList.add('hidden');
                    // Clear any selected org type
                    document.querySelectorAll('input[name="custom_org_type"]').forEach(radio => {
                        radio.checked = false;
                    });
                }
            }
            
            validateCustomRole().then(() => {
                updateContinueButton();
            });
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
            
            // Auto-fill acronym based on selected organization
            const selectedOrganization = this.value;
            if (selectedOrganization && organizationAcronymInput) {
                // Extract acronym from the selected option text
                const selectedOption = organizationNameInput.options[organizationNameInput.selectedIndex];
                const acronymMatch = selectedOption.text.match(/\(([A-Z]+)\)$/);
                
                if (acronymMatch && acronymMatch[1]) {
                    organizationAcronymInput.value = acronymMatch[1];
                    // Trigger validation for acronym
                    const event = new Event('input');
                    organizationAcronymInput.dispatchEvent(event);
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
            const customRoleValid = customRoleName.value.trim() !== '' && 
                                   document.querySelector('input[name="custom_role_type"]:checked') !== null;
            
            // If it's a student custom role, also check org type selection
            const selectedRoleType = document.querySelector('input[name="custom_role_type"]:checked');
            let orgTypeValid = true;
            
            if (selectedRoleType && selectedRoleType.value === 'student') {
                orgTypeValid = document.querySelector('input[name="custom_org_type"]:checked') !== null;
            }
            
            const allValid = customRoleValid && orgTypeValid;
            
            continueToNextBtn.disabled = !allValid;
            continueToNextBtn.classList.toggle('opacity-50', !allValid);
            continueToNextBtn.classList.toggle('cursor-not-allowed', !allValid);
        } else {
            // Standard role - just check if a valid role is selected
            const roleValid = roleSelect.value !== '';
            
            continueToNextBtn.disabled = !roleValid;
            continueToNextBtn.classList.toggle('opacity-50', !roleValid);
            continueToNextBtn.classList.toggle('cursor-not-allowed', !roleValid);
        }
    }
    
    // Function to update student submit button state
    function updateStudentSubmitButton() {
        if (!submitStudentBtn) return;
        
        const isValid = organizationNameInput.value.trim() !== '' && 
                       organizationAcronymInput.value.trim() !== '' &&
                       studentEmailInput.value.trim() !== '' &&
                       !organizationNameInput.classList.contains('border-red-500') &&
                       !organizationAcronymInput.classList.contains('border-red-500') &&
                       !studentEmailInput.classList.contains('border-red-500');
                       
        submitStudentBtn.disabled = !isValid;
        submitStudentBtn.classList.toggle('opacity-50', !isValid);
        submitStudentBtn.classList.toggle('cursor-not-allowed', !isValid);
    }
    
    // Function to update admin submit button state
    function updateAdminSubmitButton() {
        if (!submitAdminBtn) return;
        
        const isValid = usernameInput.value.trim() !== '' && 
                       adminEmailInput.value.trim() !== '' &&
                       !usernameInput.classList.contains('border-red-500') &&
                       !adminEmailInput.classList.contains('border-red-500');
                       
        submitAdminBtn.disabled = !isValid;
        submitAdminBtn.classList.toggle('opacity-50', !isValid);
        submitAdminBtn.classList.toggle('cursor-not-allowed', !isValid);
    }

    // Function to update role options based on existing roles
    function updateRoleOptions(existingRoles) {
        if (!roleSelect) return;

        const restrictedRoles = ['Student Services', 'Academic Services', 'Administrative Services', 'Campus Director'];
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
    function updateOrganizationDropdown(orgType) {
        if (!organizationNameInput) return;
        
        // Clear existing options
        while (organizationNameInput.options.length > 1) {
            organizationNameInput.remove(1);
        }
        
        // Reset acronym field
        if (organizationAcronymInput) {
            organizationAcronymInput.value = '';
        }

        // Remove any existing style tag
        const existingStyle = document.getElementById('org-select-style');
        if (existingStyle) existingStyle.remove();
        
        // Add new style tag
        const style = document.createElement('style');
        style.id = 'org-select-style';
        style.textContent = `
            #organization_name {
                width: 100%;
                text-overflow: ellipsis;
                white-space: nowrap;
                overflow: hidden;
            }
            
            #organization_name option {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 100%;
                padding: 8px 12px;
                font-size: 14px;
                line-height: 1.4;
            }
            
            /* Ensure dropdown fits container width */
            #organization_name:focus {
                outline: none;
            }
            
            /* Additional styling for better UX */
            #organization_name option:hover {
                background-color: #f3f4f6;
            }
        `;
        document.head.appendChild(style);
            
        // Define academic and non-academic organizations
        const academicOrgs = [
            { name: 'Eligible League of Information Technology Enthusiast', acronym: 'ELITE' },
            { name: 'Association of Electronics and Communications Engineering Students', acronym: 'AECES' },
            { name: 'Association of Competent and Aspiring Psychologists', acronym: 'ACAP' },
            { name: 'Junior Marketing Association of the Philippines', acronym: 'JMAP' },
            { name: 'Philippine Institute of Industrial Engineers', acronym: 'PIIE' },
            { name: 'Guild of Imporous and Valuable Educators', acronym: 'GIVE' },
            { name: 'Junior Philippine Institute of Accountants', acronym: 'JPIA' },
            { name: 'Junior Executives of Human Resources Association', acronym: 'JEHRA' }
        ];
        
        const nonAcademicOrgs = [
            { name: 'Transformation Advocates through Purpose-driven and Noble Objectives Toward Community Holism', acronym: 'TAPNOTCH' },
            { name: 'PUP SRC CHORALE', acronym: 'CHORALE' },
            { name: 'Supreme Innovators\' Guild for Mathematics Advancement', acronym: 'SIGMA' },
            { name: 'Artist Guild Dance Squad', acronym: 'AGDS' }
        ];
        
        // Determine which organizations to show based on the selected role
        const selectedRole = roleSelect.value;
        let orgsToShow;

        if (selectedRole === 'Academic Organization') {
            orgsToShow = academicOrgs;
        } else if (selectedRole === 'Non-Academic Organization') {
            orgsToShow = nonAcademicOrgs;
        } else {
            // Default to academic if somehow neither is selected
            orgsToShow = academicOrgs;
        }
            
        // Function to truncate text for display while preserving full value
        function createTruncatedOption(org) {
            const option = document.createElement('option');
            option.value = org.name;
            
            // Create display text with intelligent truncation
            const fullText = `${org.name} (${org.acronym})`;
            const maxLength = 50; // Adjust this value based on your dropdown width
            
            let displayText;
            if (fullText.length > maxLength) {
                // Try to truncate the organization name while keeping the acronym
                const namePartMaxLength = maxLength - org.acronym.length - 4; // Account for " (...)"
                if (namePartMaxLength > 10) { // Ensure we have reasonable space for the name
                    displayText = `${org.name.substring(0, namePartMaxLength).trim()}... (${org.acronym})`;
                } else {
                    // If name is too long, just show acronym
                    displayText = `${org.acronym} - ${org.name.substring(0, maxLength - org.acronym.length - 3).trim()}...`;
                }
            } else {
                displayText = fullText;
            }
            
            option.textContent = displayText;
            option.title = fullText; // Show full text on hover
            option.setAttribute('data-full-name', fullText);
            
            return option;
        }
        
        // Add organizations to dropdown with proper truncation
        orgsToShow.forEach(org => {
            const option = createTruncatedOption(org);
            organizationNameInput.appendChild(option);
        });
        
        // Update toggle buttons if they exist
        const academicOrgBtn = document.getElementById('academic-org-btn');
        const nonAcademicOrgBtn = document.getElementById('non-academic-org-btn');
        
        if (academicOrgBtn && nonAcademicOrgBtn) {
            if (orgType === 'academic') {
                academicOrgBtn.classList.add('bg-blue-600', 'text-white');
                academicOrgBtn.classList.remove('bg-gray-200', 'text-gray-700');
                nonAcademicOrgBtn.classList.add('bg-gray-200', 'text-gray-700');
                nonAcademicOrgBtn.classList.remove('bg-blue-600', 'text-white');
            } else {
                nonAcademicOrgBtn.classList.add('bg-blue-600', 'text-white');
                nonAcademicOrgBtn.classList.remove('bg-gray-200', 'text-gray-700');
                academicOrgBtn.classList.add('bg-gray-200', 'text-gray-700');
                academicOrgBtn.classList.remove('bg-blue-600', 'text-white');
            }
        }
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

    // Custom role validation
    async function validateCustomRole() {
        customRoleNameError.classList.add('hidden');
        customRoleName.classList.remove('border-red-500');
        
        const customRole = customRoleName.value.trim();
        const MAX_ROLE_LENGTH = 50;

        // Check if custom role name is provided
        if (customRole === '') {
            showCustomRoleNameError('Custom role name cannot be empty');
            return false;
        }

        if (customRole.startsWith(' ')) {
            showCustomRoleNameError('Role name cannot start with a space');
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

        if (!/^[a-zA-Z\s]+$/.test(customRole)) {
            showCustomRoleNameError('Role name can only contain letters and spaces');
            return false;
        }

        return true;
    }

    function showCustomRoleNameError(message) {
        customRoleNameError.textContent = message;
        customRoleNameError.classList.remove('hidden');
        customRoleName.classList.add('border-red-500');
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

        // Specifically check for gmail.com
        if (!email.toLowerCase().endsWith('@gmail.com')) {
            showStudentEmailError('Only @gmail.com email addresses are accepted');
            return false;
        }

        // Check for number instead of letter in domain (.c0m instead of .com)
        if (/\.c0m$|\.c0m@/.test(email.toLowerCase())) {
            showStudentEmailError('Invalid domain (.c0m is not valid)');
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
        const MAX_EMAIL_LENGTH = 50;

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

        // Specifically check for gmail.com
        if (!email.toLowerCase().endsWith('@gmail.com')) {
            showAdminEmailError('Only @gmail.com email addresses are accepted');
            return false;
        }

        // Check for number instead of letter in domain (.c0m instead of .com)
        if (/\.c0m$|\.c0m@/.test(email.toLowerCase())) {
            showAdminEmailError('Invalid domain (.c0m is not valid)');
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
            
            // Set processing flag
            if (isProcessing) return;

            try {
            isProcessing = true;
            submitStudentBtn.disabled = true;
            submitStudentBtn.innerHTML = 'Adding...';

            const formData = {
                username: organizationNameInput.value.trim(),
                email: studentEmailInput.value.trim().toLowerCase(),
                password: Math.random().toString(36).slice(-8), // Generate random password
                role: 'student',
                role_name: finalRoleNameInput.value,
                organization_acronym: organizationAcronymInput.value.trim(),
                active: true,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };

            console.log('Sending data:', formData);

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
            submitStudentBtn.disabled = false;
            submitStudentBtn.innerHTML = 'Add User';

            // Re-enable close button and backdrop
            if (closeAddUserModalBtn) {
                closeAddUserModalBtn.disabled = false;
                closeAddUserModalBtn.style.opacity = '1';
                closeAddUserModalBtn.style.cursor = 'pointer';
            }
        }
    });
}

    // Admin form submission
    if (submitAdminBtn) {
        submitAdminBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            // Set processing flag
            isProcessing = true;

             isProcessing = true;
            submitAdminBtn.disabled = true;
            submitAdminBtn.innerHTML = 'Adding...';
            
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
            // Get form data
            const formData = {
                username: usernameInput.value.trim(),
                email: adminEmailInput.value.trim().toLowerCase(),
                password: Math.random().toString(36).slice(-10), // Generate random password
                role_name: finalRoleNameInput.value,
                role: 'admin',
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };

            const response = await fetch('/users', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) {
                throw new Error('Failed to add user');
            }

            const data = await response.json();
            
            if (data.success) {
                resetForm();
                addUserModal.classList.add('hidden');
                showSuccessModal();
            }

        } catch (error) {
            console.error('Error:', error);
            handleSubmissionError(error);
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
});