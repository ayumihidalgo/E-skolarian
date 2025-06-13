document.addEventListener('DOMContentLoaded', function() {
    const activityLogBtn = document.getElementById('activityLogBtn');
    const activityLogModal = document.getElementById('activityLogModal');
    const closeActivityLogBtn = document.getElementById('closeActivityLogBtn');
    const viewAllBtn = document.querySelector('#viewAllActivities');
    
    // Update the URL path in viewAllBtn click handler
    if (viewAllBtn) {
        viewAllBtn.addEventListener('click', function() {
            window.location.href = '/super-admin/activity-logs';
            activityLogModal.classList.add('hidden');
        });
    }
    
    function positionModal() {
        if (activityLogBtn && activityLogModal) {
            const buttonRect = activityLogBtn.getBoundingClientRect();
            
            // Position the modal below the button using fixed positioning
            // Remove window.scrollY since we're using position: fixed
            activityLogModal.style.position = 'fixed';
            activityLogModal.style.top = `${buttonRect.bottom + 10}px`; // 10px gap, no scrollY needed
            activityLogModal.style.right = `${window.innerWidth - buttonRect.right}px`;
            activityLogModal.style.zIndex = '9999'; // Ensure it stays on top
        }
    }

    if (activityLogBtn) {
        activityLogBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (activityLogModal) {
                const isHidden = activityLogModal.classList.contains('hidden');
                
                // Toggle visibility
                activityLogModal.classList.toggle('hidden');
                
                if (isHidden) {
                    positionModal();
                }
            }
        });
    }

    // Close button handler
    if (closeActivityLogBtn) {
        closeActivityLogBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (activityLogModal) {
                activityLogModal.classList.add('hidden');
            }
        });
    }

    // Click outside to close
    document.addEventListener('click', function(e) {
        if (activityLogModal && !activityLogModal.contains(e.target) && 
            !activityLogBtn.contains(e.target)) {
            activityLogModal.classList.add('hidden');
        }
    });

    // Reposition modal on window resize
    window.addEventListener('resize', function() {
        if (!activityLogModal.classList.contains('hidden')) {
            positionModal();
        }
    });

    // Reposition modal on scroll to keep it aligned with the button
    window.addEventListener('scroll', function() {
        if (!activityLogModal.classList.contains('hidden')) {
            positionModal();
        }
    });

    // ESC key handler
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && activityLogModal && 
            !activityLogModal.classList.contains('hidden')) {
            activityLogModal.classList.add('hidden');
        }
    });
});