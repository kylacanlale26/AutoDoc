document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('documentSearch');
    const filterButtons = document.querySelectorAll('[data-status-filter]');
    const rows = document.querySelectorAll('.document-item[data-status]');
    const emptyMessage = document.getElementById('filterEmpty');
    const filterToggle = document.querySelector('[data-filter-toggle]');
    const filterPanel = document.getElementById('studentDocumentFilters');
    let activeFilter = 'all';

    function applyFilters() {
        const query = searchInput?.value.trim().toLowerCase() ?? '';
        let visibleCount = 0;

        rows.forEach((row) => {
            const statusMatches =
                activeFilter === 'all' || row.dataset.status === activeFilter;
            const documentName = row.querySelector('.document-info h3')
                ?.textContent.trim().toLowerCase() ?? '';
            const visible = statusMatches && documentName.includes(query);

            row.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });

        if (emptyMessage) {
            emptyMessage.style.display =
                rows.length > 0 && visibleCount === 0 ? 'flex' : 'none';
        }
    }

    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            activeFilter = button.dataset.statusFilter;

            filterButtons.forEach((item) => {
                const active = item === button;
                item.classList.toggle('active', active);
                item.setAttribute('aria-pressed', active ? 'true' : 'false');
            });

            applyFilters();
        });
    });

    searchInput?.addEventListener('input', applyFilters);

    filterToggle?.addEventListener('click', () => {
        const expanded = filterToggle.getAttribute('aria-expanded') === 'true';
        filterToggle.setAttribute('aria-expanded', String(!expanded));
        if (filterPanel) filterPanel.hidden = expanded;
    });

    const closeMenus = (except = null) => {
        document.querySelectorAll('.document-actions-menu').forEach((menu) => {
            if (menu === except) return;
            menu.hidden = true;
            menu.parentElement?.querySelector('.submission-more-button')
                ?.setAttribute('aria-expanded', 'false');
        });
    };

    document.addEventListener('click', (event) => {
        const button = event.target.closest('.submission-more-button');
        if (button) {
            const menu = button.parentElement.querySelector('.document-actions-menu');
            const shouldOpen = menu.hidden;
            closeMenus(menu);
            menu.hidden = !shouldOpen;
            button.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
            return;
        }

        const row = event.target.closest('.document-item[data-document-url]');
        if (row && !event.target.closest('.document-actions, a, button')) {
            window.location.assign(row.dataset.documentUrl);
            return;
        }

        if (!event.target.closest('.document-actions')) {
            closeMenus();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeMenus();

        const row = event.target.closest('.document-item[data-document-url]');
        if (row && event.target === row && (event.key === 'Enter' || event.key === ' ')) {
            event.preventDefault();
            window.location.assign(row.dataset.documentUrl);
        }
    });
});
