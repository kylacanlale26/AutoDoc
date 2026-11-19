document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('studentSearch');
    const rows = document.querySelectorAll('[data-student-row]');
    const emptyMessage = document.getElementById('studentSearchEmpty');
    const courseFilter = document.getElementById('studentCourseFilter');
    const filterToggle = document.querySelector('[data-filter-toggle]');
    const filterPanel = document.getElementById('studentCourseFilterPanel');

    function filterStudents() {
        const query = searchInput?.value.trim().toLowerCase() ?? '';
        let visibleCount = 0;

        rows.forEach((row) => {
            const searchText = row.dataset.search?.toLowerCase() ?? '';
            const selectedCourse = courseFilter?.value.toLowerCase() ?? '';
            const courseMatches = !selectedCourse || row.dataset.course?.toLowerCase() === selectedCourse;
            const visible = searchText.includes(query) && courseMatches;

            row.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });

        if (emptyMessage) {
            emptyMessage.style.display = rows.length > 0 && visibleCount === 0 ? 'flex' : 'none';
        }
    }

    searchInput?.addEventListener('input', filterStudents);
    courseFilter?.addEventListener('change', filterStudents);
    filterToggle?.addEventListener('click', () => {
        const expanded = filterToggle.getAttribute('aria-expanded') === 'true';
        filterToggle.setAttribute('aria-expanded', String(!expanded));
        if (filterPanel) filterPanel.hidden = expanded;
    });
});
