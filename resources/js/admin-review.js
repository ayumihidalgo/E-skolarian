// ------------------------GLOBAL VARIABLES------------------------------
// Hide action toast decalred gloabally
window.hideActionToast = hideActionToast;
// Added to the window object so it can be called from HTML
window.openCommentAttachmentPreview = openCommentAttachmentPreview;
// Added this line to make sortTable accessible globally
window.sortTable = sortTable;

const MESSAGE_CHARACTER_LIMITS = {
    'resubmissionMessage': 1000,   // For resubmission feedback (needs more detail)
    'approvalMessage': 500         // For approval messages
};

// Toast timeout storage
let documentActionToastTimeout = null;
window.ASSET_URLS = window.ASSET_URLS || {
  successIcon: '/images/successful.svg',
  errorIcon: '/images/error.svg'
};

// __________________________HELPER FUNCTIONS______________________________________
function repositionActionButtons() {
    const container = document.getElementById('actionButtonsContainer');
    const statusSection = document.getElementById('statusSection');
    const headerButtonArea = document.querySelector('#detailsView .flex.justify-end');
    
    if (!container) return; // Exit if container not found
    
    // Store the visibility state before moving
    const wasHidden = container.classList.contains('hidden');
    
    if (window.innerWidth < 768) { // Mobile view
        // Append to the status section specifically
        if (statusSection && container) {
            statusSection.appendChild(container);
        }
    } else { // Desktop view
        // Move back to header area for desktop
        if (headerButtonArea && container) {
            headerButtonArea.prepend(container);
        }
    }

    console.log("Is button hidden:", wasHidden);

    // Restore the visibility state after moving
    if (wasHidden) {
        container.classList.add('hidden');
    } else {
        // Additional check for document status, regardless of previous visibility
        const processedStatusIndicator = document.getElementById('processedStatusIndicator');
        const returnedStatusIndicator = document.getElementById('returnedStatusIndicator');
        
        // If any status indicator is visible, the buttons should be hidden
        if ((processedStatusIndicator && !processedStatusIndicator.classList.contains('hidden')) || 
            (returnedStatusIndicator && !returnedStatusIndicator.classList.contains('hidden'))) {
            container.classList.add('hidden');
        } else {
            container.classList.remove('hidden'); // Only show if document is not processed
        }
    }
}

// Run on page load and window resize
document.addEventListener('DOMContentLoaded', function() {
    // Original repositionActionButtons call
    repositionActionButtons();
    
    // Also observe changes to status indicators
    const processedStatusIndicator = document.getElementById('processedStatusIndicator');
    const returnedStatusIndicator = document.getElementById('returnedStatusIndicator');
    
    if (processedStatusIndicator) {
        const observer = new MutationObserver(function(mutations) {
            repositionActionButtons();
        });
        observer.observe(processedStatusIndicator, { attributes: true, attributeFilter: ['class'] });
    }
    
    if (returnedStatusIndicator) {
        const observer = new MutationObserver(function(mutations) {
            repositionActionButtons();
        });
        observer.observe(returnedStatusIndicator, { attributes: true, attributeFilter: ['class'] });
    }
});
window.addEventListener('resize', repositionActionButtons);

let currentDocumentId = null;
// Function to clear input fields in modals
function clearModalInputs(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        // Find all input, textarea and select elements in the modal
        const inputs = modal.querySelectorAll('input:not([type="button"]):not([type="submit"]), textarea, select');
        
        // Reset each input field
        inputs.forEach(input => {
            if (input.tagName === 'SELECT') {
                input.selectedIndex = 0; // Reset select to first option
            } else {
                input.value = ''; // Clear text inputs and textareas
            }
        });
    }
}

// Function to manage button loading states
function setButtonLoading(buttonId, isLoading, modalId = null) {
    const button = document.getElementById(buttonId);
    if (!button) return;
    
    const originalText = button.getAttribute('data-original-text') || button.innerHTML;
    
    if (isLoading) {
        // Save original text if not already saved
        if (!button.getAttribute('data-original-text')) {
            button.setAttribute('data-original-text', button.innerHTML);
        }
        
        // Replace with loading spinner
        button.innerHTML = `
            <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        `;
        button.classList.remove('cursor-pointer');
        button.classList.add('cursor-not-allowed');
        button.disabled = true;
        
        // Also disable related close/cancel buttons if a modal ID is provided
        if (modalId) {
            disableModalCloseButtons(modalId, true);
        }
    } else {
        // Restore original text
        button.innerHTML = originalText;
        button.classList.add('cursor-pointer');
        button.classList.remove('cursor-not-allowed');
        button.disabled = false;
        
        // Re-enable related close/cancel buttons if a modal ID is provided
        if (modalId) {
            disableModalCloseButtons(modalId, false);
        }
    }
}

// Helper function to disable/enable modal close and cancel buttons
function disableModalCloseButtons(modalId, disable) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    // Map of modals to their close/cancel button IDs
    const modalButtonMap = {
        'returnModal': ['closeReturnModalBtn'],
        'finalReturnConfirmationModal': ['closeFinalReturnModalBtn', 'cancelFinalReturnBtn'],
        'finalizeConfirmationModal': ['closeFinalizeModalBtn', 'cancelFinalizeBtn'],
        'approvalMessageModalBtn' : ['closeApprovalMessageModalBtn']
    };
    
    // Get the button IDs for the current modal
    const buttonIds = modalButtonMap[modalId] || [];
    
    // Disable/enable each close/cancel button
    buttonIds.forEach(buttonId => {
        const button = document.getElementById(buttonId);
        if (button) {
            button.disabled = disable;
            if (disable) {
                button.classList.remove('cursor-pointer');
                button.classList.add('opacity-50', 'cursor-not-allowed');
                // Store original pointer events style if not already stored
                if (!button.getAttribute('data-original-pointer-events')) {
                    button.setAttribute('data-original-pointer-events', 
                                        window.getComputedStyle(button).pointerEvents);
                }
                button.style.pointerEvents = 'none';
            } else {
                button.classList.remove('opacity-50', 'cursor-not-allowed');
                button.classList.add('cursor-pointer');
                // Restore original pointer events style if stored
                const originalPointerEvents = button.getAttribute('data-original-pointer-events');
                if (originalPointerEvents) {
                    button.style.pointerEvents = originalPointerEvents;
                } else {
                    button.style.pointerEvents = '';
                }
            }
        }
    });
}

// Add this function to your existing code
function setupCharacterLimits() {
    // For each input field that needs a character limit
    Object.entries(MESSAGE_CHARACTER_LIMITS).forEach(([inputId, limit]) => {
        const input = document.getElementById(inputId);
        if (!input) return;
        
        // Add maxlength attribute
        input.setAttribute('maxlength', limit);
        
        // Create or get counter element
        let counter = document.getElementById(`${inputId}Counter`);
        if (!counter) {
            counter = document.createElement('div');
            counter.id = `${inputId}Counter`;
            counter.className = 'text-sm text-gray-500 mt-1 text-right';
            input.parentNode.insertBefore(counter, input.nextSibling);
        }
        
        // Initial count
        updateCharacterCount(input, counter, limit);
        
        // Update count on input
        input.addEventListener('input', function() {
            updateCharacterCount(this, counter, limit);
        });
    });
}

// Helper function to update character count
function updateCharacterCount(input, counter, limit) {
    const remaining = limit - input.value.length;
    counter.textContent = `${input.value.length}/${limit}`;
    
    // Visual indication when approaching limit
    if (remaining <= 50) {
        counter.classList.add('text-orange-500');
    } else {
        counter.classList.remove('text-orange-500');
    }
    
    if (remaining <= 10) {
        counter.classList.add('text-red-500');
        counter.classList.remove('text-orange-500');
    } else {
        counter.classList.remove('text-red-500');
    }
}

/**
 * Fixes the overlapping message labels in modal forms
 */
function fixModalLabelOverlap() {
  // Get all message fields with floating labels
  const messageFields = document.querySelectorAll('#resubmissionMessage');
  
  // Process each field
  messageFields.forEach(field => {
    if (!field) return;
    
    // Add top padding to prevent text overlap with label
    field.style.paddingTop = '16px';
    
    // Find the associated label
    const label = field.parentNode.querySelector('label[for="' + field.id + '"]');
    if (label) {
      // Adjust label position to ensure it's never overlapping text
      label.style.top = '-10px';
      label.style.left = '10px';
      label.style.backgroundColor = 'white';
      label.style.zIndex = '10';
      label.style.padding = '0 5px';
      label.style.fontWeight = '500';
    }
    
    // Add focus and change handling
    field.addEventListener('focus', function() {
      if (label) label.classList.add('text-[#7A1212]');
    });
    
    field.addEventListener('blur', function() {
      if (label) label.classList.remove('text-[#7A1212]');
    });
  });
  
  // Special handling for select fields (like adminSelect)
  const selectFields = document.querySelectorAll('select[id$="Select"]');
  selectFields.forEach(field => {
    if (!field) return;
    
    field.style.paddingTop = '10px';
    
    // Find the associated label
    const label = field.parentNode.querySelector('label[for="' + field.id + '"]');
    if (label) {
      label.style.top = '-10px';
      label.style.left = '10px';
    }
  });
}

// Track unsaved changes in modals
let hasUnsavedChanges = false;
const formsToTrack = [
    { inputId: 'resubmissionMessage', modalId: 'returnModal' },
    { inputId: 'approvalMessage', modalId: 'finalApprovalMessageModal' }
];

// Function to set up unsaved changes tracking
function setupUnsavedChangesTracking() {
    // Track changes in modal textareas and inputs
    formsToTrack.forEach(form => {
        const input = document.getElementById(form.inputId);
        if (input) {
            // Set initial state when modal opens
            const modal = document.getElementById(form.modalId);
            if (modal) {
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.attributeName === 'class' && 
                            !modal.classList.contains('hidden')) {
                            // Modal just opened - reset the input's original value
                            input.setAttribute('data-original-value', input.value);
                            checkForChanges(input);
                        }
                    });
                });
                
                observer.observe(modal, { attributes: true });
            }
            
            // Track changes while typing
            input.addEventListener('input', () => {
                checkForChanges(input);
            });
            
            // Reset tracking when the form is submitted successfully
            const submitButtons = getSubmitButtonsForInput(form.inputId);
            submitButtons.forEach(buttonId => {
                const button = document.getElementById(buttonId);
                if (button) {
                    button.addEventListener('click', () => {
                        // Only consider this as a form submission intent - actual success
                        // will be handled later in the response handlers
                        hasUnsavedChanges = false;      
                        // Also reset the original value to match current value
                        // to prevent re-triggering the unsaved changes
                        const formInputId = form.inputId;
                        const formInput = document.getElementById(formInputId);
                        if (formInput) {
                            formInput.setAttribute('data-original-value', formInput.value);
                        }
                    });
                }
            });
        }
    });
    
    // Add close modal button handlers to check for unsaved changes
    document.querySelectorAll('[id$="ModalBtn"], [id^="cancel"], [id^="close"]').forEach(button => {
        if (button && !button.hasUnsavedChangesHandler) {
            // Instead of modifying onclick, add a capturing event listener that runs BEFORE any other click handlers
            button.addEventListener('click', function(e) {
                if (hasUnsavedChanges) {
                    if (!confirm('You have unsaved changes. Are you sure you want to close this window?')) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                    // If user confirmed, reset the tracking
                    hasUnsavedChanges = false;
                }
            }, true); // true means use capturing phase (runs before regular handlers)
            
            button.hasUnsavedChangesHandler = true;
        }
    });

    // Add the beforeunload event listener to the window
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            // The message text is determined by the browser and can't be customized for security reasons
            const confirmationMessage = 'You have unsaved changes. If you leave now, your changes will be lost.';
            e.returnValue = confirmationMessage;
            return confirmationMessage;
        }
    });
    
    // Reset tracking when modals are closed
    document.querySelectorAll('.modal, [id$="Modal"]').forEach(modal => {
        if (modal) {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'class' && 
                        modal.classList.contains('hidden')) {
                        // Modal was just closed - reset tracking
                        hasUnsavedChanges = false;
                    }
                });
            });
            
            observer.observe(modal, { attributes: true });
        }
    });
}

