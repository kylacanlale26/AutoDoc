const toggle = document.getElementById('dark-mode');
const themeIcon = document.getElementById('themeIcon');
const brandLogo = document.getElementById('brandLogo');

const savedTheme = localStorage.getItem('theme');

if (savedTheme === 'dark') {
    document.body.classList.add('dark');

    themeIcon.src = '/icons/autodoc-logo.svg';
    brandLogo.src = '/icons/autodoc-logo-darkmode.svg';
}

// Toggle dark mode
toggle.addEventListener('click', () => {

    document.body.classList.toggle('dark');

    const isDark = document.body.classList.contains('dark');

    themeIcon.src = isDark
        ? '/icons/light-mode.svg'
        : '/icons/dark-mode.svg';

    brandLogo.src = isDark
        ? '/icons/autodoc-logo-darkmode.svg'
        : '/icons/autodoc-logo.svg';

    localStorage.setItem('theme', isDark ? 'dark' : 'light');
});