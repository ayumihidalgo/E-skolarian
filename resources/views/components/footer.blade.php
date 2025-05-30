<style>
    /* Custom scrollbar for Webkit browsers */
    #privacyModal .overflow-y-auto::-webkit-scrollbar {
        width: 8px;
        background: #fff;
    }

    #privacyModal .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #7A1212;
        border-radius: 6px;
    }

    #privacyModal .overflow-y-auto::-webkit-scrollbar-button {
        display: none;
        height: 0;
    }

    /* Custom scrollbar for Terms Modal */
    #termsModal .overflow-y-auto::-webkit-scrollbar {
        width: 8px;
        background: #fff;
    }

    #termsModal .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #7A1212;
        border-radius: 6px;
    }

    #termsModal .overflow-y-auto::-webkit-scrollbar-button {
        display: none;
        height: 0;
    }
</style>
<footer
    class="w-full bg-white h-auto p-4 text-black border-t border-black flex flex-col md:flex-row justify-center items-center">
    <div class="flex flex-col md:flex-row justify-between items-center w-full px-2 md:px-4 gap-2 md:gap-0">
        <div class="flex items-center mb-2 md:mb-0">
            <img src="{{ asset('images/isko.svg') }}" alt="Isko Logo" class="h-6 w-20 md:w-30">
            <span class="mr-2 h-6 border-l border-gray-400 hidden md:inline-block"></span>
            <span class="text-xs md:text-sm" style="font-family: 'Marcellus SC', serif;">
                &copy; {{ date('Y') }} <span class="font-normal">BSIT 3-1. All rights reserved.️</span>
            </span>
        </div>
        <div class="flex flex-col md:flex-row items-center gap-1 md:gap-4">
            <button class="text-xs md:text-xs focus:outline-none underline font-medium cursor-pointer"
                style="font-family: 'Manrope', sans-serif; color: #9099A5;" onclick="termModal()">
                Terms & Conditions
            </button>
            <button class="text-xs md:text-xs focus:outline-none underline font-medium cursor-pointer"
                style="font-family: 'Manrope', sans-serif; color: #9099A5;" onclick="privacyModal()">
                Privacy Policy
            </button>
        </div>
    </div>
