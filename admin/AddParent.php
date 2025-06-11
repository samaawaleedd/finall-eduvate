<?php
include '../connection.php';

if(isset($_POST['addparent'])){
    $name=$_POST['name'];
    $email=$_POST['email'];
    $pass=$_POST['pass'];
    $confirmPass=$_POST['confirmPass'];
    $phone=$_POST['phone'];

    $lowercase=preg_match('@[a-z]@',$pass);
    $uppercase=preg_match('@[A-Z]@',$pass);
    $number=preg_match('@[0-9]@',$pass);
    $specialcharacter=preg_match('@[^\w]@',$pass);

    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

    $select="SELECT * FROM `parents` WHERE `ParentEmail`='$email'";
    $runselect=mysqli_query($connect , $select);
    $rows= mysqli_num_rows($runselect);

    $error = '';

    if(empty($name)){
        $error = "Name is required";
    }elseif(empty($email)){
        $error = "Email is required";
    }elseif($rows>0){
        $error = "Email is already taken";
    }elseif(empty($pass)){
        $error = "Password is required";
    }elseif($pass!=$confirmPass){
        $error = "Password doesn't match confirm password";
    }elseif($lowercase <1 || $uppercase<1 || $number<1 || $specialcharacter<1 ){
        $error = "Weak password, must contain lowercase, uppercase, numbers and special character";
    }elseif(strlen($phone)!=11){
        $error = "Phone Number Invalid";
    }else{
        // Store the validated data in session
        $_SESSION['parent_data'] = [
            'name' => $name,
            'email' => $email,
            'password' => $hashed_password,
            'phone' => $phone
        ];
        
        // Redirect to assign children page
        header("Location: AssignChildren.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Parents</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="CSS/parent.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="../fontawesome-free-6.4.0-web/css/all.min.css">

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
        <!-- Page Content -->
        <div class="page-content">
            <div class="form-container">
                <h3><i class="fas fa-user-plus"></i> Add Parent - Step 1</h3>
                <?php if(isset($error) && !empty($error)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" id="parentForm">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> Parent Name</label>
                        <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" placeholder="Enter parent's full name">
                    </div>

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" placeholder="Enter parent's email">
                    </div>

                    <div class="form-group">
                        <label for="pass"><i class="fas fa-lock"></i> Password</label>
                        <input type="password" id="pass" name="pass" required placeholder="Enter password">
                    </div>

                    <div class="form-group">
                        <label for="confirmPass"><i class="fas fa-lock"></i> Confirm Password</label>
                        <input type="password" id="confirmPass" name="confirmPass" required placeholder="Confirm password">
                    </div>

                    <div class="form-group">
                        <label for="Phone"><i class="fas fa-phone"></i> Phone Number</label>
                        <input type="number" id="Phone" name="phone" required value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" placeholder="Enter phone number">
                    </div>

                    <button type="submit" name="addparent" class="submit-btn">
                        <i class="fas fa-arrow-right"></i> Continue to Add Children
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script src="js/parent.js"></script>
</body>
</html>
