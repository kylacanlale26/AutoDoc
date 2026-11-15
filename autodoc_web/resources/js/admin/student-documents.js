document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('studentSearch');
    const rows = document.querySelectorAll('[data-student-row]');
    const emptyMessage = document.getElementById('studentSearchEmpty');

    function filterStudents() {
        const query = searchInput?.value.trim().toLowerCase() ?? '';
        let visibleCount = 0;

        rows.forEach((row) => {
            const searchText = row.dataset.search?.toLowerCase() ?? '';
            const visible = searchText.includes(query);

            row.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });

        if (emptyMessage) {
            emptyMessage.style.display = rows.length > 0 && visibleCount === 0 ? 'flex' : 'none';
        }
    }

    searchInput?.addEventListener('input', filterStudents);
});
