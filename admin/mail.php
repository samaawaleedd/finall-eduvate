<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'mail/src/Exception.php';
require 'mail/src/PHPMailer.php';
require 'mail/src/SMTP.php';
require '../connection.php';

$mail = new PHPMailer();
$mail->isSMTP();                                            //Send using SMTP
$mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
$mail->Username   = "notyourbusiness960@gmail.com";                     //SMTP username
$mail->Password   = "capq xzqo hrnv swma";                          //SMTP password
$mail->SMTPSecure = "ssl";                                      //Enable implicit TLS encryption
$mail->Port       = 465;
$mail->isHTML(true);                                  //Set email format to HTML
$mail ->CharSet ="UTF-8";
?>
