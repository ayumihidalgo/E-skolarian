let currentDocumentId = null;

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

function updateDocumentDetailsView(docData) {
    console.log("Document data:", docData);
    
    // Get all elements in the details view
    const detailsView = document.getElementById('detailsView');
    
    // Format date
    const formattedDate = new Date(docData.created_at).toLocaleDateString('en-US', {
        month: 'long', 
        day: 'numeric', 
        year: 'numeric'
    });
    
    try {
        // Instead of using complex selectors with square brackets, navigate the DOM step by step
        const leftSideDiv = detailsView.querySelector('.w-2\\/3'); // Use escaped backslashes for fractions
        if (!leftSideDiv) {
            console.error('Left side div not found');
            return;
        }
        
        const contentDiv = leftSideDiv.querySelector('div'); // First div inside left side div
        if (!contentDiv) {
            console.error('Content div not found');
            return;
        }
        
        // Header div which has the "font-bold" class
        const headerDiv = contentDiv.querySelector('.font-bold');
        if (headerDiv) {
            // First paragraph (date)
            const datePara = headerDiv.querySelector('p:first-child');
            if (datePara) datePara.textContent = formattedDate;
            
            // Organization
            const fromPara = headerDiv.querySelector('p:nth-child(2)');
            if (fromPara) fromPara.innerHTML = `<span class="text-[#FFFFFF91] font-normal">From:</span> ${docData.organization}`;
            
            // Title
            const titlePara = headerDiv.querySelector('p:nth-child(3)');
            if (titlePara) titlePara.innerHTML = `<span class="text-[#FFFFFF91] font-normal">Title:</span> ${docData.subject}`;
            
            // Document Type
            const typePara = headerDiv.querySelector('p:nth-child(4)');
            if (typePara) typePara.innerHTML = `<span class="text-[#FFFFFF91] font-normal">Document Type:</span> ${docData.type}`;
        }
        
        // Update tag in the right corner - find the text-right element
        const rightDiv = contentDiv.querySelector('.text-right');
        if (rightDiv) {
            const tagElement = rightDiv.querySelector('p');
            if (tagElement) tagElement.textContent = docData.control_tag;
        }
        
        // Update summary - find it by navigating through the DOM structure
        // Look for the second div in the parent container
        const divs = leftSideDiv.querySelectorAll(':scope > div');
        if (divs.length >= 2) {
            const summaryDiv = divs[1]; // The second div contains the summary
            const summaryElement = summaryDiv.querySelector('p#documentSummary');
            if (summaryElement) {
                console.log("Setting summary to:", docData.summary || 'No summary available');
                summaryElement.textContent = docData.summary || 'No summary available';
            }
        }
        
        // Update attachment (if available) - third div in the structure
        if (divs.length >= 3) {
            const attachmentDiv = divs[2];
            const attachmentButton = attachmentDiv.querySelector('.bg-gray-200');
            if (attachmentButton) {
                const attachmentSpan = attachmentButton.querySelector('span');
                if (attachmentSpan && docData.file_path) {
                    const fileName = docData.file_path.split('/').pop();
                    attachmentSpan.textContent = fileName;
                    
                    // Set up the click handler for the attachment
                    attachmentButton.onclick = function() {
                        openDocumentViewer(fileName, 'application/pdf');
                    };
                }
            }
        }
        
        // Update organization info in right panel
        const rightSideDiv = detailsView.querySelector('.w-1\\/3'); // Use escaped backslash for fraction
        if (rightSideDiv) {
            const orgNameElement = rightSideDiv.querySelector('.font-bold.text-lg');
            if (orgNameElement) {
                orgNameElement.textContent = docData.organization === 'Eligible League of Information Technology Enthusiasts' ? 'ELITE' : (docData.organization || 'Organization Name');
            }
            
            // Set organization initial
            const orgInitial = rightSideDiv.querySelector('#orgInitial');
            if (orgInitial && docData.organization) {
                orgInitial.textContent = docData.organization.charAt(0).toUpperCase();
            }
        }
        
        // Update status history if available
        // Look for status div which is the fourth div
        if (divs.length >= 4) {
            const statusDiv = divs[3];
            const statusHistory = statusDiv.querySelector('#statusHistory');
            if (statusHistory && docData.reviews && Array.isArray(docData.reviews)) {
                let statusHTML = '';
                
                docData.reviews.forEach(review => {
                    statusHTML += `
                        <div class="bg-white text-gray-800 rounded-lg p-3">
                            <p><strong>Reviewer:</strong> ${review.reviewer_name || 'Unknown'}</p>
                            <p><strong>Status:</strong> ${review.status || 'Unknown'}</p>
                            <p><strong>Message:</strong> ${review.message || 'No message'}</p>
                            <p><strong>Date:</strong> ${new Date(review.created_at).toLocaleDateString()}</p>
                        </div>
                    `;
                });
                
                if (statusHTML === '') {
                    statusHTML = '<p class="text-gray-300">No status updates available</p>';
                }
                
                statusHistory.innerHTML = statusHTML;
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

function loadComments(documentId) {
    fetch(`/comments/${documentId}`)
        .then(response => response.json())
        .then(comments => {
            const container = document.getElementById('commentsContainer');
            container.innerHTML = comments.map(comment => `
                <div class="flex items-start gap-2">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118h15.998c-.023-3.423-3.454-6.118-6.911-6.118-3.457 0-6.888 2.695-6.911 6.118z" />
                        </svg>
                    </div>
                    <div class="flex-1 flex justify-between items-center rounded-lg px-4 py-2">
                        <div class="text-white">
                            <p class="font-bold text-sm">${comment.user_name}</p>
                            <p class="text-sm mt-1">${comment.content}</p>
                        </div>
                        <p class="text-xs text-gray-300 ml-4 whitespace-nowrap">
                            ${new Date(comment.created_at).toLocaleString()}
                        </p>
                    </div>
                </div>
            `).join('');
        });
}

function submitComment() {
    const input = document.getElementById('commentInput');
    const content = input.value.trim();

    if (!content || !currentDocumentId) return;

    fetch('/comments', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            document_id: currentDocumentId,
            content: content
        })
    })
    .then(response => response.json())
    .then(comment => {
        input.value = '';
        loadComments(currentDocumentId);
    });
}

// Add event listener for Enter key
document.getElementById('commentInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        submitComment();
    }
});

