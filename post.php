<?php
require_once '../copydiss-local/wp-load.php';

// honey
if ($_SERVER['REQUEST_METHOD'] != 'POST') log_and_die( 'not a post request' );
if ( sanitize_text_field( $_POST["the_password"] != "" ) ) log_and_die( 'honey error' ); // no honey

// duration
$start = intval( sanitize_text_field ( $_POST["timestamp"] ) );
$duration = time() - $start;
if ($duration < 3) log_and_die( 'form submitted too fast' ); // too fast, must be bot

// nonce
if ( ! isset( $_POST['cdf-nonce'] )
	|| ! wp_verify_nonce( $_POST['cdf-nonce'], 'cdf-nonce' )
) {
	log_and_die( "Unverifed number used once." );
}


require 'send-mail.php';

$label = sanitize_text_field( $_POST["target"] );
$target = "copydiss_forms_target_$label";
$target();
log_and_die( "successful $label", false );
echo "ok";


function copydiss_forms_parse_template($s, $vars) {
	$s = sanitize_textarea_field($s);
	foreach ($vars as $k => $v) {
		if (is_array( $v )) {
			$expanded = "";
			foreach ($v as $el) {
				$expanded .= "\n---\n\n" . $el;
			}
			$s = str_replace( $k, $expanded, $s);
		} else {
			$s = str_replace( $k, $v, $s);
		}
	}
	return nl2br($s);
}


function copydiss_forms_target_contact() {
    $contactname = sanitize_text_field( $_POST["contactname"] );
    $contactphone = sanitize_text_field( $_POST["contactphone"] );
    $contactemail = sanitize_email( $_POST["contactemail"] );
	$message = sanitize_textarea_field( $_POST["message"] );
	
    $useremail = sanitize_email( get_option( 'cdf_email' ) );
	$username = sanitize_text_field( get_option( 'cdf_name' ) );

	$vars = array(
		'[customer-name]' => $contactname,
		'[customer-phone]' => $contactphone,
		'[customer-email]' => $contactemail,
		'[customer-message]' => $message,
		'[user-name]' => $username,
		'[user-email]' => $useremail
	);

	$destination = copydiss_forms_parse_template( get_option( 'cdf_contact-destination' ), $vars );
	$destination_name = copydiss_forms_parse_template( get_option( 'cdf_contact-destination-name' ), $vars );
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
	function create_file_description($i, $target_file) {
		global $_POST;

		$inkcolor = sanitize_text_field( $_POST["inkcolor_$i"] );
		if (! ($inkcolor === "black" || $inkcolor === "color") ) log_and_die( "invalid ink" );

		$papercolor = sanitize_text_field( $_POST["papercolor_$i"] );
		if (! ($papercolor === "white" || $papercolor === "color") ) log_and_die( "invalid paper color" );

		$sided = sanitize_text_field( $_POST["sided_$i"] );
		if (! ($sided === "single" || $sided === "double") ) log_and_die( "invalid sided value" );

		$pages = sanitize_text_field( $_POST["num_pages_$i"] );
		if ( ! is_numeric( $pages ) || intval( $pages ) < 1 ) log_and_die( "invalid pages number" );

		$copies = sanitize_text_field( $_POST["num_copies_$i"] );
		if ( ! is_numeric( $copies ) || intval( $copies ) < 1 ) log_and_die( "invalid copies number" );

		$size = sanitize_text_field( $_POST["size_$i"] );

		$quality = sanitize_text_field( $_POST["quality_$i"] );

		$file_desc = "Filename: " . basename($target_file) . "<br />"
				   . "Ink Colour: $inkcolor<br />"
				   . "Paper Colour: $papercolor<br />"
				   . "Sided: $sided<br />"
				   . "Pages: $pages<br />"
				   . "Copies: $copies<br />"
				   . "Size: $size<br />"
				   . "Quality: $quality<br />";
		return $file_desc;
	}
	
	// Parse POST variables
    $contactname = sanitize_text_field( $_POST["contactname"] );
    $contactphone = sanitize_text_field( $_POST["contactphone"] );
    $contactemail = sanitize_email( $_POST["contactemail"] );
	$comments = sanitize_textarea_field( $_POST["comments"] );
	
    $useremail = sanitize_email( get_option( 'cdf_email' ) );
	$username = sanitize_text_field( get_option( 'cdf_name' ) );

	// move uploaded files, building list of attachments and their descriptions
	$attachment_paths = [];
	$file_descriptions = [];

	$max_size = intval( get_option( 'cdf_printing-file-size' ) ) * pow(1024, 2);

	$allowed_extensions = trim( get_option( 'cdf_printing-allowed-extensions' ) );
	$allowed_extensions = str_replace( "  ", " ", $allowed_extensions );
	$allowed_extensions = explode( " " , $allowed_extensions );

	foreach (explode(",", $_POST['fileIds']) as $i) {
		$attachment = copydiss_forms_move_file($i, $contactname, $max_size, $allowed_extensions);
		$file_descriptions[] = create_file_description($i, $attachment);
		$attachment_paths[] = $attachment;
	}
	
	// send out emails
	$vars = array(
		'[customer-name]' => $contactname,
		'[customer-phone]' => $contactphone,
		'[customer-email]' => $contactemail,
		'[customer-message]' => $comments,
		'[user-name]' => $username,
		'[user-email]' => $useremail,
		'[file-descriptions]' => $file_descriptions,
		'[file-count]' => sizeof($attachment_paths)
	);

	// to print assistant
	$destination = copydiss_forms_parse_template( get_option( 'cdf_printing-assistant-destination' ), $vars );
	$destination_name = copydiss_forms_parse_template( get_option( 'cdf_printing-assistant-destination-name' ), $vars );
	$subject = copydiss_forms_parse_template( get_option( 'cdf_printing-assistant-subject' ), $vars );
	$body = copydiss_forms_parse_template( get_option( 'cdf_printing-assistant-body' ), $vars );

	send_mail(
		$useremail, $username,
		$destination, $destination_name,
		$subject,
		$body,
		$attachment_paths
	);
	
	// to customer
	$subject = copydiss_forms_parse_template( get_option( 'cdf_printing-confirmation-subject' ), $vars );
	$body = copydiss_forms_parse_template( get_option( 'cdf_printing-confirmation-body' ), $vars );

    send_mail(
		$useremail, $username,
		$contactemail, $contactname,
		$subject,
		$body,
		[]
	);    
}


