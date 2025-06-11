 let validOTP = "1234"; // Example of a valid OTP sent to the user's email

    function moveToNext(currentInput, nextInputId) {
        // Move to the next input if the current input is filled
        if (currentInput.value.length >= 1) {
            if (nextInputId) {
                document.getElementById(nextInputId).focus(); // Focus on the next input
            }
        }
    }

    function isNumber(event) {
        // Allow only numbers (0-9)
        const char = String.fromCharCode(event.which);
        if (!/[0-9]/.test(char)) {
            event.preventDefault(); // Prevent input if it's not a number
        }
    }

    function validateOTP(event) {
        event.preventDefault(); // Prevent form submission
        let otp = '';
        for (let i = 1; i <= 4; i++) {
            otp += document.getElementById('otp' + i).value; // Collect OTP from inputs
        }

        // Validate the OTP
        if (otp === validOTP) {
            alert("OTP is valid! Redirecting to change password page."); // Alert for valid OTP
            window.location.href = "changepass.html"; // Redirect to the change password page
        } else {
            document.getElementById('otpMessage').innerText = "Invalid OTP. Please try again."; // Show invalid message
        }
    }

    function resendOTP() {
        // Generate a new OTP (for demonstration, we will just change it to a random 4-digit number)
        validOTP = Math.floor(1000 + Math.random() * 9000).toString(); 
    }


    function resendOTP() {
        // Clear the existing OTP inputs
        document.getElementById('otp1').value = '';
        document.getElementById('otp2').value = '';
        document.getElementById('otp3').value = '';
        document.getElementById('otp4').value = '';
        document.getElementById('otp5').value = '';
    
        // Optionally, display a message or call a function to send a new OTP
        document.getElementById('otpMessage').innerText = 'A new OTP has been sent to your registered mobile number or email.';
        
        // Here you can add the logic to actually send the new OTP
        // sendNewOTP(); // Uncomment and implement this function as needed
    }