// Helper function to check if an input has unsaved changes
function checkForChanges(input) {
    const originalValue = input.getAttribute('data-original-value') || '';
    const currentValue = input.value || '';
    
    // Only mark as having changes if the field actually has content
    // This prevents warnings when closing empty modals
    if (currentValue.trim() !== originalValue.trim() && currentValue.trim() !== '') {
        hasUnsavedChanges = true;
    } else {
        // Check if any other tracked inputs have changes before setting to false
        let anyOtherChanges = false;
        formsToTrack.forEach(form => {
            const otherInput = document.getElementById(form.inputId);
            if (otherInput && otherInput !== input) {
                const otherOriginal = otherInput.getAttribute('data-original-value') || '';
                const otherCurrent = otherInput.value || '';
                if (otherCurrent.trim() !== otherOriginal.trim() && otherCurrent.trim() !== '') {
                    anyOtherChanges = true;
                }
            }
        });
        
        hasUnsavedChanges = anyOtherChanges;
    }
}

// Helper function to map input IDs to their submit button IDs
function getSubmitButtonsForInput(inputId) {
    const buttonMap = {
        'resubmissionMessage': ['submitReturnBtn'],
        'approvalMessage': ['sendApprovalMessageBtn']
    };
    
    return buttonMap[inputId] || [];
}

// Initialize unsaved changes tracking when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    setupUnsavedChangesTracking();
    setupCharacterLimits();
    fixModalLabelOverlap();
    
    // Reset unsaved changes flag after successful form submissions
    const originalShowToast = window.showDocumentActionToast;
    
    if (typeof originalShowToast === 'function') {
        window.showDocumentActionToast = function(action, message = '', isSuccess = true) {
            // If the action was successful, reset the unsaved changes flag
            if (isSuccess) {
                hasUnsavedChanges = false;
            }
            
            // Call the original function
            return originalShowToast(action, message, isSuccess);
        };
    }
});

