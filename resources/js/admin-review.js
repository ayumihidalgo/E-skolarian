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
            <svg class="animate-spin h-5 w-5 mx-auto cursor-not-allowed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        `;
        button.disabled = true;
        
        // Also disable related close/cancel buttons if a modal ID is provided
        if (modalId) {
            disableModalCloseButtons(modalId, true);
        }
    } else {
        // Restore original text
        button.innerHTML = originalText;
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
        'sendToAdminModal': ['closeSendToAdminModalBtn'],
        'resubmissionModal': ['closeResubmissionModalBtn'],
        'rejectConfirmationModal': ['closeRejectConfirmationModalBtn'],
        'finalRejectConfirmationModal': ['closeFinalRejectModalBtn', 'cancelFinalRejectBtn'],
        'finalizeConfirmationModal': ['closeFinalizeModalBtn', 'cancelFinalizeBtn']
    };
    
    // Get the button IDs for the current modal
    const buttonIds = modalButtonMap[modalId] || [];
    
    // Disable/enable each close/cancel button
    buttonIds.forEach(buttonId => {
        const button = document.getElementById(buttonId);
        if (button) {
            button.disabled = disable;
            if (disable) {
                button.classList.add('opacity-50', 'cursor-not-allowed');
                // Store original pointer events style if not already stored
                if (!button.getAttribute('data-original-pointer-events')) {
                    button.setAttribute('data-original-pointer-events', 
                                        window.getComputedStyle(button).pointerEvents);
                }
                button.style.pointerEvents = 'none';
            } else {
                button.classList.remove('opacity-50', 'cursor-not-allowed');
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

// Function to handle document click
document.addEventListener('DOMContentLoaded', function() {
    const documentRows = document.querySelectorAll('tbody tr');
    const tableView = document.getElementById('tableView');
    const detailsView = document.getElementById('detailsView');
    
    console.log("Found elements:", {
        rows: documentRows.length,
        tableView: !!tableView,
        detailsView: !!detailsView
    });
    
    documentRows.forEach(row => {
        row.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get document ID from the data attribute - this is the actual numeric ID
            const documentId = this.getAttribute('data-document-id');
            
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
                    this.classList.remove('border-[#7A1212]', 'bg-white');
                    this.classList.add('border-[#D9D9D9]', 'bg-[#D9ACAC33]');
                    
                    // Remove the red dot indicator
                    const dotIndicator = this.querySelector('td:last-child span[class*="bg-["]');
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
            .then(response => response.json())
            .then(docData => {
                console.log("Document details received:", docData);
                // Update the details view with document data
                updateDocumentDetailsView(docData);
                
                // Add this line to load comments for the current document
                loadComments(documentId);
                
                console.log("BEFORE: tableView hidden:", tableView.classList.contains('hidden'));
                console.log("BEFORE: detailsView hidden:", detailsView.classList.contains('hidden'));
                
                // Show details view, hide table view
                tableView.classList.add('hidden');
                detailsView.classList.remove('hidden');
                
                console.log("AFTER: tableView hidden:", tableView.classList.contains('hidden'));
                console.log("AFTER: detailsView hidden:", detailsView.classList.contains('hidden'));
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});

// Viewing Document Details
// function updateDocumentDetailsView(docData) {
//     console.log("Document data:", docData);
    
//     // Format date
//     const formattedDate = new Date(docData.created_at).toLocaleDateString('en-US', {
//         month: 'long', 
//         day: 'numeric', 
//         year: 'numeric'
//     });
    
//     // Organization name to acronym mapping
//     const orgMap = {
//         'Association of Competent and Aspiring Psychologists': 'ACAP',
//         'Association of Electronics and Communications Engineering Students': 'AECES',
//         'Eligible League of Information Technology Enthusiasts': 'ELITE',
//         'Guild of Imporous and Valuable Educators': 'GIVE',
//         'Junior Executive of Human Resource Association': 'JEHRA',
//         'Junior Marketing Association of the Philippines': 'JMAP',
//         'Junior Philippine Institute of Accountants': 'JPIA',
//         'Philippine Institute of Industrial Engineers': 'PIIE',
//         'Artist Guild Dance Squad': 'AGDS',
//         'PUP SRC Chorale': 'Chorale',
//         'Supreme Innovators\' Guild for Mathematics Advancements': 'SIGMA',
//         'Transformation Advocates through Purpose-driven and Noble Objectives Toward Community Holism': 'TAPNOTCH',
//         'Office of the Student Council': 'OSC'
//     };
    
//     // Get organization acronym if available, otherwise use full name
//     function getOrgAcronym(fullName) {
//         return orgMap[fullName] || fullName;
//     }
    
//     try {
//         // Update document information using direct IDs
//         document.getElementById('documentDate').textContent = formattedDate;
//         document.getElementById('documentOrg').innerHTML = `<span class="text-[#FFFFFF91] font-normal">From:</span> ${docData.organization}`;
//         document.getElementById('documentTitle').innerHTML = `<span class="text-[#FFFFFF91] font-normal">Title:</span> ${docData.subject || docData.title}`;
//         document.getElementById('documentType').innerHTML = `<span class="text-[#FFFFFF91] font-normal">Document Type:</span> ${docData.type}`;
//         document.getElementById('documentTag').textContent = docData.control_tag || docData.tag;

//         // Update summary
//         document.getElementById('documentSummary').textContent = docData.summary || 'No summary available';
            
//         // Update attachment (if available)
//         const attachmentButton = document.getElementById('documentAttachment');
//         const attachmentSpan = document.getElementById('documentFileName');
        
//         if (attachmentButton && attachmentSpan && docData.file_path) {
//             const fileName = docData.file_path.split('/').pop();
//             attachmentSpan.textContent = fileName;
                
//             // Set up the click handler for the attachment
//             attachmentButton.onclick = function() {
//                 openDocumentViewer(docData.file_path, 'application/pdf');
//             };
//         }
            
//         // Update organization info in right panel
//         document.getElementById('orgName').textContent = getOrgAcronym(docData.organization) || 'Organization Name';
            
//         // Set organization initial
//         const orgInitial = document.getElementById('orgInitial');
//         if (orgInitial && docData.organization) {
//             // If we have an acronym, use its first letter, otherwise use the first letter of the full name
//             const acronym = getOrgAcronym(docData.organization);
//             orgInitial.textContent = acronym.charAt(0).toUpperCase();
//         }

//         // Update status history with timeline style similar to the image
//         const statusHistory = document.getElementById('statusHistory');
//         const processedStatusIndicator = document.getElementById('processedStatusIndicator');
//         const actionButtonsContainer = document.getElementById('actionButtonsContainer');
        
//         if (statusHistory && docData.reviews && Array.isArray(docData.reviews)) {
//             // Group reviews by reviewer
//             const grouped = {};
//             docData.reviews.forEach(r => {
//                 if (!grouped[r.reviewer_name]) grouped[r.reviewer_name] = [];
//                 grouped[r.reviewer_name].push(r);
//             });

//             // Flatten into timeline steps: always start with "Under Review", then show other statuses in order
//             let timelineSteps = [];
//             Object.entries(grouped).forEach(([reviewer, reviews]) => {
//                 // Sort reviews by created_at ascending
//                 reviews.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
//                 // Always add "Under Review" as the first step (created_at)
//                 timelineSteps.push({
//                     reviewer_name: reviewer,
//                     status: "Under Review",
//                     time: reviews[0].created_at
//                 });
//                 // Add all other statuses for this reviewer except "Under Review"
//                 reviews.forEach(r => {
//                     if (r.status && r.status.toLowerCase() !== "under review") {
//                         timelineSteps.push({
//                             reviewer_name: reviewer,
//                             status: r.status,
//                             time: r.updated_at // Use updated_at for these statuses
//                         });
//                     }
//                 });
//             });

//             // Sort the timeline steps by time ascending
//             timelineSteps.sort((a, b) => new Date(a.time) - new Date(b.time));

//             // Build timeline HTML
//             let timelineHTML = `<div class="relative pl-5">`;
//             timelineSteps.forEach((step, idx) => {
//                 // Determine colors
//                 let dot = "bg-gray-300";
//                 let statusColor = "text-white";
//                 if (/approved/i.test(step.status)) {
//                     dot = "bg-green-500";
//                     statusColor = "text-green-400 font-semibold";
//                 } else if (/rejected/i.test(step.status)) {
//                     dot = "bg-red-500";
//                     statusColor = "text-red-400 font-semibold";
//                 } else if (/resubmission/i.test(step.status)) {
//                     dot = "bg-yellow-400";
//                     statusColor = "text-yellow-300 font-semibold";
//                 } else if (/sent/i.test(step.status)) {
//                     dot = "bg-blue-400";
//                     statusColor = "text-blue-300 font-semibold";
//                 }

//                 // Format date and status
//                 let displayStatus = step.status;
//                 let displayTime = new Date(step.time).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });

//                 // Timeline bullet and line
//                 timelineHTML += `
//                     <div class="flex items-start relative">
//                         ${idx < timelineSteps.length - 1
//                             ? `<span class="absolute left-1 top-4 w-px h-full bg-gray-400" style="height: calc(100% - 10px);"></span>`
//                             : ''}
//                         <span class="flex-shrink-0 w-3 h-3 rounded-full ${dot} mt-1.5 mr-3"></span>
//                         <div>
//                             <span class="font-bold">${step.reviewer_name || 'Unknown'}</span>
//                             <div class="ml-0">
//                                 <span class="${statusColor}">${displayStatus}</span>
//                                 <span class="ml-1 text-white/80">${displayTime}</span>
//                             </div>
//                         </div>
//                     </div>
//                 `;
//             });
//             timelineHTML += '</div>';

//             if (timelineSteps.length === 0) {
//                 timelineHTML = '<p class="text-gray-300">No status updates available</p>';
//             }

//             statusHistory.innerHTML = timelineHTML;

//             // Show or hide action buttons and processed indicator based on document status
//             const finalDecisionExists = timelineSteps.some(step =>
//                 step.status && (
//                     step.status.toLowerCase() === 'approved' ||
//                     step.status.toLowerCase() === 'rejected'
//                 )
//             );
//             if (docData.has_decision || finalDecisionExists) {
//                 if (actionButtonsContainer) actionButtonsContainer.classList.add('hidden');
//                 if (processedStatusIndicator) processedStatusIndicator.classList.remove('hidden');
//             } else {
//                 if (actionButtonsContainer) actionButtonsContainer.classList.remove('hidden');
//                 if (processedStatusIndicator) processedStatusIndicator.classList.add('hidden');
//             }
//         }
//     } catch (error) {
//         console.error('Error updating document details:', error);
//     }
// }

function updateDocumentDetailsView(docData) {
    console.log("Document data:", docData);
    
    // Format date
    const formattedDate = new Date(docData.created_at).toLocaleDateString('en-US', {
        month: 'long', 
        day: 'numeric', 
        year: 'numeric'
    });
    
    // Organization name to acronym mapping
    const orgMap = {
        'Association of Competent and Aspiring Psychologists': 'ACAP',
        'Association of Electronics and Communications Engineering Students': 'AECES',
        'Eligible League of Information Technology Enthusiasts': 'ELITE',
        'Guild of Imporous and Valuable Educators': 'GIVE',
        'Junior Executive of Human Resource Association': 'JEHRA',
        'Junior Marketing Association of the Philippines': 'JMAP',
        'Junior Philippine Institute of Accountants': 'JPIA',
        'Philippine Institute of Industrial Engineers': 'PIIE',
        'Artist Guild Dance Squad': 'AGDS',
        'PUP SRC Chorale': 'Chorale',
        'Supreme Innovators\' Guild for Mathematics Advancements': 'SIGMA',
        'Transformation Advocates through Purpose-driven and Noble Objectives Toward Community Holism': 'TAPNOTCH',
        'Office of the Student Council': 'OSC'
    };
    
    // Get organization acronym if available, otherwise use full name
    function getOrgAcronym(fullName) {
        return orgMap[fullName] || fullName;
    }
    
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
        const attachmentButton = document.getElementById('documentAttachment');
        const attachmentSpan = document.getElementById('documentFileName');
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
        document.getElementById('orgName').textContent = getOrgAcronym(docData.organization) || 'Organization Name';
            
        // Set organization initial
        const orgInitial = document.getElementById('orgInitial');
        if (orgInitial && docData.organization) {
            // If we have an acronym, use its first letter, otherwise use the first letter of the full name
            const acronym = getOrgAcronym(docData.organization);
            orgInitial.textContent = acronym.charAt(0).toUpperCase();
        }

        // Update status history with timeline style similar to the image
        const statusHistory = document.getElementById('statusHistory');
        const processedStatusIndicator = document.getElementById('processedStatusIndicator');
        const actionButtonsContainer = document.getElementById('actionButtonsContainer');
        
        if (statusHistory && docData.reviews && Array.isArray(docData.reviews)) {
            // Group reviews by reviewer
            const grouped = {};
            docData.reviews.forEach(r => {
                if (!grouped[r.reviewer_name]) grouped[r.reviewer_name] = [];
                grouped[r.reviewer_name].push(r);
            });

            // Flatten into timeline steps: always start with "Under Review", then show other statuses in order
            let timelineSteps = [];
            Object.entries(grouped).forEach(([reviewer, reviews]) => {
                // Sort reviews by created_at ascending
                reviews.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                // Always add "Under Review" as the first step (created_at)
                timelineSteps.push({
                    reviewer_name: reviewer,
                    status: "Under Review",
                    time: reviews[0].created_at
                });
                // Add all other statuses for this reviewer except "Under Review"
                reviews.forEach(r => {
                    if (r.status && r.status.toLowerCase() !== "under review") {
                        timelineSteps.push({
                            reviewer_name: reviewer,
                            status: r.status,
                            time: r.updated_at // Use updated_at for these statuses
                        });
                    }
                });
            });

            // Sort the timeline steps by time ascending
            timelineSteps.sort((a, b) => new Date(a.time) - new Date(b.time));

            // Build timeline HTML
            let timelineHTML = `<div class="relative pl-5">`;
            timelineSteps.forEach((step, idx) => {
                // Determine colors
                let dot = "bg-gray-300";
                let statusColor = "text-white";
                if (/approved/i.test(step.status)) {
                    dot = "bg-green-500";
                    statusColor = "text-green-400 font-semibold";
                } else if (/rejected/i.test(step.status)) {
                    dot = "bg-red-500";
                    statusColor = "text-red-400 font-semibold";
                } else if (/resubmission/i.test(step.status)) {
                    dot = "bg-yellow-400";
                    statusColor = "text-yellow-300 font-semibold";
                } else if (/sent/i.test(step.status)) {
                    dot = "bg-blue-400";
                    statusColor = "text-blue-300 font-semibold";
                }

                // Format date and status
                let displayStatus = step.status;
                let displayTime = new Date(step.time).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });

                // Timeline bullet and line
                timelineHTML += `
                    <div class="flex items-start relative">
                        ${idx < timelineSteps.length - 1
                            ? `<span class="absolute left-1 top-4 w-px h-full bg-gray-400" style="height: calc(100% - 10px);"></span>`
                            : ''}
                        <span class="flex-shrink-0 w-3 h-3 rounded-full ${dot} mt-1.5 mr-3"></span>
                        <div>
                            <span class="font-bold">${step.reviewer_name || 'Unknown'}</span>
                            <div class="ml-0">
                                <span class="${statusColor}">${displayStatus}</span>
                                <span class="ml-1 text-white/80">${displayTime}</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            timelineHTML += '</div>';

            if (timelineSteps.length === 0) {
                timelineHTML = '<p class="text-gray-300">No status updates available</p>';
            }

            statusHistory.innerHTML = timelineHTML;

            // Show or hide action buttons and processed indicator based on document status
            const finalDecisionExists = timelineSteps.some(step =>
                step.status && (
                    step.status.toLowerCase() === 'approved' ||
                    step.status.toLowerCase() === 'rejected'
                )
            );
            if (docData.has_decision || finalDecisionExists) {
                if (actionButtonsContainer) actionButtonsContainer.classList.add('hidden');
                if (processedStatusIndicator) processedStatusIndicator.classList.remove('hidden');
            } else {
                if (actionButtonsContainer) actionButtonsContainer.classList.remove('hidden');
                if (processedStatusIndicator) processedStatusIndicator.classList.add('hidden');
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

// Comment rendering
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
                    
                    // Determine icon based on file type
                    let icon = '';
                    if (fileType.startsWith('image/')) {
                        icon = '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>';
                    } else if (fileType === 'application/pdf') {
                        icon = '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>';
                    } else {
                        icon = '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
                    }
                    
                    attachmentHTML = `
                        <div class="mt-2 bg-gray-100 rounded p-2 inline-block">
                            <a href="${filePath}" target="_blank" class="flex items-center text-blue-600 hover:underline">
                                ${icon}
                                <span class="text-xs truncate max-w-[200px]">${fileName}</span>
                            </a>
                        </div>
                    `;
                }
                
                return `
                    <div class="border-b border-[#782626] pb-4 mb-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" class="w-6 h-6 text-gray-600">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118h15.998c-.023-3.423-3.454-6.118-6.911-6.118-3.457 0-6.888 2.695-6.911 6.118z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <h4 class="font-bold text-white text-lg">${comment.sender ? comment.sender.username : 'Unknown User'}</h4>
                                    <span class="text-gray-300 text-sm">${new Date(comment.created_at).toLocaleString('en-US', {hour: '2-digit', minute: '2-digit'})}</span>
                                </div>
                                <p class="text-white mt-1">${comment.comment || ''}</p>
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

// Comment submitting
function submitComment() {
    const input = document.getElementById('commentInput');
    const fileInput = document.getElementById('commentAttachment');
    const attachmentPreview = document.getElementById('attachmentPreview');
    
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
        // Validate file size - minimum 5MB for production (using 1KB for testing)
        if (file.size < 1024) { // 1KB minimum for testing
            showDocumentActionToast('comment', 'Attachment must be at least 1KB', false);
            return;
        }
        
        // Validate file size - maximum 10MB
        if (file.size > 10 * 1024 * 1024) { // 10MB maximum
            showDocumentActionToast('comment', 'Attachment cannot exceed 10MB', false);
            return;
        }
        
        formData.append('attachment', file);
    }

    // Create loading indicator
    const submitBtn = document.getElementById('submitCommentBtn');
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
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.errors ? Object.values(data.errors).flat().join(', ') : 'Server error: ' + response.status);
            });
        }
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
    
    // Clear the PDF viewer
    pdfViewer.innerHTML = '';
    
    modal.classList.add('hidden');
}

// Approval Modal function
document.addEventListener('DOMContentLoaded', function () {
    const approveButton = document.getElementById('approveButton');
    const approvalModal = document.getElementById('approvalModal');
    const closeApprovalModalBtn = document.getElementById('closeApprovalModalBtn');
    const sendToAnotherAdminBtn = document.getElementById('sendToAnotherAdminBtn');
    const finalizeApprovalBtn = document.getElementById('finalizeApprovalBtn');

    // Open the modal when the Approve button is clicked, but check if disabled first
    if (approveButton) {
        approveButton.addEventListener('click', function(e) {
            if (this.disabled) {
                e.preventDefault();
                showDocumentActionToast('approved', 'This document has already been reviewed and cannot be modified.', false);
                return;
            }
            
            // If not disabled, show the approval modal
            approvalModal.classList.remove('hidden');
        });
    }

    // Close the modal when the close button is clicked
    if (closeApprovalModalBtn) {
        closeApprovalModalBtn.addEventListener('click', function() {
            document.getElementById('approvalModal').classList.add('hidden');
        });
    }
});

// Send to Another Admin functionality
document.addEventListener('DOMContentLoaded', function () {
    const sendToAnotherAdminBtn = document.getElementById('sendToAnotherAdminBtn');
    const sendToAdminModal = document.getElementById('sendToAdminModal');
    const closeSendToAdminModalBtn = document.getElementById('closeSendToAdminModalBtn');
    const sendToAdminSubmitBtn = document.getElementById('sendToAdminSubmitBtn');
    const adminSelect = document.getElementById('adminSelect');
    const adminMessage = document.getElementById('adminMessage');

    // Load admin list when the modal is opened
    sendToAnotherAdminBtn.addEventListener('click', function () {
        // Clear previous options except the placeholder
        while (adminSelect.options.length > 1) {
            adminSelect.remove(1);
        }
        
        // Clear previous message
        adminMessage.value = '';
        
        // Hide the approval modal
        document.getElementById('approvalModal').classList.add('hidden');
        
        // Fetch available admins
        fetch('/admin/get-admins', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(admins => {
            // Add admins to the select dropdown
            admins.forEach(admin => {
                const option = document.createElement('option');
                option.value = admin.id;
                option.textContent = admin.username || admin.name;
                adminSelect.appendChild(option);
            });
            
            // Show the modal
            sendToAdminModal.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error loading admins:', error);
            alert('Failed to load administrators. Please try again.');
        });
    });

    // Close the "Send to Another Admin" modal
    if (closeSendToAdminModalBtn) {
        closeSendToAdminModalBtn.addEventListener('click', function() {
            document.getElementById('sendToAdminModal').classList.add('hidden');
            clearModalInputs('sendToAdminModal');
        });
    }

    // Handle the "SEND" button click
    if (sendToAdminSubmitBtn) {
        sendToAdminSubmitBtn.addEventListener('click', function () {
            const selectedAdmin = document.getElementById('adminSelect').value;
            const message = document.getElementById('adminMessage').value.trim();

            if (!selectedAdmin) {
                showDocumentActionToast('forward', 'Please select an admin to send the document to.', false);
                return;
            }

            if (!message) {
                showDocumentActionToast('forward', 'Please enter a message for the admin.', false);
                return;
            }
            
            if (!currentDocumentId) {
                showDocumentActionToast('forward', 'Error: Document ID is missing.', false);
                return;
            }

            // Show loading state
            setButtonLoading('sendToAdminSubmitBtn', true, 'sendToAdminModal');

            // Send the data to the server
            fetch(`/admin/documents/${currentDocumentId}/forward`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    forward_to: selectedAdmin,
                    message: message
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.error || 'Failed to forward document');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success toast
                    showDocumentActionToast('forward');
                    
                    // Close the modal
                    document.getElementById('sendToAdminModal').classList.add('hidden'); 
                    clearModalInputs('sendToAdminModal');
                    
                    // Return to table view
                    closeDetailsPanel();
                    
                    // Refresh the page
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                } else {
                    showDocumentActionToast('forward', data.error || 'Failed to forward the document.', false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showDocumentActionToast('forward', error.message || 'An error occurred while forwarding the document.', false);
            })
            .finally(() => {
                // Reset button state
                setButtonLoading('sendToAdminSubmitBtn', false, 'sendToAdminModal');
            });
        });
    }
});

// Handle "Finalize Approval" button click - show confirmation modal
finalizeApprovalBtn.addEventListener('click', function () {
    // Hide the approval modal
    approvalModal.classList.add('hidden');
    
    // Show the finalize confirmation modal
    const finalizeConfirmationModal = document.getElementById('finalizeConfirmationModal');
    finalizeConfirmationModal.classList.remove('hidden');
});

// Handle confirmation modal buttons
const closeFinalizeModalBtn = document.getElementById('closeFinalizeModalBtn');
if (closeFinalizeModalBtn) {
    closeFinalizeModalBtn.addEventListener('click', function() {
        document.getElementById('finalizeConfirmationModal').classList.add('hidden');
    });
}

// Cancel buttons
const cancelFinalizeBtn = document.getElementById('cancelFinalizeBtn');
if (cancelFinalizeBtn) {
    cancelFinalizeBtn.addEventListener('click', function() {
        document.getElementById('finalizeConfirmationModal').classList.add('hidden');
    });
}

// Finalize approval handler
const confirmFinalizeBtn = document.getElementById('confirmFinalizeBtn');
if (confirmFinalizeBtn) {
    confirmFinalizeBtn.addEventListener('click', function() {
        // Make sure we have a valid ID
        if (!currentDocumentId) {
            showDocumentActionToast('approved', "Error: Document ID is missing. Please try again.", false);
            return;
        }
        
        // Show loading state
        setButtonLoading('confirmFinalizeBtn', true, 'finalizeConfirmationModal');

        // Make an AJAX request to approve the document
        fetch(`/admin/documents/${currentDocumentId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                message: 'Document approved and finalized'
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => { 
                    throw new Error(data.error || 'Failed to approve document');
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
                
                // Return to table view
                closeDetailsPanel();
                
                // Refresh the page to update the document list
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            } else {
                // Show failure toast
                showDocumentActionToast('approved', data.error || "An unknown error occurred during approval.", false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showDocumentActionToast('approved', error.message || "An error occurred while approving the document.", false);
        })
        .finally(() => {
            // Reset button state
            setButtonLoading('confirmFinalizeBtn', false, 'finalizeConfirmationModal');
        });
    });
}

// Reject modal functionality
document.addEventListener('DOMContentLoaded', function () {
    // Get elements
    const rejectButton = document.getElementById('rejectButton');
    const rejectModal = document.getElementById('rejectModal');
    const closeRejectModalBtn = document.getElementById('closeRejectModalBtn');

    // Open the reject modal when the Reject button is clicked, but check if disabled first
    if (rejectButton) {
        rejectButton.addEventListener('click', function(e) {
            if (this.disabled) {
                e.preventDefault();
                showDocumentActionToast('rejected', 'This document has already been reviewed and cannot be modified.', false);
                return;
            }
            
            // If not disabled, show the rejection modal
            rejectModal.classList.remove('hidden');
        });
    }

    // Close the reject modal
    if (closeRejectModalBtn) {
        closeRejectModalBtn.addEventListener('click', function() {
            rejectModal.classList.add('hidden');
        });
    }
});

// Handle Request Resubmission button click
document.addEventListener('DOMContentLoaded', function() {
    const requestResubmissionBtn = document.getElementById('requestResubmissionBtn');
    const resubmissionModal = document.getElementById('resubmissionModal');
    const closeResubmissionModalBtn = document.getElementById('closeResubmissionModalBtn');
    const submitResubmissionBtn = document.getElementById('submitResubmissionBtn');
    
    // Open resubmission modal when Request Resubmission is clicked
    if (requestResubmissionBtn) {
        requestResubmissionBtn.addEventListener('click', function() {
            // Close the reject modal first
            document.getElementById('rejectModal').classList.add('hidden');
            // Show the resubmission modal
            resubmissionModal.classList.remove('hidden');
        });
    }
    
    // Close resubmission modal
    if (closeResubmissionModalBtn) {
        closeResubmissionModalBtn.addEventListener('click', function() {
            document.getElementById('resubmissionModal').classList.add('hidden');
            clearModalInputs('resubmissionModal');
        });
    }
    
    // Handle submit resubmission
    if (submitResubmissionBtn) {
        submitResubmissionBtn.addEventListener('click', function() {
            const message = document.getElementById('resubmissionMessage').value.trim();
    
            if (!message) {
                showDocumentActionToast('resubmit', 'Please provide feedback for resubmission.', false);
                return;
            }
            
            if (!currentDocumentId) {
                showDocumentActionToast('resubmit', 'Error: Document ID is missing.', false);
                return;
            }

            // Show loading state
            setButtonLoading('submitResubmissionBtn', true, 'resubmissionModal');
            
            // Submit the resubmission request
            fetch(`/admin/documents/${currentDocumentId}/request-resubmission`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message: message
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
                    showDocumentActionToast('resubmit');
                    
                    // Close the modal
                    document.getElementById('resubmissionModal').classList.add('hidden');
                    
                    // Return to table view
                    closeDetailsPanel();
                    
                    // Refresh the page
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                } else {
                    showDocumentActionToast('resubmit', data.error || 'Failed to send resubmission request.', false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showDocumentActionToast('resubmit', error.message || 'An error occurred while requesting resubmission.', false);
            })
            .finally(() => {
                // Reset button state
                setButtonLoading('submitResubmissionBtn', false, 'resubmissionModal');
            });
        });
    }
});

// Handle "Confirm Reject" button click - show rejection message modal
document.getElementById('confirmRejectBtn').addEventListener('click', function() {
    // Hide the reject modal
    document.getElementById('rejectModal').classList.add('hidden');
    
    // Show the reject confirmation modal
    const rejectConfirmationModal = document.getElementById('rejectConfirmationModal');
    rejectConfirmationModal.classList.remove('hidden');
});

// Close reject confirmation modal
const closeRejectConfirmationModalBtn = document.getElementById('closeRejectConfirmationModalBtn');
if (closeRejectConfirmationModalBtn) {
    closeRejectConfirmationModalBtn.addEventListener('click', function() {
        document.getElementById('rejectConfirmationModal').classList.add('hidden');
        clearModalInputs('rejectConfirmationModal');
    });
}

// Handle "Confirm Final Reject" button click
document.getElementById('confirmFinalRejectBtn').addEventListener('click', function() {
    // Get the rejection message
    const rejectionMessage = document.getElementById('rejectionMessage').value.trim();
    
    if (!rejectionMessage) {
        alert('Please provide a reason for rejection.');
        return;
    }
    
    // Hide the rejection message modal
    document.getElementById('rejectConfirmationModal').classList.add('hidden');

    // Show loading state
    setButtonLoading('confirmFinalRejectBtn', true, 'rejectConfirmationModal');
    
    // Show the final confirmation modal
    const finalRejectConfirmationModal = document.getElementById('finalRejectConfirmationModal');
    finalRejectConfirmationModal.classList.remove('hidden');
});

// Close final reject confirmation modal
const closeFinalRejectModalBtn = document.getElementById('closeFinalRejectModalBtn');
if (closeFinalRejectModalBtn) {
    closeFinalRejectModalBtn.addEventListener('click', function() {
        document.getElementById('finalRejectConfirmationModal').classList.add('hidden');
    });
}

// Cancel final rejection
const cancelFinalRejectBtn = document.getElementById('cancelFinalRejectBtn');
if (cancelFinalRejectBtn) {
    cancelFinalRejectBtn.addEventListener('click', function() {
        document.getElementById('finalRejectConfirmationModal').classList.add('hidden');
    });
}

// Finalize rejection
document.getElementById('finalizeRejectionBtn').addEventListener('click', function() {
    // Get the rejection message
    const rejectionMessage = document.getElementById('rejectionMessage').value.trim();
    
    if (!rejectionMessage) {
        showDocumentActionToast('rejected', 'Please provide a reason for rejection.', false);
        return;
    }

    // Show loading state
    setButtonLoading('finalizeRejectionBtn', true, 'finalRejectConfirmationModal');
    
    // Submit the rejection
    fetch(`/admin/documents/${currentDocumentId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            message: rejectionMessage
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.error || 'Failed to reject document');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success toast
            showDocumentActionToast('rejected');
            
            // Close the modals
            document.getElementById('finalRejectConfirmationModal').classList.add('hidden');
            
            // Return to table view
            closeDetailsPanel();
            
            // Refresh the page
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        } else {
            showDocumentActionToast('rejected', data.error || 'Failed to reject document.', false);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showDocumentActionToast('rejected', error.message || 'An error occurred while rejecting the document.', false);
    })
    .finally(() => {
        // Reset button state
        setButtonLoading('finalizeRejectionBtn', false, 'finalRejectConfirmationModal');
    });
});


// --------------- TOASTS ---------------

// Toast timeout storage
let documentActionToastTimeout = null;

/**
 * Shows a toast notification for document actions
 * @param {string} action - The action type: 'approved', 'rejected', 'resubmit', 'forward'
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
        case 'rejected':
            title = isSuccess ? 'Document Successfully Rejected' : 'Rejection Failed';
            defaultMessage = isSuccess 
                ? 'The document has been rejected and the submitter has been notified.' 
                : 'Failed to reject document. Please try again later.';
            break;
        case 'resubmit':
            title = isSuccess ? 'Resubmission Successfully Requested' : 'Resubmission Request Failed';
            defaultMessage = isSuccess 
                ? 'The resubmission request has been sent to the document submitter.' 
                : 'Failed to request document resubmission. Please try again later.';
            break;
        case 'forward':
            title = isSuccess ? 'Document Successfully Forwarded' : 'Forward Failed';
            defaultMessage = isSuccess 
                ? 'The document has been forwarded to another admin for review.' 
                : 'Failed to forward document. Please try again later.';
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
    toast.classList.add("hidden");
    if (documentActionToastTimeout) {
        clearTimeout(documentActionToastTimeout);
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