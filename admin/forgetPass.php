<?php
include 'mail.php';
$error="";
$err=FALSE;
if(isset($_POST['submit'])){
    $email=$_POST['email'];
    $_SESSION['email']=$email;
    
    $select="SELECT * FROM `admins` WHERE `AdminEmail`='$email'";
    $runselect=mysqli_query($connect,$select);
    if(mysqli_num_rows($runselect)>0){
        $fetch=mysqli_fetch_assoc($runselect);
    $rand=rand(10000,100000);
    $msg="Hello your OTP is $rand";

         // php mail start->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
$mail->setFrom('notyourbusiness.960@gmail.com', 'Eduvate');          //sender mail address , website name
$mail->addAddress($email);      //reciever mail address
$mail->isHTML(true);                               
$mail->Subject = 'OTP';             //mail subject
$mail->Body=($msg);                  //mail content
$mail->send(); 
       //php mail end ->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 
       $_SESSION['otp']=$rand;
header("location:OTP.php");

    }else{
        $error= "email not found";
        $err=TRUE;

    }
}



?>



<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Rubik:ital,wght@0,300..900;1,300..900&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <!-- bootstrab -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- css -->
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet"
        href="https://unpkg.com/bs-brain@2.0.4/components/password-resets/password-reset-9/assets/css/password-reset-9.css">
    <link rel="stylesheet" href="./css/Forgot Password.css">
</head>

<body>
    <!-- Password Reset 9 - Bootstrap Brain Component -->
    <section class=" py-3 py-md-5 py-xl-8">
       
        <div class="container">
            <div class="row gy-4 align-items-center">
                <div class=" col-12 col-md-6 col-xl-7">
                    <div class="left d-flex justify-content-center text">
                        <div class="col-12 col-xl-9">
                            <img class="img-fluid rounded mb-4" loading="lazy" src="./assets/img/bsb-logo-light.svg"
                                width="245" height="80" alt="BootstrapBrain Logo">
                            <hr class="subtle mb-4">
                            <h2 class="h1 mb-4">Your account's security is our priority.</h2>
                            <p class="lead mb-5">To ensure its safety, please use a strong and unique password.</p>
                            <div class="text-endx">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor"
                                    class="bi bi-grip-horizontal" viewBox="0 0 16 16">
                                    <path
                                        d="M2 8a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="right col-12 col-md-6 col-xl-5">
                    <div class="card border-0 rounded-4">
                        <div class="card-body p-3 p-md-4 p-xl-5">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-4">
                                        <h2 class="h">Forgot Password</h2>
                                        <h3 class="hs fs-6 fw-normal text-secondary m-0">Provide the email address
                                            associated with your account to recover your password.</h3>
                                    </div>
                                </div>
                            </div>
                            <!-- ---------------------------------------form start---------------------------------- -->
                            <form method="post">
                                <div class="row gy-3 overflow-hidden">
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control" name="email" id="email"
                                                placeholder="name@example.com" required>
                                            <label for="email" class="form-label">Email</label>
                                           
                                        </div>
                                        <div class="error">
                                            <?php if ($err){ ?>
                                            <p>error</p>
                                            <?php echo $error; } ?>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-center">
                                            <button class="btn btn-primary btn-lg" type="submit" name="submit">Forgot
                                                Password</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- bootstrap js link -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
    <!-- js -->
    <script src="../js/otp.js"></script>
</body>

</html>