// -------------------------------------------------------------
// ------Document Viewer Functionality--------
function handleRowClick(row) {
    // Get document ID from the data attribute
    const documentId = row.getAttribute('data-document-id');
    
    console.log("Row clicked, document ID:", documentId);
    
    if (!documentId) {
        console.error('Document ID is missing for this row.');
        return;
    }
    
    // Store the real document ID in the global variable
    currentDocumentId = documentId;
    console.log("Set currentDocumentId to:", currentDocumentId);
    
    // Mark the document as opened first (server-side update)
    fetch(`/admin/documents/${documentId}/mark-as-opened`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(result => {
        console.log("Mark as opened result:", result);
        if (result.success) {
            // Update the visual appearance of this row immediately
            row.classList.remove('border-[#7A1212]', 'bg-white');
            row.classList.add('border-[#D9D9D9]', 'bg-[#D9ACAC33]');
            
            // Remove the red dot indicator
            const dotIndicator = row.querySelector('td:last-child span[class*="bg-["]');
            if (dotIndicator) {
                dotIndicator.remove();
            }
            
            // Fetch document details and show them
            return fetch(`/admin/documents/${documentId}/details`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
        } else {
            throw new Error('Failed to mark document as opened');
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                console.error("Error response:", text);
                throw new Error('Failed to mark document as opened: ' + response.status);
            });
        }
        return response.json();
    })
    .then(docData => {
        // Update the details view with document data
        updateDocumentDetailsView(docData);
        
        // Add this line to load comments for the current document
        loadComments(documentId);
        
        // Show details view, hide table view
        const tableView = document.getElementById('tableView');
        const detailsView = document.getElementById('detailsView');
        tableView.classList.add('hidden');
        detailsView.classList.remove('hidden');
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Initial loading
document.addEventListener('DOMContentLoaded', function() {
    const documentRows = document.querySelectorAll('tbody tr');
    documentRows.forEach(row => {
        row.addEventListener('click', function(e) {
            e.preventDefault();
            handleRowClick(this);
        });
    });
});

// Filter and Search Functions
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the search functionality
    initSearchAndFilters();
    
    // Function to initialize search and filters
    function initSearchAndFilters() {
        // Get form elements
        const searchInput = document.getElementById('searchInput');
        const organizationFilter = document.getElementById('organizationFilter');
        const documentTypeFilter = document.getElementById('documentTypeFilter');
        const searchButton = document.getElementById('searchButton');
        const clearButton = document.getElementById('clearButton');
        
        // Initialize from URL parameters on page load
        const urlParams = new URLSearchParams(window.location.search);
        
        // Set search input from URL
        if (searchInput && urlParams.has('search')) {
            searchInput.value = urlParams.get('search');
        }
        
        // Set organization filter from URL
        if (organizationFilter && urlParams.has('organization')) {
            const orgValue = urlParams.get('organization');
            // Check if the option exists before setting it
            const optionExists = Array.from(organizationFilter.options).some(option => option.value === orgValue);
            if (optionExists) {
                organizationFilter.value = orgValue;
            }
        }
        
        // Set document type filter from URL
        if (documentTypeFilter && urlParams.has('documentType')) {
            const docTypeValue = urlParams.get('documentType');
            // Check if the option exists before setting it
            const optionExists = Array.from(documentTypeFilter.options).some(option => option.value === docTypeValue);
            if (optionExists) {
                documentTypeFilter.value = docTypeValue;
            }
        }
        
        if (searchInput && organizationFilter && documentTypeFilter) {
            // Remove existing event listeners first (to prevent duplicates)
            searchInput.removeEventListener('input', handleSearchInput);
            searchInput.removeEventListener('keydown', handleSearchKeydown);
            organizationFilter.removeEventListener('change', handleFilterChange);
            documentTypeFilter.removeEventListener('change', handleFilterChange);
            
            // Add debounced search input handler
            let searchTimeout;
            function handleSearchInput() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    submitAjaxSearch();
                }, 500); // 500ms debounce
            }
            searchInput.addEventListener('input', handleSearchInput);
            
            // Handle Enter key
            function handleSearchKeydown(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    clearTimeout(searchTimeout);
                    submitAjaxSearch();
                }
            }
            searchInput.addEventListener('keydown', handleSearchKeydown);
            
            // Add filter change listeners - apply filters immediately
            function handleFilterChange() {
                // Clear any pending search timeout
                clearTimeout(searchTimeout);
                // Apply the combined search and filters
                submitAjaxSearch();
            }
            organizationFilter.addEventListener('change', handleFilterChange);
            documentTypeFilter.addEventListener('change', handleFilterChange);
            
            // Add search button handler if it exists
            if (searchButton) {
                searchButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    clearTimeout(searchTimeout);
                    submitAjaxSearch();
                });
            }
            
            // Add clear button handler if it exists
            if (clearButton) {
                clearButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Reset all filters and search
                    searchInput.value = '';
                    organizationFilter.selectedIndex = 0; // Use selectedIndex to reset to first option
                    documentTypeFilter.selectedIndex = 0; // Use selectedIndex to reset to first option
                    
                    // Reset URL and refresh content
                    history.pushState(null, '', window.location.pathname);
                    
                    // Refresh the page content with no filters
                    fetch(window.location.pathname, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        updateTableContent(html);
                    });
                });
            }
            
            // Initialize event listeners for the table
            attachRowEventListeners();
        }
    }
    
    // Function to submit search via AJAX
    function submitAjaxSearch() {
        const searchInput = document.getElementById('searchInput');
        const organizationFilter = document.getElementById('organizationFilter');
        const documentTypeFilter = document.getElementById('documentTypeFilter');
        const searchTerm = searchInput.value.trim();
        
        // Show loading indicator
        showLoader();
        
        // Initialize search parameters with current values
        let searchParams = new URLSearchParams();
        
        // Always include the search term if it exists (even when empty)
        if (searchTerm) {
            // Check for full date format (MM/DD/YYYY)
            const fullDatePattern = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/;
            const fullDateMatch = searchTerm.match(fullDatePattern);
            
            if (fullDateMatch) {
                // Extract values for validation
                const month = parseInt(fullDateMatch[1], 10);
                const day = parseInt(fullDateMatch[2], 10);
                const year = parseInt(fullDateMatch[3], 10);
                
                // Validate date values
                if (isValidDate(month, day, year)) {
                    // Format as MM/DD/YYYY for consistency
                    searchParams.append('fullDate', `${month.toString().padStart(2, '0')}/${day.toString().padStart(2, '0')}/${year}`);
                } else {
                    // If invalid date, just use as regular search term
                    searchParams.append('search', searchTerm);
                }
            } else {
                // Check for month/day pattern (M/D or MM/DD)
                const monthDayPattern = /^(\d{1,2})\/(\d{1,2})$/;
                const monthDayMatch = searchTerm.match(monthDayPattern);
                
                if (monthDayMatch && searchTerm.length <= 5) {
                    // Extract values for validation
                    const month = parseInt(monthDayMatch[1], 10);
                    const day = parseInt(monthDayMatch[2], 10);
                    
                    // Validate month and day values
                    if (isValidDate(month, day, new Date().getFullYear())) {
                        // Format as MM/DD for consistency
                        searchParams.append('monthDayPattern', `${month.toString().padStart(2, '0')}/${day.toString().padStart(2, '0')}`);
                    } else {
                        // If invalid month/day, just use as regular search term
                        searchParams.append('search', searchTerm);
                    }
                } else {
                    // Not a date pattern, use as direct search term
                    searchParams.append('search', searchTerm);
                }
            }
        } else {
            // If search is empty but we're coming from a search action, 
            // explicitly include empty search to clear previous search
            searchParams.append('search', '');
        }
        
        // Always add filter values, regardless of search term
        // This is key for maintaining both filters and search simultaneously
        if (organizationFilter.value && organizationFilter.value !== '' && organizationFilter.value !== 'All') {
            searchParams.append('organization', organizationFilter.value);
        }
        
        if (documentTypeFilter.value && documentTypeFilter.value !== '' && documentTypeFilter.value !== 'All') {
            searchParams.append('documentType', documentTypeFilter.value);
        }
        
        // Update URL with all parameters
        const newUrl = window.location.pathname + '?' + searchParams.toString();
        history.pushState(null, '', newUrl);
        
        // Make AJAX request
        fetch(newUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Preserve current filter and search values before updating content
            const currentSearchValue = searchInput.value;
            const currentOrgValue = organizationFilter.value;
            const currentDocTypeValue = documentTypeFilter.value;
            
            // Update table content
            updateTableContent(html);
            
            // Restore filter and search values
            if (searchInput) searchInput.value = currentSearchValue;
            if (organizationFilter) organizationFilter.value = currentOrgValue || '';
            if (documentTypeFilter) documentTypeFilter.value = currentDocTypeValue || '';
            
            hideLoader();
        })
        .catch(error => {
            console.error('Error fetching results:', error);
            hideLoader();
            
            // Show error message to user
            const tableView = document.getElementById('tableView');
            if (tableView) {
                tableView.innerHTML = `
                    <div class="p-4 text-center">
                        <div class="text-red-500 mb-2">Error loading results</div>
                        <button id="retryButton" class="bg-[#7A1212] text-white px-4 py-2 rounded">
                            Retry
                        </button>
                    </div>
                `;
                
                // Add retry button functionality
                document.getElementById('retryButton').addEventListener('click', function() {
                    submitAjaxSearch();
                });
            }
        });
    }
    
    // Helper function to update table content and reinitialize event listeners
    function updateTableContent(html) {
        // Parse the HTML response
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Extract and update the table content
        const newTable = doc.querySelector('#tableView');
        if (newTable) {
            const tableView = document.querySelector('#tableView');
            
            // Get current values BEFORE replacing content
            const searchInput = document.getElementById('searchInput');
            const orgFilter = document.getElementById('organizationFilter');
            const docTypeFilter = document.getElementById('documentTypeFilter');
            
            const searchValue = searchInput ? searchInput.value : '';
            const orgValue = orgFilter ? orgFilter.value : '';
            const docTypeValue = docTypeFilter ? docTypeFilter.value : '';
            
            // Update the table content
            tableView.innerHTML = newTable.innerHTML;
            
            // Restore input values AFTER replacing content
            const newSearchInput = document.getElementById('searchInput');
            const newOrgFilter = document.getElementById('organizationFilter');
            const newDocTypeFilter = document.getElementById('documentTypeFilter');
            
            if (newSearchInput) newSearchInput.value = searchValue;
            
            // Carefully restore dropdown values, checking if options exist
            if (newOrgFilter && orgValue) {
                const optionExists = Array.from(newOrgFilter.options).some(option => option.value === orgValue);
                if (optionExists) {
                    newOrgFilter.value = orgValue;
                }
            }
            
            if (newDocTypeFilter && docTypeValue) {
                const optionExists = Array.from(newDocTypeFilter.options).some(option => option.value === docTypeValue);
                if (optionExists) {
                    newDocTypeFilter.value = docTypeValue;
                }
            }
            
            // Reattach all event listeners
            attachRowEventListeners();
        }
        
        // Display "no results" message if needed
        const tableBody = document.querySelector('tbody');
        if (tableBody && !tableBody.querySelector('tr')) {
            const colSpan = document.querySelectorAll('thead th').length || 6;
            tableBody.innerHTML = `
                <tr>
                    <td colspan="${colSpan}" class="text-center py-4 text-gray-500">
                        <div class="py-8">
                            <p class="mb-4">No documents found matching your search criteria</p>
                            <button id="clearFiltersBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded">
                                Clear All Filters
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            
            // Add event listener to the clear filters button
            const clearFiltersBtn = document.getElementById('clearFiltersBtn');
            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', function() {
                    // Reset search and filters
                    const searchInput = document.getElementById('searchInput');
                    const orgFilter = document.getElementById('organizationFilter');
                    const docTypeFilter = document.getElementById('documentTypeFilter');
                    
                    if (searchInput) searchInput.value = '';
                    if (orgFilter) orgFilter.selectedIndex = 0;
                    if (docTypeFilter) docTypeFilter.selectedIndex = 0;
                    
                    // Reset URL and reload
                    history.pushState(null, '', window.location.pathname);
                    location.reload();
                });
            }
        }
        
        // Make sure search and filter event listeners are reattached
        initSearchAndFilters();
    }
    
    // Function to attach event listeners to table rows
    function attachRowEventListeners() {
        const rows = document.querySelectorAll('tr[data-document-id]');
        rows.forEach(row => {
            row.addEventListener('click', function(e) {
                e.preventDefault();
                handleRowClick(this);
            });
        });
        
        // Also reattach pagination listeners if needed
        attachPaginationEventListeners();
    }
    
    // Function to attach pagination event listeners
    function attachPaginationEventListeners() {
        const paginationLinks = document.querySelectorAll('.pagination-btn, .pagination-btn-prev, .pagination-btn-next');
        paginationLinks.forEach(link => {
            if (link.getAttribute('onclick') === 'return false;') return;
            
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                if (url && url !== '#') {
                    // Show loader
                    showLoader();
                    
                    // We need to preserve search and filter params when paginating
                    const currentUrl = new URL(url, window.location.origin);
                    const currentParams = new URLSearchParams(currentUrl.search);
                    
                    // Get current search and filter values from the form
                    const searchInput = document.getElementById('searchInput');
                    const orgFilter = document.getElementById('organizationFilter');
                    const docTypeFilter = document.getElementById('documentTypeFilter');
                    
                    // Add search parameter if it exists
                    if (searchInput && searchInput.value.trim()) {
                        currentParams.set('search', searchInput.value.trim());
                    }
                    
                    // Add organization filter if set
                    if (orgFilter && orgFilter.value && orgFilter.value !== 'All') {
                        currentParams.set('organization', orgFilter.value);
                    }
                    
                    // Add document type filter if set
                    if (docTypeFilter && docTypeFilter.value && docTypeFilter.value !== 'All') {
                        currentParams.set('documentType', docTypeFilter.value);
                    }
                    
                    // Build the new URL with all parameters
                    const newPaginationUrl = currentUrl.pathname + '?' + currentParams.toString();
                    
                    // Update URL without page reload
                    history.pushState(null, '', newPaginationUrl);
                    
                    // Make AJAX request for pagination
                    fetch(newPaginationUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        updateTableContent(html);
                        hideLoader();
                    })
                    .catch(error => {
                        console.error('Error fetching paginated results:', error);
                        hideLoader();
                    });
                }
            });
        });
    }
    
    // Helper function for date validation
    function isValidDate(month, day, year) {
        // Basic range checks
        if (month < 1 || month > 12 || day < 1 || day > 31 || year < 1900 || year > 2100) {
            return false;
        }
        
        // Days in month validation
        const daysInMonth = [0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        
        // Adjust February for leap years
        if (month === 2) {
            const isLeapYear = (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
            if (isLeapYear) {
                if (day > 29) return false;
            } else {
                if (day > 28) return false;
            }
        } else if (day > daysInMonth[month]) {
            return false;
        }
        
        return true;
    }
    
    // Simple loader functions
    function showLoader() {
        // Remove any existing loader
        const oldLoader = document.getElementById('search-loader');
        if (oldLoader) oldLoader.remove();

        // Find the table container
        const tableView = document.getElementById('tableView');
        if (tableView) {
            // Create loader overlay
            const loader = document.createElement('div');
            loader.id = 'search-loader';
            loader.className = 'absolute inset-0 flex items-center justify-center bg-transparent backdrop-blur-sm z-10';
            loader.style.minHeight = '200px';
            loader.innerHTML = `
                <div class="bg-white p-5 rounded-lg shadow-lg flex items-center">
                    <svg class="animate-spin h-5 w-5 mr-3 text-[#7A1212]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Searching...</span>
                </div>
            `;
            // Set position: relative to parent if not already set
            if (getComputedStyle(tableView).position === 'static') {
                tableView.style.position = 'relative';
            }
            tableView.appendChild(loader);
        }
    }
    
    function hideLoader() {
        const loader = document.getElementById('search-loader');
        if (loader) {
            loader.remove();
        }
    }
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        // Fetch content for the new URL
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            updateTableContent(html);
            
            // Update filter fields to match URL params
            const urlParams = new URLSearchParams(window.location.search);
            
            // Update search input
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.value = urlParams.get('search') || '';
            }
            
            // Update organization filter
            const organizationFilter = document.getElementById('organizationFilter');
            if (organizationFilter) {
                const orgValue = urlParams.get('organization') || '';
                if (orgValue) {
                    const optionExists = Array.from(organizationFilter.options).some(option => option.value === orgValue);
                    if (optionExists) {
                        organizationFilter.value = orgValue;
                    } else {
                        organizationFilter.selectedIndex = 0; // Default to first option if not found
                    }
                } else {
                    organizationFilter.selectedIndex = 0;
                }
            }
            
            // Update document type filter
            const documentTypeFilter = document.getElementById('documentTypeFilter');
            if (documentTypeFilter) {
                const docTypeValue = urlParams.get('documentType') || '';
                if (docTypeValue) {
                    const optionExists = Array.from(documentTypeFilter.options).some(option => option.value === docTypeValue);
                    if (optionExists) {
                        documentTypeFilter.value = docTypeValue;
                    } else {
                        documentTypeFilter.selectedIndex = 0; // Default to first option if not found
                    }
                } else {
                    documentTypeFilter.selectedIndex = 0;
                }
            }
        });
    });
    
    // Make functions available globally if needed
    window.attachRowEventListeners = attachRowEventListeners;
    window.submitAjaxSearch = submitAjaxSearch;
    
    // Add a global function to clear filters
    window.clearFiltersAndSearch = function() {
        const searchInput = document.getElementById('searchInput');
        const orgFilter = document.getElementById('organizationFilter');
        const docTypeFilter = document.getElementById('documentTypeFilter');
        
        if (searchInput) searchInput.value = '';
        if (orgFilter) orgFilter.selectedIndex = 0;
        if (docTypeFilter) docTypeFilter.selectedIndex = 0;
        
        // Reset URL
        history.pushState(null, '', window.location.pathname);
        
        // Reload content
        fetch(window.location.pathname, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            updateTableContent(html);
        });
    };
});

// Sorting Functionality
let currentSort = {
    column: -1,
    direction: 'asc'
};

function sortTable(columnIndex, type) {
    const table = document.querySelector('table');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const headers = table.querySelectorAll('th i');

    // Update sort direction
    if (currentSort.column === columnIndex) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.column = columnIndex;
        currentSort.direction = 'asc';
    }

    // Update sort icons
    headers.forEach(icon => {
        icon.className = 'fa-solid fa-sort text-[#9099A5]';
    });

    const currentHeader = headers[columnIndex];
    currentHeader.className = `fa-solid text-[#9099A5] fa-sort-${currentSort.direction === 'asc' ? 'up' : 'down'}`;

    // Sort rows
    rows.sort((a, b) => {
        let aValue = a.cells[columnIndex].textContent.trim();
        let bValue = b.cells[columnIndex].textContent.trim();

        if (type === 'date') {
            // Convert date strings to Date objects
            aValue = new Date(aValue.split('/').map((n, i) => i === 2 ? n : n.padStart(2, '0')).join('/'));
            bValue = new Date(bValue.split('/').map((n, i) => i === 2 ? n : n.padStart(2, '0')).join('/'));
        }

        if (type === 'text') {
            aValue = aValue.toLowerCase();
            bValue = bValue.toLowerCase();
        }

        if (aValue < bValue) return currentSort.direction === 'asc' ? -1 : 1;
        if (aValue > bValue) return currentSort.direction === 'asc' ? 1 : -1;
        return 0;
    });

    // Reorder table rows
    rows.forEach(row => tbody.appendChild(row));

    // Update zebra striping
    rows.forEach((row) => {
        // Remove just the background classes
        row.classList.remove('bg-white', 'bg-gray-50', 'bg-[#D9ACAC33]');
        
        // Add proper background class based on opened status
        const isOpened = row.classList.contains('border-[#D9D9D9]');
        if (isOpened) {
            row.classList.add('bg-[#D9ACAC33]');
        } else {
            row.classList.add('bg-white');
        }
    });
}

// Viewing Document Details
function updateDocumentDetailsView(docData) {
    console.log("Document data:", docData);
    
    // Format date
    const formattedDate = new Date(docData.created_at).toLocaleDateString('en-US', {
        month: 'long', 
        day: 'numeric', 
        year: 'numeric'
    });
    
    try {
        // Update document information using direct IDs
        document.getElementById('documentDate').textContent = formattedDate;
        document.getElementById('documentOrg').innerHTML = `<span class="text-[#FFFFFF91] font-normal">From:</span> ${docData.organization}`;
        document.getElementById('documentTitle').innerHTML = `<span class="text-[#FFFFFF91] font-normal">Title:</span> ${docData.subject || docData.title}`;
        document.getElementById('documentType').innerHTML = `<span class="text-[#FFFFFF91] font-normal">Document Type:</span> ${docData.type}`;
        document.getElementById('documentTag').textContent = docData.control_tag || docData.tag;

        // Update summary
        document.getElementById('documentSummary').textContent = docData.summary || 'No summary available';
            
        // Update attachment (if available)
        const attachmentSection = document.getElementById('attachmentSection');

        if (attachmentSection) {
            // Clear previous attachments
            attachmentSection.innerHTML = '';
            
            // If we have multiple attachments/versions, display them all
            if (docData.attachments && docData.attachments.length > 0) {
                // Add all versions with the latest version marked
                const versions = docData.attachments;
                versions.forEach((version, index) => {
                    const fileName = version.file_path.split('/').pop();
                    const isLatest = version.is_latest;
                    
                    // Create attachment button
                    const attachmentItem = document.createElement('div');
                    attachmentItem.className = 'mb-2';
                    
                    const button = document.createElement('button');
                    button.className = 'bg-gray-200 text-gray-800 inline-flex items-center rounded-lg px-3 md:px-4 py-1.5 md:py-2 cursor-pointer hover:bg-gray-300 text-sm md:text-base max-w-full';
                    
                    button.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <span class="break-words truncate">${fileName} (v${version.version})</span>
                        ${isLatest ? '<span class="bg-green-500 text-white text-xs px-2 py-0.5 rounded ml-2">Latest</span>' : ''}
                    `;
                    
                    // Set up click handler for viewing the document
                    button.onclick = function() {
                        openDocumentViewer(version.file_path, 'application/pdf');
                    };
                    
                    attachmentItem.appendChild(button);
                    attachmentSection.appendChild(attachmentItem);
                });
            } else if (docData.file_path) {
                // For backward compatibility - just one attachment
                const fileName = docData.file_path.split('/').pop();
                
                // Create attachment button
                const button = document.createElement('button');
                button.id = 'documentAttachment';
                button.className = 'bg-gray-200 text-gray-800 inline-flex items-center rounded-lg px-3 md:px-4 py-1.5 md:py-2 cursor-pointer hover:bg-gray-300 text-sm md:text-base max-w-full';
                
                button.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span id="documentFileName" class="break-words truncate">${fileName}</span>
                `;
                
                // Set up click handler for viewing the document
                button.onclick = function() {
                    openDocumentViewer(docData.file_path, 'application/pdf');
                };
                
                attachmentSection.appendChild(button);
            } else {
                // No attachments
                attachmentSection.innerHTML = '<p class="text-gray-500">No attachments available</p>';
            }
        }
        
        // If we have multiple versions, show them in a dropdown or list
        const versionsContainer = document.getElementById('documentVersions');
        if (versionsContainer && docData.versions && docData.versions.length > 0) {
            // Clear previous versions
            versionsContainer.innerHTML = '';
            
            // Show the versions container
            versionsContainer.classList.remove('hidden');
            
            // Add heading
            const heading = document.createElement('h2');
            heading.className = 'text-base md:text-lg text-[#FFFFFF91] font-bold mb-1 md:mb-2';
            heading.textContent = 'Document Versions';
            versionsContainer.appendChild(heading);
            
            // Create version list
            const versionList = document.createElement('div');
            versionList.className = 'bg-[#EFEFEF] text-gray-800 rounded-lg p-3 md:p-4 max-h-[200px] overflow-y-auto';
            
            // Add versions
            docData.versions.forEach(version => {
                const versionDate = new Date(version.submitted_at).toLocaleString();
                
                const versionItem = document.createElement('div');
                versionItem.className = 'flex justify-between items-center p-2 border-b border-gray-300 last:border-0';
                
                // Version info
                const versionInfo = document.createElement('div');
                versionInfo.className = 'flex-1';
                
                const versionNumber = document.createElement('span');
                versionNumber.className = 'font-bold';
                versionNumber.textContent = `Version ${version.version}`;
                if (version.is_latest) {
                    versionNumber.innerHTML += ' <span class="bg-green-500 text-white text-xs px-2 py-0.5 rounded ml-2">Latest</span>';
                }
                
                const versionTime = document.createElement('div');
                versionTime.className = 'text-sm text-gray-600';
                versionTime.textContent = versionDate;
                
                const versionComments = document.createElement('div');
                versionComments.className = 'text-sm mt-1';
                versionComments.textContent = version.comments || 'No comments provided';
                
                versionInfo.appendChild(versionNumber);
                versionInfo.appendChild(versionTime);
                versionInfo.appendChild(versionComments);
                
                // View button
                const viewButton = document.createElement('button');
                viewButton.className = 'bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-sm';
                viewButton.textContent = 'View';
                viewButton.onclick = function() {
                    openDocumentViewer(version.file_path, 'application/pdf');
                };
                
                versionItem.appendChild(versionInfo);
                versionItem.appendChild(viewButton);
                
                versionList.appendChild(versionItem);
            });
            
            versionsContainer.appendChild(versionList);
        } else if (versionsContainer) {
            // Hide the versions container if there are no versions
            versionsContainer.classList.add('hidden');
        }
            
        // Update organization info in right panel
        document.getElementById('orgName').textContent = docData.user.organization_acronym || 'Organization Name';
        document.getElementById('orgType').textContent = 'Academic Organization';

        // Set organization initial
        const orgInitial = document.getElementById('orgInitial');
        const orgProfileContainer = document.getElementById('orgProfileContainer');

        if (orgInitial && orgProfileContainer && docData.organization) {
            // If the document user has a profile picture
            if (docData.user && docData.user.profile_pic) {
                // Replace the initial with the profile image
                orgInitial.style.display = 'none';
                orgProfileContainer.innerHTML = `
                    <img src="/storage/${docData.user.profile_pic}" 
                        alt="${docData.organization}" 
                        class="w-full h-full object-cover">
                `;
            } else {
                // Use the organization_acronym field directly from the user data
                // Fall back to first letter of organization name if acronym is not available
                let initial = '';
                if (docData.user.organization_acronym) {
                    initial = docData.user.organization_acronym.charAt(0).toUpperCase();
                } else if (docData.organization) {
                    initial = docData.organization.charAt(0).toUpperCase();
                } else {
                    initial = 'O'; // Default fallback
                }
                
                orgInitial.textContent = initial;
                orgInitial.style.display = 'block';
            }
        }
        
        // Status history with timeline style 
        const statusHistory = document.getElementById('statusHistory');
        const processedStatusIndicator = document.getElementById('processedStatusIndicator');
        const returnedStatusIndicator = document.getElementById('returnedStatusIndicator');
        const actionButtonsContainer = document.getElementById('actionButtonsContainer');

        if (statusHistory && docData.timeline && Array.isArray(docData.timeline)) {
            // Sort timeline by created_at
            const timeline = [...docData.timeline].sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
            
            // Process timeline entries chronologically with grouping by user_role
            let timelineHTML = '';
            
            // Variables to track the latest return entry (if any)
            let hasBeenReturned = false;
            let latestReturnEntry = null;
            
            // Group consecutive entries by the same user_role
            let currentUserRole = null;
            let currentGroupItems = [];
            let groupedTimeline = [];
            
            // First pass: group entries by user_role
            timeline.forEach((entry, index) => {
                // Check if this is a return entry
                if (entry.status === 'returned' || entry.action_type === 'Return') {
                    hasBeenReturned = true;
                    latestReturnEntry = entry;
                }
                
                if (entry.user_role !== currentUserRole) {
                    // Start a new group when user_role changes
                    if (currentUserRole !== null && currentGroupItems.length > 0) {
                        // Save the previous group
                        groupedTimeline.push({
                            userRole: currentUserRole,
                            entries: [...currentGroupItems]
                        });
                    }
                    
                    // Start a new group
                    currentUserRole = entry.user_role;
                    currentGroupItems = [entry];
                } else {
                    // Add to current group
                    currentGroupItems.push(entry);
                }
            });
            
            // Don't forget to add the last group
            if (currentUserRole !== null && currentGroupItems.length > 0) {
                groupedTimeline.push({
                    userRole: currentUserRole,
                    entries: [...currentGroupItems]
                });
            }
            
            // Second pass: generate HTML for each group
            groupedTimeline.forEach((group, groupIndex) => {
                // Group header with its own bullet
                timelineHTML += `
                    <div class="mb-4">
                        <div class="flex items-start">
                            <div class="flex flex-col items-center mr-1">
                                <div class="w-3 h-3 rounded-full bg-[#D4B2B2] mt-1.5"></div>
                                <div class="w-[1px] flex-1 bg-[#D4B2B2]"></div>
                            </div>
                            <span class="font-bold text-white text-base">${group.userRole || 'Unknown'}</span>
                        </div>
                `;

                // Group entries aligned under the same vertical line
                group.entries.forEach((entry, entryIndex) => {
                    const entryDate = new Date(entry.created_at);
                    const formattedDate = entryDate.toLocaleString('en-US', {
                        month: 'long', day: 'numeric', year: 'numeric',
                        hour: 'numeric', minute: '2-digit', hour12: true
                    });

                    const isLatestEntry = (groupIndex === groupedTimeline.length - 1) &&
                                        (entryIndex === group.entries.length - 1);

                    let statusColor = 'text-white/90';
                    let bgColor = 'bg-[#B07575]'
                    switch (entry.status) {
                        case 'under_review': 
                            statusColor = isLatestEntry ? 'text-yellow-400' : 'text-white'; 
                            bgColor = isLatestEntry ? 'bg-yellow-700' : 'bg-[#B07575]';
                            break;
                        case 'approved':     
                            statusColor = 'text-green-400'; 
                            bgColor = 'bg-green-700';
                            break;
                        case 'returned':     
                            statusColor = 'text-orange-400'; 
                            bgColor = 'bg-orange-700';
                            break;
                        default:
                            statusColor = 'text-white/90';
                            bgColor = 'bg-[#B07575]';
                            break;
                    }

                    const displayStatus = entry.status.replace(/_/g, ' ')
                        .replace(/\b\w/g, c => c.toUpperCase());

                    timelineHTML += `
                        <div class="flex items-start ml-0.5">
                            <div class="flex flex-col items-center mr-2 -mt-2">
                                <div class="w-[1px] h-4 bg-[#D4B2B2]"></div>
                                <div class="w-1.5 h-1.5 rounded-full ${bgColor}"></div>
                                ${entryIndex !== group.entries.length - 1 ? `<div class="w-[1px] h-1 bg-[#D4B2B2]"></div>` : ''}
                            </div>
                            <div class="text-sm ${statusColor}">
                                ${displayStatus}, ${formattedDate}
                                ${isLatestEntry ? `
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Current
                                    </span>` : ''}
                            </div> 
                        </div>
                    `;
                });

                timelineHTML += `</div>`;
            });
            
            if (timeline.length === 0) {
                timelineHTML = '<p class="text-gray-300">No activity recorded yet</p>';
            }
            
            statusHistory.innerHTML = timelineHTML;

            // Show or hide action buttons and processed indicator based on document status
            const finalDecisionExists = docData.has_decision;
            const isCurrentReceiver = docData.is_current_receiver;

            // Hide all status indicators first
            if (processedStatusIndicator) processedStatusIndicator.classList.add('hidden');
            if (returnedStatusIndicator) returnedStatusIndicator.classList.add('hidden');
            
            // First handle returned status specifically
            if (hasBeenReturned) {
                // If document has been returned, hide action buttons
                if (actionButtonsContainer) actionButtonsContainer.classList.add('hidden');
                
                // Show returned status indicator
                if (returnedStatusIndicator) {
                    returnedStatusIndicator.classList.remove('hidden');
                    
                    // Format the return date
                    const returnDate = latestReturnEntry ? 
                        new Date(latestReturnEntry.created_at).toLocaleDateString('en-US', {
                            month: 'long', day: 'numeric', year: 'numeric'
                        }) : 'Unknown date';
                    
                    // Set the returned date and message
                    const forwardedDateElement = document.getElementById('forwardedDate');
                    if (forwardedDateElement) forwardedDateElement.textContent = returnDate;
                    
                    const forwardedMessageElement = document.getElementById('forwardedMessage');
                    if (forwardedMessageElement && latestReturnEntry && latestReturnEntry.message) {
                        forwardedMessageElement.textContent = latestReturnEntry.message;
                    } else if (forwardedMessageElement) {
                        forwardedMessageElement.textContent = 'No message provided';
                    }
                }
            } 
            // Then handle other cases
            else if (finalDecisionExists) {
                // If a decision has been made, hide action buttons
                if (actionButtonsContainer) actionButtonsContainer.classList.add('hidden');
                if (processedStatusIndicator) processedStatusIndicator.classList.remove('hidden');
            } else if (!isCurrentReceiver) {
                // If user is not the current receiver, hide buttons
                if (actionButtonsContainer) actionButtonsContainer.classList.add('hidden');
            } else {
                // Current receiver and no decision made yet - show enabled action buttons
                if (actionButtonsContainer) actionButtonsContainer.classList.remove('hidden');
                
                // Make sure buttons are enabled
                const approveButton = document.getElementById('approveButton');
                const rejectButton = document.getElementById('rejectButton');
                
                if (approveButton) {
                    approveButton.disabled = false;
                    approveButton.classList.remove('opacity-50', 'cursor-not-allowed');
                }
                
                if (rejectButton) {
                    rejectButton.disabled = false;
                    rejectButton.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }
    } catch (error) {
        console.error('Error updating document details:', error);
    }
}

// Close button handler
window.closeDetailsPanel = function() {
    const tableView = document.getElementById('tableView');
    const detailsView = document.getElementById('detailsView');

    // Hide details view and show table view
    detailsView.classList.add('hidden');
    tableView.classList.remove('hidden');
}

// Function to format relative time (e.g., "2 hours ago", "1 day ago")
function timeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    const minutes = Math.floor(seconds / 60);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);
    const months = Math.floor(days / 30);
    const years = Math.floor(days / 365);
    
    if (seconds < 60) {
        return 'just now';
    } else if (minutes < 60) {
        return minutes === 1 ? '1 minute ago' : `${minutes} minutes ago`;
    } else if (hours < 24) {
        return hours === 1 ? '1 hour ago' : `${hours} hours ago`;
    } else if (days < 30) {
        return days === 1 ? '1 day ago' : `${days} days ago`;
    } else if (months < 12) {
        return months === 1 ? '1 month ago' : `${months} months ago`;
    } else {
        return years === 1 ? '1 year ago' : `${years} years ago`;
    }
}

// Comment rendering
// function loadComments(documentId) {
//     fetch(`/comments/${documentId}`)
//         .then(response => response.json())
//         .then(comments => {
//             const container = document.getElementById('commentsContainer');
//             if (!container) {
//                 console.error('Comments container not found');
//                 return;
//             }
            
//             if (!Array.isArray(comments) || comments.length === 0) {
//                 container.innerHTML = '<p class="text-gray-400">No comments yet</p>';
//                 return;
//             }
            
//             container.innerHTML = comments.map(comment => {
//                 // Determine if there's an attachment
//                 const hasAttachment = comment.attachment_path && comment.attachment_name;
                
//                 // Generate attachment HTML if needed
//                 let attachmentHTML = '';
//                 if (hasAttachment) {
//                     const filePath = `/storage/${comment.attachment_path}`;
//                     const fileName = comment.attachment_name;
//                     const fileType = comment.attachment_type;
//                     const fileExt = fileName.split('.').pop().toLowerCase();
                    
//                     // Determine icon based on file type
//                     let icon = '';
//                     if (fileType.startsWith('image/')) {
//                         icon = '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>';
//                     } else if (fileType === 'application/pdf' || fileExt === 'pdf') {
//                         icon = '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>';
//                     } else if (fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || 
//                               fileExt === 'docx' || 
//                               fileType === 'application/msword' || 
//                               fileExt === 'doc') {
//                         icon = '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
//                     } else {
//                         icon = '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
//                     }
                    
//                     attachmentHTML = `
//                         <div class="mt-2 bg-gray-100 rounded p-2 inline-block max-w-full">
//                             <a href="javascript:void(0);" onclick="openCommentAttachmentPreview('${filePath}', '${fileType}', '${fileName}')" class="flex items-center text-blue-600 hover:underline">
//                                 ${icon}
//                                 <span class="text-xs truncate max-w-[200px]">${fileName}</span>
//                             </a>
//                         </div>
//                     `;
//                 }

//                 // Determine profile image
//                 let profileHTML = '';
//                 if (comment.sender && comment.sender.profile_pic) {
//                     // Use user's profile image
//                     profileHTML = `
//                         <div class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0 border border-gray-600">
//                             <img src="/storage/${comment.sender.profile_pic}" alt="Profile" class="w-full h-full object-cover">
//                         </div>
//                     `;
//                 } else {
//                     // Use default profile icon
//                     profileHTML = `
//                         <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0 border border-gray-600">
//                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
//                                 stroke="currentColor" class="w-6 h-6 text-gray-600">
//                                 <path stroke-linecap="round" stroke-linejoin="round"
//                                     d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118h15.998c-.023-3.423-3.454-6.118-6.911-6.118-3.457 0-6.888 2.695-6.911 6.118z" />
//                             </svg>
//                         </div>
//                     `;
//                 }
                
//                 // Format and wrap comment text to prevent overflow
//                 const commentText = comment.comment || '';
                
//                 // Use timeAgo for relative timestamps
//                 const relativeTime = timeAgo(comment.created_at);
                
//                 return `
//                     <div class="pb-4 mb-4">
//                         <div class="flex items-start gap-3">
//                             <div class="flex-shrink-0">
//                                 ${profileHTML}
//                             </div>
//                             <div class="flex-1 min-w-0"> <!-- Added min-w-0 to make sure flexbox respects child sizes -->
//                                 <div class="flex justify-between items-center">
//                                     <h3 class="font-bold text-white text-lg break-words">${comment.sender ? comment.sender.role_name : 'Unknown User'}</h3>
//                                     <span class="text-white text-sm whitespace-nowrap ml-2" title="${new Date(comment.created_at).toLocaleString()}">${relativeTime}</span>
//                                 </div>
//                                 <p class="text-white mt-1 break-words whitespace-pre-wrap">${commentText}</p>
//                                 ${attachmentHTML}
//                             </div>
//                         </div>
//                     </div>
//                 `;
//             }).join('');
//         })
//         .catch(error => {
//             console.error('Error loading comments:', error);
//         });
// }

function loadComments(documentId) {
    fetch(`/comments/${documentId}`)
        .then(response => response.json())
        .then(comments => {
            const container = document.getElementById('commentsContainer');
            if (!container) {
                console.error('Comments container not found');
                return;
            }
            
            if (!Array.isArray(comments) || comments.length === 0) {
                container.innerHTML = '<p class="text-gray-400">No comments yet</p>';
                return;
            }
            
            container.innerHTML = comments.map(comment => {
                // Determine if there's an attachment
                const hasAttachment = comment.attachment_path && comment.attachment_name;
                
                // Generate attachment HTML if needed
                let attachmentHTML = '';
                if (hasAttachment) {
                    const filePath = `/storage/${comment.attachment_path}`;
                    const fileName = comment.attachment_name;
                    const fileType = comment.attachment_type;
                    const fileExt = fileName.split('.').pop().toLowerCase();
                    
                    // Check if it's an image type
                    const isImage = fileType.startsWith('image/') || ['jpg', 'jpeg', 'png', 'gif'].includes(fileExt);
                    
                    if (isImage) {
                        // Display image directly in the comment
                        attachmentHTML = `
                            <div class="mt-2 max-w-full">
                                <div class="rounded overflow-hidden max-w-[250px] cursor-pointer" 
                                     onclick="openCommentAttachmentPreview('${filePath}', '${fileType}', '${fileName}')">
                                    <img src="${filePath}" alt="${fileName}" class="max-w-full h-auto">
                                    <div class="text-xs text-gray-400 mt-1 truncate">${fileName}</div>
                                </div>
                            </div>
                        `;
                    } else {
                        // For non-image files, keep the current link format
                        // Determine icon based on file type
                        let icon = '';
                        if (fileType === 'application/pdf' || fileExt === 'pdf') {
                            icon = '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>';
                        } else if (fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || 
                                  fileExt === 'docx' || 
                                  fileType === 'application/msword' || 
                                  fileExt === 'doc') {
                            icon = '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
                        } else {
                            icon = '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
                        }
                        
                        attachmentHTML = `
                            <div class="mt-2 bg-gray-100 rounded p-2 inline-block max-w-full">
                                <a href="javascript:void(0);" onclick="openCommentAttachmentPreview('${filePath}', '${fileType}', '${fileName}')" class="flex items-center text-blue-600 hover:underline">
                                    ${icon}
                                    <span class="text-xs truncate max-w-[200px]">${fileName}</span>
                                </a>
                            </div>
                        `;
                    }
                }

                // Determine profile image
                let profileHTML = '';
                if (comment.sender && comment.sender.profile_pic) {
                    // Use user's profile image
                    profileHTML = `
                        <div class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0 border border-gray-600">
                            <img src="/storage/${comment.sender.profile_pic}" alt="Profile" class="w-full h-full object-cover">
                        </div>
                    `;
                } else {
                    // Use default profile icon
                    profileHTML = `
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0 border border-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6 text-gray-600">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118h15.998c-.023-3.423-3.454-6.118-6.911-6.118-3.457 0-6.888 2.695-6.911 6.118z" />
                            </svg>
                        </div>
                    `;
                }
                
                // Format and wrap comment text to prevent overflow
                const commentText = comment.comment || '';
                
                // Use timeAgo for relative timestamps
                const relativeTime = timeAgo(comment.created_at);
                
                return `
                    <div class="pb-4 mb-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                ${profileHTML}
                            </div>
                            <div class="flex-1 min-w-0"> <!-- Added min-w-0 to make sure flexbox respects child sizes -->
                                <div class="flex justify-between items-center">
                                    <h3 class="font-bold text-white text-lg break-words">${comment.sender ? comment.sender.role_name : 'Unknown User'}</h3>
                                    <span class="text-white text-sm whitespace-nowrap ml-2" title="${new Date(comment.created_at).toLocaleString()}">${relativeTime}</span>
                                </div>
                                <p class="text-white mt-1 break-words whitespace-pre-wrap">${commentText}</p>
                                ${attachmentHTML}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        })
        .catch(error => {
            console.error('Error loading comments:', error);
        });
}

// Add the comment attachment preview function
function openCommentAttachmentPreview(filePath, fileType, fileName) {
    console.log("Opening comment attachment preview for:", filePath);
    const modal = document.getElementById('documentViewerModal');
    const pdfViewer = document.getElementById('pdfViewer');
    const imageViewer = document.getElementById('imageViewer');
    const downloadView = document.getElementById('downloadView');
    const documentTitle = document.getElementById('documentTitle');
    const previewTab = document.getElementById('previewTab');
    const downloadTab = document.getElementById('downloadTab');
    const downloadButton = document.getElementById('downloadButton');
    const downloadFileName = document.getElementById('downloadFileName');
    
    // Set the document title and download filename
    documentTitle.textContent = fileName;
    downloadFileName.textContent = fileName;
    
    // Set up download link
    downloadButton.href = filePath;
    downloadButton.setAttribute('download', fileName);
    
    // Show modal first to ensure container is visible
    modal.classList.remove('hidden');
    
    // Tab switching event listeners (reuse existing functionality)
    previewTab.click(); // Show preview by default
    
    // Determine content type and display appropriately
    const isDocx = fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || 
                  fileName.toLowerCase().endsWith('.docx') ||
                  fileType === 'application/msword' ||
                  fileName.toLowerCase().endsWith('.doc');
                  
    const isPdf = fileType === 'application/pdf' || fileName.toLowerCase().endsWith('.pdf');
    
    if (isPdf || isDocx) {
        // For PDF and DOCX files
        pdfViewer.innerHTML = '';
        imageViewer.classList.add('hidden');
        pdfViewer.classList.remove('hidden');
        
        const viewerDiv = document.createElement('div');
        viewerDiv.id = 'pdf-viewer-container';
        viewerDiv.className = 'h-full';
        pdfViewer.appendChild(viewerDiv);
        
        // Initialize WebViewer
        WebViewer({
            path: '/webviewer',
            initialDoc: filePath,
            extension: isDocx ? 'docx' : 'pdf',
            enableFilePicker: false,
            enableAnnotations: false,
        }, viewerDiv).then(instance => {
            // Save instance for later cleanup
            window.currentPdfViewerInstance = instance;
            
            // Basic configuration
            const { docViewer, UI } = instance;
            
            // Enable download button in WebViewer
            UI.enableElements(['downloadButton']);
            UI.disableElements(['printButton']);
            
            // For DOCX files, configure specific options
            if (isDocx) {
                UI.setToolbarGroup('toolbarGroup-View');
            }
        }).catch(error => {
            console.error("Failed to load WebViewer:", error);
            pdfViewer.innerHTML = `
                <div class="p-4 text-red-500">Failed to load document viewer. Error: ${error.message}</div>
                <div class="p-4">
                    <a href="${filePath}" download="${fileName}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Download Document Instead
                    </a>
                </div>
            `;
        });
    } else if (fileType.startsWith('image/') || ['jpg', 'jpeg', 'png', 'gif'].some(ext => fileName.toLowerCase().endsWith(ext))) {
        // For image files
        pdfViewer.classList.add('hidden');
        imageViewer.classList.remove('hidden');
        imageViewer.innerHTML = `<img src="${filePath}" class="max-h-full max-w-full object-contain" alt="Document Preview">`;
    } else {
        // For other file types, directly show download view
        pdfViewer.classList.add('hidden');
        imageViewer.classList.add('hidden');
        downloadView.classList.remove('hidden');
        
        // Activate download tab
        downloadTab.classList.add('bg-blue-500', 'text-white');
        downloadTab.classList.remove('text-gray-700');
        previewTab.classList.remove('bg-blue-500', 'text-white');
        previewTab.classList.add('text-gray-700');
    }
}   

// Comment submitting
function submitComment() {
    const input = document.getElementById('commentInput');
    const fileInput = document.getElementById('commentAttachment');
    const attachmentPreview = document.getElementById('attachmentPreview');
    const submitBtn = document.getElementById('submitCommentBtn');
    
    if (!input || !fileInput) {
        console.error('Comment input fields not found');
        return;
    }
    
    const comment = input.value.trim();
    const file = fileInput.files[0];

    // Validate - need at least a comment or a file
    if (!comment && !file) {
        showDocumentActionToast('comment', 'Please enter a comment or attach a file', false);
        return;
    }
    
    // Check if we have a valid document ID
    if (!currentDocumentId) {
        console.error('Missing document ID');
        return;
    }

    // Create FormData object to handle file uploads
    const formData = new FormData();
    formData.append('document_id', currentDocumentId);
    
    if (comment) {
        formData.append('comment', comment);
    }
    
    if (file) {
        formData.append('attachment', file);
    }

    // Create loading indicator
    const originalInnerHTML = submitBtn.innerHTML;
    submitBtn.innerHTML = `
        <svg class="animate-spin h-4 w-4 md:h-5 md:w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    `;
    submitBtn.disabled = true;

    fetch('/comments', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'  // Explicitly request JSON response
        },
        body: formData
    })
    .then(response => {
        // Check if the response is ok (status in the range 200-299)
        if (!response.ok) {
            // Check if the response is HTML instead of JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('text/html')) {
                // Return a custom error object that won't break JSON parsing
                return { 
                    success: false, 
                    message: `Server returned HTML (status ${response.status}). This might be due to a server error or session timeout.` 
                };
            }
            
            // Try to get error details from JSON response
            return response.json().then(errorData => {
                throw new Error(errorData.message || `Server returned ${response.status}`);
            }).catch(jsonError => {
                // If JSON parsing fails, throw a generic error
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            });
        }
        
        // If response is OK, parse as JSON
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Reset form fields
            input.value = '';
            fileInput.value = '';
            attachmentPreview.classList.add('hidden');
            
            // Reload comments
            loadComments(currentDocumentId);
            
            // Show success message
            showDocumentActionToast('comment', 'Comment added successfully', true);
        } else {
            throw new Error(data.message || 'Failed to add comment');
        }
    })
    .catch(error => {
        console.error('Error submitting comment:', error);
        showDocumentActionToast('comment', error.message || 'Failed to add comment', false);
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalInnerHTML;
        submitBtn.disabled = false;
    });
}

// Add event listener for Enter key
document.getElementById('commentInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        submitComment();
    }
});

// Event listener for comment submit button
document.addEventListener('DOMContentLoaded', function() {
    const submitCommentBtn = document.getElementById('submitCommentBtn');
    if (submitCommentBtn) {
        submitCommentBtn.addEventListener('click', function() {
            submitComment();
        });
    }
});

// Event listeners for file input
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('commentAttachment');
    const attachmentPreview = document.getElementById('attachmentPreview');
    const attachmentName = document.getElementById('attachmentName');
    const removeAttachment = document.getElementById('removeAttachment');
    
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Display file name in preview area
                attachmentName.textContent = file.name;
                attachmentPreview.classList.remove('hidden');
                
                // Validate file type
                const validTypes = ['image/jpeg', 'image/png', 'application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword'];
                if (!validTypes.includes(file.type)) {
                    showDocumentActionToast('comment', 'Invalid file type. Please upload an image, PDF, or Word document.', false);
                    fileInput.value = '';
                    attachmentPreview.classList.add('hidden');
                    return;
                }
                
                // Validate file size (minimum 1KB for testing)
                if (file.size < 1024) {
                    showDocumentActionToast('comment', 'File is too small. Minimum size is 1KB.', false);
                    fileInput.value = '';
                    attachmentPreview.classList.add('hidden');
                    return;
                }
                
                // Validate file size (maximum 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    showDocumentActionToast('comment', 'File is too large. Maximum size is 10MB.', false);
                    fileInput.value = '';
                    attachmentPreview.classList.add('hidden');
                    return;
                }
            }
        });
    }
    
    if (removeAttachment) {
        removeAttachment.addEventListener('click', function() {
            fileInput.value = '';
            attachmentPreview.classList.add('hidden');
        });
    }
    
    // Prevent form submission on Enter
    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
        });
    }
});

