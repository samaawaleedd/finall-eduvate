<?php
include '../connection.php';

// Check if we're adding children to an existing parent or creating a new parent
$isExistingParent = isset($_SESSION['existing_parent']);
$isNewParent = isset($_SESSION['parent_data']);

if (!$isExistingParent && !$isNewParent) {
    header("Location: AddParent.php");
    exit();
}

// Get parent information based on the context
if ($isExistingParent) {
    $parent = $_SESSION['existing_parent'];
    $parent_id = $parent['id'];
} else {
    $parent_data = $_SESSION['parent_data'];
}

// Function to insert new parent and return the ID
function insertParent($connect, $parent_data) {
    $insert = "INSERT INTO `parents` VALUES (NULL, '{$parent_data['name']}', '{$parent_data['email']}', '{$parent_data['password']}', '{$parent_data['phone']}', 0)";
    if(mysqli_query($connect, $insert)) {
        return mysqli_insert_id($connect);
    }
    return false;
}

$success = true;
$message = "";

// Handle skip action (only for new parents)
if(isset($_GET['skip']) && !$isExistingParent) {
    $parent_id = insertParent($connect, $parent_data);
    if($parent_id) {
        unset($_SESSION['parent_data']);
        header("Location: dashboard.php");
        exit();
    } else {
        $success = false;
        $message = "Error creating parent account";
    }
}

// Handle submit action
if(isset($_POST['submit'])) {
    // For new parents, insert the parent first
    if (!$isExistingParent) {
        $parent_id = insertParent($connect, $parent_data);
        if(!$parent_id) {
            $success = false;
            $message = "Error creating parent account";
        }
    }

    if($success) {
        // If there are child emails submitted
        if(isset($_POST['child_emails']) && is_array($_POST['child_emails'])) {
            foreach($_POST['child_emails'] as $child_email) {
                if(!empty($child_email)) {
                    // Check if child exists
                    $check_child = "SELECT * FROM `students` WHERE `StudentEmail`='$child_email'";
                    $child_result = mysqli_query($connect, $check_child);
                    
                    if(mysqli_num_rows($child_result) > 0) {
                        $child = mysqli_fetch_assoc($child_result);
                        
                        // Check if this parent-child pair already exists
                        $check_assignment = "SELECT * FROM `family` WHERE `StudentID`='{$child['StudentID']}' AND `ParentID`='$parent_id'";
                        $assignment_result = mysqli_query($connect, $check_assignment);
                        
                        if(mysqli_num_rows($assignment_result) == 0) {
                            // Assign child to parent
                            $insert = "INSERT INTO `family` (`ParentID`, `StudentID`) VALUES ('$parent_id', '{$child['StudentID']}')";
                            if(!mysqli_query($connect, $insert)) {
                                $success = false;
                                $message .= "Error assigning child {$child_email}. ";
                            }
                        } else {
                            $success = false;
                            $message .= "Child {$child_email} is already assigned to this parent. ";
                        }
                    } else {
                        $success = false;
                        $message .= "Child with email {$child_email} not found. ";
                    }
                }
            }
        }
        
        if($success) {
            if ($isExistingParent) {
                $message = "Children assigned successfully!";
                unset($_SESSION['existing_parent']);
                header("refresh:2;url=ParentProfile.php?id=" . $parent_id);
            } else {
                $message = "Parent account created and children assigned successfully!";
                unset($_SESSION['parent_data']);
                header("refresh:2;url=dashboard.php");
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Children</title>
    <link rel="stylesheet" href="../fontawesome-free-6.4.0-web/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="CSS/parent.css">
   
    <script src="js/sidebar.js" defer></script>
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
            <li>
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
            <li class="active">
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

    <!-- Main Content Area -->
    <main class="main-content">
        <!-- Top Navigation Bar -->
        <header class="top-nav">
            <button class="sidebar-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="nav-right">
                <a href="MyProfile.php" class="nav-profile">
                    <i class="fas fa-user-circle"></i>
                    <span>Profile</span>
                </a>
                <a href="logout.php" class="nav-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </header>

        <!-- Page Content -->
        <div class="page-content">
            <div class="form-container">
                <h3>
                    <i class="fas fa-child"></i> 
                    <?php echo $isExistingParent ? "Assign Children to Existing Parent" : "Add Children - Step 2"; ?>
                </h3>

                <?php if (!empty($message)): ?>
                    <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="post" id="assignChildrenForm">
                    <div id="childrenContainer">
                        <div class="form-group child-input-container">
                            <label>
                                <i class="fas fa-envelope"></i> Child's Email
                            </label>
                            <div class="input-group">
                                <input type="email" name="child_emails[]" class="form-control" placeholder="Enter child's email address" required>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="add-child-btn" onclick="addChildInput()">
                        <i class="fas fa-plus"></i> Add Another Child
                    </button>

                    <div class="button-group">
                        <?php if (!$isExistingParent): ?>
                            <a href="?skip=1" class="skip-btn">
                                <i class="fas fa-forward"></i> Skip
                            </a>
                        <?php endif; ?>
                        <button type="submit" name="submit" class="submit-btn">
                            <i class="fas fa-check"></i> 
                            <?php echo $isExistingParent ? "Assign Children" : "Complete Registration"; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="js/parent.js"></script>
    <script>
        function addChildInput() {
            const container = document.getElementById('childrenContainer');
            const newInput = document.createElement('div');
            newInput.className = 'form-group child-input-container';
            newInput.innerHTML = `
                <label><i class="fas fa-envelope"></i> Child's Email</label>
                <div class="input-group">
                    <input type="email" name="child_emails[]" class="form-control" placeholder="Enter child's email address" required>
                    <button type="button" class="remove-child-btn" onclick="removeChildInput(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            container.appendChild(newInput);
        }

        function removeChildInput(button) {
            button.closest('.child-input-container').remove();
        }
    </script>
</body>
</html>
