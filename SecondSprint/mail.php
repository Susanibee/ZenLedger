<?php
//----THIS SCRIPT REQUIRES PHPMAILER----
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

$email = $_POST['send_email'];
$subject = $_POST['send_subject'];
$message = $_POST['send_message'];
try{
    //init mailer
    $mail = new PHPMailer(true);
    
    //server side settings
    $mail->isSMTP();
    $mail->SMTPAuth = true;

    //--- ACCOUNT VERIFICATION ---
    // currently we do not have a smtp server to send our emails through

    $mail->SMTPSecure = 'tls'; 
    $mail->Host = 'smtp-mail.outlook.com';
    $mail->Port = 587; 
    $mail->Username = 'DJFKLSJDCIOEJWNKdsjkc@outlook.com';  
    $mail->Password = 'QxjNWpFF6UlRiD';           


    //recipiant
    $mail->addAddress($email);
    
    //contents
    $mail->Subject = $subject;
    $mail->Body = $message;

    //send the email
    $mail->send();

    echo 'Message has been sent';
}catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mailer</title>
</head>
<body>

    <button onclick="window.history.back();">Go Back</button>

</body>
</html>