// Function to show document preview
function openDocumentViewer(filePath, fileType) {
    console.log("Opening document viewer for:", filePath);
    const modal = document.getElementById('documentViewerModal');
    const pdfViewer = document.getElementById('pdfViewer');
    const imageViewer = document.getElementById('imageViewer');
    const downloadView = document.getElementById('downloadView');
    const documentTitle = document.getElementById('documentTitle');
    const previewTab = document.getElementById('previewTab');
    const downloadTab = document.getElementById('downloadTab');
    const downloadButton = document.getElementById('downloadButton');
    const downloadFileName = document.getElementById('downloadFileName');
    
    // Extract just the filename for display
    const filename = filePath.split('/').pop();
    documentTitle.textContent = filename;
    downloadFileName.textContent = filename;
    
    // Set up download link
    const fullPath = filePath.startsWith('/') ? filePath : `/${filePath}`;
    downloadButton.href = fullPath;
    downloadButton.setAttribute('download', filename);
    
    // Show modal first to ensure container is visible
    modal.classList.remove('hidden');
    
    // Tab switching event listeners
    previewTab.addEventListener('click', function() {
        // Activate preview tab
        previewTab.classList.add('bg-blue-500', 'text-white');
        previewTab.classList.remove('text-gray-700');
        downloadTab.classList.remove('bg-blue-500', 'text-white');
        downloadTab.classList.add('text-gray-700');
        
        // Show preview, hide download view
        downloadView.classList.add('hidden');
        
        if (fileType === 'application/pdf' || filename.toLowerCase().endsWith('.pdf')) {
            pdfViewer.classList.remove('hidden');
            imageViewer.classList.add('hidden');
        } else {
            pdfViewer.classList.add('hidden');
            imageViewer.classList.remove('hidden');
        }
    });
    
    downloadTab.addEventListener('click', function() {
        // Activate download tab
        downloadTab.classList.add('bg-blue-500', 'text-white');
        downloadTab.classList.remove('text-gray-700');
        previewTab.classList.remove('bg-blue-500', 'text-white');
        previewTab.classList.add('text-gray-700');
        
        // Hide preview, show download view
        pdfViewer.classList.add('hidden');
        imageViewer.classList.add('hidden');
        downloadView.classList.remove('hidden');
    });
    
    // Show preview by default
    previewTab.click();
    
    // For PDF files
    if (fileType === 'application/pdf' || filename.toLowerCase().endsWith('.pdf')) {
        // Create viewer container
        pdfViewer.innerHTML = '';
        const viewerDiv = document.createElement('div');
        viewerDiv.id = 'pdf-viewer-container';
        viewerDiv.className = 'h-full';
        pdfViewer.appendChild(viewerDiv);
        
        // Generate the full PDF URL
        const pdfUrl = fullPath;
        
        // Initialize WebViewer
        WebViewer({
            path: '/webviewer',
            initialDoc: pdfUrl,
        }, viewerDiv).then(instance => {
            // Save instance for later cleanup
            window.currentPdfViewerInstance = instance;
            
            // Basic configuration
            const { docViewer, UI } = instance;
            
            // Enable download button in WebViewer
            UI.enableElements(['downloadButton']);
            UI.disableElements(['printButton']);
        }).catch(error => {
            console.error("Failed to load WebViewer:", error);
            pdfViewer.innerHTML = `
                <div class="p-4 text-red-500">Failed to load document viewer. Error: ${error.message}</div>
                <div class="p-4">
                    <a href="${fullPath}" download="${filename}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Download Document Instead
                    </a>
                </div>
            `;
        });
    } else {
        // For image files
        imageViewer.innerHTML = `<img src="${fullPath}" class="max-h-full max-w-full" alt="Document Preview">`;
    }
}

