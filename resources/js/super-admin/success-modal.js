// Success Modal Functionality
document.addEventListener('DOMContentLoaded', function () {
    // Success Modal Elements
    const successModal = document.getElementById('successModal');
    const closeSuccessModalBtn = document.getElementById('closeSuccessModalBtn');
    const successModalBackdrop = document.querySelector('.success-modal-backdrop');

    // Set up modal functionality
    if (window.setupModalClose) {
        window.setupModalClose(successModal, '#closeSuccessModalBtn', '.success-modal-backdrop');
    }
});
