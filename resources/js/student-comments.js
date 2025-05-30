// student-comments.js - Handle comments functionality for student view

let currentDocumentId = null;
let echo = null;
let isUserScrolledUp = false;
let scrollDebounceTimer = null;

document.addEventListener('DOMContentLoaded', function () {
    // Get the document ID from URL or data attribute
    const urlParts = window.location.pathname.split('/');
    currentDocumentId = document.getElementById('record-container')?.getAttribute('data-document-id') || urlParts[urlParts.length - 1];

    console.log("Current document ID:", currentDocumentId);

    if (currentDocumentId) {
        // Initialize Echo/Pusher only if Pusher configuration is available
        initializeEcho();

        // Load comments when page loads - only if comments container exists
        if (document.getElementById('commentsContainer')) {
            loadComments(currentDocumentId);
        } else {
            console.warn("Comments container not found - comments functionality disabled");
        }

        // Listen for new comments
        setupCommentListener();
    } else {
        console.error("No document ID found");
    }

    // Set up event listeners
    setupEventListeners();
});

function initializeEcho() {
    // Check if Pusher configuration is available in window object or meta tags
    const pusherKey = window.pusherConfig?.key || document.querySelector('meta[name="pusher-key"]')?.content;
    const pusherCluster = window.pusherConfig?.cluster || document.querySelector('meta[name="pusher-cluster"]')?.content;

    if (!pusherKey || !pusherCluster) {
        console.warn('Pusher configuration not found - real-time updates disabled');
        return;
    }

    // Import Pusher and Echo dynamically if not already available globally
    if (typeof window.Echo === 'undefined') {
        import('pusher-js').then(Pusher => {
            window.Pusher = Pusher.default;
            return import('laravel-echo');
        }).then(Echo => {
            window.Echo = new Echo.default({
                broadcaster: 'pusher',
                key: pusherKey,
                cluster: pusherCluster,
                forceTLS: true,
                encrypted: true,
                authEndpoint: '/broadcasting/auth',
            });

            setupCommentListener();
        }).catch(error => {
            console.error('Error loading Pusher/Echo:', error);
        });
    } else {
        setupCommentListener();
    }
}

function setupCommentListener() {
    if (!window.Echo || !currentDocumentId) return;

    try {
        // Listen for new comments on this document's channel
        window.Echo.private(`document.${currentDocumentId}`)
            .listen('.comment.created', (data) => {
                console.log('New comment received:', data);
                appendNewComment(data.comment);
            })
            .listen('.comment.updated', (data) => {
                console.log('Comment updated:', data);
                // You might want to implement comment updates if needed
            });
    } catch (error) {
        console.error('Error setting up comment listener:', error);
    }
}

function setupEventListeners() {
    const commentInput = document.getElementById('commentInput');
    const sendButton = document.getElementById('sendCommentBtn');
    const commentForm = document.getElementById('commentForm');
    const commentsContainer = document.getElementById('commentsContainer');

    if (commentsContainer) {
        // Add scroll event listener to detect when user scrolls
        commentsContainer.addEventListener('scroll', function () {
            // Clear any existing debounce timer
            clearTimeout(scrollDebounceTimer);

            // Set a new debounce timer
            scrollDebounceTimer = setTimeout(() => {
                // Check if user has scrolled up (not at bottom)
                const isAtBottom = commentsContainer.scrollHeight - commentsContainer.scrollTop <= commentsContainer.clientHeight + 50;
                isUserScrolledUp = !isAtBottom;

                console.log('User scrolled:', isUserScrolledUp ? 'Up' : 'Bottom');
            }, 200);
        });
    }

    if (commentInput) {
        // Listen for Enter key in the input field
        commentInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                submitComment();
            }
        });

        // Focus input when comments area is clicked
        if (commentsContainer) {
            commentsContainer.addEventListener('click', function () {
                commentInput.focus();
            });
        }
    }

    // Handle form submission
    if (commentForm) {
        commentForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent default form submission
            submitComment();
        });
    }

    if (sendButton) {
        // Listen for click on send button
        sendButton.addEventListener('click', function (e) {
            e.preventDefault();
            submitComment();
        });
    }
}

