window.openProfilePreviewModal = openProfilePreviewModal;
window.closeProfilePreviewModal = closeProfilePreviewModal;
window.openRemoveProfileModal = openRemoveProfileModal;
window.closeRemoveProfileModal = closeRemoveProfileModal;
window.submitRemoveProfileForm = submitRemoveProfileForm;
window.closeModal = closeModal;
window.keepEditing = function () {
    const cancelEditImageModal = document.getElementById(
        "cancelEditImageModal"
    );
    cancelEditImageModal.classList.add("hidden");
    cancelEditImageModal.style.display = "none";
};

function openProfilePreviewModal() {
    const profilePreviewModal = document.getElementById("profilePreviewModal");
    profilePreviewModal.classList.remove("hidden");
    profilePreviewModal.style.display = "flex";
}

function closeProfilePreviewModal() {
    const profilePreviewModal = document.getElementById("profilePreviewModal");
    profilePreviewModal.classList.add("hidden");
    profilePreviewModal.style.display = "none";
}
const input = document.getElementById("profileImageInput");
const modal = document.getElementById("imagePreviewModal");
const preview = document.getElementById("previewImage");
const base64Input = document.getElementById("profileImageBase64");
const zoomSlider = document.getElementById("zoomRange");
const requirements = document.getElementById("requirements");
const toLargefile = document.getElementById("toLargefile");
const toFiletype = document.getElementById("toFiletype");
let cropper;

let isEditing = false;

window.onbeforeunload = function (e) {
    if (isEditing) {
        e.preventDefault();
        // Required for Chrome to show alert
        e.returnValue = "";
        return "";
    }
};

input.addEventListener("change", function (e) {
    const file = e.target.files[0];
    // Hide all error/info messages first
    if (file) {
        const allowedTypes = [
            "image/jpeg",
            "image/jpg",
            "image/jfif",
            "image/png",
        ];
        const maxSize = 10 * 1024 * 1024; // 10MB

        if (!allowedTypes.includes(file.type)) {
            document.getElementById("requirements").classList.add("hidden");
            document.getElementById("toFiletype").classList.remove("hidden");
            document.getElementById("toLargefile").classList.add("hidden");
            e.target.value = "";
            return;
        }
        if (file.size > maxSize) {
            document.getElementById("requirements").classList.add("hidden");
            document.getElementById("toLargefile").classList.remove("hidden");
            document.getElementById("toFiletype").classList.add("hidden");
            e.target.value = "";
            return;
        }
        // If valid, show requirements and hide errors
        document.getElementById("requirements").classList.remove("hidden");
        document.getElementById("toLargefile").classList.add("hidden");
        document.getElementById("toFiletype").classList.add("hidden");

        isEditing = true;

        const reader = new FileReader();
        reader.onload = function (event) {
            preview.src = event.target.result;
            modal.classList.remove("hidden");
            modal.style.display = "flex";

            const styleEl = document.createElement("style");
            styleEl.id = "cropperCustomStyles";
            styleEl.innerHTML = `
                .cropper-line, .cropper-point {
                    background-color: white !important;
                }
                .cropper-view-box {
                    outline: 3px solid white !important;
                    outline-color: white !important;
                }
                .cropper-face {
                    background-color: transparent !important;
                }
                .cropper-dashed {
                    border-color: white !important;
                }
            `;
            document.head.appendChild(styleEl);

            preview.onload = function () {
                if (cropper) cropper.destroy();

                cropper = new Cropper(preview, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: "move",
                    autoCropArea: 1,
                    background: false,
                    zoomOnWheel: true,
                    guides: false,
                    highlight: false,
                    cropBoxMovable: false,
                    cropBoxResizable: false,
                    movable: true,
                    cropBoxHighlight: true,
                    modal: true,
                    minCropBoxWidth: 500,
                    minCropBoxHeight: 500,

                    ready() {
                        zoomSlider.value = 0;

                        // Make crop box circular
                        const cropBox =
                            document.querySelector(".cropper-crop-box");
                        const viewBox =
                            document.querySelector(".cropper-view-box");
                        const cropperFace =
                            document.querySelector(".cropper-face");

                        if (cropBox && viewBox) {
                            // Ensure the crop box is visible
                            cropBox.style.display = "block";
                            viewBox.style.display = "block";

                            // Apply circular mask to the crop box
                            cropBox.style.borderRadius = "50%";
                            viewBox.style.borderRadius = "50%";

                            if (cropperFace) {
                                cropperFace.style.borderRadius = "50%";
                            }

                            // Manually set the crop box size to be larger (if needed)
                            const containerData = cropper.getContainerData();
                            const size =
                                Math.min(
                                    containerData.width,
                                    containerData.height
                                ) * 0.9;

                            // Center the crop box
                            const left = (containerData.width - size) / 2;
                            const top = (containerData.height - size) / 2;

                            // Set the crop box data
                            cropper.setCropBoxData({
                                left: left,
                                top: top,
                                width: size,
                                height: size,
                            });

                            // Add proper highlight for circular area
                            document.querySelector(
                                ".cropper-modal"
                            ).style.opacity = "0.5";
                        }
                    },
                });
            };
        };
        reader.readAsDataURL(file);
    }
});
zoomSlider.addEventListener("input", function () {
    if (cropper) cropper.zoomTo(parseFloat(this.value));
});

