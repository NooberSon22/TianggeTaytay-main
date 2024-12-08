<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function verify($sendto = null) {
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->Host     = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bantajio22@gmail.com';
        $mail->Password = 'mgqx cwpm dlrv ujxr';
        $mail->SMTPSecure = 'tls';
        $mail->Port     = 587;
    
        $mail->setFrom('e-tiangge@gmail.com', 'E-Tiangge Portal');
        $mail->addAddress('kennethsanpedro1108@gmail.com');
        // $mail->addAddress($sendto);
    
        $mail->isHTML(true);
        $mail->Body = '
        HTML message body in <b>bold</b> 
        <a href="http://localhost/ETianggeTaytay/pages/login.php" >Login</a>
        ';
        $mail->send();
        echo "Mail has been sent successfully!";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

verify();  