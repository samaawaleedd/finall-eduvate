// Mobile sidebar toggle
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggles = document.querySelectorAll('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');

    sidebarToggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    });
}); 
