document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('studentAccountSearch');
    const rows = document.querySelectorAll('.student-account-item[data-student-search]');
    const emptyMessage = document.getElementById('studentAccountSearchEmpty');

    function filterStudentAccounts() {
        const query = searchInput?.value.trim().toLowerCase() ?? '';
        let visibleCount = 0;

        rows.forEach((row) => {
            const details = row.dataset.studentSearch?.toLowerCase() ?? '';
            const visible = details.includes(query);

            row.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });

        if (emptyMessage) {
            emptyMessage.style.display = rows.length > 0 && visibleCount === 0 ? 'flex' : 'none';
        }
    }

    searchInput?.addEventListener('input', filterStudentAccounts);
});
