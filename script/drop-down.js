const dropdown = document.getElementById('dropdown');
const dropdownMenu = document.getElementById('dropdown-menu');
const arrow = document.getElementById('arrow');

dropdown.addEventListener('click', () => {
    const isMenuVisible = dropdownMenu.style.display === 'block';
    dropdownMenu.style.display = isMenuVisible ? 'none' : 'block';
    arrow.style.transform = isMenuVisible ? 'rotate(90deg)' : 'rotate(270deg)';
});

document.addEventListener('click', (event) => {
    if (!dropdown.contains(event.target)) {
        dropdownMenu.style.display = 'none';
        arrow.style.transform = 'rotate(90deg)';
    }
});