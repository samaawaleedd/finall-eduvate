<?php
include '../connection.php';

$selectGrade="SELECT * FROM `grades`";
$runGrades=mysqli_query($connect, $selectGrade);

$selectClass="SELECT * FROM `class`";
$runClass=mysqli_query($connect, $selectClass);

$selectBus="SELECT * FROM `Bus`";
$runBus=mysqli_query($connect, $selectBus);

$error_msg = '';
$success_msg = '';

if(isset($_POST['addstudent'])){
    $name=$_POST['name'];
    $email=$_POST['email'];
    $pass=$_POST['pass'];
    $confirmPass=$_POST['confirmPass'];
    $address=$_POST['address'];
    $phone=$_POST['phone'];
    $busSub=$_POST['bussub'];

    $lowercase=preg_match('@[a-z]@',$pass);
    $uppercase=preg_match('@[A-Z]@',$pass);
    $number=preg_match('@[0-9]@',$pass);
    $specialcharacter=preg_match('@[^\w]@',$pass);

    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

    $select="SELECT * FROM `students` WHERE `StudentEmail`='$email'";
    $runselect=mysqli_query($connect , $select);
    $rows= mysqli_num_rows($runselect);

    if(empty($name)){
        $error_msg = "Name is required";
    }elseif(empty($email)){
        $error_msg = "Email is required";
    }elseif($rows>0){
        $error_msg = "Email is already taken";
    }elseif(empty($hashed_password)){
        $error_msg = "Password is required";
    }elseif($pass!=$confirmPass){
        $error_msg = "Password doesn't match confirm password";
    }elseif($lowercase <1 || $uppercase<1 || $number<1 || $specialcharacter<1 ){
        $error_msg = "weak password , must contain lowercase , uppercase , numbers and specialcharacter";
    }elseif(strlen($phone)!=11){
        $error_msg = "Phone Number Invalid";
    }elseif(empty($_POST['class']) || empty($_POST['grade'])){
        $error_msg = "please choose a value";
    }else{
        $grade=$_POST['grade'];
        $class=$_POST['class'];
        $busNumber=$_POST['bus'];

        if($busSub==0){
            $insert="INSERT INTO `students` VALUES (NULL, '$name' , '$email' , '$hashed_password', '$address', '$phone', 0, NULL, '$grade', '$class', 'studentpp.png')";
            $run_insert=mysqli_query($connect, $insert);
            $success_msg = "Student $name Added Successfully";
        }else{
            $insert="INSERT INTO `students` VALUES (NULL, '$name' , '$email' , '$hashed_password', '$address', '$phone', 1, '$busNumber', '$grade', '$class', 'studentpp.png')";
            $run_insert=mysqli_query($connect, $insert);
            $success_msg = "Student $name Added Successfully";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
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
            <li  class="active">
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
    <!-- Main Content -->
    <main class="main-content">
        <!-- Form Container -->
        <div class="page-content">
            <div class="container">
                <div class="form-section">
                    <h3>Add New Student</h3>
                    <?php if($error_msg != ''): ?>
                        <div class="alert alert-danger">
                            <?php echo $error_msg; ?>
                        </div>
                    <?php endif; ?>
                    <?php if($success_msg != ''): ?>
                        <div class="alert alert-success">
                            <?php echo $success_msg; ?>
                        </div>
                    <?php endif; ?>
                    <form method="post" id="studentForm">
                        <div class="form-group">
                            <label for="name">Student Name</label>
                            <input type="text" id="name" name="name" required placeholder="Enter full name">
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required placeholder="Enter email address">
                        </div>

                        <div class="form-group">
                            <label for="pass">Password</label>
                            <input type="password" id="pass" name="pass" required placeholder="Create password">
                        </div>

                        <div class="form-group">
                            <label for="confirmPass">Confirm Password</label>
                            <input type="password" id="confirmPass" name="confirmPass" required placeholder="Confirm password">
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" required placeholder="Enter address">
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" required placeholder="Enter phone number" pattern="[0-9]{11}">
                            <small class="hint">Format: 11 digits</small>
                    </div>

                        <div class="form-group">
                            <label for="grade">Grade</label>
                            <select id="grade" name="grade" required>
                                <option value="" selected disabled>Choose Grade</option>
                                <?php foreach($runGrades as $option){?>
                                <option value="<?php echo $option['GradeID'];?>"><?php echo $option['GradeNumber'];?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="class">Class</label>
                            <select id="class" name="class" required>
                                <option value="" selected disabled>Choose Class</option>
                                <?php foreach($runClass as $option){?>
                                <option value="<?php echo $option['ClassID'];?>"><?php echo $option['ClassName'];?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <button type="submit" name="addstudent" class="submit-btn">
                            <i class="fas fa-user-plus"></i> Add Student
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="admin.js"></script>
</body>
</html>
