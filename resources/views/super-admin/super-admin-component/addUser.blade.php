<!-- Add User Component -->
<div class="bg-white rounded-[25px] shadow-xl w-full max-w-lg relative z-50">
    <!-- Close button -->
    <button id="closeAddUserModalBtn"
                class="absolute top-4 right-4 text-gray-500 hover:text-[#7A1212] transition-colors duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
    <div class="p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">ADD USER (Admin/Organization)</h3>
        <p class="text-gray-500 text-sm mb-6">Create new user by adding their email and role</p>
        <form id="addUserForm">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium mb-2 text-gray-700">Username</label>
                <input type="text"
                    name="username"
                    id="username"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]"
                    required
                    placeholder="Complete Organization Name or Admin Name">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                <input type="email"
                    name="email"
                    id="email"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]"
                    required
                    placeholder="Enter user's email">
            </div>

            <div class="mb-6">
                <label for="role_name" class="block text-sm font-medium mb-2 text-gray-700">Role</label>
                <select name="role_name"
                        id="role_name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]"
                        required>
                    <option value="" disabled selected>Choose a role for the user</option>
                    <option value="Academic Organization">Academic Organization</option>
                    <option value="Non-Academic Organization">Non-Academic Organization</option>
                    <option value="Student Services">Student Services</option>
                    <option value="Academic Services">Academic Services</option>
                    <option value="Administrative Services">Administrative Services</option>
                </select>
            </div>
            <p class="text-sm text-gray-500 mb-6 text-center">The reset password link will be sent to the user via email.</p>
            <button type="submit"
                    class="w-full px-3 py-2 bg-[#7A1212] text-white rounded-md hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-[#7A1212]">
                Add User
            </button>
        </form>
    </div>
</div>
