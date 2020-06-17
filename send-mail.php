<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

require_once '../copydiss-local/wp-load.php'; // so we can use 'get_option'
 
$mail = new PHPMailer(true);

function send_mail($from, $fromname, $to, $toname, $subject, $message, $attachments) {
    global $mail;
    try {
        $mail->setFrom($from, $fromname);
        $mail->addAddress($to, $toname);
        $mail->Subject = $subject;
        $mail->Body = $message;
        foreach ($attachments as $ap) {
            $mail->addAttachment($ap);
        }
        $mail->send();
        $mail->clearAttachments();
        $mail->clearAddresses();
    } catch (Exception $e) {
        die("PHPMailer Error: " . $e->errorMessage());
    }
}

function configure_email() {
    global $mail;
    $host = get_option( 'cdf_host' );
    $email = get_option( 'cdf_email' );
    $password = get_option('cdf_password' );
    $protocol = get_option( 'cdf_protocol' );
    $port = get_option( 'cdf_port' );

    $mail->isSMTP();
    $mail->Host = $host;
    $mail->SMTPAuth = true;
    $mail->Username = $email;
    $mail->Password = $password;
    $mail->SMTPSecure = $protocol;
    $mail->Port = intval( $port );
    $mail->isHTML( true );
}

configure_email();


?>
