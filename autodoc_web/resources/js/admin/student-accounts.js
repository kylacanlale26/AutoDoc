document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('studentAccountSearch');
    const rows = document.querySelectorAll('.student-account-item[data-student-search]');
    const emptyMessage = document.getElementById('studentAccountSearchEmpty');
    const courseFilter = document.getElementById('accountCourseFilter');
    const filterToggle = document.querySelector('[data-filter-toggle]');
    const filterPanel = document.getElementById('accountCourseFilterPanel');

    function filterStudentAccounts() {
        const query = searchInput?.value.trim().toLowerCase() ?? '';
        let visibleCount = 0;

        rows.forEach((row) => {
            const details = row.dataset.studentSearch?.toLowerCase() ?? '';
            const selectedCourse = courseFilter?.value.toLowerCase() ?? '';
            const courseMatches = !selectedCourse || row.dataset.course?.toLowerCase() === selectedCourse;
            const visible = details.includes(query) && courseMatches;

            row.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });

        if (emptyMessage) {
            emptyMessage.style.display = rows.length > 0 && visibleCount === 0 ? 'flex' : 'none';
        }
    }

    searchInput?.addEventListener('input', filterStudentAccounts);
    courseFilter?.addEventListener('change', filterStudentAccounts);
    filterToggle?.addEventListener('click', () => {
        const expanded = filterToggle.getAttribute('aria-expanded') === 'true';
        filterToggle.setAttribute('aria-expanded', String(!expanded));
        if (filterPanel) filterPanel.hidden = expanded;
    });
});
