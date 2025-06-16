// student-comments.js - Handle comments functionality for student view

let currentDocumentId = null;
let echo = null;
let isUserScrolledUp = false;
let scrollDebounceTimer = null;

document.addEventListener("DOMContentLoaded", function () {
    // Get the document ID from URL or data attribute
    const urlParts = window.location.pathname.split("/");
    currentDocumentId =
        document
            .getElementById("record-container")
            ?.getAttribute("data-document-id") || urlParts[urlParts.length - 1];

    console.log("Current document ID:", currentDocumentId);

    if (currentDocumentId) {
        // Initialize Echo/Pusher only if Pusher configuration is available
        initializeEcho();

        // Load comments when page loads - only if comments container exists
        if (document.getElementById("commentCont")) {
            loadComments(currentDocumentId);
        } else {
            console.warn(
                "Comments container not found - comments functionality disabled"
            );
        }

        // Listen for new comments
        setupCommentListener();
    } else {
        console.error("No document ID found");
    }

    // Set up event listeners
    setupEventListeners();
    // Attachment preview modal for comments
    document.body.addEventListener("click", function (e) {
        const target = e.target.closest(".comment-attachment-link");
        if (target) {
            e.preventDefault();
            const fileUrl = target.getAttribute("data-file-url");
            const fileName = target.getAttribute("data-file-name");
            const ext = target.getAttribute("data-file-ext");

            // Open the modal and show the file
            openDocumentViewerModal(fileUrl, fileName, ext);
        }
    });
});

function openDocumentViewerModal(fileUrl, fileName, ext) {
    // Get modal elements (make sure these IDs match your Blade modal)
    const modal = document.getElementById("documentViewerModal");
    const pdfViewer = document.getElementById("pdfViewer");
    const imageViewer = document.getElementById("imageViewer");
    const downloadView = document.getElementById("downloadView");
    const documentTitle = document.getElementById("documentTitle");
    const downloadFileName = document.getElementById("downloadFileName");
    const downloadButton = document.getElementById("downloadButton");

    if (!modal) return;

    documentTitle.textContent = fileName;
    downloadFileName.textContent = fileName;
    downloadButton.href = fileUrl;

    // Reset viewers
    pdfViewer.innerHTML = "";
    imageViewer.innerHTML = "";
    imageViewer.classList.add("hidden");
    pdfViewer.classList.remove("hidden");
    downloadView.classList.add("hidden");

    if (["pdf"].includes(ext)) {
        pdfViewer.innerHTML = `<iframe src="${fileUrl}#toolbar=0" class="w-full h-full" frameborder="0"></iframe>`;
    } else if (["jpg", "jpeg", "png", "gif", "bmp", "webp"].includes(ext)) {
        pdfViewer.classList.add("hidden");
        imageViewer.classList.remove("hidden");
        imageViewer.innerHTML = `<img src="${fileUrl}" alt="${fileName}" class="max-h-full max-w-full rounded shadow" />`;
    } else {
        pdfViewer.innerHTML = `<div class="flex items-center justify-center h-full text-gray-500">Preview not available for this file type.</div>`;
    }

    // Show the modal
    modal.classList.remove("hidden");
}