</footer>
<!-- Terms & Conditions Modal -->
<div id="termsModal" class="fixed inset-0 bg-black/60 backdrop-blur-[3px] hidden items-center justify-center z-50">
    <div
        class="bg-white rounded-2xl p-4 sm:p-6 w-full max-w-xs sm:max-w-lg md:max-w-2xl max-h-[90vh] overflow-hidden relative flex flex-col">
        <!-- Header -->
        <div class="flex items-center mb-4 px-2 sm:px-4">
            <img src="{{ asset('images/terms.svg') }}" alt="Terms Icon" class="h-10 w-auto sm:h-12 mr-2 sm:mr-4">
            <h2 class="flex-1 font-bold text-lg sm:text-2xl text-[#7A1212]" style="font-family: 'Roboto', sans-serif;">
                Terms and Conditions
            </h2>
        </div>

        <!-- Scrollable Content -->
        <div class="text-xs sm:text-sm overflow-y-auto px-2 sm:px-4 pr-1 sm:pr-2 space-y-4 leading-relaxed text-[#6B6B6B]"
            style="font-family: 'Roboto Flex', sans-serif; max-height: 50vh; sm:max-height: 60vh;">

            <div>
                <h3 class="font-semibold">1. Acceptance of Terms</h3><br>
                <p>By accessing or using the E-SKOLARIAN Document Management System (the "System"), you agree to be
                    bound by these Terms and Conditions ("Terms"). If you do not agree with these Terms, you should
                    immediately cease using the System.</p>
            </div>

            <div>
                <h3 class="font-semibold">2. Use of the System</h3><br>
                <p>The E-SKOLARIAN Document Management System is intended for the secure management, storage, and
                    sharing of documents related to educational purposes. You are granted a limited, non-exclusive, and
                    non-transferable license to access and use the System in accordance with these Terms.</p>
            </div>

            <div>
                <h3 class="font-semibold">3. User Responsibilities</h3><br>
                <ul class="list-disc ml-6 space-y-1">
                    <li>You agree to provide accurate, current, and complete information during the registration
                        process.</li>
                    <li>You are responsible for maintaining the confidentiality of your account credentials.</li>
                    <li>You agree to use the System only for lawful purposes and will not engage in any activities that
                        could harm, disrupt, or interfere with the System's operation.</li>
                    <li>You are responsible for backing up your own documents stored in the System. The provider is not
                        responsible for data loss due to technical issues.</li>
                </ul>
            </div>

            <div>
                <h3 class="font-semibold">4. Prohibited Activities</h3><br>
                <ul class="list-disc ml-6 space-y-1">
                    <li>Uploading or sharing illegal, offensive, or harmful content.</li>
                    <li>Attempting to hack, modify, or gain unauthorized access to the System.</li>
                    <li>Engaging in activities that may damage or impair the functionality of the System.</li>
                </ul>
            </div>

            <div>
                <h3 class="font-semibold">5. Account Termination</h3><br>
                <p>We reserve the right to suspend or terminate your account if you violate these Terms. Upon
                    termination, you will no longer have access to your account, and any stored documents may be
                    deleted.</p>
            </div>

            <div>
                <h3 class="font-semibold">6. Limitation of Liability</h3><br>
                <p>The E-SKOLARIAN Document Management System is provided "as is" without any warranty of any kind. The
                    provider is not liable for any direct, indirect, incidental, or consequential damages arising from
                    the use or inability to use the System.</p>
            </div>

            <div>
                <h3 class="font-semibold">7. Changes to the Terms</h3><br>
                <p>We may update or modify these Terms at any time. Any changes will be effective immediately upon
                    posting to this page. You are encouraged to review these Terms periodically.</p>
            </div>

            <div>
                <h3 class="font-semibold">8. Governing Law</h3><br>
                <p>These Terms are governed by the laws of the Philippines, and any disputes shall be resolved in the
                    courts of [Insert Jurisdiction].</p>
            </div>
        </div>
        <div class="flex justify-end pt-6 sm:pt-10">
            <button
                class="px-3 sm:px-4 py-1 bg-red-900 text-white rounded-xl hover:bg-red-600 w-28 sm:w-40 cursor-pointer hover:text-white transition-colors duration-200 text-xs sm:text-base"
                onclick="document.getElementById('termsModal').classList.add('hidden')">
                Close
            </button>
        </div>
    </div>
