document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('submissionSearch');
    const filterButtons = document.querySelectorAll('[data-status-filter]');
    const rows = document.querySelectorAll('.student-submission-item[data-status]');
    const emptyMessage = document.getElementById('submissionFilterEmpty');
    const filterToggle = document.querySelector('[data-filter-toggle]');
    const filterPanel = document.getElementById('submissionFilters');
    let activeFilter = 'all';

    function applyFilters() {
        const query = searchInput?.value.trim().toLowerCase() ?? '';
        let visibleCount = 0;

        rows.forEach((row) => {
            const statusMatches =
                activeFilter === 'all' || row.dataset.status === activeFilter;
            const searchText = row.dataset.search?.toLowerCase() ?? row.textContent.toLowerCase();
            const visible = statusMatches && searchText.includes(query);

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

    filterToggle?.addEventListener('click', () => {
        const expanded = filterToggle.getAttribute('aria-expanded') === 'true';
        filterToggle.setAttribute('aria-expanded', String(!expanded));
        if (filterPanel) filterPanel.hidden = expanded;
    });
});