function scrollToBottom(smooth = true) {
    const container = document.getElementById('commentsContainer');
    if (!container) return;

    // Only auto-scroll if user hasn't manually scrolled up
    if (!isUserScrolledUp) {
        container.scrollTo({
            top: container.scrollHeight,
            behavior: smooth ? 'smooth' : 'auto'
        });
    } else {
        console.log('Not scrolling to bottom - user has scrolled up');
    }
}

function loadComments(documentId) {
    const container = document.getElementById('commentsContainer');
    if (!container) {
        console.error('Comments container not found');
        return;
    }

    // Show loading indicator
    container.innerHTML = '<p class="text-gray-400 text-center">Loading comments...</p>';

    fetch(`/comments/${documentId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Server error: ' + response.status);
            }
            return response.json();
        })
        .then(comments => {
            if (!Array.isArray(comments) || comments.length === 0) {
                container.innerHTML = '<p class="text-gray-400 text-center">No comments yet</p>';
                return;
            }

            // Sort comments by created_at (oldest first for initial load)
            comments.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

            container.innerHTML = comments.map(comment => {
                return createCommentElement(comment);
            }).join('');

            // Scroll to bottom after loading comments (without animation)
            setTimeout(() => {
                scrollToBottom(false);
                // Reset the scroll flag after initial load
                isUserScrolledUp = false;
            }, 100);
        })
        .catch(error => {
            console.error('Error loading comments:', error);
            container.innerHTML = '<p class="text-red-400 text-center">Failed to load comments. Please refresh the page.</p>';
        });
}

function createCommentElement(comment) {
    const timeAgo = getTimeAgo(new Date(comment.created_at));
    const username = comment.sender ? comment.sender.username : 'Unknown User';
    const userRole = comment.sender && comment.sender.role ? comment.sender.role : '';

    // Make sure we have a string for the comment text
    const commentText = typeof comment.comment === 'string' ? comment.comment : '';

    return `
        <div class="comment-item flex items-start space-x-3 mb-4 animate-fade-in">
            <div class="bg-gray-200 rounded-full p-2 mt-1 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="text-gray-700">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <div class="flex-1 overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium truncate">${username}</p>
                        ${userRole ? `<p class="text-xs text-gray-300">${userRole}</p>` : ''}
                    </div>
                    <span class="text-xs text-gray-300 flex-shrink-0 ml-2">${timeAgo}</span>
                </div>
                <p class="text-sm break-words">${formatCommentText(commentText)}</p>
            </div>
        </div>
    `;
}

// Format comment text with basic link detection and emoji support
function formatCommentText(text) {
    // Check if text is null/undefined or not a string
    if (!text || typeof text !== 'string') {
        return ''; // Return empty string if no valid text
    }

    // Convert URLs to clickable links
    const urlRegex = /(https?:\/\/[^\s]+)/g;
    return text.replace(urlRegex, url => `<a href="${url}" target="_blank" class="text-blue-300 hover:underline">${url}</a>`);
}

function appendNewComment(commentData) {
    const container = document.getElementById('commentsContainer');
    if (!container) {
        console.error('Comments container not found');
        return;
    }

    // Remove "No comments yet" message if it exists
    const noCommentsMsg = container.querySelector('.text-gray-400');
    if (noCommentsMsg && noCommentsMsg.textContent === 'No comments yet') {
        container.innerHTML = '';
    }

    // Create new comment element
    const commentDiv = document.createElement('div');
    commentDiv.innerHTML = createCommentElement(commentData);

    // Add to the end (oldest to newest)
    container.appendChild(commentDiv.firstElementChild);

    // Scroll to show the new comment
    scrollToBottom(true);
}

function submitComment() {
    const form = document.getElementById('commentForm');
    const input = document.getElementById('commentInput');
    const attachmentInput = document.getElementById('commentAttachment');

    if (!form || !input) {
        console.error('Comment form or input field not found');
        return;
    }

    const comment = input.value.trim();
    const hasAttachment = attachmentInput && attachmentInput.files.length > 0;

    // Check if we have either a comment or an attachment
    if (!comment && !hasAttachment) {
        return;
    }

    console.log('Submitting comment:', comment);

    // Show loading state in the input
    const oldValue = input.value;
    input.value = 'Sending...';
    input.disabled = true;

    // Show loading state on the button
    const sendButton = document.getElementById('sendCommentBtn');
    if (sendButton) {
        sendButton.disabled = true;
        sendButton.classList.add('opacity-50');
    }

    // Create FormData from the form
    const formData = new FormData(form);

    // Ensure we have the document ID in the form data
    if (!formData.has('document_id') && currentDocumentId) {
        formData.append('document_id', currentDocumentId);
    }

    // Get the correct route URL from the form action or construct it
    let actionUrl = form.getAttribute('action');
    if (!actionUrl) {
        actionUrl = `/comments/${currentDocumentId}`;
    }

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                     document.querySelector('input[name="_token"]')?.value;

    if (!csrfToken) {
        console.error('CSRF token not found');
        input.value = oldValue;
        input.disabled = false;
        if (sendButton) {
            sendButton.disabled = false;
            sendButton.classList.remove('opacity-50');
        }
        alert('Security token not found. Please refresh the page.');
        return;
    }

    fetch(actionUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);

        // Handle different response types
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json().then(data => ({ data, status: response.status, ok: response.ok }));
        } else {
            return response.text().then(text => ({ data: { message: text }, status: response.status, ok: response.ok }));
        }
    })
    .then(({ data, status, ok }) => {
        console.log('Response data:', data);

        if (ok && data.success) {
            // Clear input field and file input
            input.value = '';
            if (attachmentInput) {
                attachmentInput.value = '';
            }

            // Reset scroll flag to ensure we scroll to the new comment
            isUserScrolledUp = false;

            // Make sure we're passing proper comment data
            if (data.comment) {
                appendNewComment(data.comment);
            }

            // Focus back on input for continuing the conversation
            input.focus();
        } else {
            // Handle validation errors (422) and other errors
            let errorMessage = 'Failed to send comment.';

            if (status === 422 && data.errors) {
                // Laravel validation errors
                errorMessage = Object.values(data.errors).flat().join('\n');
            } else if (data.message) {
                errorMessage = data.message;
            }

            throw new Error(errorMessage);
        }
    })
    .catch(error => {
        console.error('Error submitting comment:', error);
        // Restore the original input text so the user doesn't lose their message
        input.value = oldValue;
        alert('Failed to send comment: ' + error.message);
    })
    .finally(() => {
        // Reset states
        input.disabled = false;
        if (sendButton) {
            sendButton.disabled = false;
            sendButton.classList.remove('opacity-50');
        }
    });
}

// Helper function to format time ago
function getTimeAgo(date) {
    const now = new Date();
    const diffMs = now - date;
    const diffSec = Math.floor(diffMs / 1000);
    const diffMin = Math.floor(diffSec / 60);
    const diffHour = Math.floor(diffMin / 60);
    const diffDay = Math.floor(diffHour / 24);

    if (diffSec < 60) return 'just now';
    if (diffMin < 60) return `${diffMin} minute${diffMin > 1 ? 's' : ''} ago`;
    if (diffHour < 24) return `${diffHour} hour${diffHour > 1 ? 's' : ''} ago`;
    if (diffDay < 30) return `${diffDay} day${diffDay > 1 ? 's' : ''} ago`;

    // If more than 30 days, return the actual date
    return date.toLocaleDateString();
}