// Add function to close the document viewer
window.closeDocumentViewer = function() {
    const modal = document.getElementById('documentViewerModal');
    const pdfViewer = document.getElementById('pdfViewer');
    
    // Clean up any existing WebViewer instance
    if (window.currentPdfViewerInstance) {
        try {
            // For newer versions of WebViewer
            if (typeof window.currentPdfViewerInstance.dispose === 'function') {
                window.currentPdfViewerInstance.dispose();
            } 
            // For older versions
            else if (window.currentPdfViewerInstance.docViewer && 
                    typeof window.currentPdfViewerInstance.docViewer.dispose === 'function') {
                window.currentPdfViewerInstance.docViewer.dispose();
            }
        } catch (error) {
            console.error("Error disposing WebViewer:", error);
        }
        window.currentPdfViewerInstance = null;
    }
    
    // Clear the PDF viewer
    pdfViewer.innerHTML = '';
    
    modal.classList.add('hidden');
}

// Handle Approve button
document.addEventListener('DOMContentLoaded', function () {
    const approveButton = document.getElementById('approveButton');

    // Open the modal when the Approve button is clicked, but check if disabled first
    if (approveButton) {
        approveButton.addEventListener('click', function(e) {
            if (this.disabled) {
                e.preventDefault();
                showDocumentActionToast('approved', 'This document has already been reviewed and cannot be modified.', false);
                return;
            }
            
            // If not disabled, show the approval message modal directly
            document.getElementById('finalApprovalMessageModal').classList.remove('hidden');
        });
    }

    // Close approval message modal when close button is clicked
    const closeApprovalMessageModalBtn = document.getElementById('closeApprovalMessageModalBtn');
    if (closeApprovalMessageModalBtn) {
        closeApprovalMessageModalBtn.addEventListener('click', function() {
            const messageField = document.getElementById('approvalMessage');
              
            // Hide the modal
            document.getElementById('finalApprovalMessageModal').classList.add('hidden');

            // Clear the approval message input field
            const approvalMessage = document.getElementById('approvalMessage');
            if (approvalMessage) {
                approvalMessage.value = '';
                
                // Also reset the character counter
                const counter = document.getElementById('approvalMessageCounter');
                if (counter) {
                    counter.textContent = `0/${MESSAGE_CHARACTER_LIMITS.approvalMessage}`;
                    counter.classList.remove('text-orange-500', 'text-red-500');
                }
            }
            
            // Clear the input fields using the existing helper function
            clearModalInputs('finalApprovalMessageModal');
            
            // Clear any validation errors
            if (messageField) {
                messageField.classList.remove('border-red-500');
            }
            
            const errorMsg = document.getElementById('approvalMessageError');
            if (errorMsg) {
                errorMsg.remove();
            }
            
            // Reset unsaved changes flag
            hasUnsavedChanges = false;
        });
    }
});

