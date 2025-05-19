// student-comments.js - Handle comments functionality for student view

let currentDocumentId = null;
let echo = null;
let isUserScrolledUp = false;
let scrollDebounceTimer = null;

document.addEventListener('DOMContentLoaded', function() {
    // Get the document ID from URL or data attribute
    const urlParts = window.location.pathname.split('/');
    currentDocumentId = document.getElementById('record-container')?.getAttribute('data-document-id') || urlParts[urlParts.length - 1];

    console.log("Current document ID:", currentDocumentId);

    if (currentDocumentId) {
        // Initialize Echo/Pusher
        initializeEcho();

        // Load comments when page loads
        loadComments(currentDocumentId);

        // Listen for new comments
        setupCommentListener();
    } else {
        console.error("No document ID found");
    }

    // Set up event listeners
    setupEventListeners();
});

function initializeEcho() {
    // Import Pusher and Echo dynamically if not already available globally
    if (typeof window.Echo === 'undefined') {
        import('pusher-js').then(Pusher => {
            window.Pusher = Pusher.default;
            return import('laravel-echo');
        }).then(Echo => {
            window.Echo = new Echo.default({
                broadcaster: 'pusher',
                key: process.env.MIX_PUSHER_APP_KEY,
                cluster: process.env.MIX_PUSHER_APP_CLUSTER,
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
}

function setupEventListeners() {
    const commentInput = document.getElementById('commentInput');
    const sendButton = document.getElementById('sendCommentBtn');
    const commentsContainer = document.getElementById('commentsContainer');

    if (commentsContainer) {
        // Add scroll event listener to detect when user scrolls
        commentsContainer.addEventListener('scroll', function() {
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
        commentInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                submitComment();
            }
        });

        // Focus input when comments area is clicked
        if (commentsContainer) {
            commentsContainer.addEventListener('click', function() {
                commentInput.focus();
            });
        }
    }

    if (sendButton) {
        // Listen for click on send button
        sendButton.addEventListener('click', function() {
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
                <p class="text-sm break-words">${formatCommentText(comment.comment)}</p>
            </div>
        </div>
    `;
}

// Format comment text with basic link detection and emoji support
function formatCommentText(text) {
    // Convert URLs to clickable links
    const urlRegex = /(https?:\/\/[^\s]+)/g;
    text = text.replace(urlRegex, url => `<a href="${url}" target="_blank" class="text-blue-300 hover:underline">${url}</a>`);

    return text;
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
    const input = document.getElementById('commentInput');
    if (!input) {
        console.error('Comment input field not found');
        return;
    }

    const comment = input.value.trim();

    if (!comment || !currentDocumentId) {
        return;
    }

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

    fetch('/comments', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        },
        body: JSON.stringify({
            document_id: currentDocumentId,
            comment: comment
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Server error: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        // Clear input field
        input.value = '';

        // Reset scroll flag to ensure we scroll to the new comment
        isUserScrolledUp = false;

        // Use the actual comment data returned from the server
        appendNewComment(data);

        // Focus back on input for continuing the conversation
        input.focus();
    })
    .catch(error => {
        console.error('Error submitting comment:', error);
        // Restore the original input text so the user doesn't lose their message
        input.value = oldValue;
        alert('Failed to send comment. Please try again.');
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
