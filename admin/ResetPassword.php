<?php
include '../connection.php';

// Check if user is logged in
if (!isset($_SESSION['AdminID'])) {
    header("Location: login.php");
    exit();
}

$adminId = $_SESSION['AdminID'];

// Handle form submission
if (isset($_POST['reset'])) {
    $currentPassword = mysqli_real_escape_string($connect, $_POST['current_password']);
    $newPassword = mysqli_real_escape_string($connect, $_POST['new_password']);
    $confirmPassword = mysqli_real_escape_string($connect, $_POST['confirm_password']);
    
    // Verify current password
    $query = "SELECT AdminPass FROM admins WHERE AdminID = '$adminId'";
    $result = mysqli_query($connect, $query);
    $admin = mysqli_fetch_assoc($result);
    
    if (password_verify($currentPassword, $admin['AdminPass'])) {
        if ($newPassword === $confirmPassword) {
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update password
            $update = "UPDATE admins SET AdminPass = '$hashedPassword' WHERE AdminID = '$adminId'";
            if (mysqli_query($connect, $update)) {
                $success = "Password updated successfully!";
            } else {
                $error = "Error updating password: " . mysqli_error($connect);
            }
        } else {
            $error = "New passwords do not match!";
        }
    } else {
        $error = "Current password is incorrect!";
    }
}

// Fetch admin information for display
$query = "SELECT a.*, r.RoleTitle 
          FROM admins a 
          LEFT JOIN roles r ON a.RoleId = r.RoleID 
          WHERE a.AdminID = '$adminId'";
$result = mysqli_query($connect, $query);
$admin = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="profile-header">
            <h1>Reset Password</h1>
            <div class="role-badge">
                <?php echo htmlspecialchars($admin['RoleTitle']); ?>
            </div>
        </div>

        <?php if (isset($success)): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="profile-section">
            <form method="post" class="edit-form">
                <div class="info-group">
                    <label for="current_password">
                        <i class="fas fa-lock"></i> Current Password
                    </label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="info-group">
                    <label for="new_password">
                        <i class="fas fa-key"></i> New Password
                    </label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>

                <div class="info-group">
                    <label for="confirm_password">
                        <i class="fas fa-check-circle"></i> Confirm New Password
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <div class="action-buttons">
                    <button type="submit" name="reset" class="btn save-btn">
                        <i class="fas fa-save"></i> Update Password
                    </button>
                    <a href="MyProfile.php" class="btn back-btn">
                        <i class="fas fa-arrow-left"></i> Back to Profile
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 