// Handle close finalize approval button
const closeFinalizeModalBtn = document.getElementById('closeFinalizeModalBtn');
if (closeFinalizeModalBtn) {
    closeFinalizeModalBtn.addEventListener('click', function() {
        const approvalMessage = document.getElementById('approvalMessage');
        // Check if there are unsaved changes in the approval message
        if (approvalMessage && approvalMessage.value.trim() !== '') {
            // Show confirmation dialog
            if (confirm('You have unsaved changes. Are you sure you want to close this window?')) {
                // Clear the approval message input field
                const approvalMessage = document.getElementById('approvalMessage');
                if (approvalMessage) {
                    approvalMessage.value = '';
                    
                    // Also reset the character counter
                    const counter = document.getElementById('approvalMessageCounter');
                    if (counter) {
                        counter.textContent = `0/${MESSAGE_CHARACTER_LIMITS.approvalMessage}`;
                        counter.classList.remove('text-orange-500', 'text-red-500');
                    }
                }
                // Hide the modal
                document.getElementById('finalizeConfirmationModal').classList.add('hidden');
            }
            // If user cancels, the modal stays open
        } else {
            // No unsaved changes, just close the modal
            document.getElementById('finalizeConfirmationModal').classList.add('hidden');
        }
    });
}

// Finalized Approve Cancel buttons
const cancelFinalizeBtn = document.getElementById('cancelFinalizeBtn');
if (cancelFinalizeBtn) {
    cancelFinalizeBtn.addEventListener('click', function() {
        // Hide confirmation modal
        document.getElementById('finalizeConfirmationModal').classList.add('hidden');
        
        // Clear the approval message input field
        const approvalMessage = document.getElementById('approvalMessage');
        if (approvalMessage) {
            approvalMessage.value = '';
            
            // Also reset the character counter
            const counter = document.getElementById('approvalMessageCounter');
            if (counter) {
                counter.textContent = `0/${MESSAGE_CHARACTER_LIMITS.approvalMessage}`;
                counter.classList.remove('text-orange-500', 'text-red-500');
            }
        }
        
        // Clear other input fields using the existing helper function
        clearModalInputs('finalApprovalMessageModal');
        
        // Reset any error states that might exist
        if (approvalMessage) {
            approvalMessage.classList.remove('border-red-500');
        }
        
        const errorMsg = document.getElementById('approvalMessageError');
        if (errorMsg) {
            errorMsg.remove();
        }
        
        // Reset the unsaved changes flag
        hasUnsavedChanges = false;
    });
}