function initializeEcho() {
    // Check if Pusher configuration is available in window object or meta tags
    const pusherKey =
        window.pusherConfig?.key ||
        document.querySelector('meta[name="pusher-key"]')?.content;
    const pusherCluster =
        window.pusherConfig?.cluster ||
        document.querySelector('meta[name="pusher-cluster"]')?.content;

    if (!pusherKey || !pusherCluster) {
        console.warn(
            "Pusher configuration not found - real-time updates disabled"
        );
        return;
    }

    // Import Pusher and Echo dynamically if not already available globally
    if (typeof window.Echo === "undefined") {
        import("pusher-js")
            .then((Pusher) => {
                window.Pusher = Pusher.default;
                return import("laravel-echo");
            })
            .then((Echo) => {
                window.Echo = new Echo.default({
                    broadcaster: "pusher",
                    key: pusherKey,
                    cluster: pusherCluster,
                    forceTLS: true,
                    encrypted: true,
                    authEndpoint: "/broadcasting/auth",
                });

                setupCommentListener();
            })
            .catch((error) => {
                console.error("Error loading Pusher/Echo:", error);
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
            .listen(".comment.created", (data) => {
                console.log("New comment received:", data);
                appendNewComment(data.comment);
            })
            .listen(".comment.updated", (data) => {
                console.log("Comment updated:", data);
                // You might want to implement comment updates if needed
            });
    } catch (error) {
        console.error("Error setting up comment listener:", error);
    }
}

function setupEventListeners() {
    const commentInput = document.getElementById("commentInput");
    const sendButton = document.getElementById("submitCommentBtn");
    const commentForm = document.getElementById("commentForm");
    const commentsContainer = document.getElementById("commentCont");
    const attachmentInput = document.getElementById("commentAttachment");
    const attachmentPreview = document.getElementById("attachmentPreview");
    const attachmentName = document.getElementById("attachmentName");
    const removeAttachment = document.getElementById("removeAttachment");

    if (commentsContainer) {
        // Add scroll event listener to detect when user scrolls
        commentsContainer.addEventListener("scroll", function () {
            // Clear any existing debounce timer
            clearTimeout(scrollDebounceTimer);

            // Set a new debounce timer
        });
    }

    if (commentInput) {
        // Listen for Enter key in the input field
        commentInput.addEventListener("keypress", function (e) {
            if (e.key === "Enter" && !e.shiftKey) {
                e.preventDefault();
                submitComment();
            }
        });

        // Focus input when comments area is clicked
        if (commentsContainer) {
            commentsContainer.addEventListener("click", function () {
                commentInput.focus();
            });
        }
    }

    // Handle form submission
    if (commentForm) {
        commentForm.addEventListener("submit", function (e) {
            e.preventDefault(); // Prevent default form submission
            submitComment();
        });
    }

    if (sendButton) {
        // Listen for click on send button
        sendButton.addEventListener("click", function (e) {
            e.preventDefault();
            submitComment();
        });
    }

    // --- Attachment preview logic ---
    if (attachmentInput && attachmentPreview && attachmentName) {
        attachmentInput.addEventListener("change", function () {
            if (attachmentInput.files && attachmentInput.files.length > 0) {
                const file = attachmentInput.files[0];
                attachmentName.textContent = file.name;
                attachmentPreview.classList.remove("hidden");
            } else {
                attachmentPreview.classList.add("hidden");
                attachmentName.textContent = "";
            }
        });
    }

    if (
        removeAttachment &&
        attachmentInput &&
        attachmentPreview &&
        attachmentName
    ) {
        removeAttachment.addEventListener("click", function () {
            attachmentInput.value = "";
            attachmentPreview.classList.add("hidden");
            attachmentName.textContent = "";
        });
    }
}
// Function to load comments for the current document
function loadComments(documentId) {
    const container = document.getElementById("commentCont");
    if (!container) {
        console.error("Comments container not found");
        return;
    }

    // Show loading indicator
    container.innerHTML =
        '<p class="text-gray-400 text-center">Loading comments...</p>';

    fetch(`/comments/${documentId}`)
        .then((response) => {
            if (!response.ok) {
                throw new Error("Server error: " + response.status);
            }
            return response.json();
        })
        .then((comments) => {
            if (!Array.isArray(comments) || comments.length === 0) {
                container.innerHTML =
                    '<p class="text-gray-400 text-center">No comments yet</p>';
                return;
            }

            // Sort comments by created_at (oldest first for initial load)
            comments.sort(
                (a, b) => new Date(b.created_at) - new Date(a.created_at)
            );

            container.innerHTML = comments
                .map((comment) => {
                    return createCommentElement(comment);
                })
                .join("");
        })
        .catch((error) => {
            console.error("Error loading comments:", error);
            container.innerHTML =
                '<p class="text-red-400 text-center">Failed to load comments. Please refresh the page.</p>';
        });
}

function createCommentElement(comment) {
    const username =
        comment.sender && comment.sender.username
            ? comment.sender.username
            : "Unknown User";
    const userRole =
        comment.sender && comment.sender.role_name
            ? comment.sender.role_name
            : "Unknown User";
    const profilePic =
        comment.sender && comment.sender.profile_pic
            ? `<div class="border-1 border-gray-300 rounded-full w-10 h-10 bg-white flex items-center justify-center">
                <img src="/storage/${comment.sender.profile_pic}" alt="Profile" class="w-10 h-10 rounded-full object-cover">
           </div>`
            : `<div class="w-10 h-10 bg-white rounded-full bg-maroon-700 flex items-center border-1 justify-center text-white text-3xl font-bold">
                <img src="/images/dprofile.svg" class="w-10 h-10" alt="camera icon">
           </div>`;

    const time = comment.created_at
        ? new Date(comment.created_at).toLocaleTimeString([], {
              hour: "2-digit",
              minute: "2-digit",
          })
        : "";

    let attachment = "";
    if (comment.attachment_path) {
        const ext = comment.attachment_path.split(".").pop().toLowerCase();
        const fileUrl = `/storage/${comment.attachment_path}`;
        const fileName = comment.attachment_name.split("/").pop();

        // Use a single clickable link for all types, opening the modal
        const attachmentLink = `
            <a href="#" 
                class="comment-attachment-link flex items-center"
                data-file-url="${fileUrl}"
                data-file-name="${fileName}"
                data-file-ext="${ext}">
        `;

        if (["jpg", "jpeg", "png"].includes(ext)) {
            attachment = `
                <div class="mt-2 max-w-full">
                    ${attachmentLink}
                        <div class="rounded overflow-hidden max-w-[250px] cursor-pointer">
                            <img src="${fileUrl}" alt="Attachment Image" class="max-w-full h-auto">
                            <div class="text-xs text-gray-400 mt-1 truncate">${fileName}</div>
                        </div>
                    </a>
                </div>
            `;
        } else if (["pdf", "doc", "docx"].includes(ext)) {
            // Determine icon based on file type
            let icon = "";
            if (ext === "pdf") {
                icon =
                    '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>';
            } else if (ext === "docx" || ext === "doc") {
                icon =
                    '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
            } else {
                icon =
                    '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
            }

            attachment = `
                <div class="mt-2 bg-gray-100 rounded p-2 inline-block max-w-full flex items-center text-blue-600 hover:underline">
                    ${attachmentLink}
                        ${icon}
                        <span class="text-xs truncate max-w-[200px]">${fileName}</span>
                    </a>
                </div>
            `;
        }
    }

    // Use formatCommentText for comment body if you want links/emoji support
    const commentText =
        typeof comment.comment === "string"
            ? formatCommentText(comment.comment)
            : "";

    return `
        <div class=" pb-4 mb-4">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    ${profilePic}
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium truncate">${username}</p>
                            <p class="text-xs text-gray-300">${userRole}</p>
                        </div>
                        <span class="text-gray-300 text-sm">${
                            comment.created_at
                                ? getTimeAgo(new Date(comment.created_at))
                                : ""
                        }</span>
                    </div>
                    <p class="text-white mt-1">${commentText}</p>
                    ${attachment}
                </div>
            </div>
        </div>
    `;
}

// Format comment text with basic link detection and emoji support
function formatCommentText(text) {
    // Check if text is null/undefined or not a string
    if (!text || typeof text !== "string") {
        return ""; // Return empty string if no valid text
    }

    // Convert URLs to clickable links
    const urlRegex = /(https?:\/\/[^\s]+)/g;
    return text.replace(
        urlRegex,
        (url) =>
            `<a href="${url}" target="_blank" class="text-blue-300 hover:underline">${url}</a>`
    );
}

function appendNewComment(commentData) {
    const container = document.getElementById("commentCont");
    if (!container) {
        console.error("Comments container not found");
        return;
    }

    // Remove "No comments yet" message if it exists
    const noCommentsMsg = container.querySelector(".text-gray-400");
    if (noCommentsMsg && noCommentsMsg.textContent === "No comments yet") {
        container.innerHTML = "";
    }

    // Create new comment element
    const commentDiv = document.createElement("div");
    commentDiv.innerHTML = createCommentElement(commentData);

    // Since comments are reversed (newest at top), insert at the top
    container.insertBefore(commentDiv.firstElementChild, container.firstChild);

    // Scroll to top to show the newest comment
    container.scrollTop = 0;
}

function submitComment() {
    const form = document.getElementById("commentForm");
    const input = document.getElementById("commentInput");
    const attachmentInput = document.getElementById("commentAttachment");

    if (!form || !input) {
        console.error("Comment form or input field not found");
        return;
    }

    const comment = input.value.trim();
    const hasAttachment = attachmentInput && attachmentInput.files.length > 0;

    if (!comment && !hasAttachment) return;

    // Build FormData BEFORE changing input value
    const formData = new FormData(form);

    if (!formData.has("document_id") && currentDocumentId) {
        formData.append("document_id", currentDocumentId);
    }

    const oldValue = input.value;
    input.value = "Sending...";
    input.disabled = true;

    const sendButton = document.getElementById("sendCommentBtn");
    if (sendButton) {
        sendButton.disabled = true;
        sendButton.classList.add("opacity-50");
    }

    let actionUrl =
        form.getAttribute("action") || `/comments/${currentDocumentId}`;

    const csrfToken =
        document.querySelector('meta[name="csrf-token"]')?.content ||
        document.querySelector('input[name="_token"]')?.value;

    if (!csrfToken) {
        console.error("CSRF token not found");
        alert("Security token missing. Please refresh the page.");
        resetInputState();
        return;
    }

    fetch(actionUrl, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
        },
        body: formData,
    })
        .then(async (response) => {
            const contentType = response.headers.get("content-type");
            const isJson =
                contentType && contentType.includes("application/json");
            const data = isJson
                ? await response.json()
                : { message: await response.text() };

            // Treat any non-2xx response or data.success === false as an error
            if (!response.ok || data.success === false) {
                let errorMessage = "Failed to send comment.";
                if (response.status === 422 && data.errors) {
                    errorMessage = Object.values(data.errors).flat().join("\n");
                } else if (data.message) {
                    errorMessage = data.message;
                }
                throw new Error(errorMessage);
            }

            // Success
            input.value = "";
            if (attachmentInput) attachmentInput.value = "";
            isUserScrolledUp = false;

            // Hide attachment preview after submit
            if (attachmentPreview && attachmentName) {
                attachmentPreview.classList.add("hidden");
                attachmentName.textContent = "";
            }

            if (data.comment) {
                appendNewComment(data.comment);
            }

            input.focus();
        })
        .catch((error) => {
            console.error("Error submitting comment:", error);
            input.value = oldValue;
            alert("Failed to send comment: " + error.message);
        })
        .finally(() => {
            resetInputState();
        });

    function resetInputState() {
        input.disabled = false;
        if (sendButton) {
            sendButton.disabled = false;
            sendButton.classList.remove("opacity-50");
        }
    }
}
// Helper function to format time ago
function getTimeAgo(date) {
    const now = new Date();
    const diffMs = now - date;
    const diffSec = Math.floor(diffMs / 1000);
    const diffMin = Math.floor(diffSec / 60);
    const diffHour = Math.floor(diffMin / 60);
    const diffDay = Math.floor(diffHour / 24);

    if (diffSec < 60) return "just now";
    if (diffMin < 60) return `${diffMin} minute${diffMin > 1 ? "s" : ""} ago`;
    if (diffHour < 24) return `${diffHour} hour${diffHour > 1 ? "s" : ""} ago`;
    if (diffDay < 30) return `${diffDay} day${diffDay > 1 ? "s" : ""} ago`;

    // If more than 30 days, return the actual date
    return date.toLocaleDateString();
}
