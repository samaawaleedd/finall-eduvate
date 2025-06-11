function forgotPassword() {
    var email;
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Regular expression for email validation

    // Prompt the user for email until a valid email is entered or they cancel
    while (true) {
        email = prompt("Please enter your email address:");
        if (email === null) {
            // User clicked "Cancel"
            alert("Email entry was cancelled.");
            return; // Exit the function to remain on the sign-in page
        }
        if (emailPattern.test(email)) {
            // Redirect to the OTP page
            window.location.href = "otp.html"; // Change "otp.html" to the actual path of your OTP page
            break; // Exit the loop if a valid email is entered
        } else {
            // Invalid email entered
            alert("Please enter a valid email address.");
        }
    }
}

function forgotPassword() {
    // Show the modal
    var myModal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'));
    myModal.show();
}