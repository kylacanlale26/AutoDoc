const themeToggle = document.getElementById("themeToggle");
const themeIcon = document.getElementById("themeIcon");
const appLogo = document.getElementById("appLogo");

const notificationIcon = document.getElementById("notificationIcon");
const profileButton = document.getElementById("profileButton");
const profileMenu = document.getElementById("profileMenu");

const searchIcon = document.getElementById("searchIcon");
const downloadIcon = document.getElementById("downloadIcon");

// apply saved theme
const savedTheme = localStorage.getItem("theme");

const currentTheme = savedTheme === "dark"
    ? "dark"
    : "light";

function applyTheme(theme) {
    const isDark = theme === "dark";
    document.body.classList.toggle("dark", isDark);
    appLogo.src = isDark
        ? "/icons/autodoc-logo-darkmode.svg"
        : "/icons/autodoc-logo.svg";
    themeIcon.src = isDark
        ? "/icons/light-mode.svg"
        : "/icons/dark-mode.svg";
    notificationIcon.src = isDark
        ? "/icons/notif-dark.svg"
        : "/icons/notif-light.svg";
    searchIcon.src = isDark
        ? "/icons/search-dark.svg"
        : "/icons/search-light.svg";
    downloadIcon.src = isDark
        ? "/icons/download-dark.svg"
        : "/icons/download-light.svg";
}

applyTheme(currentTheme);

// theme toggle
themeToggle.addEventListener("click", () => {
    const isDark = document.body.classList.contains("dark");
    const newTheme = isDark
        ? "light"
        : "dark";
    localStorage.setItem("theme", newTheme);
    applyTheme(newTheme);
});

// profile
if (profileButton && profileMenu) {
    profileButton.addEventListener("click", (event) => {
        event.stopPropagation();
        profileMenu.classList.toggle("show");
    });
    document.addEventListener("click", (event) => {
        if (
            !profileMenu.contains(event.target) &&
            !profileButton.contains(event.target)
        ) {
            profileMenu.classList.remove("show");
        }
    });
}