
const passwordInput = document.getElementById("password");
const togglePassword = document.getElementById("togglePassword");
const visibilityIcon = document.getElementById("visibilityIcon");

// apply saved theme
const savedTheme = localStorage.getItem("theme");

const currentTheme = savedTheme === "dark"
    ? "dark"
    : "light";

if (currentTheme === "dark") {
    document.body.classList.add("dark");
}

function updateVisibilityIcon(isVisible) {
    if (currentTheme === "dark") {
        visibilityIcon.src = isVisible
            ? "/icons/visibility-dark.svg"
            : "/icons/visibility-off-dark.svg";
    } else {
        visibilityIcon.src = isVisible
            ? "/icons/visibility-light.svg"
            : "/icons/visibility-off-light.svg";
    }
}

// default hidden password state
updateVisibilityIcon(false);

// toggle password visibility
togglePassword.addEventListener("click", () => {
    const isCurrentlyHidden =
        passwordInput.type === "password";
    if (isCurrentlyHidden) {
        passwordInput.type = "text";
        updateVisibilityIcon(true);
        togglePassword.setAttribute(
            "aria-label",
            "Hide password"
        );
    } else {
        passwordInput.type = "password";
        updateVisibilityIcon(false);
        togglePassword.setAttribute(
            "aria-label",
            "Show password"
        );
    }
});