// Success Modal Functionality
document.addEventListener('DOMContentLoaded', function () {
    // Success Modal Elements
    const successModal = document.getElementById('successModal');
    const closeSuccessModalBtn = document.getElementById('closeSuccessModalBtn');

    // Set up modal functionality
    if (window.setupModalClose) {
        window.setupModalClose(successModal, '#closeSuccessModalBtn');
    }
});
