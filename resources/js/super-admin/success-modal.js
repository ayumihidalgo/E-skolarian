// Success Modal Functionality
document.addEventListener('DOMContentLoaded', function () {
    // Success Modal Elements
    const successModal = document.getElementById('successModal');
    const closeSuccessModalBtn = document.getElementById('closeSuccessModalBtn');

    // Only show success modal if it has the user_success session
    if (successModal && !successModal.classList.contains('hidden')) {
        // Add click event listeners for both the okay button and close button
        if (closeSuccessModalBtn) {
            closeSuccessModalBtn.addEventListener('click', () => {
                successModal.classList.add('hidden');
                window.location.reload();
            });
        }
    }

    // Set up modal functionality
    if (window.setupModalClose) {
        window.setupModalClose(successModal, '#closeSuccessModalBtn');
    }
});