function openDocumentViewer(filename, fileType) {
    const modal = document.getElementById('documentViewerModal');
    const pdfViewer = document.getElementById('pdfViewer');
    const imageViewer = document.getElementById('imageViewer');
    const documentTitle = document.getElementById('documentTitle');
    
    documentTitle.textContent = filename;
    
    if (fileType === 'application/pdf') {
        // For PDF files
        pdfViewer.classList.remove('hidden');
        imageViewer.classList.add('hidden');
        
        // Clear previous content
        pdfViewer.innerHTML = '';
        
        // Generate the PDF URL
        const pdfUrl = `/documents/${filename}`;
        
        // Use PDFObject to embed the PDF
        const options = {
            fallbackLink: `<p>This browser does not support embedded PDFs. <a href="${pdfUrl}" target="_blank">Click here to download the PDF</a>.</p>`
        };
        
        PDFObject.embed(pdfUrl, "#pdfViewer", options);
    } else {
        // For image files
        pdfViewer.classList.add('hidden');
        imageViewer.classList.remove('hidden');
        document.getElementById('imageCanvas').src = `/documents/${filename}`;
    }
    
    modal.classList.remove('hidden');
}

// Approval Modal function
document.addEventListener('DOMContentLoaded', function () {
    const approveButton = document.getElementById('approveButton'); // Assuming you have an Approve button elsewhere
    const approvalModal = document.getElementById('approvalModal');
    const closeApprovalModalBtn = document.getElementById('closeApprovalModalBtn');
    const sendToAnotherAdminBtn = document.getElementById('sendToAnotherAdminBtn');
    const finalizeApprovalBtn = document.getElementById('finalizeApprovalBtn');

    // Open the modal when the Approve button is clicked
    if (approveButton) {
        approveButton.addEventListener('click', function () {
            approvalModal.classList.remove('hidden');
        });
    }

    // Close the modal when the close button is clicked
    closeApprovalModalBtn.addEventListener('click', function () {
        approvalModal.classList.add('hidden');
    });

    // Close modal when clicking outside the modal
    window.addEventListener('click', function (event) {
        if (event.target === approvalModal) {
            approvalModal.classList.add('hidden');
        }
    });
});

