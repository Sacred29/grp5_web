<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "vendor/autoload.php";


$mail = new PHPMailer(true);

$config_file = '/var/www/private/db-config.ini';

if (file_exists($config_file)) {
    // Parse the INI file
    $config = parse_ini_file($config_file);
} else {
    // Get configuration from environment variables
    $config['servername'] = getenv('SERVERNAME');
    $config['username'] = getenv('DB_USERNAME');
    $config['password'] = getenv('DB_PASSWORD');
    $config['dbname'] = getenv('DBNAME');
    $config["mailer_pub_key"] = getenv("MAILER_PUB");
    $config["mailer_priv_key"] = getenv("MAILER_PRIV"); 
    
}

// Usage example

$senderName = isset($senderName) ? $senderName : "admin"; // Set default value if not provided
$senderEmail = isset($senderEmail) ? $senderEmail : "admin@thedaniel.life";
$customerName = isset($customerName) ? $customerName : "Customer";
$customerEmail = isset($customerEmail) ? $customerEmail : null;
$subject = isset($subject) ? $subject : "Book Information";
$body = isset($body) ? $body : "<b>Please Ignore This Message</b>";

try {
    $mail->isSMTP();
    $mail->Host = 'in-v3.mailjet.com'; // host
    $mail->SMTPAuth = true;
    $mail->Username = $config["mailer_pub_key"]; //username
    $mail->Password = $config["mailer_priv_key"]; //password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587; //smtp port

    $mail->setFrom($senderEmail, $senderName);
    $mail->addAddress($customerEmail, $customerName);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $body;

    $mail->send();
    echo "<script>console.log('Email Successfully Sent');</script>";
} catch (Exception $e) {
    echo "<script>console.log('" . $mail->ErrorInfo . "');</script>";
}
