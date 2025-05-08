let currentDocumentId = null;

function loadComments(documentId) {
    currentDocumentId = documentId;
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

// Table row selection functionality
document.addEventListener('DOMContentLoaded', function() {
    const tableView = document.getElementById('tableView');
    const detailsView = document.getElementById('detailsView');
    const tableRows = document.querySelectorAll('tbody tr');

    // Add click event to all table rows
    tableRows.forEach(row => {
        row.addEventListener('click', function() {
            const tag = row.cells[0].textContent.trim();

            // Remove the red border and bullet
            row.classList.remove('border-[#7A1212]', 'bg-white');
            row.classList.add('border-[#D9D9D9]', 'bg-[#D9ACAC33]');
            const bulletPoint = row.querySelector('span[class*="bg-[#7A1212]"]'); // Use attribute selector
            if (bulletPoint) {
                bulletPoint.remove();
            }


            // Rest of your code...
            const organization = row.cells[1].textContent.trim();
            const title = row.cells[2].textContent.trim();
            const date = row.cells[3].textContent.trim();
            const type = row.cells[4].textContent.trim();

            // Update panel content
            updatePanelContent(tag, organization, title, date, type);

            // Load comments for this document
            loadComments(tag);

            // Show details view, hide table view
            tableView.classList.add('hidden');
            detailsView.classList.remove('hidden');
        });
    });

    // Function to show table view
    window.showTableView = function() {
        detailsView.classList.add('hidden');
        tableView.classList.remove('hidden');
    }

    // Function to update panel content
    function updatePanelContent(tag, organization, title, date, type) {
        // Update tag
        document.querySelector('#detailsView .text-right p').textContent = tag;

        // Update header details
        const headerDiv = document.querySelector('#detailsView .flex.justify-between.items-start div:first-child');
        headerDiv.innerHTML = `
            <p class="text-sm mb-1 text-[#FFFFFF91]">${date}</p>
            <p><span class="font-bold text-[#FFFFFF91]">From:</span> ${organization}</p>
            <p><span class="font-bold text-[#FFFFFF91]">Title:</span> ${title}</p>
            <p><span class="font-bold text-[#FFFFFF91]">Document Type:</span> ${type}</p>
        `;

        // Update organization name in chat panel
        const orgNameElement = document.querySelector('#detailsView .space-y-6 div:first-child p:first-child');
        orgNameElement.textContent = organization;

        // Update document viewer button
        const attachmentButton = document.querySelector('#detailsView .bg-gray-200');
        attachmentButton.onclick = () => openDocumentViewer(`7Cb9Ul5wCge78G1HY387IwPjMwiFoqcm3XkjTPOd.pdf`, 'application/pdf');
    }
});

// Close button handler
window.closeDetailsPanel = function() {
    const tableView = document.getElementById('tableView');
    const detailsView = document.getElementById('detailsView');

    // Hide details view and show table view
    detailsView.classList.add('hidden');
    tableView.classList.remove('hidden');
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

function closeDocumentViewer() {
    const modal = document.getElementById('documentViewerModal');
    const pdfViewer = document.getElementById('pdfViewer');
    
    // Clear the PDF viewer
    pdfViewer.innerHTML = '';
    
    modal.classList.add('hidden');
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

document.getElementById('confirmFinalizeBtn').addEventListener('click', function() {
    // Perform the actual finalization here
    console.log('Document approval finalized');
    
    // You can make an AJAX request here to update the document status
    fetch('/admin/finalize-document', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            documentId: currentDocumentId
        })
    })
    .then(response => {
        if (response.ok) {
            alert('Document has been finalized and approved.');
            // Maybe redirect or update UI
        } else {
            alert('Failed to finalize document. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
    
    // Hide the modal
    document.getElementById('finalizeConfirmationModal').classList.add('hidden');
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
        
        // Submit the resubmission request
        fetch('/admin/request-resubmission', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                documentId: currentDocumentId,
                message: message
            })
        })
        .then(response => {
            if (response.ok) {
                alert('Resubmission request sent successfully.');
                resubmissionModal.classList.add('hidden');
            } else {
                alert('Failed to send resubmission request. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
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

// Handle final rejection submission
document.getElementById('confirmFinalRejectBtn').addEventListener('click', function() {
    const rejectionMessage = document.getElementById('rejectionMessage').value.trim();
    
    if (!rejectionMessage) {
        alert('Please provide a reason for rejection.');
        return;
    }
    
    // Submit the rejection
    fetch('/admin/reject-document', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            documentId: currentDocumentId,
            message: rejectionMessage
        })
    })
    .then(response => {
        if (response.ok) {
            alert('Document rejected successfully.');
            document.getElementById('rejectConfirmationModal').classList.add('hidden');
        } else {
            alert('Failed to reject document. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const rejectConfirmationModal = document.getElementById('rejectConfirmationModal');
    if (event.target === rejectConfirmationModal) {
        rejectConfirmationModal.classList.add('hidden');
    }
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
    
    // Submit the final rejection
    fetch('/admin/finalize-reject-document', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            documentId: currentDocumentId,
            message: rejectionMessage
        })
    })
    .then(response => {
        if (response.ok) {
            alert('Document has been rejected.');
            // Maybe redirect or update UI
            document.getElementById('finalRejectConfirmationModal').classList.add('hidden');
        } else {
            alert('Failed to reject document. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const finalRejectConfirmationModal = document.getElementById('finalRejectConfirmationModal');
    if (event.target === finalRejectConfirmationModal) {
        finalRejectConfirmationModal.classList.add('hidden');
    }
});