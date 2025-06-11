document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle functionality
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggles = document.querySelectorAll('.sidebar-toggle');
    
    function toggleSidebar() {
        sidebar.classList.toggle('active');
    }
    
    sidebarToggles.forEach(toggle => {
        toggle.addEventListener('click', toggleSidebar);
    });
    
    // Password validation
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
    
    password.addEventListener('input', validatePassword);
    confirmPassword.addEventListener('input', validatePassword);
    
    // Form submission
    const teacherForm = document.getElementById('teacherForm');
    
    teacherForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validatePassword()) {
            alert('Please make sure your passwords match!');
            return;
        }
        
        // Get form values
        const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            role: document.getElementById('role').value,
            subject: document.getElementById('subject').value,
            password: password.value
        };
        
        // In a real app, you would send this data to a server
        console.log('Form submitted:', formData);
        
        // Show success message
        alert('Teacher added successfully!');
        
        // Reset form
        teacherForm.reset();
        passwordMatch.textContent = '';
    });
    
    // Close sidebar when clicking on menu items (mobile)
    const menuItems = document.querySelectorAll('.sidebar-menu a');
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                toggleSidebar();
            }
        });
    });
});
