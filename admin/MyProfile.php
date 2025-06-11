<?php
include '../connection.php';

// Check if user is logged in
if (!isset($_SESSION['AdminID'])) {
    header("Location: login.php");
    exit();
}

$adminId = $_SESSION['AdminID'];

// Fetch admin information
$query = "SELECT a.*, r.RoleTitle 
          FROM admins a 
          LEFT JOIN roles r ON a.RoleId = r.RoleID 
          WHERE a.AdminID = '$adminId'";
$result = mysqli_query($connect, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: login.php");
    exit();
}

$admin = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="../fontawesome-free-6.4.0-web/css/all.min.css">

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
            <li class="active">
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
            <li>
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

    <div class="main-content mb-5">
    <div class="container">
        <div class="profile-header">
            <h1>My Profile</h1>
            <div class="role-badge">
                <?php echo htmlspecialchars($admin['RoleTitle']); ?>
            </div>
        </div>

        <div class="profile-section">
            <div class="profile-info">
                <div class="info-group">
                    <label><i class="fas fa-user"></i> Full Name</label>
                    <p><?php echo htmlspecialchars($admin['AdminName']); ?></p>
                </div>

                <div class="info-group">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <p><?php echo htmlspecialchars($admin['AdminEmail']); ?></p>
                </div>

                <div class="info-group">
                    <label><i class="fas fa-phone"></i> Phone Number</label>
                    <p><?php echo htmlspecialchars($admin['AdminNumber']); ?></p>
                </div>

                <div class="info-group">
                    <label><i class="fas fa-user-shield"></i> Role</label>
                    <p><?php echo htmlspecialchars($admin['RoleTitle']); ?></p>
                </div>
            </div>

            <div class="action-buttons">
                <a href="EditProfile.php" class="btn edit-btn">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
                <a href="ResetPassword.php" class="btn password-btn">
                    <i class="fas fa-key"></i> Reset Password
                </a>
                <a href="dashboard.php" class="btn back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
    </div>
</body>
</html> 
