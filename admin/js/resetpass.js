document.getElementById('changePasswordForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Get the values of the password fields
    var newPassword = document.getElementById('newPassword').value;
    var confirmNewPassword = document.getElementById('confirmnewPassword').value;

    // Check if the new password and confirm password match
    if (newPassword === confirmNewPassword) {
        // Simulate a successful password change
        setTimeout(function() {
            // Show the success message
            document.getElementById('successMessage').style.display = 'block';
        }, 1000); // Simulate a delay for the password change process
    } else {
        alert("Passwords do not match. Please try again."); // Alert if passwords do not match
    }
});