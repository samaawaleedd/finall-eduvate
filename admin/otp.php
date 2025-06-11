<?php
include 'mail.php';

$time=time();
$_SESSION['time']=$time;

$rand=$_SESSION['otp'];
$time=$_SESSION['time'];
$email=$_SESSION['email'];

$error="";
$time+=180;
if (isset($_POST['submit'])){
    $otp=$_POST['1'].$_POST['2'].$_POST['3'].$_POST['4'].$_POST['5'];
    if($rand==$otp){
        $new_time=time();
        if($new_time <=$time){
            header("location:changepass.php");
        }else{
            $error= "expired otp";
        }
    }else{
        $error= "incorrect otp";
    }
}
if (isset($_POST['resend'])){
    $rand3=rand(10000,99999);
    $_SESSION['otp']=$rand3;
    $msg="hello your otp is $rand3";
    // php mail start->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    $mail->setFrom('notyourbusiness960@gmail.com', 'Eduvate');          //sender mail address , website name
    $mail->addAddress($email);      //reciever mail address
    $mail->isHTML(true);                               
    $mail->Subject = 'OTP';             //mail subject
    $mail->Body=($msg);                  //mail content
    $mail->send(); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Message</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./cssf/all.min.css">
    <link rel="stylesheet" href="./css/otp.css">
    <script src="./js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card d-flex">
                    <div class="card-body">
                        <h5 class="card-title text-center fs-2 mt-2 mb-3">Enter OTP</h5>
                        <p class="text-center mb-4">Please enter the OTP sent to your registered mobile number or Email.</p>
                        <form method="POST">
                            <div class="form-group d-flex justify-content-center mb-4">
                                <div class="input-group w-50">
                                    <input type="text" name="1" class="form-control otp-input rounded-3" maxlength="1" id="otp1" required oninput="moveToNext(this, 'otp2')">
                                    <input type="text" name="2" class="form-control otp-input rounded-3" maxlength="1" id="otp2" required oninput="moveToNext(this, 'otp3')">
                                    <input type="text" name="3" class="form-control otp-input rounded-3" maxlength="1" id="otp3" required oninput="moveToNext(this, 'otp4')">
                                    <input type="text" name="4" class="form-control otp-input rounded-3" maxlength="1" id="otp4" required oninput="moveToNext(this, 'otp5')">
                                    <input type="text" name="5" class="form-control otp-input rounded-3" maxlength="1" id="otp5" required oninput="moveToNext(this, '')">
                                </div>
                            </div>
                            <div class="d-flex justify-content-center mb-4">
                                <button type="submit" name="submit" class="btn w-50 py-2 rounded-4">Verify</button>
                            </div>
                        </form>
                        <form method="post">
                            <div class="text-center mt-3">
                                <button type="submit" name="resend" class="text-black">Resend OTP</button>
                            </div>
                        </form>
                        <div class="text-center mt-3 text-danger">
                            <div class="error">
                                <p><?php echo $error ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="./js/otp.js"></script>
</body>
</html> 
