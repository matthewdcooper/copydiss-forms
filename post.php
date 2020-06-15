<?php

if ($_SERVER['REQUEST_METHOD'] != 'POST') die();
if ($_POST["the_password"] != "") die('no honey'); // no honey

$start = intval($_POST["timestamp"]);
$duration = time() - $start;
if ($duration < 3) die('too fast'); // too fast, must be bot

require 'send-mail.php';
require 'validate.php';

$target = "copydiss_form_target_" . $_POST["target"];
$target();

function copydiss_form_target_contact() {
    global $useremail;
    global $username;

    $contactname = validate_name($_POST["contactname"]);
    $contactphone = validate_phone($_POST["contactphone"]);
    $contactemail = validate_email($_POST["contactemail"]);
    $message = validate_message($_POST["message"]);

    $subject = "Web Message From " . $contactname;
        
    $body = "";
    $body .= "Dear Print Assistant,<br/><br/>";
    $body .= "We have received a new message from " . $contactname . ".<br/><br/>";
    $body .= "Tel: " . $contactphone . "<br/>";
    $body .= "Email: " . $contactemail . "<br/>";
    $body .= "Message: <br/><br/>";
    $body .= str_replace("\r\n", "<br/><br/>", $message);
    $body .= "<br/><br/>Kind regards,<br/>CopyDiss";

    send_mail($useremail, $username,
            $contactemail, $contactname,
            $subject,
            $body, []);

}

echo "ok";
?>