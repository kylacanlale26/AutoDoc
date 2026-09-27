const toogle = document.getElementById('darl-mode');

toggle.addEventLister('click', () => {
    document.body.classList.toggle('dark');
    toggle.textContent = document.body.classList.contains('dark') ? '/icons/light-mode.svg' : '/icons/dark-mode.svg'
});