document.addEventListener('DOMContentLoaded', function() {
    const TIMEOUT_DURATION = 15 * 60 * 1000; // 15 minutes in milliseconds
    let timeoutId;

    function startIdleTimer() {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(logout, TIMEOUT_DURATION);
    }

    function resetTimer() {
        clearTimeout(timeoutId);
        startIdleTimer();
    }

    async function logout() {
        const warningModal = document.getElementById('logoutWarningModal');
        if (warningModal) {
            warningModal.classList.remove('hidden');
            
            // Auto logout after 1 minute if no action is taken
            setTimeout(async () => {
                try {
                    const response = await fetch('/superadmin/logout', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                    });

                    if (response.ok) {
                        window.location.href = '/superadmin/login';
                    } else {
                        console.error('Logout failed');
                    }
                } catch (error) {
                    console.error('Error during logout:', error);
                }
            }, 60000); // 1 minute warning
        } else {
            // If modal doesn't exist, logout immediately
            try {
                const response = await fetch('/superadmin/logout', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                });

                if (response.ok) {
                    window.location.href = '/superadmin/login';
                }
            } catch (error) {
                console.error('Error during logout:', error);
            }
        }
    }

    // Monitor user activity
    const events = [
        'mousedown',
        'mousemove',
        'keypress',
        'scroll',
        'touchstart'
    ];

    events.forEach(event => {
        document.addEventListener(event, resetTimer);
    });

    // Start the initial timer
    startIdleTimer();
});