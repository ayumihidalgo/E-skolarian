window.openChangePasswordModal = openChangePasswordModal;
window.closeChangePasswordModal = closeChangePasswordModal;
window.closeUnsavedModal = closeUnsavedModal;
window.keepEditing = keepEditing;

const changePasswordButton = document.getElementById("changePasswordButton");
const currentPasswordInput = document.getElementById("current_password");
const newPasswordInput = document.getElementById("new_password");
const confirmPasswordInput = document.getElementById(
    "new_password_confirmation"
);

let isFormDirty = false;
let isSubmitting = false;

// Mark form as dirty if any field is changed
[currentPasswordInput, newPasswordInput, confirmPasswordInput].forEach(
    (input) => {
        input.addEventListener("input", () => {
            isFormDirty =
                currentPasswordInput.value !== "" ||
                newPasswordInput.value !== "" ||
                confirmPasswordInput.value !== "";
        });
    }
);

// Mark as submitting when form is submitted
document.getElementById("changePasswordForm").addEventListener("submit", () => {
    isSubmitting = true;
});

// Warn user if trying to leave with unsaved changes
window.addEventListener("beforeunload", function (e) {
    if (isFormDirty && !isSubmitting) {
        e.preventDefault();
        e.returnValue = ""; // Required for Chrome and modern browsers
    }
});

// Optional: Reset flags after modal closes
function resetDirtyFlags() {
    isFormDirty = false;
    isSubmitting = false;
}

const checkIcon = `<span style="color: #16a34a; font-weight: bold;">&#10003;</span>&nbsp;`;
const bulletIcon = `•&nbsp;`;

function updatePasswordRequirements(password) {
    // Requirement checks
    const hasMinLength = password.length >= 8;
    const hasNumber = /\d/.test(password);
    const hasLower = /[a-z]/.test(password);
    const hasUpper = /[A-Z]/.test(password);
    const hasSpecial = /[@$!%*?&#]/.test(password);
    const noSpaces = !/\s/.test(password);

    // Update checklist
    document.getElementById("characterLimit").innerHTML =
        (hasMinLength ? checkIcon : bulletIcon) +
        "Require at least 8 characters";
    document.getElementById("oneNumber").innerHTML =
        (hasNumber ? checkIcon : bulletIcon) + "Require at least one number";
    document.getElementById("lowerCase").innerHTML =
        (hasLower ? checkIcon : bulletIcon) +
        "Require at least one lower case letter";
    document.getElementById("upperCase").innerHTML =
        (hasUpper ? checkIcon : bulletIcon) +
        "Require at least one uppercase letter";
    document.getElementById("specialCharacter").innerHTML =
        (hasSpecial ? checkIcon : bulletIcon) +
        "Require at least one special character";
    document.getElementById("noSpaces").innerHTML =
        (noSpaces ? checkIcon : bulletIcon) + "Password cannot contain spaces";
}

// Attach event listener to new password input
newPasswordInput.addEventListener("input", function () {
    updatePasswordRequirements(this.value);
});

window.togglePassword = togglePassword;
function togglePassword(event, inputId) {
    event.preventDefault();
    const input = document.getElementById(inputId);
    const showIcon = document.getElementById("showPass_" + inputId);
    const hideIcon = document.getElementById("hidePass_" + inputId);
    if (input.type === "password") {
        input.type = "text";
        showIcon.classList.add("hidden");
        hideIcon.classList.remove("hidden");
    } else {
        input.type = "password";
        showIcon.classList.remove("hidden");
        hideIcon.classList.add("hidden");
    }
}
// Function to check if password meets requirements
function passwordMeetsRequirements(password) {
    const hasMinLength = password.length >= 8;
    const hasNumber = /\d/.test(password);
    const hasLower = /[a-z]/.test(password);
    const hasUpper = /[A-Z]/.test(password);
    const hasSpecial = /[@$!%*?&#]/.test(password);
    const noSpaces = !/\s/.test(password);
    return (
        hasMinLength &&
        hasNumber &&
        hasLower &&
        hasUpper &&
        hasSpecial &&
        noSpaces
    );
}
// Function to toggle the button state based on input values
function toggleButtonState() {
    const current = currentPasswordInput.value.trim();
    const newPass = newPasswordInput.value.trim();
    const confirm = confirmPasswordInput.value.trim();

    if (current && newPass && confirm && passwordMeetsRequirements(newPass)) {
        changePasswordButton.disabled = false;
    } else {
        changePasswordButton.disabled = true;
    }
}

currentPasswordInput.addEventListener("input", toggleButtonState);
newPasswordInput.addEventListener("input", toggleButtonState);
confirmPasswordInput.addEventListener("input", toggleButtonState);
// Initialize the change password modal
const changePasswordModal = document.getElementById("changePasswordModal");
const leaveWithoutSavingModal = document.getElementById(
    "leaveWithoutSavingModal"
);
document
    .getElementById("changePasswordForm")
    .addEventListener("submit", function (e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        // Set button to loading state
        changePasswordButton.disabled = true;
        changePasswordButton.textContent = "Changing...";

        // Clear previous error messages and remove error borders
        document.getElementById("currentPasswordError").textContent = "";
        document.getElementById("newPasswordError").textContent = "";
        document.getElementById("confirmPasswordError").textContent = "";
        [
            "current_password",
            "new_password",
            "new_password_confirmation",
        ].forEach((id) => {
            document.getElementById(id).classList.remove("border-red-500");
        });
        // Validate new password
        const currentPassword =
            document.getElementById("current_password").value;
        const newPassword = document.getElementById("new_password").value;

        if (newPassword === currentPassword) {
            document.getElementById("newPasswordError").textContent =
                "The new password cannot be the same as the current password.";
            document
                .getElementById("new_password")
                .classList.add("border-red-500");
            resetButtonState();
            return;
        }

        if (newPassword.trim() !== newPassword || /^\s*$/.test(newPassword)) {
            document.getElementById("newPasswordError").textContent =
                "The new password cannot contain leading or trailing spaces or be all spaces.";
            document
                .getElementById("new_password")
                .classList.add("border-red-500");
            resetButtonState();
            return;
        }

        fetch(form.action, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                Accept: "application/json",
            },
            body: formData,
        })
            .then((response) => {
                if (!response.ok) {
                    return response.json().then((data) => {
                        if (data.errors) {
                            // Display validation errors and add red border to fields with errors
                            if (data.errors.current_password) {
                                // Customize the error message for current password
                                document.getElementById(
                                    "currentPasswordError"
                                ).textContent =
                                    "Incorrect current password. Please try again.";
                                document
                                    .getElementById("current_password")
                                    .classList.add("border-red-500");
                            }
                            if (data.errors.new_password) {
                                // Customize the error message for new password
                                document.getElementById(
                                    "newPasswordError"
                                ).textContent =
                                    "The passwords do not match. Please confirm your new password.";
                                document
                                    .getElementById("new_password")
                                    .classList.add("border-red-500");
                            }
                        }
                        throw new Error("Validation failed");
                    });
                }
                return response.json();
            })
            .then((data) => {
                if (data.logout) {
                    // Optionally show the modal for a brief moment
                    const successModal =
                        document.getElementById("successModal");
                    const changePasswordModal = document.getElementById(
                        "changePasswordModal"
                    );
                    changePasswordModal.classList.add("hidden");
                    changePasswordModal.style.display = "none";
                    successModal.classList.remove("hidden");
                    successModal.style.display = "flex";
                    resetDirtyFlags();

                    // Redirect to login after a short delay (e.g., 1 second)
                    setTimeout(function () {
                        window.location.href = "/login"; // or use your route helper if available
                    }, 10000);
                }
            })
            .catch((error) => {
                console.error(error);
            })
            .finally(() => {
                resetButtonState();
            });
    });

