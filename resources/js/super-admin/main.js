// Main JavaScript file that imports all modules
// This file coordinates the loading of all other JavaScript modules

document.addEventListener('DOMContentLoaded', function () {
    console.log('Super Admin Dashboard loaded');

    // Initialize a global namespace for sharing data between modules
    window.superAdmin = {
        currentUserId: null,
        userEmail: null
    };
});