function copydiss_forms_target_photo() {
	function create_file_description($i, $target_file) {
		global $_POST;

		$size = sanitize_text_field( $_POST["size_$i"] );
		$finalsize = sanitize_text_field( $_POST["finalsize_$i"] );
		$paper = sanitize_text_field( $_POST["paper_$i"] );
		$copies = sanitize_text_field( $_POST["num_copies_$i"] );
		if ( ! is_numeric( $copies ) || intval( $copies ) < 1 ) log_and_die( "invalid copies number" );

		$file_desc = "Filename: " . basename($target_file) . "<br />"
				   . "Size: $size<br />"
				   . "Final Size: $finalsize<br />"
				   . "Paper: $paper<br />"
				   . "Copies: $copies<br />";
		return $file_desc;
	}
	
	// Parse POST variables
    $contactname = sanitize_text_field( $_POST["contactname"] );
    $contactphone = sanitize_text_field( $_POST["contactphone"] );
    $contactemail = sanitize_email( $_POST["contactemail"] );
	$comments = sanitize_textarea_field( $_POST["comments"] );
	
    $useremail = sanitize_email( get_option( 'cdf_email' ) );
	$username = sanitize_text_field( get_option( 'cdf_name' ) );

	// move uploaded files, building list of attachments and their descriptions
	$attachment_paths = [];
	$file_descriptions = [];

	$max_size = intval( get_option( 'cdf_photo-file-size' ) ) * pow(1024, 2);

	$allowed_extensions = trim( get_option( 'cdf_photo-allowed-extensions' ) );
	$allowed_extensions = str_replace( "  ", " ", $allowed_extensions );
	$allowed_extensions = explode( " " , $allowed_extensions );

	foreach (explode(",", $_POST['fileIds']) as $i) {
		$attachment = copydiss_forms_move_file($i, $contactname, $max_size, $allowed_extensions);
		$file_descriptions[] = create_file_description($i, $attachment);
		$attachment_paths[] = $attachment;
	}
	
	// send out emails
	$vars = array(
		'[customer-name]' => $contactname,
		'[customer-phone]' => $contactphone,
		'[customer-email]' => $contactemail,
		'[customer-message]' => $comments,
		'[user-name]' => $username,
		'[user-email]' => $useremail,
		'[file-descriptions]' => $file_descriptions,
		'[file-count]' => sizeof($attachment_paths)
	);

	// to print assistant
	$destination = copydiss_forms_parse_template( get_option( 'cdf_photo-assistant-destination' ), $vars );
	$destination_name = copydiss_forms_parse_template( get_option( 'cdf_photo-assistant-destination-name' ), $vars );
	$subject = copydiss_forms_parse_template( get_option( 'cdf_photo-assistant-subject' ), $vars );
	$body = copydiss_forms_parse_template( get_option( 'cdf_photo-assistant-body' ), $vars );

	send_mail(
		$useremail, $username,
		$destination, $destination_name,
		$subject,
		$body,
		$attachment_paths
	);
	
	// to customer
	$subject = copydiss_forms_parse_template( get_option( 'cdf_photo-confirmation-subject' ), $vars );
	$body = copydiss_forms_parse_template( get_option( 'cdf_photo-confirmation-body' ), $vars );

    send_mail(
		$useremail, $username,
		$contactemail, $contactname,
		$subject,
		$body,
		[]
	);    
}

function copydiss_forms_move_file($i, $contactname, $max_size, $allowed_file_extensions) {
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
		if ($_FILES[$file_key]['size'] > $max_size) { 
			throw new RuntimeException('Exceeded filesize limit: ' . $_FILES[$file_key]['size']);
		}

		// Check file extension is allowed. 
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
		log_and_die( $e->getMessage() );
	}
	
	return $target_file;
}

function log_and_die($msg, $should_die=true ) {
	$line = date( DATE_ATOM ) . " " . $_SERVER['REMOTE_ADDR'] . " " .  $_SERVER['HTTP_USER_AGENT'] . " $msg\n";
	file_put_contents( "log.txt", $line, FILE_APPEND );
	if ( $should_die ) die( $msg );
}

?>