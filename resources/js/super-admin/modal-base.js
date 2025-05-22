// Base modal functionality
document.addEventListener('DOMContentLoaded', function () {
    // Close all modals when Escape key is pressed
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (modal) modal.classList.add('hidden');
            });
        }
    });

    // Generic modal close function
    function setupModalClose(modalElement, closeButtonSelector, backdropSelector) {
        const closeBtn = document.querySelector(closeButtonSelector);
        const backdrop = document.querySelector(backdropSelector);

        if (closeBtn && modalElement) {
            closeBtn.addEventListener('click', function () {
                modalElement.classList.add('hidden');
            });
        }

        if (backdrop && modalElement) {
            backdrop.addEventListener('click', function () {
                modalElement.classList.add('hidden');
            });
        }
    }

    // Export the setupModalClose function to window for access from other modules
    window.setupModalClose = setupModalClose;
});