</div>  
<!-- Privacy Policy Modal -->
<div id="privacyModal" class="fixed inset-0 bg-black/60 backdrop-blur-[3px] items-center justify-center z-50 hidden">
    <div
        class="bg-white rounded-2xl p-4 sm:p-6 w-full max-w-xs sm:max-w-lg md:max-w-2xl max-h-[90vh] overflow-hidden relative flex flex-col">
        <!-- Header -->
        <div class="flex items-center mb-4 px-2 sm:px-4">
            <img src="{{ asset('images/privacy.svg') }}" alt="Privacy Icon" class="h-10 w-auto sm:h-12 mr-2 sm:mr-4">
            <h2 class="flex-1 font-bold text-lg sm:text-2xl text-[#7A1212]" style="font-family: 'Roboto', sans-serif;">
                Privacy Policy
            </h2>
        </div>

        <!-- Scrollable Content -->
        <div class="text-xs sm:text-sm overflow-y-auto px-2 sm:px-4 pr-1 sm:pr-2 space-y-4 leading-relaxed text-[#6B6B6B]"
            style="font-family: 'Roboto Flex', sans-serif; max-height: 50vh; sm:max-height: 60vh;">

            <div>
                <p class="font-semibold">1. Information Collection</p><br>
                <p>We collect personal information when you register for an account or use certain features of the
                    E-SKOLARIAN Document Management System. This information may include your name, email address,
                    institution, and other information necessary to provide our services.</p>
            </div>
            <div>
                <p class="font-semibold">2. Use of Information</p><br>
                <p>The information we collect is used to:</p>
                <ul class="list-disc list-inside ml-4">
                    <li>Provide and improve the System’s features and functionality.</li>
                    <li>Communicate with you regarding updates, maintenance, or other important notices related to the
                        System.</li>
                    <li>Comply with legal obligations and enforce our Terms and Conditions.</li>
                </ul>
            </div>
            <div>
                <p class="font-semibold">3. Document Storage</p><br>
                <p>Documents you upload to the System are stored securely. We do not share your documents with third
                    parties unless required by law or as stated in this Privacy Policy.</p>
            </div>
            <div>
                <p class="font-semibold">4. Data Security</p><br>
                <p>We employ reasonable technical and organizational measures to protect your personal information from
                    unauthorized access, use, alteration, or disclosure. However, no security system is completely
                    foolproof, and we cannot guarantee the absolute security of your information.</p>
            </div>
            <div>
                <p class="font-semibold">5. Sharing of Information</p><br>
                <p>We do not sell, rent, or share your personal information with third parties except as described in
                    this Privacy Policy or as required by law.</p>
            </div>
            <div>
                <p class="font-semibold">6. Cookies and Tracking Technologies</p><br>
                <p>The System may use cookies and similar technologies to enhance your user experience. These
                    technologies help us analyze usage patterns and improve the functionality of the System. You can
                    control cookies through your browser settings.</p>
            </div>
            <div>
                <p class="font-semibold">7. Retention of Data</p><br>
                <p>We retain personal information and documents for as long as necessary to fulfill the purposes
                    outlined in this Privacy Policy, unless a longer retention period is required by law.</p>
            </div>
            <div>
                <p class="font-semibold">8. Your Rights</p><br>
                <p>You have the right to:</p>
                <ul class="list-disc list-inside ml-4">
                    <li>Access the personal information we hold about you.</li>
                    <li>Correct or update your personal information.</li>
                    <li>Request the deletion of your personal information, subject to certain legal restrictions.</li>
                </ul>
                <p>To exercise your rights, please contact us at [Insert Contact Information].</p>
            </div>
            <div>
                <p class="font-semibold">9. Changes to the Privacy Policy</p><br>
                <p>We may update this Privacy Policy from time to time. Any changes will be posted on this page, and the
                    "Effective Date" will be updated accordingly. We encourage you to review this Privacy Policy
                    periodically.</p>
            </div>
            <div>
                <p class="font-semibold">10. Contact Information</p><br>
                <p>If you have any questions about these Terms and Conditions or our Privacy Policy, please contact us
                    at:</p>
                <ul class="list-none ml-4">
                    <li><span class="font-semibold">Email:</span>
                        {{ \App\Models\User::where('role', 'super_admin')->first()?->email ?? '[Not Available]' }}
                    </li>
                    <li><span class="font-semibold">Address:</span> Polytechnic University of the Philippines Santa Rosa
                        City</li>
                    <li>Arambulo St, Barangay Kanluran, Santa Rosa, 4026 Laguna, Sta. Rosa</li>
                </ul>
            </div>
        </div>
        <div class="flex justify-end pt-6 sm:pt-10">
            <button
                class="px-3 sm:px-4 py-1 bg-red-900 text-white rounded-xl hover:bg-red-600 w-28 sm:w-40 cursor-pointer hover:text-white transition-colors duration-200 text-xs sm:text-base"
                onclick="document.getElementById('privacyModal').classList.add('hidden')">
                Close
            </button>
        </div>
    </div>
</div>



<script>
    function termModal() {
        document.getElementById('termsModal').classList.remove('hidden');
        document.getElementById('termsModal').classList.add('flex');
    }

    function privacyModal() {
        document.getElementById('privacyModal').classList.remove('hidden');
        document.getElementById('privacyModal').classList.add('flex');
    }
</script>
