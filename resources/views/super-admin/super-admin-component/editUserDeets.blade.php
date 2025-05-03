<!-- Modal Content -->
<div class="bg-white rounded-[25px] shadow-xl w-full max-w-md relative z-50 overflow-hidden">
        
        <!-- Close Button -->
        <button type="button" id="closeEditModalBtn" class="absolute top-7 right-5 text-black-500 hover:text-[#7A1212] transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Edit Form -->
        <div class="p-8">
        <div class="">
                <h3 class="text-xl font-bold text-[#181D27] text-[Lexend]">View Account Details</h3>
                <div class="flex justify-between items-center">
                <p class="text-gray-500 text-sm mb-6">View, edit, or deactivate the account.</p>
                </div>  
            </div>
            <!-- User Details -->
            <div class="space-y-4">
                <div class="block text-sm font-medium text-gray-700 mb-1">
                    <div class="flex justify-between items-center">
                    <label class="block text-sm font-medium text-black mb-1 font-[Lexend]">Username</label>
                        <button 
                            type="button"
                            class="bg-[#7A1212] px-4 py-1 mb-1 rounded-[8px] text-white font-[Lexend] hover:bg-red-800 transition duration-200 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                    </div>
                    <form id="editUserForm" class="space-y-4">
                <div class="block text-sm font-medium text-gray-700 mb-2">
                    <input type="text" id="editUsername" 
                    class="text-lg font-semibold text-center text-[#3f434a] font-[DM Sans] w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]">
                </div>
                <div class="block text-sm font-medium text-gray-700 mb-2">
                    <label class="block text-sm font-medium text-black mb-1 font-[Lexend]">Email</label>
                    <input type="email" id="editEmail" 
                        class="text-lg font-semibold text-center text-[#3f434a] font-[DM Sans] w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]">
                </div>

                <div class="block text-sm font-medium text-gray-700 mb-2">
                <label class="block text-sm font-medium text-black mb-1 font-[Lexend]">Role</label>
                    <select id="editRole" 
                        class="text-lg font-semibold text-center text-[#3f434a] font-[DM Sans] w-full px-3 py-2 border border-black rounded-md focus:outline-none focus:ring-2 focus:ring-[#7A1212]">
                        <option value="Academic Organization">Academic Organization</option>
                        <option value="Non-Academic Organization">Non-Academic Organization</option>
                        <option value="Student Services">Student Services</option>
                        <option value="Academic Services">Academic Services</option>
                        <option value="Administrative Services">Administrative Services</option>
                    </select>
                </div>

                <p class="text-sm text-gray-500 mt-5 text-center">Any changes made will notify the account owner via email.</p>

                <div class="flex justify-center">
                    <button type="submit"
                        class="group flex items-center bg-white border border-[#A40202] px-4 py-2 rounded-[10px] shadow-sm text-sm font-bold text-[#7A1212] hover:bg-red-800 hover:text-white">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>