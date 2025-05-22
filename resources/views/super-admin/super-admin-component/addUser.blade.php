<!-- Add User Component -->
<div class="bg-white rounded-[25px] shadow-xl w-full max-w-lg relative z-50">
    <!-- Close button -->
    <button id="closeAddUserModalBtn" 
            class="absolute top-7 right-5 text-gray-500 hover:text-[#7A1212] transition-colors duration-200 cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
    <div class="p-6">
        <h3 class="text-xl font-semibold text-gray-800">ADD USER (Admin/Organization)</h3>
        <p class="text-gray-500 text-sm mb-6">Create new user by selecting a role first</p>
        <form id="addUserForm">
            <!-- Step 1: Role Selection -->
            <div id="step-role" class="step-container active">
                <div class="mb-4">
                    <label for="role_name" class="block text-sm font-medium mb-2 text-gray-700">Role</label>
                    <select name="role_name" 
                            id="role_name" 
                            class="w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212] cursor-pointer" 
                            required>
                        <option value="" disabled selected>Choose a role for the user</option>
                        <optgroup label="Student Organizations">
                            <option value="Academic Organization" data-role="student">Academic Organization</option>
                            <option value="Non-Academic Organization" data-role="student">Non-Academic Organization</option>
                        </optgroup>
                        <optgroup label="Administrative Staff">
                            <option value="Student Services" data-role="admin">Student Services</option>
                            <option value="Academic Services" data-role="admin">Academic Services</option>
                            <option value="Administrative Services" data-role="admin">Administrative Services</option>
                            <option value="Campus Director" data-role="admin">Campus Director</option>
                        </optgroup>
                        <option value="custom_role" data-role="custom">+ Add New Role</option>
                    </select>
                    <p id="roleError" class="text-red-600 text-xs mt-1 hidden"></p>
                </div>
                
                <!-- Custom Role Field (Initially Hidden) -->
                <div id="custom-role-container" class="mb-4 hidden">
                    <label for="custom_role_name" class="block text-sm font-medium mb-2 text-gray-700">New Role Name</label>
                    <input type="text" 
                        name="custom_role_name" 
                        id="custom_role_name" 
                        class="w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]" 
                        placeholder="Enter new role name">
                    <p id="customRoleError" class="text-red-600 text-xs mt-1 hidden"></p>
                    
                    <div class="mt-3">
                        <label class="block text-sm font-medium mb-2 text-gray-700">Role Type</label>
                        <div class="flex space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="custom_role_type" value="student" class="form-radio h-5 w-5 text-[#7A1212]">
                                <span class="ml-2 text-gray-700">Student Organization</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="custom_role_type" value="admin" class="form-radio h-5 w-5 text-[#7A1212]">
                                <span class="ml-2 text-gray-700">Administrative Staff</span>
                            </label>
                        </div>
                        <p id="roleTypeError" class="text-red-600 text-xs mt-1 hidden"></p>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="button"
                            id="continueToNextBtn"
                            class="w-full px-3 py-2 bg-[#7A1212] text-white rounded-md hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-[#7A1212] opacity-50 disabled:opacity-50 cursor-not-allowed"
                            disabled>
                        Continue
                    </button>
                </div>
            </div>
            
            <!-- Step 2: Student Organization Fields (Initially Hidden) -->
            <div id="step-student" class="step-container hidden">
                <div class="mb-4">
                    <label for="organization_name" class="block text-sm font-medium mb-2 text-gray-700">Organization Name</label>
                    <select name="organization_name" 
                            id="organization_name" 
                            class="w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212] cursor-pointer text-ellipsis"
                            style="text-overflow: ellipsis;">
                        <option value="" disabled selected>Select an organization</option>
                        <optgroup label="Academic Organization (Student)">
                            <option value="Eligible League of Information Technology Enthusiast">Eligible League of Information Technology Enthusiast</option>
                            <option value="Association of Electronics Engineering Students">Association of Electronics Engineering Students</option>
                            <option value="Association of Competent and Aspiring Psychologists">Association of Competent and Aspiring Psychologists</option>
                            <option value="Junior Marketing Association of the Philippines">Junior Marketing Association of the Philippines</option>
                            <option value="Philippine Institute of Industrial Engineers">Philippine Institute of Industrial Engineers</option>
                            <option value="Guild of Imporous and Valuable Educators">Guild of Imporous and Valuable Educators</option>
                            <option value="Junior Philippine Institute of Accountants">Junior Philippine Institute of Accountants</option>
                            <option value="Junior Executives of Human Resources Association">Junior Executives of Human Resources Association</option>
                        </optgroup>
                        <optgroup label="Non-Academic Organization (Student)">
                            <option value="Transformation Advocates through Purpose-driven and Noble Objectives Toward Community Holism">Transformation Advocates through Purpose-driven and Noble Objectives Toward Community Holism</option>
                            <option value="PUP SRC CHORALE">PUP SRC CHORALE</option>
                            <option value="Supreme Innovators' Guild for Mathematics Advancement">Supreme Innovators' Guild for Mathematics Advancement</option>
                            <option value="Artist Guild Dance Squad">Artist Guild Dance Squad</option>
                        </optgroup>
                    </select>
                    <p id="organizationNameError" class="text-red-600 text-xs mt-1 hidden"></p>
                </div>
                
                <div class="mb-4">
                    <label for="organization_acronym" class="block text-sm font-medium mb-2 text-gray-700">Organization Acronym</label>
                    <input type="text" 
                        name="organization_acronym" 
                        id="organization_acronym" 
                        maxlength="20"
                        class="w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]" 
                        placeholder="Organization Acronym (e.g. SSG)">
                    <p id="acronymError" class="text-red-600 text-xs mt-1 hidden"></p>
                </div>
                
                <div class="mb-4">
                    <label for="student_email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                    <input type="email" 
                        name="student_email" 
                        id="student_email" 
                        maxlength="50"
                        class="w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]" 
                        placeholder="Enter organization email">
                    <p id="studentEmailError" class="text-red-600 text-xs mt-1 hidden"></p>
                </div>
                
                <div class="flex space-x-4 mt-6">
                    <button type="button"
                            id="backToRoleBtn"
                            class="w-1/2 px-3 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Back
                    </button>
                    <button type="submit"
                            id="submitStudentBtn"
                            class="w-1/2 px-3 py-2 bg-[#7A1212] text-white rounded-md hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-[#7A1212] opacity-50 disabled:opacity-50 cursor-not-allowed"
                            disabled>
                        Add User
                    </button>
                </div>
            </div>
            
            <!-- Step 2: Admin User Fields (Initially Hidden) -->
            <div id="step-admin" class="step-container hidden">
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium mb-2 text-gray-700">Name</label>
                    <input type="text" 
                        name="username" 
                        id="username" 
                        maxlength="150"
                        class="w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]" 
                        placeholder="Admin Name">
                    <p id="usernameError" class="text-red-600 text-xs mt-1 hidden"></p>
                </div>
                
                <div class="mb-4">
                    <label for="admin_email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                    <input type="email" 
                        name="admin_email" 
                        id="admin_email" 
                        maxlength="50"
                        class="w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]" 
                        placeholder="Enter admin email">
                    <p id="adminEmailError" class="text-red-600 text-xs mt-1 hidden"></p>
                </div>
                
                <div class="flex space-x-4 mt-6">
                    <button type="button"
                            id="backToRoleFromAdminBtn"
                            class="w-1/2 px-3 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Back
                    </button>
                    <button type="submit"
                            id="submitAdminBtn"
                            class="w-1/2 px-3 py-2 bg-[#7A1212] text-white rounded-md hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-[#7A1212] opacity-50 disabled:opacity-50 cursor-not-allowed"
                            disabled>
                        Add User
                    </button>
                </div>
            </div>

            <!-- Hidden field to store the actual role value (admin/student/custom) -->
            <input type="hidden" id="actual_role" name="role" value="">
            <input type="hidden" id="final_role_name" name="final_role_name" value="">
        </form>
    </div>
    
    <!-- Close Confirmation Modal -->
    <div id="closeConfirmModal" class="fixed inset-0 flex items-center justify-center z-[60] hidden">
        <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
        <div class="bg-white rounded-[25px] shadow-xl w-full max-w-md relative z-[70] p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Confirm Leave Page</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to leave this page? Unsaved changes may be lost.</p>
            <div class="flex justify-end space-x-4">
                <button type="button" 
                        id="cancelCloseBtn"
                        class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancel
                </button>
                <button type="button" 
                        id="confirmCloseBtn"
                        class="px-4 py-2 bg-[#7A1212] text-white rounded-md hover:bg-red-800">
                    Leave Page
                </button>
            </div>
        </div>
    </div>
</div>