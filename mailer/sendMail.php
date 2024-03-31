<?php



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "vendor/autoload.php";
$config = parse_ini_file('/var/www/private/db-config.ini');

$mail = new PHPMailer(true);
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
    $mail->Username = $config["mailer_pub_key"] ? $config["mailer_pub_key"] : getenv("MAILER_PUB"); //username
    $mail->Password = $config["mailer_priv_key"] ? $config["mailer_priv_key"] : getenv("MAILER_PRIV"); //password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587; //smtp port

    $mail->setFrom($senderEmail, $senderName);
    $mail->addAddress($customerEmail, $customerName);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $body;

    $mail->send();
    echo 'Email sent successfully.';
} catch (Exception $e) {
    echo 'Email could not be sent. Mailer Error: '. $mail->ErrorInfo;
}
