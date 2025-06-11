document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle functionality
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggles = document.querySelectorAll('.sidebar-toggle');
    const mainContent = document.querySelector('.main-content');
    
    // Toggle sidebar on mobile
    function toggleSidebar() {
        sidebar.classList.toggle('active');
    }
    
    // Add event listeners to all toggle buttons
    sidebarToggles.forEach(toggle => {
        toggle.addEventListener('click', toggleSidebar);
    });
    
    // Password match validation
    const password = document.getElementById('pass');
    const confirmPassword = document.getElementById('confirmPass');
    const passwordMatch = document.getElementById('passwordMatch');
    
    function validatePassword() {
        if (password.value !== confirmPassword.value) {
            passwordMatch.textContent = "Passwords don't match";
            passwordMatch.style.color = 'red';
            return false;
        } else {
            passwordMatch.textContent = "Passwords match";
            passwordMatch.style.color = 'green';
            return true;
        }
    }
    
    password.addEventListener('change', validatePassword);
    confirmPassword.addEventListener('keyup', validatePassword);
    
    // Form submission
    const adminForm = document.getElementById('adminForm');
    
    adminForm.addEventListener('submit', function(e) {
        if (!validatePassword()) {
            e.preventDefault();
            alert('Please make sure your passwords match!');
            return;
        }
        
        // Additional validation can be added here
        console.log('Form submitted successfully');
    });
    
    // Auto-close sidebar when clicking on menu items (mobile only)
    const menuItems = document.querySelectorAll('.sidebar-menu a');
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                toggleSidebar();
            }
        });
    });
});