document
    .getElementById("saveProfileButton")
    .addEventListener("click", function (e) {
        e.preventDefault();
        if (!cropper) return;

        const size = 300;
        const canvas = cropper.getCroppedCanvas({
            width: size,
            height: size,
            imageSmoothingQuality: "high",
            fillColor: "transparent",
            imageSmoothingEnabled: true,
        });

        const circularCanvas = document.createElement("canvas");
        circularCanvas.width = size;
        circularCanvas.height = size;
        const ctx = circularCanvas.getContext("2d");

        ctx.beginPath();
        ctx.arc(size / 2, size / 2, size / 2, 0, Math.PI * 2);
        ctx.closePath();
        ctx.clip();
        ctx.drawImage(canvas, 0, 0, size, size);

        base64Input.value = circularCanvas.toDataURL("image/png");
        const saveChangesModal = document.getElementById("saveChangesModal");

        // Open Save Changes Modal
        saveChangesModal.classList.remove("hidden");
        saveChangesModal.style.display = "flex";

        // Handle Save Changes Modal buttons
        document
            .querySelector('#saveChangesModal button[onclick="keepEditing()"]')
            .addEventListener("click", function () {
                saveChangesModal.classList.add("hidden");
                saveChangesModal.style.display = "none";
                // Submit the form
                isEditing = false;
                window.onbeforeunload = null;
                document.getElementById("uploadForm").submit();
            });

        document
            .querySelector(
                '#saveChangesModal button[onclick="closeChangesModal()"]'
            )
            .addEventListener("click", function () {
                saveChangesModal.classList.add("hidden");
                saveChangesModal.style.display = "none";
            });
    });

function openRemoveProfileModal() {
    const removeProfileModal = document.getElementById("removeProfileModal");
    removeProfileModal.classList.remove("hidden");
    removeProfileModal.style.display = "flex";
}

function closeRemoveProfileModal() {
    const removeProfileModal = document.getElementById("removeProfileModal");
    removeProfileModal.classList.add("hidden");
    removeProfileModal.style.display = "none";
}

function submitRemoveProfileForm() {
    const form = document.querySelector("#profilePreviewModal form");
    if (form) {
        form.submit();
    }
}

function closeModal() {
    const cancelEditImageModal = document.getElementById(
        "cancelEditImageModal"
    );
    cancelEditImageModal.classList.remove("hidden");
    cancelEditImageModal.style.display = "flex";

    document
        .querySelector('#cancelEditImageModal button[onclick="closeModal()"]')
        .addEventListener("click", function () {
            modal.classList.add("hidden");
            modal.style.display = "none";
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            preview.src = "";
            input.value = "";
            cancelEditImageModal.classList.add("hidden");
            cancelEditImageModal.style.display = "none";
        });
    document
        .querySelector('#cancelEditImageModal button[onclick="keepEditing()"]')
        .addEventListener("click", function () {
            const cancelEditImageModal = document.getElementById(
                "cancelEditImageModal"
            );
            cancelEditImageModal.classList.add("hidden");
            cancelEditImageModal.style.display = "none";
        });
}

setTimeout(() => {
    const toast = document.getElementById("Toast");
    if (toast) {
        toast.style.display = "none";
    }
}, 5000);
