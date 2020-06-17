<?php

if ($_SERVER['REQUEST_METHOD'] != 'POST') die();
if ($_POST["the_password"] != "") die('no honey'); // no honey

$start = intval($_POST["timestamp"]);
$duration = time() - $start;
if ($duration < 3) die('too fast'); // too fast, must be bot

require 'send-mail.php';
require 'validate.php';

$target = "copydiss_forms_target_" . $_POST["target"];
$target();

function copydiss_forms_parse_template($s, $vars) {
	foreach ($vars as $k => $v) {
		$s = str_replace( $k, $v, $s);
	}
	return nl2br($s);
}

function copydiss_forms_target_contact() {
    $useremail = get_option( 'cdf_email' );
	$username = get_option( 'cdf_name' );
	$destination = get_option( 'cdf_contact-destination' );
	$destination_name = get_option( 'cdf_contact-destination-name' );

    $contactname = validate_name($_POST["contactname"]);
    $contactphone = validate_phone($_POST["contactphone"]);
    $contactemail = validate_email($_POST["contactemail"]);
	$message = validate_message($_POST["message"]);
	
	$vars = array(
		'[customer-name]' => $contactname,
		'[customer-phone]' => $contactphone,
		'[customer-email]' => $contactemail,
		'[customer-message]' => $message,
		'[user-name]' => $username
	);

	$subject = copydiss_forms_parse_template( get_option( 'cdf_contact-subject' ), $vars );
	$body = copydiss_forms_parse_template( get_option( 'cdf_contact-body' ), $vars );

    send_mail(
		$useremail, $username,
		$destination, $destination_name,
        $subject,
		$body,
		[]
	);

}

function copydiss_forms_target_printing() {
    $useremail = get_option( 'cdf_email' );
    $username = get_option( 'cdf_name' );
    
	function create_confirmation_message($contactname) {
		$message = "";
		$message .= "Dear " . $contactname . ", <br /><br />";
		$message .= "Thank you for your order. We will be in touch shortly to confirm the cost and pickup time. <br /><br />";
		$message .= "Kind regards,<br />CopyDiss<br />";
		return $message;
	}
	
	function create_print_assistant_message($contactname, $contactphone, $contactemail,
											 $comments, $file_descriptions) {
		$message = "";
		$message .= "Dear Print Assistant, <br /><br />";
		$message .= "We have received a new order from " . $contactname;
		$message .= " that includes " . sizeof($file_descriptions) . " file(s).<br /><br />";
		$message .= "Phone: " . $contactphone . "<br />";
		$message .= "Email: " . $contactemail . "<br /><br />";
		$message .= "Comments: " . $comments . "<br /><br />";
		$message .= "Please find the files attached and their descriptions below.<br /><br />";
	
		foreach ($file_descriptions as $fd) {
			$message .= $fd;
			$message .= "<br /><br /><hr /><br /><br />";
		}
	
		$message .= "Kind regards,<br />CopyDiss<br />";
	
		return $message;
	}
	
	function create_file_description($i, $target_file) {
		global $_POST;
		$file_desc = "";
		$file_desc .= "Filename: " . basename($target_file) . "<br />";
		$file_desc .= "Ink Colour: " . $_POST["inkcolor_".$i] . "<br />";
		$file_desc .= "Paper Colour: " . $_POST["papercolor_".$i] . "<br />";
		$file_desc .= "Sided: " . $_POST["sided_".$i] . "<br />";
		$file_desc .= "Pages : " . $_POST["num_pages_".$i] . "<br />";
		$file_desc .= "Copies: " . $_POST["num_copies_".$i] . "<br />";
		$file_desc .= "Size: " . $_POST["size_".$i] . "<br />";
		$file_desc .= "Quality: " . $_POST["quality_".$i] . "<br />";
		return $file_desc;
	}
	
	
	// HANDLE UPLOADS
	
	/* Iterate over the uploaded files, move them to the upload folder, save path in array for creating attachments later. */
	
	function move_file($i, $contactname) {
		// using index $i to identify a file in $_FILES,
		// check it for errors and then
		// attempt to move it to the upload directory
		// RETURN: the file path of its location (to be used later to send as attachment)
	
		$file_key = "file_".$i;
	
		// clean $contactname to make it suitable to be used as a filename:
		// - no special characters
		// - no whitespace
		// - only lowercase ascii letters and hyphens
		$name = "";
		foreach (str_split(strtolower($contactname)) as $c) {
			if ($c == " ") {
				$name .= "-";
			} else if (ord($c) >= 97 && ord($c) <= 122) {
				$name .= $c;
			}
		}
	
	
		try {
		
			// Undefined | Multiple Files | $_FILES Corruption Attack
			// If this request falls under any of them, treat it invalid.
			if (
				!isset($_FILES[$file_key]['error']) ||
				is_array($_FILES[$file_key]['error'])
			) {
				throw new RuntimeException('Invalid parameters.');
			}
	
			// Check $_FILES[$file_key]['error'] value.
			switch ($_FILES[$file_key]['error']) {
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_NO_FILE:
					throw new RuntimeException('No file sent.');
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					throw new RuntimeException('Exceeded filesize limit.');
				default:
					throw new RuntimeException('Unknown errors.');
			}
	
			// Don't accept files greater than n MBs.
			if ($_FILES[$file_key]['size'] > intval( get_option( 'cdf_printing-file-size' ) ) * 1024 * 1024) { 
				throw new RuntimeException('Exceeded filesize limit: ' . $_FILES[$file_key]['size']);
			}
	
			// Check file extension is allowed. 
			$allowed_file_extensions = get_option( 'cdf_printing-allowed-extensions' );
			$allowed_file_extensions = trim( $allowed_file_extensions );
			$allowed_file_extensions = str_replace( "  ", " ", $allowed_file_extensions );
			$allowed_file_extensions = explode( " " , $allowed_file_extensions );
            $ext = pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION);
			if (in_array($ext, $allowed_file_extensions) != True) {
				throw new RuntimeException('Invalid file extension: ' . $ext);
			}
    
            // TODO: read target_dir from database
            $target_dir = "uploads/";
			// Move temp file to upload directory, giving it a safe and identifiable name.
			$target_file = $target_dir . date('Y-m-d-Hi-') . $name . "_" . $i . "." . $ext;
			if (!move_uploaded_file($_FILES[$file_key]["tmp_name"], $target_file)) {
				throw new RuntimeException('Failed to move uploaded file.');
			}
        
        } catch (RuntimeException $e) {
            die($e->getMessage());
        }
        
        return $target_file;
    }

	
	// Parse POST variables
	$contactname = validate_name($_POST["contactname"]);
	$contactphone = validate_phone($_POST["contactphone"]);
	$contactemail = validate_email($_POST["contactemail"]);
	$comments = validate_message($_POST['comments']);
	
	// error check and move uploaded file(s)
	$attachment_paths = [];
	$file_descriptions = [];
	foreach (explode(",", $_POST['fileIds']) as $i) {
		$attachment = move_file($i, $contactname);
		$file_descriptions[] = create_file_description($i, $attachment);
		$attachment_paths[] = $attachment;
	}

	// send email to print assistant
	$message = create_print_assistant_message($contactname, $contactphone, $contactemail,
			   $comments, $file_descriptions);
	send_mail($useremail, $username,
			  $useremail, $username,
			  "New order received from " . $contactname, 
			  $message, $attachment_paths);
	
	// send confirmation email to customer
    send_mail($useremail, $username,
			  $contactemail, $contactname,
			  "Thank you for your order.",
			  create_confirmation_message($contactname),
			  []);    
}

echo "ok";
?>