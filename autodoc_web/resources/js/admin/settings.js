document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-edit-type]').forEach((button) => {
        button.addEventListener('click', () => {
            const panel = document.getElementById(button.dataset.editType);
            if (!panel) return;

            panel.hidden = !panel.hidden;
            button.setAttribute('aria-expanded', panel.hidden ? 'false' : 'true');

            if (!panel.hidden) {
                panel.querySelector('input[name="name"]')?.focus();
            }
        });
    });
});
