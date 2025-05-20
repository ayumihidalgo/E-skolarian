{{-- editUserModal --}}
<!-- Modal Content -->
<div class="bg-white rounded-[25px] shadow-xl w-full max-w-md relative z-50 overflow-hidden">

    <!-- Close Button -->
    <button type="button" id="closeEditModalBtn" class="absolute top-7 right-5 text-black-500 hover:text-[#7A1212] transition-colors duration-200 cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <!-- Edit Form -->
    <div class="p-8">
        <div class="">
            <h3 class="text-xl font-bold text-[#181D27] text-[Lexend]">Edit Account Details</h3>
            <div class="flex justify-between items-center">
            <p class="text-gray-500 text-sm mb-6">Make changes to the account details.</p>
            </div>
        </div>
        <!-- User Details -->
        <form id="editUserForm" class="space-y-4">
            <div class="block text-sm font-medium text-gray-700 mb-2">
                <label class="block text-sm font-medium text-black mb-1 font-[Lexend]">Username</label>
                <input type="text" id="editUsername" name="username"
                class="text-lg font-semibold text-center text-[#3f434a] font-[DM Sans] w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]">
            </div>

            <div class="block text-sm font-medium text-gray-700 mb-2">
                <label class="block text-sm font-medium text-black mb-1 font-[Lexend]">Email</label>
                <input type="email" id="editEmail" name="email"
                    class="text-lg font-semibold text-center text-[#3f434a] font-[DM Sans] w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]">
            </div>

            <div class="block text-sm font-medium text-gray-700 mb-2">
                <label class="block text-sm font-medium text-black mb-1 font-[Lexend]">Role</label>
                <select id="editRoleName" name="role_name"
                    class="text-lg font-semibold text-center text-[#3f434a] font-[DM Sans] w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]">
                    <option value="Academic Organization" data-role="student">Academic Organization</option>
                    <option value="Non-Academic Organization" data-role="student">Non-Academic Organization</option>
                    <option value="Student Services" data-role="admin">Student Services</option>
                    <option value="Academic Services" data-role="admin">Academic Services</option>
                    <option value="Administrative Services" data-role="admin">Administrative Services</option>
                    <option value="Campus Director" data-role="admin">Campus Director</option>
                </select>
                <!-- Hidden field to store the actual role value (admin/student) -->
                <input type="hidden" id="editActualRole" name="role" value="">
            </div>

            <p class="text-sm text-gray-500 mt-5 text-center">Any changes made will notify the account owner via email.</p>

            <div class="flex justify-center">
                <button type="submit"
                    class="group flex items-center bg-white border border-[#A40202] px-4 py-2 rounded-[10px] shadow-sm text-sm font-bold text-[#7A1212] hover:bg-red-800 hover:text-white cursor-not-allowed">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
    <div id="closeEditConfirmModal" class="fixed inset-0 flex items-center justify-center z-[60] hidden">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
    <div class="bg-white rounded-[25px] shadow-xl w-full max-w-md relative z-[70] overflow-hidden">
        <div class="p-8">
            <!-- Title -->
            <h3 class="text-xl font-bold text-[#181D27] mb-2">
                Confirm Leave Page
            </h3>

            <!-- Message -->
            <p class="text-gray-600 text-base mb-8">
                Are you sure you want to leave this page? Unsaved changes may be lost.
            </p>

            <!-- Buttons -->
            <div class="flex justify-end items-center space-x-4">
                <button type="button" 
                        id="cancelEditCloseBtn"
                        class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-[10px] hover:bg-gray-50 transition-colors duration-200">
                    Cancel
                </button>
                <button type="button" 
                        id="confirmEditCloseBtn"
                        class="px-6 py-2.5 bg-[#A40202] text-white font-medium rounded-[10px] hover:bg-[#7A1212] transition-colors duration-200">
                    Leave Page
                </button>
            </div>
        </div>
    </div>
</div>
</div>