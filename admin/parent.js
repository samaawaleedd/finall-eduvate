document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle functionality
    const sidebarToggles = document.querySelectorAll('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    sidebarToggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('sidebar-hidden');
        });
    });

    // Password validation for parent form
    const parentForm = document.getElementById('parentForm');
    if (parentForm) {
        const passwordInput = document.getElementById('pass');
        const confirmPasswordInput = document.getElementById('confirmPass');
        const phoneInput = document.getElementById('Phone');

        parentForm.addEventListener('submit', function(e) {
            let isValid = true;
            let message = '';

        });

        // Real-time password match validation
        confirmPasswordInput.addEventListener('input', function() {
            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.setCustomValidity('Passwords do not match');
            } else {
                confirmPasswordInput.setCustomValidity('');
            }
        });
    }

    // Children form validation
    const childrenForm = document.getElementById('childrenForm');
    if (childrenForm) {
        childrenForm.addEventListener('submit', function(e) {
            const emailInputs = document.querySelectorAll('input[type="email"]');
            let hasValue = false;

            emailInputs.forEach(input => {
                if (input.value.trim() !== '') {
                    hasValue = true;
                }
            });

            if (!hasValue) {
                const proceed = confirm('No children will be assigned. Do you want to continue?');
                if (!proceed) {
                    e.preventDefault();
                }
            }
        });
    }
}); 
