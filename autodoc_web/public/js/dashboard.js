const themeToggle = document.getElementById("themeToggle");
const themeIcon = document.getElementById("themeIcon");
const brandLogo = document.getElementById("brandLogo");
const notificationIcon = document.getElementById("notificationIcon");
const profileButton = document.getElementById("profileButton");
const profileMenu = document.getElementById("profileMenu");
const searchIcon = document.getElementById("searchIcon");
const downloadIcon = document.getElementById("downloadIcon");
const documentSearch = document.getElementById("documentSearch");
const documentTableBody = document.getElementById("documentTableBody");
const enlistmentLink = document.querySelector('a[href="#enlistment"]');
const enlistmentPage = document.getElementById("enlistment");


/* =========================
   THEME
========================= */

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

    searchIcon.src = isDark
        ? "/icons/search-dark.svg"
        : "/icons/search-light.svg";

    downloadIcon.src = isDark
        ? "/icons/download-dark.svg"
        : "/icons/download-light.svg";
}


applyTheme(currentTheme);


/* =========================
   THEME TOGGLE
========================= */

themeToggle.addEventListener("click", () => {

    const isDark = document.body.classList.contains("dark");

    const newTheme = isDark
        ? "light"
        : "dark";

    localStorage.setItem("theme", newTheme);

    applyTheme(newTheme);
});

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

/* =========================
   DOCUMENT SEARCH
========================= */

documentSearch.addEventListener("input", () => {

    const searchValue = documentSearch.value.toLowerCase();

    const rows = documentTableBody.querySelectorAll("tr");

    rows.forEach(row => {

        const rowText = row.textContent.toLowerCase();

        row.style.display = rowText.includes(searchValue)
            ? ""
            : "none";
    });
});

if (enlistmentLink && enlistmentPage) {
    enlistmentLink.addEventListener("click", (event) => {
        event.preventDefault();

        enlistmentPage.scrollIntoView({
            behavior: "smooth",
            block: "start"
        });
    });
}