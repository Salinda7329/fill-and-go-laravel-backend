document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("registrationForm");

    if (form) {
        form.classList.add("visible");
    } else {
        console.warn("Registration form with ID 'registrationForm' not found.");
        return;
    }

    // Remove the default submit handler to avoid double submission
    form.onsubmit = null;

    // Clear all error messages first
    form.querySelectorAll('.error-message').forEach(el => {
        el.textContent = '';
        el.classList.remove('show');
    });
});
