const themeToggle = document.getElementById("themeToggle");
const themeIcon = document.getElementById("themeIcon");
const brandLogo = document.getElementById("appLogo");

const notificationIcon = document.getElementById("notificationIcon");

const check = document.getElementById("check");
const x = document.getElementById("x");
const clock = document.getElementById("clock");
const clock1 = document.getElementById("clock1");

const profileButton = document.getElementById("profileButton");
const profileMenu = document.getElementById("profileMenu");

const scheduleModalOverlay = document.getElementById("scheduleModalOverlay");
const scheduleModalTitle = document.getElementById("scheduleModalTitle");
const scheduleModalForm = document.getElementById("scheduleModalForm");

const scheduleDateInput = document.getElementById("scheduleDateInput");
const scheduleTimeInput = document.getElementById("scheduleTimeInput");

const scheduleModalCancel = document.getElementById("scheduleModalCancel");

// apply saved theme
const savedTheme = localStorage.getItem("theme");

const currentTheme = savedTheme === "dark"
    ? "dark"
    : "light";

function applyTheme(theme) {
    const isDark = theme === "dark";
    document.body.classList.toggle("dark", isDark);
    brandLogo.src = isDark
        ? "/icons/autodoc-logo-darkmode.svg"
        : "/icons/autodoc-logo.svg";
    themeIcon.src = isDark
        ? "/icons/light-mode.svg"
        : "/icons/dark-mode.svg";
    notificationIcon.src = isDark
        ? "/icons/notif-dark.svg"
        : "/icons/notif-light.svg";
    document.querySelectorAll(".theme-icon").forEach(icon => {
        icon.src = isDark
            ? icon.dataset.dark
            : icon.dataset.light;
    });
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

let activeScheduleTarget = null;

// schedule modal
document.querySelectorAll(".update-schedule-button[data-target]").forEach(button => {
    button.addEventListener("click", () => {
        activeScheduleTarget = button.dataset.target;
        scheduleModalTitle.textContent = button.dataset.title;
        scheduleDateInput.value = "";
        scheduleTimeInput.value = "";
        scheduleModalOverlay.classList.add("show");
    });

});

function closeScheduleModal() {
    scheduleModalOverlay.classList.remove("show");
    activeScheduleTarget = null;
}

scheduleModalCancel.addEventListener("click", closeScheduleModal);

scheduleModalOverlay.addEventListener("click", (event) => {
    if (event.target === scheduleModalOverlay) {
        closeScheduleModal();
    }
});

// apply edit
scheduleModalForm.addEventListener("submit", (event) => {
    event.preventDefault();
    if (!activeScheduleTarget) return;
    const dateValue = scheduleDateInput.value;
    const timeValue = scheduleTimeInput.value;
    const [year, month, day] = dateValue.split("-");
    const formattedDate = `${month}/${day}/${year}`;
    const dateEl = document.querySelector(`[data-date-for="${activeScheduleTarget}"]`);
    if (dateEl) {
        dateEl.textContent = formattedDate;
    }
    closeScheduleModal();
});