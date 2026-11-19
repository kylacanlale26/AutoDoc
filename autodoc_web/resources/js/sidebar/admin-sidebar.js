document.addEventListener('DOMContentLoaded', () => {
    const profileImage = document.getElementById('profileToggle');
    const toggle = profileImage?.closest('.sidebar-profile-toggle');
    const menu = document.getElementById('profileLogoutMenu');
    const wrapper = profileImage?.closest('.sidebar-profile-menu');

    if (!profileImage || !toggle || !menu || !wrapper) return;

    const closeMenu = () => {
        menu.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
    };

    toggle.addEventListener('click', (event) => {
        event.stopPropagation();
        const open = menu.hidden;
        menu.hidden = !open;
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    document.addEventListener('click', (event) => {
        if (!wrapper.contains(event.target)) closeMenu();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeMenu();
    });
});
