  GNU nano 6.2                                                             sendOtp.php                                                                      <?php

$config = parse_ini_file('/var/www/private/db-config.ini');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "vendor/autoload.php";

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'in-v3.mailjet.com'; // host
    $mail->SMTPAuth = true;
    $mail->Username = 'c7b87ffb5f730f5385b24cd973c18567'; //username
    $mail->Password = '79635e489f479b5bba83931ee3ee10a1'; //password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587; //smtp port

    $mail->setFrom('test@thedaniel.life', 'SENDER_NAME');
    $mail->addAddress('bull.daniel.3@gmail.com', 'RECIPIENT_NAME');

    $mail->isHTML(true);
    $mail->Subject = 'Email Subject';
    $mail->Body    = '<b>Email Body</b>';

    $mail->send();
    echo 'Email sent successfully.';
} catch (Exception $e) {
    echo 'Email could not be sent. Mailer Error: '. $mail->ErrorInfo;
}
