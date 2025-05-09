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
        <p class="text-gray-500 text-sm mb-6">Create new user by adding their email and role</p>
        <form id="addUserForm">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium mb-2 text-gray-700">Username</label>
                <input type="text" 
                    name="username" 
                    id="username" 
                    class="w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]" 
                    required 
                    placeholder="Complete Organization Name or Admin Name">
            </div>
            
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                <input type="email" 
                    name="email" 
                    id="email" 
                    class="w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]" 
                    required 
                    placeholder="Enter user's email">
            </div>
            
            <div class="mb-6">
                <label for="role_name" class="block text-sm font-medium mb-2 text-gray-700">Role</label>
                <select name="role_name" 
                        id="role_name" 
                        class="w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212] cursor-pointer" 
                        required>
                    <option value="" disabled selected>Choose a role for the user</option>
                    <option value="Academic Organization" data-role="student">Academic Organization</option>
                    <option value="Non-Academic Organization" data-role="student">Non-Academic Organization</option>
                    <option value="Student Services" data-role="admin">Student Services</option>
                    <option value="Academic Services" data-role="admin">Academic Services</option>
                    <option value="Administrative Services" data-role="admin">Administrative Services</option>
                </select>
                <!-- Hidden field to store the actual role value (admin/student) -->
                <input type="hidden" id="actual_role" name="role" value="">
            </div>
            <p class="text-sm text-gray-500 mb-6 text-center">The reset password link will be sent to the user via email.</p>
            <button type="submit"
                    class="w-full px-3 py-2 bg-[#7A1212] text-white rounded-md hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-[#7A1212] cursor-pointer">
                Add User
            </button>
        </form>
    </div>
</div>