// Send to Another Admin functionality
document.addEventListener('DOMContentLoaded', function () {
    const sendToAnotherAdminBtn = document.getElementById('sendToAnotherAdminBtn');
    const sendToAdminModal = document.getElementById('sendToAdminModal');
    const closeSendToAdminModalBtn = document.getElementById('closeSendToAdminModalBtn');
    const sendToAdminSubmitBtn = document.getElementById('sendToAdminSubmitBtn');
    const adminSelect = document.getElementById('adminSelect');
    const adminMessage = document.getElementById('adminMessage');

    // Open the "Send to Another Admin" modal and close the approval modal
    sendToAnotherAdminBtn.addEventListener('click', function () {
        approvalModal.classList.add('hidden'); // Hide the approval modal
        sendToAdminModal.classList.remove('hidden'); // Show the "Send to Another Admin" modal
    });

    // Close the "Send to Another Admin" modal
    closeSendToAdminModalBtn.addEventListener('click', function () {
        sendToAdminModal.classList.add('hidden'); // Hide the "Send to Another Admin" modal
    });

    // Close the modal when clicking outside the modal
    window.addEventListener('click', function (event) {
        if (event.target === sendToAdminModal) {
            sendToAdminModal.classList.add('hidden'); // Hide the "Send to Another Admin" modal
        }
    });

    // Handle the "SEND" button click
    sendToAdminSubmitBtn.addEventListener('click', function () {
        const selectedAdmin = adminSelect.value;
        const message = adminMessage.value.trim();

        if (!selectedAdmin) {
            alert('Please select an admin to send the document to.');
            return;
        }

        if (!message) {
            alert('Please enter a message.');
            return;
        }

        // Example: Send the data to the server
        fetch('/admin/send-to-another-admin', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                admin: selectedAdmin,
                message: message,
                documentId: currentDocumentId // Replace with the actual document ID
            })
        })
        .then(response => {
            if (response.ok) {
                alert('Document successfully sent to another admin.');
                sendToAdminModal.classList.add('hidden'); // Close the modal
            } else {
                alert('Failed to send the document. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
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
document.getElementById('closeFinalizeModalBtn').addEventListener('click', function() {
    document.getElementById('finalizeConfirmationModal').classList.add('hidden');
});

document.getElementById('cancelFinalizeBtn').addEventListener('click', function() {
    document.getElementById('finalizeConfirmationModal').classList.add('hidden');
});

// Finalize approval handler
document.getElementById('confirmFinalizeBtn').addEventListener('click', function() {

    // Perform the actual finalization here
    console.log('Document approval finalized, ID:', currentDocumentId);
    
    // Make sure we have a valid ID
    if (!currentDocumentId) {
        alert('Error: Document ID is missing. Please try again.');
        return;
    }
    
    // Make an AJAX request to approve the document with the numeric ID
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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert('Document has been approved and finalized.');
            
            // Close the modal
            document.getElementById('finalizeConfirmationModal').classList.add('hidden');
            
            // Return to table view
            closeDetailsPanel();
            
            // Refresh the page to update the document list
            window.location.reload();
        } else {
            alert('Failed to approve document: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while approving the document. Please try again.');
    });
});

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const finalizeConfirmationModal = document.getElementById('finalizeConfirmationModal');
    if (event.target === finalizeConfirmationModal) {
        finalizeConfirmationModal.classList.add('hidden');
    }
});

// Reject modal functionality
document.addEventListener('DOMContentLoaded', function () {
    // Get elements
    const rejectButton = document.getElementById('rejectButton');
    const rejectModal = document.getElementById('rejectModal');
    const closeRejectModalBtn = document.getElementById('closeRejectModalBtn');
    const requestResubmissionBtn = document.getElementById('requestResubmissionBtn');
    const confirmRejectBtn = document.getElementById('confirmRejectBtn');
    
    // Resubmission modal elements
    const resubmissionModal = document.getElementById('resubmissionModal');
    const closeResubmissionModalBtn = document.getElementById('closeResubmissionModalBtn');
    const submitResubmissionBtn = document.getElementById('submitResubmissionBtn');

    // Open the reject modal when the Reject button is clicked
    rejectButton.addEventListener('click', function () {
        rejectModal.classList.remove('hidden');
    });

    // Close the reject modal
    closeRejectModalBtn.addEventListener('click', function () {
        rejectModal.classList.add('hidden');
    });

    // Handle Request Resubmission button click
    requestResubmissionBtn.addEventListener('click', function () {
        rejectModal.classList.add('hidden'); // Hide reject modal
        resubmissionModal.classList.remove('hidden'); // Show resubmission modal
    });

    // Close resubmission modal
    closeResubmissionModalBtn.addEventListener('click', function () {
        resubmissionModal.classList.add('hidden');
    });

    // Close modals when clicking outside
    window.addEventListener('click', function (event) {
        if (event.target === rejectModal) {
            rejectModal.classList.add('hidden');
        }
        if (event.target === resubmissionModal) {
            resubmissionModal.classList.add('hidden');
        }
    });
});

// Handle Request Resubmission button click
document.addEventListener('DOMContentLoaded', function() {
    const requestResubmissionBtn = document.getElementById('requestResubmissionBtn');
    const resubmissionModal = document.getElementById('resubmissionModal');
    const closeResubmissionModalBtn = document.getElementById('closeResubmissionModalBtn');
    const submitResubmissionBtn = document.getElementById('submitResubmissionBtn');
    
    // Open resubmission modal when Request Resubmission is clicked
    requestResubmissionBtn.addEventListener('click', function() {
        // Close the reject modal first
        document.getElementById('rejectModal').classList.add('hidden');
        // Show the resubmission modal
        resubmissionModal.classList.remove('hidden');
    });
    
    // Close resubmission modal
    closeResubmissionModalBtn.addEventListener('click', function() {
        resubmissionModal.classList.add('hidden');
    });
    
    // Handle submit resubmission
    submitResubmissionBtn.addEventListener('click', function() {
        const message = document.getElementById('resubmissionMessage').value.trim();
        
        if (!message) {
            alert('Please provide feedback for resubmission.');
            return;
        }
        
        // Submit the resubmission request with the correct endpoint
        fetch(`/admin/documents/${currentDocumentId}/request-resubmission`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert('Resubmission request has been sent successfully.');
                
                // Close the modal
                document.getElementById('resubmissionModal').classList.add('hidden');
                
                // Return to table view
                closeDetailsPanel();
                
                // Refresh the page to update the document list
                window.location.reload();
            } else {
                alert('Failed to send resubmission request: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while requesting resubmission. Please try again.');
        });
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === resubmissionModal) {
            resubmissionModal.classList.add('hidden');
        }
    });
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
document.getElementById('closeRejectConfirmationModalBtn').addEventListener('click', function() {
    document.getElementById('rejectConfirmationModal').classList.add('hidden');
});

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
    
    // Show the final confirmation modal
    const finalRejectConfirmationModal = document.getElementById('finalRejectConfirmationModal');
    finalRejectConfirmationModal.classList.remove('hidden');
});

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const rejectConfirmationModal = document.getElementById('rejectConfirmationModal');
    if (event.target === rejectConfirmationModal) {
        rejectConfirmationModal.classList.add('hidden');
    }
});


