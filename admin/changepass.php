<?php
include '../connection.php';

$error="";
$err=FALSE;
if(isset($_POST['submit'])){
     $email=$_SESSION['email'];
     $password=$_POST['pass'];
     $confirm_password=$_POST['confirm_password'];

     $hashed=password_hash($password,PASSWORD_DEFAULT);

     $lowercase=preg_match('@[a-z]@',$password);
     $uppercase=preg_match('@[A-Z]@',$password);
     $numbers=preg_match('@[0-9]@',$password);
     $character=preg_match('@[^\w]@', $password);

     if (strlen($password) < 6) {
        $error = "The password should have at least 6 characters.";
        $err = TRUE;
    }elseif ($password !=$confirm_password){
        $error="password doesn't match confirm password";
        $err = TRUE;
    }elseif ($uppercase<1 ||$lowercase<1 ||$numbers<1 ||$character<1 ){
        $error= "password must contain upeercase, lowercase, number and character ";
        $err = TRUE;
    }else{
     $update="UPDATE `admins` SET `AdminPass`='$hashed' WHERE `AdminEmail`='$email'";
     $ruunupdate=mysqli_query($connect,$update);

     echo "Password Changed Successfully";

     unset($_SESSION['otp']);
     unset($_SESSION['email']);
     header("location:login.php");
}}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change password forget</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./cssf/all.min.css">
    <link rel="stylesheet" href="./css/changepass.css">
    <script src="./js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card" id="passwordChangeCard">
            <div class="d-flex justify-content-center mb-2">
                <img src="./imgs/pass.png" alt="Key Icon" class="key-icon">
            </div> 
            <h2 class="text-center fw-bold">Forget your password</h2>
            <p class="text-center mt-2 mb-4">Enter a new password below to change your password</p>

            <form method="POST">
                <div class="form-group mb-3">
                    <label for="newPassword">New password</label>
                    <input name="pass" type="password" class="form-control" id="newPassword" required>
                </div>
                <div class="form-group mb-4">
                    <label for="confirmPassword">Confirm password</label>
                    <input type="password" name="confirm_password" class="form-control" id="confirmPassword" required>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="submit" name="submit" class="btn rounded-4 mt-3">Change Password</button>
                </div>
            </form>

            <div class="error">
                <?php if ($err){ ?>
                    <?php echo $error; } ?>
            </div>
        </div>
    </div>

    <script src="./js/changepass.js"></script>
</body>
</html> 
