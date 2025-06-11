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
    const studentForm = document.getElementById('studentForm');
    
    studentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validatePassword()) {
            alert('Please make sure your passwords match!');
            return;
        }
        
        // Get form values
        const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            address: document.getElementById('address').value,
            phone: document.getElementById('phone').value,
            bussub: document.querySelector('input[name="bussub"]:checked').value,
            bus: document.getElementById('bus').value,
            grade: document.getElementById('grade').value,
            class: document.getElementById('class').value,
            password: password.value
        };
        
        // In a real app, you would send this data to a server
        console.log('Form submitted:', formData);
        
        // Show success message
        alert('Student added successfully!');
        
        // Reset form
        studentForm.reset();
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
    
    // Toggle bus selection based on radio button
    const busRadioYes = document.getElementById('yes');
    const busSelect = document.getElementById('bus');
    
    busRadioYes.addEventListener('change', function() {
        busSelect.disabled = !this.checked;
    });
    
    document.getElementById('no').addEventListener('change', function() {
        busSelect.disabled = this.checked;
    });
    
    // Initialize bus select state
    busSelect.disabled = !busRadioYes.checked;
});