// Close final reject confirmation modal
document.getElementById('closeFinalRejectModalBtn').addEventListener('click', function() {
    document.getElementById('finalRejectConfirmationModal').classList.add('hidden');
});

// Cancel final rejection
document.getElementById('cancelFinalRejectBtn').addEventListener('click', function() {
    document.getElementById('finalRejectConfirmationModal').classList.add('hidden');
});

// Finalize rejection
document.getElementById('finalizeRejectionBtn').addEventListener('click', function() {
    // Get the rejection message from the previous modal
    const rejectionMessage = document.getElementById('rejectionMessage').value.trim();
    
    if (!rejectionMessage) {
        alert('Please provide a reason for rejection.');
        return;
    }
    
    // Submit the final rejection with the correct endpoint
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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert('Document has been rejected successfully.');
            
            // Close the modals
            document.getElementById('finalRejectConfirmationModal').classList.add('hidden');
            
            // Return to table view
            closeDetailsPanel();
            
            // Refresh the page to update the document list
            window.location.reload();
        } else {
            alert('Failed to reject document: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while rejecting the document. Please try again.');
    });
});

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const finalRejectConfirmationModal = document.getElementById('finalRejectConfirmationModal');
    if (event.target === finalRejectConfirmationModal) {
        finalRejectConfirmationModal.classList.add('hidden');
    }
});