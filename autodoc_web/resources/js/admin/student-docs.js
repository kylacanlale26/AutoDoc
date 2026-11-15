document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('adminStudentDocumentSearch');
    const filterButtons = document.querySelectorAll('[data-status-filter]');
    const rows = document.querySelectorAll('.student-document-item[data-status]');
    const emptyMessage = document.getElementById('adminStudentDocumentFilterEmpty');
    let activeFilter = 'all';

    function applyFilters() {
        const query = searchInput?.value.trim().toLowerCase() ?? '';
        let visibleCount = 0;

        rows.forEach((row) => {
            const statusMatches = activeFilter === 'all' || row.dataset.status === activeFilter;
            const documentName = row.querySelector('.student-document-details h2')
                ?.textContent.trim().toLowerCase() ?? '';
            const visible = statusMatches && documentName.includes(query);

            row.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });

        if (emptyMessage) {
            emptyMessage.style.display = rows.length > 0 && visibleCount === 0 ? 'flex' : 'none';
        }
    }

    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            activeFilter = button.dataset.statusFilter ?? 'all';

            filterButtons.forEach((item) => {
                const active = item === button;
                item.classList.toggle('active', active);
                item.setAttribute('aria-pressed', active ? 'true' : 'false');
            });

            applyFilters();
        });
    });

    searchInput?.addEventListener('input', applyFilters);

    const closeMenus = (except = null) => {
        document.querySelectorAll('.document-actions-menu').forEach((menu) => {
            if (menu === except) return;
            menu.hidden = true;
            menu.parentElement?.querySelector('.document-more-button')
                ?.setAttribute('aria-expanded', 'false');
        });
    };

    document.addEventListener('click', (event) => {
        const button = event.target.closest('.document-more-button');

        if (button) {
            const menu = button.parentElement?.querySelector('.document-actions-menu');
            if (!menu) return;

            const shouldOpen = menu.hidden;
            closeMenus(menu);
            menu.hidden = !shouldOpen;
            button.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
            return;
        }

        if (!event.target.closest('.document-actions')) closeMenus();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeMenus();
    });
});
