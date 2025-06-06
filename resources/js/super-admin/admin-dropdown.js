document.addEventListener('DOMContentLoaded', () => {
    const dropdownBtn = document.getElementById('adminDropdownBtn');
    const dropdownMenu = document.getElementById('adminDropdownMenu');
    const dropdownContainer = document.getElementById('adminDropdownContainer');

    if (!dropdownBtn || !dropdownMenu || !dropdownContainer) {
        console.error('Dropdown elements not found');
        return;
    }

    let isOpen = false;

    const toggleDropdown = () => {
        isOpen = !isOpen;
        dropdownMenu.classList.toggle('hidden');
    };

    // Toggle dropdown on button click
    dropdownBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleDropdown();
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (isOpen && !dropdownContainer.contains(e.target)) {
            toggleDropdown();
        }
    });

    // Close dropdown on Escape key
    document.addEventListener('keydown', (e) => {
        if (isOpen && e.key === 'Escape') {
            toggleDropdown();
        }
    });
});