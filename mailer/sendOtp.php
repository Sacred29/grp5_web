<?php

$config = parse_ini_file('/var/www/private/db-config.ini');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "vendor/autoload.php";

$mail = new PHPMailer(true);
// Usage example
$senderName = isset($senderName) ? $senderName : "admin"; // Set default value if not provided
$customerName = isset($customerName) ? $customerName : "Customer"; 
$subject = isset($subject) ? $subject : "Book Information";
$body = isset($body) ? $body : "<b>Please Ignore This Message</b>";
try {
    $mail->isSMTP();
    $mail->Host = 'in-v3.mailjet.com'; // host
    $mail->SMTPAuth = true;
    $mail->Username = 'c7b87ffb5f730f5385b24cd973c18567'; //username
    $mail->Password = '79635e489f479b5bba83931ee3ee10a1'; //password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587; //smtp port

    $mail->setFrom('test@thedaniel.life', $senderName);
    $mail->addAddress('bull.daniel.3@gmail.com', $customerName);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $body;

    $mail->send();
    echo 'Email sent successfully.';
} catch (Exception $e) {
    echo 'Email could not be sent. Mailer Error: '. $mail->ErrorInfo;
}