// A new event handler for the "SEND" button in the approval message modal
document.addEventListener('DOMContentLoaded', function() {
    const sendApprovalMessageBtn = document.getElementById('sendApprovalMessageBtn');
    if (sendApprovalMessageBtn) {
        sendApprovalMessageBtn.addEventListener('click', function() {
            // Get the message and trim whitespace
            const messageField = document.getElementById('approvalMessage');
            const message = messageField.value.trim();
            
            // Validate the message
            if (!message) {
                // Show error styling
                messageField.classList.add('border-red-500');
                
                // Add error message below textarea if it doesn't exist already
                let errorMsg = document.getElementById('approvalMessageError');
                if (!errorMsg) {
                    errorMsg = document.createElement('p');
                    errorMsg.id = 'approvalMessageError';
                    errorMsg.className = 'text-red-500 text-sm -mt-5';
                    errorMsg.textContent = 'Please provide a message for the final approval.';
                    messageField.parentNode.appendChild(errorMsg);
                }
                
                // Shake the message field to indicate error
                messageField.classList.add('error-shake');
                setTimeout(() => {
                    messageField.classList.remove('error-shake');
                }, 500);
                
                return; // Stop execution
            }
            
            // If validation passed, remove error styling
            messageField.classList.remove('border-red-500');
            const errorMsg = document.getElementById('approvalMessageError');
            if (errorMsg) {
                errorMsg.remove();
            }
            
            // Add real-time validation to clear errors as user types
            messageField.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('border-red-500');
                    const errorMsg = document.getElementById('approvalMessageError');
                    if (errorMsg) errorMsg.remove();
                }
            });
            
            // Hide the message modal
            document.getElementById('finalApprovalMessageModal').classList.add('hidden');
            
            // Show the confirmation modal with the message
            const finalizeConfirmationModal = document.getElementById('finalizeConfirmationModal');
            
            // Add the message to the confirmation screen so the admin can review it
            const confirmationMessageDisplay = document.getElementById('confirmationMessage');
            if (confirmationMessageDisplay) {
                confirmationMessageDisplay.textContent = message;
            }
            
            // Show the confirmation modal
            finalizeConfirmationModal.classList.remove('hidden');
        });
    }

    const approvalMessage = document.getElementById('approvalMessage');
    if (approvalMessage) {
        approvalMessage.addEventListener('focus', function() {
            this.classList.remove('border-red-500');
            const errorMsg = document.getElementById('approvalMessageError');
            if (errorMsg) errorMsg.remove();
        });
        
        // Add real-time validation
        approvalMessage.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('border-red-500');
                const errorMsg = document.getElementById('approvalMessageError');
                if (errorMsg) errorMsg.remove();
                hasUnsavedChanges = true;
            } else {
                // If reverted to original state
                hasUnsavedChanges = false;
            }
        });
    }

    let originalApprovalMessage = '';
    
    // Capture the original approval message when modal is opened
    const finalApprovalMessageModal = document.getElementById('finalApprovalMessageModal');
    if (finalApprovalMessageModal) {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    // If the modal just became visible
                    if (!finalApprovalMessageModal.classList.contains('hidden') && approvalMessage) {
                        // Store the original message value (usually empty)
                        originalApprovalMessage = approvalMessage.value;
                    }
                }
            });
        });
        
        observer.observe(finalApprovalMessageModal, { attributes: true });
    }

    // Enhance the existing window beforeunload listener to specifically check approval message
    const existingBeforeUnloadHandler = window.onbeforeunload;
    
    window.onbeforeunload = function(e) {
        // First check specific approval message changes
        if (approvalMessage && 
            !finalApprovalMessageModal.classList.contains('hidden') && 
            approvalMessage.value.trim() !== originalApprovalMessage.trim() && 
            approvalMessage.value.trim() !== '') {
            
            // Standard message for beforeunload event
            const confirmationMessage = 'You have unsaved changes in your approval message. If you leave now, your changes will be lost.';
            e.returnValue = confirmationMessage; // Required for Chrome
            return confirmationMessage; // For other browsers
        }
        
        // Then check global unsaved changes flag
        if (hasUnsavedChanges) {
            const confirmationMessage = 'You have unsaved changes. If you leave now, your changes will be lost.';
            e.returnValue = confirmationMessage;
            return confirmationMessage;
        }
        
        // Call existing handler if defined and we haven't returned yet
        if (existingBeforeUnloadHandler) {
            return existingBeforeUnloadHandler(e);
        }
    };
    
    // Finalize approve button handler to use the message from the input
    const confirmFinalizeBtn = document.getElementById('confirmFinalizeBtn');
    if (confirmFinalizeBtn) {
        confirmFinalizeBtn.addEventListener('click', function() {
            // Get the approval message from the input field
            const approvalMessage = document.getElementById('approvalMessage').value.trim();
            
            if (!approvalMessage) {
                showDocumentActionToast('approved', 'Please provide an approval message.', false);
                return;
            }
            
            // Make sure we have a valid ID
            if (!currentDocumentId) {
                showDocumentActionToast('approved', "Error: Document ID is missing. Please try again.", false);
                return;
            }
            
            // Reset the flag since we're submitting the form
            hasUnsavedChanges = false;
            
            // Reset original message value
            originalApprovalMessage = '';
            
            // Show loading state
            setButtonLoading('confirmFinalizeBtn', true, 'finalizeConfirmationModal');
            
            // Submit the approval request
            fetch(`/admin/documents/${currentDocumentId}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message: approvalMessage
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.error || 'Server error');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success toast
                    showDocumentActionToast('approved');
                    
                    // Close the modal
                    document.getElementById('finalizeConfirmationModal').classList.add('hidden');
                    
                    // Update the UI to reflect the approval
                    fetch(`/admin/documents/${currentDocumentId}/details`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(docData => {
                        // Update the status history and other details
                        updateDocumentDetailsView(docData);
                        
                        // Reload comments to include any system-generated comments
                        loadComments(currentDocumentId);
                        
                        // Update UI to show document is approved
                        const actionButtonsContainer = document.getElementById('actionButtonsContainer');
                        if (actionButtonsContainer) {
                            actionButtonsContainer.classList.add('hidden');
                        }
                        
                        // Show processedStatusIndicator
                        const processedStatusIndicator = document.getElementById('processedStatusIndicator');
                        if (processedStatusIndicator) {
                            processedStatusIndicator.classList.remove('hidden');
                        }
                        
                        // Ensure returnedStatusIndicator is hidden
                        const returnedStatusIndicator = document.getElementById('returnedStatusIndicator');
                        if (returnedStatusIndicator) {
                            returnedStatusIndicator.classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error refreshing document details:', error);
                    });
                } else {
                    showDocumentActionToast('approved', data.error || 'Failed to approve document.', false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showDocumentActionToast('approved', error.message || 'An error occurred while approving the document.', false);
            })
            .finally(() => {
                // Reset button state
                setButtonLoading('confirmFinalizeBtn', false, 'finalizeConfirmationModal');
            });
        });
    }
});       

// Return button functionality
document.addEventListener('DOMContentLoaded', function() {
    const rejectButton = document.getElementById('rejectButton');
    if (rejectButton) {
        rejectButton.addEventListener('click', function(e) {
            if (this.disabled) {
                e.preventDefault();
                showDocumentActionToast('return', 'This document has already been reviewed and cannot be modified.', false);
                return;
            }
            
            // If not disabled, show the resubmission modal directly
            document.getElementById('returnModal').classList.remove('hidden');
        });
    }
});

// Handles "RETURN" Button functionality
document.addEventListener('DOMContentLoaded', function() {
    const submitReturnBtn = document.getElementById('submitReturnBtn');
    const closeReturnModalBtn = document.getElementById('closeReturnModalBtn');
    const returnModal = document.getElementById('returnModal');
    
    // Close resubmission modal
    if (closeReturnModalBtn) {
        closeReturnModalBtn.addEventListener('click', function() {
            document.getElementById('returnModal').classList.add('hidden');
            clearModalInputs('returnModal');
        });
    }

    const resubmissionMessage = document.getElementById('resubmissionMessage');
    if (resubmissionMessage) {
        // Add real-time validation as soon as the element is available
        resubmissionMessage.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('border-red-500');
                const errorMsg = document.getElementById('resubmissionMessageError');
                if (errorMsg) errorMsg.remove();
            }
        });
        
        // Also add focus event for better UX
        resubmissionMessage.addEventListener('focus', function() {
            this.classList.remove('border-red-500');
            const errorMsg = document.getElementById('resubmissionMessageError');
            if (errorMsg) errorMsg.remove();
        });
    }
    
    // Handle submit resubmission
    if (submitReturnBtn) {
        submitReturnBtn.addEventListener('click', function() {
            const message = document.getElementById('resubmissionMessage').value.trim();

            if (!message) {
                // Show error styling
                const messageField = document.getElementById('resubmissionMessage');
                messageField.classList.add('border-red-500');
                
                // Add error message below textarea if it doesn't exist already
                let errorMsg = document.getElementById('resubmissionMessageError');
                if (!errorMsg) {
                    errorMsg = document.createElement('p');
                    errorMsg.id = 'resubmissionMessageError';
                    errorMsg.className = 'text-red-500 text-sm -mt-6';
                    errorMsg.textContent = 'Please provide feedback for resubmission.';
                    messageField.parentNode.appendChild(errorMsg);
                }
                
                // Shake the message field to indicate error
                messageField.classList.add('error-shake');
                setTimeout(() => {
                    messageField.classList.remove('error-shake');
                }, 500);
                
                return; // Stop execution
            }
            
            // If validation passed, remove error styling
            const messageField = document.getElementById('resubmissionMessage');
            messageField.classList.remove('border-red-500');
            const errorMsg = document.getElementById('resubmissionMessageError');
            if (errorMsg) {
                errorMsg.remove();
            }
            
            if (!currentDocumentId) {
                showDocumentActionToast('return', 'Error: Document ID is missing.', false);
                return;
            }

            // Hide the resubmission modal
            document.getElementById('returnModal').classList.add('hidden');
            
            // Show the Final Reject Confirmation Modal instead of making the fetch request here
            document.getElementById('finalReturnConfirmationModal').classList.remove('hidden');
        });
    }
});

// Finalize Return of Document Modal
document.getElementById('finalizeReturnBtn').addEventListener('click', function() {
    // Get the resubmission message 
    const resubmissionMessage = document.getElementById('resubmissionMessage').value.trim();
    
    if (!resubmissionMessage) {
        showDocumentActionToast('return', 'Please provide a reason for requesting changes.', false);
        return;
    }

    // Show loading state
    setButtonLoading('finalizeReturnBtn', true, 'finalReturnConfirmationModal');
    
    // Submit the resubmission request
    fetch(`/admin/documents/${currentDocumentId}/request-resubmission`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            message: resubmissionMessage
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.error || 'Server error');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success toast
            showDocumentActionToast('return');
            
            // Close the modal
            document.getElementById('finalReturnConfirmationModal').classList.add('hidden');
            
            // Update the UI to reflect the resubmission request
            fetch(`/admin/documents/${currentDocumentId}/details`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(docData => {
                // Update the status history and other details
                updateDocumentDetailsView(docData);
                
                // Reload comments to include any system-generated comments
                loadComments(currentDocumentId);
                
                // Update UI to show document is awaiting resubmission
                const actionButtonsContainer = document.getElementById('actionButtonsContainer');
                if (actionButtonsContainer) {
                    actionButtonsContainer.classList.add('hidden');
                }
                
                // Show returnedStatusIndicator instead of processedStatusIndicator
                const returnedStatusIndicator = document.getElementById('returnedStatusIndicator');
                if (returnedStatusIndicator) {
                    returnedStatusIndicator.classList.remove('hidden');
                }
                
                // Ensure processedStatusIndicator is hidden
                const processedStatusIndicator = document.getElementById('processedStatusIndicator');
                if (processedStatusIndicator) {
                    processedStatusIndicator.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error refreshing document details:', error);
            });
        } else {
            showDocumentActionToast('return', data.error || 'Failed to send resubmission request.', false);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showDocumentActionToast('return', error.message || 'An error occurred while requesting resubmission.', false);
    })
    .finally(() => {
        // Reset button state
        setButtonLoading('finalizeReturnBtn', false, 'finalRejectConfirmationModal');
    });
});

// Event listeners for the finalize return modal X and Cancel buttons
document.addEventListener('DOMContentLoaded', function() {
    // Close button (X) for the Final Return Confirmation Modal
    const closeFinalReturnModalBtn = document.getElementById('closeFinalReturnModalBtn');
    if (closeFinalReturnModalBtn) {
        closeFinalReturnModalBtn.addEventListener('click', function() {
            const resubmissionMessage = document.getElementById('resubmissionMessage');
            // Check if there are unsaved changes in the resubmission message
            if (resubmissionMessage && resubmissionMessage.value.trim() !== '') {
                // Show confirmation dialog
                if (confirm('You have unsaved changes. Are you sure you want to close this window?')) {
                    // Clear the resubmission message input field
                    if (resubmissionMessage) {
                        resubmissionMessage.value = '';
                        
                        // Also reset the character counter
                        const counter = document.getElementById('resubmissionMessageCounter');
                        if (counter) {
                            counter.textContent = `0/${MESSAGE_CHARACTER_LIMITS.resubmissionMessage}`;
                            counter.classList.remove('text-orange-500', 'text-red-500');
                        }
                    }
                    
                    // Hide the modal
                    document.getElementById('finalReturnConfirmationModal').classList.add('hidden');
                    
                    // Reset unsaved changes flag
                    hasUnsavedChanges = false;
                }
                // If user cancels, the modal stays open
            } else {
                // No unsaved changes, just close the modal
                document.getElementById('finalReturnConfirmationModal').classList.add('hidden');
            }
        });
    }

    // Cancel button for the Final Return Confirmation Modal
    const cancelFinalReturnBtn = document.getElementById('cancelFinalReturnBtn');
    if (cancelFinalReturnBtn) {
        cancelFinalReturnBtn.addEventListener('click', function() {
            // Hide the modal
            document.getElementById('finalReturnConfirmationModal').classList.add('hidden');
            
            // Clear the resubmission message input field
            const resubmissionMessage = document.getElementById('resubmissionMessage');
            if (resubmissionMessage) {
                resubmissionMessage.value = '';
                
                // Also reset the character counter
                const counter = document.getElementById('resubmissionMessageCounter');
                if (counter) {
                    counter.textContent = `0/${MESSAGE_CHARACTER_LIMITS.resubmissionMessage}`;
                    counter.classList.remove('text-orange-500', 'text-red-500');
                }
            }
            
            // Reset unsaved changes flag
            hasUnsavedChanges = false;
        });
    }
});

// --------------- TOASTS ---------------

/**
 * Shows a toast notification for document actions
 * @param {string} action - The action type: 'approved' and 'returned'
 * @param {string} message - Optional custom message
 * @param {boolean} isSuccess - Whether the action was successful
 */
function showDocumentActionToast(action, message = '', isSuccess = true) {
    const toast = document.getElementById("documentActionToast");
    const actionIcon = document.getElementById("actionIcon");
    const actionTitle = document.getElementById("actionTitle");
    const actionMessage = document.getElementById("actionMessage");
    
    // Clear any existing timeout
    if (documentActionToastTimeout) {
        clearTimeout(documentActionToastTimeout);
    }
    
    // Set border color based on success/failure
    if (isSuccess) {
        toast.classList.remove('border-red-300');
        toast.classList.add('border-green-400');
        actionIcon.src = ASSET_URLS.successIcon;
    } else {
        toast.classList.remove('border-green-400');
        toast.classList.add('border-red-300');
        actionIcon.src = ASSET_URLS.errorIcon;
    }
    
    // Set title based on action
    let title = '';
    let defaultMessage = '';
    
    switch(action) {
        case 'approved':
            title = isSuccess ? 'Document Successfully Approved' : 'Approval Failed';
            defaultMessage = isSuccess 
                ? 'The document has been approved successfully and the submitter has been notified.' 
                : 'Failed to approve document. Please try again later.';
            break;
        case 'return':
            title = isSuccess ? 'Document Returned Successfully' : 'Return Request Failed';
            defaultMessage = isSuccess 
                ? 'The document has been returned to the submitter for revisions.' 
                : 'Failed to return the document. Please try again later.';
            break;
        default:
            title = isSuccess ? 'Action Successful' : 'Action Failed';
            defaultMessage = isSuccess 
                ? 'The action was successfully performed.' 
                : 'The action failed. Please try again later.';
    }
    
    // Set the toast content
    actionTitle.textContent = title;
    actionMessage.textContent = message || defaultMessage;
    
    // Show the toast
    toast.classList.remove("hidden");
    
    // Auto-hide after 5 seconds
    documentActionToastTimeout = setTimeout(() => {
        toast.classList.add("hidden");
    }, 5000);
}

// Hide action toast
function hideActionToast() {
    const toast = document.getElementById("documentActionToast");
    if (toast) {
        toast.classList.add("hidden");
        if (documentActionToastTimeout) {
            clearTimeout(documentActionToastTimeout);
        }
    }
}

// Update hideAllToasts function
function hideAllToasts() {
    // Hide the new unified toast
    hideActionToast();
    
    // Keep existing toast hiding logic if needed
    if (typeof hideToast === 'function') {
        hideToast('error');
        hideToast('success');
        hideToast('fail');
        hideToast('approvalSuccess');
        hideToast('approvalFail');
    }
}