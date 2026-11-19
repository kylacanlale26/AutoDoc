import './bootstrap';

const loadingScreen = document.getElementById('autodocLoadingScreen');
const hideLoadingScreen = () => loadingScreen?.classList.add('is-hidden');
const showLoadingScreen = () => loadingScreen?.classList.remove('is-hidden');

if (loadingScreen) {
    if (document.readyState === 'complete') hideLoadingScreen();
    else window.addEventListener('load', hideLoadingScreen, { once: true });

    window.addEventListener('pageshow', hideLoadingScreen);
    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        if (link.target && link.target !== '_self' || link.hasAttribute('download')) return;
        const destination = new URL(link.href, window.location.href);
        if (destination.origin !== window.location.origin || destination.href === window.location.href) return;
        showLoadingScreen();
    });
    document.addEventListener('submit', (event) => {
        if (event.target instanceof HTMLFormElement && event.target.target !== '_blank') showLoadingScreen();
    });
}

const themeButton = document.getElementById('themeToggle');
const themeIcon = document.getElementById('themeIcon');
const appLogo = document.getElementById('appLogo');
const loadingLogo = document.getElementById('loadingLogo');
const documentMenuIcons = document.querySelectorAll('.document-more-button img, .submission-more-button img');
const searchFilterIcons = document.querySelectorAll('[data-light-theme-icon][data-dark-theme-icon]');
const themeRoot = document.documentElement;

function applyTheme(theme) {
    const isDark = theme === 'dark';
    themeRoot.dataset.theme = isDark ? 'dark' : 'light';
    if (themeButton) {
        themeButton.setAttribute('aria-pressed', String(isDark));
        themeButton.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
    }
    if (themeIcon && themeButton) {
        themeIcon.src = isDark
            ? themeButton.dataset.darkIcon
            : themeButton.dataset.lightIcon;
        themeIcon.alt = isDark ? 'Switch to light mode' : 'Switch to dark mode';
    }
    [appLogo, loadingLogo].forEach((logo) => {
        if (logo) logo.src = isDark ? logo.dataset.darkLogo : logo.dataset.lightLogo;
    });
    documentMenuIcons.forEach((icon) => {
        icon.src = isDark ? icon.dataset.darkIcon : icon.dataset.lightIcon;
    });
    searchFilterIcons.forEach((icon) => {
        icon.src = isDark ? icon.dataset.darkThemeIcon : icon.dataset.lightThemeIcon;
    });
}

applyTheme(themeRoot.dataset.theme || 'light');
themeButton?.addEventListener('click', () => {
    const nextTheme = themeRoot.dataset.theme === 'dark' ? 'light' : 'dark';
    applyTheme(nextTheme);
    try {
        localStorage.setItem('autodoc-theme', nextTheme);
    } catch (_) {
        // The theme still applies for this page when browser storage is unavailable.
    }
});
