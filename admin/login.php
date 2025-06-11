<?php
    // include "../connection.php";
    //-----------------------------------start code forgetpass-------------------------
    include '../mail.php';
    $errorf = "";
    $errf = FALSE;

    if(isset($_POST['forgot'])){
        $emaill = $_POST['email'];
        $_SESSION['email'] = $emaill;
        
        $selectF = "SELECT * FROM `admins` WHERE `AdminEmail`='$emaill'";
        $runselectF = mysqli_query($connect, $selectF);

        if(mysqli_num_rows($runselectF) > 0){
            $fetch = mysqli_fetch_assoc($runselectF);
            $rand = rand(10000,100000);
            $msg = "Hello your OTP is $rand";

            $mail->setFrom('notyourbusiness.960@gmail.com', 'Eduvate');
            $mail->addAddress($emaill);
            $mail->isHTML(true);
            $mail->Subject = 'OTP';
            $mail->Body = ($msg);
            $mail->send(); 
            $_SESSION['otp'] = $rand;
            header("location:OTP.php");
        } else {
            $errorf = "Email not found in our records";
            $errf = TRUE;
        }
    }
    //-----------------------------------end code forgetpass-------------------------


    //----------------------------------start code login--------------------------------
    $error=FALSE;
    $empty=FALSE;

    if(isset($_POST['login'])){
        $email=$_POST['email'];
        $pass=$_POST['pass'];

        if(empty($email) || empty($pass)){
            $empty=TRUE;
        }else{
            $select="SELECT * FROM `admins` WHERE `AdminEmail` = '$email'";
            $runSelect=mysqli_query($connect,$select);
            $rows=mysqli_num_rows($runSelect);

            if ($rows>0){
                $fetch=mysqli_fetch_Assoc($runSelect);
                $fetch_em=$fetch['AdminEmail'];
                $fetchPass=$fetch['AdminPass'];


                if(password_verify($pass,$fetchPass)){
                    $AdminID=$fetch['AdminID'];
                    $_SESSION['AdminID']=$AdminID;
                    Header("location:Dashboard.php");  
                }else{
                    $error=TRUE;
                }
            }else{
                $error=TRUE;
            }
        }

    }
    ?> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Sign In</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./cssf/all.min.css">
    <link rel="stylesheet" href="./css/login.css">
    <script src="./js/bootstrap.bundle.min.js"></script>
</head>
    <body >
        <div class=" container-fluid row d-flex  px-3 justify-content-evenly min-vh-100  ">

            <!--lift-->
        <div class=" d-flex justify-center items-center col-md-5 row p-5">
            <div class="logo mb-3 d-flex justify-content-center text-center">
                <img src="../media/logo.png" width="300px"> 
                
                
            </div> <br> 
        <div class="">
            <div class="d-flex justify-content-center">

                    <div class="btn  rounded-5 flex items-center justify-center me-2" style="width: 200px; height: 50px;">
                        <i class="fas fa-user text-xl"></i>
                        <p class="text-xs mt-1">Admin Login</p>
                    </div>
                
            </div>
        </div>
        
            <div class="text-center mt-4">
                <form  method="POST">
                    <div class="mb-4">
                        <input type="email" name="email" id="#" placeholder="Email" class="w-100 px-4 py-2 border  rounded">
                    </div>
                    <div class="mb-4">
                        <input type="password" id="#" name="pass" placeholder="Password" class="w-100 px-4 py-2 border  rounded">
                    </div>
                    
                    <button type="submit" name="login" class="btn w-100 py-2 rounded-4 ">Log in</button>
                    <?php if($error){?>
            <div class="alert alert-danger">Email or password is incorrect</div> 
            <?php }elseif($empty){ ?>
            <div class="alert alert-danger">Please Fill Required data</div> 
            <?php } ?>
                </form>
            </div>
        
            <div class="text-center mt-4">
                <a href="#" class="text-black" onclick="forgotPassword()">Forgot password?</a>
            </div>
            
        </div>
        
        <div class="rightside text-white col-md-5  d-flex flex-column justify-content-evenly align-items-lg-stretch row ">
            <h5 class="text-center abril-fatface-regular ">Stop struggling with common tasks and focus on the real choke points.</h5>
            <div class="d-flex justify-content-center align-items-center ">
                <img  src="./imgs/login.jpg" class="img-fluid border- rounded-2 " alt="Login Image">
            </div>
        </div>

        </div>

        <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="forgotPasswordModalLabel">Forgot Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" id="forgotPasswordForm">
                        <div class="modal-body">
                            <p>Please enter your email address to reset your password.</p>
                            <input type="email" name="email" id="email" placeholder="Email" class="form-control" required>
                            <div class="alert alert-danger mt-2" id="emailError" style="display: none;">
                                Email not found in our records
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="forgot" class="btn btn-primary">Send Reset Link</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        

        <script>
            function forgotPassword() {
                var myModal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'));
                document.getElementById('emailError').style.display = 'none';
                document.getElementById('email').value = '';
                myModal.show();
            }
            
            <?php if ($errf){ ?>
                document.addEventListener('DOMContentLoaded', function() {
                    var myModal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'));
                    document.getElementById('emailError').style.display = 'block';
                    myModal.show();
                });
            <?php } ?>

            // Hide error message when modal is closed
            document.getElementById('forgotPasswordModal').addEventListener('hidden.bs.modal', function () {
                document.getElementById('emailError').style.display = 'none';
                document.getElementById('email').value = '';
            });
        </script>

        <script src="./js/login.js"></script>
    </body>
    </html>

    </div>

    <script src="./js/login.js"></script>
</body>
</html>
