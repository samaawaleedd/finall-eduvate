<?php
include '../connection.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['AdminID'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: AllAdmins.php");
    exit();
}

$adminId = mysqli_real_escape_string($connect, $_GET['id']);
$currentAdminId = $_SESSION['AdminID'];

// Fetch current admin's role
$currentAdminQuery = "SELECT RoleId FROM admins WHERE AdminID = '$currentAdminId'";
$currentAdminResult = mysqli_query($connect, $currentAdminQuery);
$currentAdmin = mysqli_fetch_assoc($currentAdminResult);
$isAdministrator = ($currentAdmin['RoleId'] == 1);

// Fetch admin information
$query = "SELECT a.*, r.RoleTitle 
          FROM admins a 
          LEFT JOIN roles r ON a.RoleId = r.RoleID 
          WHERE a.AdminID = '$adminId'";
$result = mysqli_query($connect, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: AllAdmins.php");
    exit();
}

$admin = mysqli_fetch_assoc($result);

// Handle form submission for updating admin information
if (isset($_POST['update'])) {
    // Only allow administrators to update profiles
    if (!$isAdministrator) {
        $error = "You don't have permission to update admin profiles.";
    } else {
        $name = mysqli_real_escape_string($connect, $_POST['name']);
        $email = mysqli_real_escape_string($connect, $_POST['email']);
        $phone = mysqli_real_escape_string($connect, $_POST['phone']);
        $role = mysqli_real_escape_string($connect, $_POST['role']);
        
        // Update query
        $update = "UPDATE admins SET 
                   AdminName = '$name',
                   AdminEmail = '$email',
                   AdminNumber = '$phone',
                   RoleId = '$role'
                   WHERE AdminID = '$adminId'";
                   
        if (mysqli_query($connect, $update)) {
            $success = "Admin information updated successfully!";
            // Refresh admin data
            $result = mysqli_query($connect, $query);
            $admin = mysqli_fetch_assoc($result);
        } else {
            $error = "Error updating admin information: " . mysqli_error($connect);
        }
    }
}

// Fetch roles for dropdown (only if administrator)
if ($isAdministrator) {
    $rolesQuery = "SELECT * FROM roles WHERE RoleID IN (1, 2)"; // Only Administrator and Follow Up roles
    $roles = mysqli_query($connect, $rolesQuery);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - <?php echo htmlspecialchars($admin['AdminName']); ?></title>
    <link rel="stylesheet" href="css/AdminProfile.css">
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="profile-header">
            <h1><?php echo htmlspecialchars($admin['AdminName']); ?>'s Profile</h1>
            <div class="role-badge">
                <?php echo htmlspecialchars($admin['RoleTitle']); ?>
            </div>
        </div>

        <div class="edit-section">
            <?php if (isset($success)): ?>
                <div class="message success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="message error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="profile-section">
                    <div class="info-group">
                        <label for="name">
                            <i class="fas fa-user"></i>
                            Full Name
                        </label>
                        <input type="text" id="name" name="name" 
                               value="<?php echo htmlspecialchars($admin['AdminName']); ?>"
                               <?php echo !$isAdministrator ? 'readonly' : ''; ?> 
                               required>
                    </div>

                    <div class="info-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i>
                            Email Address
                        </label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($admin['AdminEmail']); ?>"
                               <?php echo !$isAdministrator ? 'readonly' : ''; ?> 
                               required>
                    </div>

                    <div class="info-group">
                        <label for="phone">
                            <i class="fas fa-phone"></i>
                            Phone Number
                        </label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($admin['AdminNumber']); ?>"
                               <?php echo !$isAdministrator ? 'readonly' : ''; ?> 
                               required>
                    </div>

                    <div class="info-group">
                        <label for="role">
                            <i class="fas fa-user-shield"></i>
                            Role
                        </label>
                        <?php if ($isAdministrator): ?>
                            <select name="role" id="role" required>
                                <?php while($role = mysqli_fetch_assoc($roles)): ?>
                                    <option value="<?php echo $role['RoleID']; ?>" 
                                            <?php echo ($role['RoleID'] == $admin['RoleId']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($role['RoleTitle']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        <?php else: ?>
                            <input type="text" value="<?php echo htmlspecialchars($admin['RoleTitle']); ?>" readonly>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="buttons">
                    <a href="AllAdmins.php" class="btn back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back to All Admins
                    </a>
                    <?php if ($isAdministrator): ?>
                        <button type="submit" name="update" class="btn">
                            <i class="fas fa-save"></i>
                            Update Profile
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 
