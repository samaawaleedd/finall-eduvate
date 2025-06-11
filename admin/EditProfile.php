<?php
include '../connection.php';

// Check if user is logged in
if (!isset($_SESSION['AdminID'])) {
    header("Location: login.php");
    exit();
}

$adminId = $_SESSION['AdminID'];

// Handle form submission
if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $phone = mysqli_real_escape_string($connect, $_POST['phone']);
    
    // Update query
    $update = "UPDATE admins SET 
               AdminName = '$name',
               AdminEmail = '$email',
               AdminNumber = '$phone'
               WHERE AdminID = '$adminId'";
               
    if (mysqli_query($connect, $update)) {
        $success = "Profile updated successfully!";
    } else {
        $error = "Error updating profile: " . mysqli_error($connect);
    }
}

// Fetch admin information
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
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="js/sidebar.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Vertical Sidebar Navigation -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-user-shield"></i> Admin Panel</h3>
            <button class="sidebar-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="Dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="MyProfile.php">
                    <i class="fas fa-user-circle"></i>
                    <span>My Profile</span>
                </a>
            </li>
            
            <!-- Users Management Section -->
            <li class="menu-section">
                <span class="menu-title">Users Management</span>
            </li>
            <li>
                <a href="AllAdmins.php">
                    <i class="fas fa-user-shield"></i>
                    <span>Admins</span>
                </a>
            </li>
            <li >
                <a href="AllTeachers.php">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Teachers</span>
                </a>
            </li>
            <li>
                <a href="AllStudents.php">
                    <i class="fas fa-user-graduate"></i>
                    <span>Students</span>
                </a>
            </li>
            <li>
                <a href="AllParents.php">
                    <i class="fas fa-users"></i>
                    <span>Parents</span>
                </a>
            </li>

            <!-- Class Management Section -->
            <li class="menu-section">
                <span class="menu-title">Class Management</span>
            </li>
            <li>
                <a href="AllClass.php">
                    <i class="fas fa-school"></i>
                    <span>Classes</span>
                </a>
            </li>

            <!-- Settings Section -->
            <li class="menu-section">
                <span class="menu-title">Settings</span>
            </li>
            <li class="active">
                <a href="EditProfile.php">
                    <i class="fas fa-user-edit"></i>
                    <span>Edit Profile</span>
                </a>
            </li>
            <li>
                <a href="ResetPassword.php">
                    <i class="fas fa-key"></i>
                    <span>Reset Password</span>
                </a>
            </li>
            <li>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="main-content">
    <div class="container">
        <div class="profile-header">
            <h1>Edit Profile</h1>
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
                    <label for="name">
                        <i class="fas fa-user"></i> Full Name
                    </label>
                    <input type="text" id="name" name="name" 
                           value="<?php echo htmlspecialchars($admin['AdminName']); ?>" 
                           required>
                </div>

                <div class="info-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($admin['AdminEmail']); ?>" 
                           required>
                </div>

                <div class="info-group">
                    <label for="phone">
                        <i class="fas fa-phone"></i> Phone Number
                    </label>
                    <input type="tel" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($admin['AdminNumber']); ?>" 
                           required>
                </div>

                <div class="info-group">
                    <label>
                        <i class="fas fa-user-shield"></i> Role
                    </label>
                    <input type="text" value="<?php echo htmlspecialchars($admin['RoleTitle']); ?>" 
                           readonly class="readonly-field">
                </div>

                <div class="action-buttons">
                    <button type="submit" name="update" class="btn save-btn">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="MyProfile.php" class="btn back-btn">
                        <i class="fas fa-arrow-left"></i> Back to Profile
                    </a>
                </div>
            </form>
        </div>
    </div>
    </div>
</body>
</html> 
