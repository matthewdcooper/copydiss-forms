<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
 
require 'mail-config.php';
 
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

function configure_email($host, $useremail, $password) {
    global $mail;
    $mail->isSMTP();
    $mail->Host = $host;
    $mail->SMTPAuth = true;
    $mail->Username = $useremail;
    $mail->Password = $password;
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    $mail->isHTML(true);
}

// configure using variables from 'mail-config.php'
configure_email($host, $useremail, $password);


?>
