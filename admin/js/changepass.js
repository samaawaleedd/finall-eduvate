document.getElementById('changePasswordForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (newPassword === confirmPassword) {
        window.location.href = "login.html";
        // Here you can add code to handle the password change logic (e.g., API call).
    } else {
        alert('Passwords do not match. Please try again.');
    }
});