function resetButtonState() {
    changePasswordButton.disabled = false;
    changePasswordButton.textContent = "Change Password";
}

// Automatically remove error messages and red borders on input
document
    .getElementById("current_password")
    .addEventListener("input", function () {
        document.getElementById("currentPasswordError").textContent = "";
        this.classList.remove("border-red-500");
    });

document.getElementById("new_password").addEventListener("input", function () {
    document.getElementById("newPasswordError").textContent = "";
    this.classList.remove("border-red-500");
});

document
    .getElementById("new_password_confirmation")
    .addEventListener("input", function () {
        document.getElementById("confirmPasswordError").textContent = "";
        this.classList.remove("border-red-500");
    });

function openChangePasswordModal() {
    changePasswordModal.classList.remove("hidden");
    changePasswordModal.style.display = "flex";
}

function closeChangePasswordModal() {
    if (
        document.getElementById("current_password").value === "" &&
        document.getElementById("new_password").value === "" &&
        document.getElementById("new_password_confirmation").value === ""
    ) {
        changePasswordModal.classList.add("hidden");
        changePasswordModal.style.display = "none";
        resetDirtyFlags();
    } else {
        leaveWithoutSavingModal.classList.remove("hidden");
        leaveWithoutSavingModal.style.display = "flex";
    }
}

function closeUnsavedModal() {
    changePasswordModal.classList.add("hidden");
    changePasswordModal.style.display = "none";
    leaveWithoutSavingModal.classList.add("hidden");
    leaveWithoutSavingModal.style.display = "none";
    changePasswordButton.disabled = true;
    // Clear all input fields
    document.getElementById("current_password").value = "";
    document.getElementById("new_password").value = "";
    document.getElementById("new_password_confirmation").value = "";

    // Clear red borders
    ["current_password", "new_password", "new_password_confirmation"].forEach(
        (id) => {
            const input = document.getElementById(id);
            input.classList.remove("input-error", "border-red-500"); // remove both if you mix class styles
        }
    );

    // Clear error messages
    [
        "currentPasswordError",
        "newPasswordError",
        "confirmPasswordError",
    ].forEach((errorId) => {
        const errorDiv = document.getElementById(errorId);
        if (errorDiv) errorDiv.textContent = "";
    });
    resetDirtyFlags();
}

function keepEditing() {
    leaveWithoutSavingModal.classList.add("hidden");
    leaveWithoutSavingModal.style.display = "none";
}
