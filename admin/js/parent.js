document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const toggleButtons = document.querySelectorAll('.sidebar-toggle');

    // Handle sidebar toggle
    toggleButtons.forEach(button => {
        button.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });
    });

    // Handle window resize
    function handleResize() {
        if (window.innerWidth <= 768) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        } else {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
        }
    }

    window.addEventListener('resize', handleResize);
    handleResize(); // Initial check

    // Password validation
    const passwordInput = document.getElementById('pass');
    const confirmPasswordInput = document.getElementById('confirmPass');
    const form = document.getElementById('parentForm');

    function validatePassword() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        const hasLowerCase = /[a-z]/.test(password);
        const hasUpperCase = /[A-Z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecialChar = /[^A-Za-z0-9]/.test(password);
        
        let isValid = true;
        
        if (password !== confirmPassword) {
            confirmPasswordInput.setCustomValidity("Passwords don't match");
            isValid = false;
        } else {
            confirmPasswordInput.setCustomValidity('');
        }
        
        if (!hasLowerCase || !hasUpperCase || !hasNumber || !hasSpecialChar) {
            passwordInput.setCustomValidity('Password must contain lowercase, uppercase, numbers and special characters');
            isValid = false;
        } else {
            passwordInput.setCustomValidity('');
        }
        
        return isValid;
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validatePassword()) {
                e.preventDefault();
            }
        });

        passwordInput.addEventListener('input', validatePassword);
        confirmPasswordInput.addEventListener('input', validatePassword);
    }
});
