<?php

add_action( 'admin_menu', 'copydiss_forms_admin_menu' );
function copydiss_forms_admin_menu() {
    $hookname = add_menu_page (
        'CopyDiss',
        'CopyDiss Forms',
        'manage_options',
        'copydiss_forms_settings',
        'copydiss_forms_admin_template',
        '', // TODO: add icon
        50
    );

    add_action( 'admin_init', 'copydiss_forms_settings' );
}


function copydiss_forms_settings() {

    function _add_settings_section($name, $title) {
        add_settings_section(
            'copydiss-forms-' . $name, // section slug
            $title, // title
            'copydiss_forms_' . str_replace( '-', '_', $name ), // callback
            'copydiss-forms-settings' // settings page slug
        );

    }

    function _register_setting($name, $label, $section) {
        register_setting( 'copydiss-forms-settings', 'cdf_' . $name);
        add_settings_field(
            'copydiss-forms-' . $name .'-field', // field slug
            $label, // field label
            'copydiss_forms_' . str_replace( '-', '_', $name ) . '_field', // html callback
            'copydiss-forms-settings', // settings page slug
            'copydiss-forms-' . $section // section slug
        );

    }

    // Printing
    _add_settings_section( 'printing-upload-settings', 'Printing Uploads' );
    _register_setting( 'printing-allowed-extensions', 'Allowed Extensions', 'printing-upload-settings' );
    _register_setting( 'printing-file-size', 'Maximum Upload Size', 'printing-upload-settings' );

    // Printing Assistant Template
    _add_settings_section('printing-assistant-settings', 'Printing Assistant Template');
    _register_setting('printing-assistant-destination', 'Destination', 'printing-assistant-settings');
    _register_setting('printing-assistant-destination-name', 'Destination Name', 'printing-assistant-settings');
    _register_setting('printing-assistant-subject', 'Subject', 'printing-assistant-settings');
    _register_setting('printing-assistant-body', 'Body', 'printing-assistant-settings');

    // Printing Confirmation Template
    _add_settings_section('printing-confirmation-settings', 'Printing Confirmation Template');
    _register_setting('printing-confirmation-subject', 'Subject', 'printing-confirmation-settings');
    _register_setting('printing-confirmation-body', 'Body', 'printing-confirmation-settings');


    // Photos
    _add_settings_section( 'photo-upload-settings', 'Photo Uploads' );
    _register_setting( 'photo-allowed-extensions', 'Allowed Extensions', 'photo-upload-settings' );
    _register_setting( 'photo-file-size', 'Maximum Upload Size', 'photo-upload-settings' );

    // Photo Assistant Template 
    _add_settings_section('photo-assistant-settings', 'Photo Assistant Template');
    _register_setting('photo-assistant-destination', 'Destination', 'photo-assistant-settings');
    _register_setting('photo-assistant-destination-name', 'Destination Name', 'photo-assistant-settings');
    _register_setting('photo-assistant-subject', 'Subject', 'photo-assistant-settings');
    _register_setting('photo-assistant-body', 'Body', 'photo-assistant-settings');

    // Photo Confirmation Template
    _add_settings_section('photo-confirmation-settings', 'Photo Confirmation Template');
    _register_setting('photo-confirmation-subject', 'Subject', 'photo-confirmation-settings');
    _register_setting('photo-confirmation-body', 'Body', 'photo-confirmation-settings');




    // Contact Template
    _add_settings_section('contact-settings', 'Contact Template');
    _register_setting('contact-destination', 'Destination', 'contact-settings');
    _register_setting('contact-destination-name', 'Destination Name', 'contact-settings');
    _register_setting('contact-subject', 'Subject', 'contact-settings');
    _register_setting('contact-body', 'Body', 'contact-settings');

    // Email Settings
    _add_settings_section('email-settings', 'Email Settings');
    _register_setting('name', 'User Name', 'email-settings');
    _register_setting('email', 'Email', 'email-settings');
    _register_setting('password', 'Password', 'email-settings');
    _register_setting('host', 'Host', 'email-settings');
    _register_setting('protocol', 'Protocol', 'email-settings');
    _register_setting('port', 'Port', 'email-settings');


}



///////////////////////////////
// Printing Uploads Settings //
///////////////////////////////
function copydiss_forms_printing_upload_settings() {
    ?>
    <p>Settings for the 'Upload for printing' form.</p>
    <?php
}

function copydiss_forms_printing_allowed_extensions_field() {
    $value = esc_attr( get_option( 'cdf_printing-allowed-extensions' ) );
    echo "<input style='width: 30rem' name='cdf_printing-allowed-extensions' type='text' value='$value' />";
    echo "<p class=\"description\">Space delimited, e.g. 'bmp jpg pdf'</p>";
}

function copydiss_forms_printing_file_size_field() {
    $value = esc_attr( get_option( 'cdf_printing-file-size' ) );
    echo "<input name='cdf_printing-file-size' type='number' value='$value' />";
    echo "<p class=\"description\">(MB) per file<p>";
}



////////////////////////////////////
// Printing Assistant Template //
////////////////////////////////////
function copydiss_forms_printing_assistant_settings() {
    ?>
    <p>The template used to inform the printing assistant of a new printing order.</p>
    <p>Available variables: [customer-name] [customer-phone] [customer-email] [customer-message] [user-name] [user-email] [file-descriptions] [file-count]</p>
    <?php
}

function copydiss_forms_printing_assistant_destination_field() {
    $value = esc_attr( get_option( 'cdf_printing-assistant-destination' ) );
    echo "<input name='cdf_printing-assistant-destination' type='text' value='$value' />";
}

function copydiss_forms_printing_assistant_destination_name_field() {
    $value = esc_attr( get_option( 'cdf_printing-assistant-destination-name' ) );
    echo "<input name='cdf_printing-assistant-destination-name' type='text' value='$value' />";
}

function copydiss_forms_printing_assistant_subject_field() {
    $value = esc_attr( get_option( 'cdf_printing-assistant-subject' ) );
    echo "<input name='cdf_printing-assistant-subject' type='text' value='$value' />";
}

function copydiss_forms_printing_assistant_body_field() {
    $value = esc_attr( get_option( 'cdf_printing-assistant-body' ) );
    echo "<textarea name='cdf_printing-assistant-body' rows=20 cols=40>$value</textarea>";
}




////////////////////////////////////
// Printing Confirmation Template //
////////////////////////////////////
function copydiss_forms_printing_confirmation_settings() {
    ?>
    <p>The template used to send a confirmation email to the customer.</p>
    <p>Available variables: [customer-name] [customer-phone] [customer-email] [customer-message] [user-name] [user-email] [file-descriptions] [file-count]</p>
    <?php
}

function copydiss_forms_printing_confirmation_subject_field() {
    $value = esc_attr( get_option( 'cdf_printing-confirmation-subject' ) );
    echo "<input name='cdf_printing-confirmation-subject' type='text' value='$value' />";
}

function copydiss_forms_printing_confirmation_body_field() {
    $value = esc_attr( get_option( 'cdf_printing-confirmation-body' ) );
    echo "<textarea name='cdf_printing-confirmation-body' rows=20 cols=40>$value</textarea>";
}


///////////////////////////////
// Photo Uploads Settings //
///////////////////////////////
function copydiss_forms_photo_upload_settings() {
    ?>
    <p>Settings for the 'Upload photo' form.</p>
    <?php
}

function copydiss_forms_photo_allowed_extensions_field() {
    $value = esc_attr( get_option( 'cdf_photo-allowed-extensions' ) );
    echo "<input style='width: 30rem' name='cdf_photo-allowed-extensions' type='text' value='$value' />";
    echo "<p class=\"description\">Space delimited, e.g. 'bmp jpg pdf'</p>";
}

function copydiss_forms_photo_file_size_field() {
    $value = esc_attr( get_option( 'cdf_photo-file-size' ) );
    echo "<input name='cdf_photo-file-size' type='number' value='$value' />";
    echo "<p class=\"description\">(MB) per file<p>";
}



////////////////////////////////////
// Photo Assistant Template //
////////////////////////////////////
function copydiss_forms_photo_assistant_settings() {
    ?>
    <p>The template used to inform the printing assistant of a new photo order.</p>
    <p>Available variables: [customer-name] [customer-phone] [customer-email] [customer-message] [user-name] [user-email] [file-descriptions] [file-count]</p>
    <?php
}

function copydiss_forms_photo_assistant_destination_field() {
    $value = esc_attr( get_option( 'cdf_photo-assistant-destination' ) );
    echo "<input name='cdf_photo-assistant-destination' type='text' value='$value' />";
}

function copydiss_forms_photo_assistant_destination_name_field() {
    $value = esc_attr( get_option( 'cdf_photo-assistant-destination-name' ) );
    echo "<input name='cdf_photo-assistant-destination-name' type='text' value='$value' />";
}

function copydiss_forms_photo_assistant_subject_field() {
    $value = esc_attr( get_option( 'cdf_photo-assistant-subject' ) );
    echo "<input name='cdf_photo-assistant-subject' type='text' value='$value' />";
}

function copydiss_forms_photo_assistant_body_field() {
    $value = esc_attr( get_option( 'cdf_photo-assistant-body' ) );
    echo "<textarea name='cdf_photo-assistant-body' rows=20 cols=40>$value</textarea>";
}




////////////////////////////////////
// Photo Confirmation Template //
////////////////////////////////////
function copydiss_forms_photo_confirmation_settings() {
    ?>
    <p>The template used to send a confirmation email to the customer for a photo order.</p>
    <p>Available variables: [customer-name] [customer-phone] [customer-email] [customer-message] [user-name] [user-email] [file-descriptions] [file-count]</p>
    <?php
}

function copydiss_forms_photo_confirmation_subject_field() {
    $value = esc_attr( get_option( 'cdf_photo-confirmation-subject' ) );
    echo "<input name='cdf_photo-confirmation-subject' type='text' value='$value' />";
}

function copydiss_forms_photo_confirmation_body_field() {
    $value = esc_attr( get_option( 'cdf_photo-confirmation-body' ) );
    echo "<textarea name='cdf_photo-confirmation-body' rows=20 cols=40>$value</textarea>";
}






//////////////////////
// Contact Template //
//////////////////////
function copydiss_forms_contact_settings() {
    ?>
    <p>The email template used to forward messages from the contact form.</p>
    <p>Available variables: [customer-name] [customer-phone] [customer-email] [customer-message] [user-name] [user-email]</p>
    <?php
}

function copydiss_forms_contact_destination_field() {
    $value = esc_attr( get_option( 'cdf_contact-destination' ) );
    echo "<input name='cdf_contact-destination' type='text' value='$value' />";
}

function copydiss_forms_contact_destination_name_field() {
    $value = esc_attr( get_option( 'cdf_contact-destination-name' ) );
    echo "<input name='cdf_contact-destination-name' type='text' value='$value' />";
}


function copydiss_forms_contact_subject_field() {
    $value = esc_attr( get_option( 'cdf_contact-subject' ) );
    echo "<input name='cdf_contact-subject' type='text' value='$value' />";
}

function copydiss_forms_contact_body_field() {
    $value = esc_attr( get_option( 'cdf_contact-body' ) );
    echo "<textarea name='cdf_contact-body' rows=20 cols=40>$value</textarea>";
}





////////////////////
// Email Settings //
////////////////////
function copydiss_forms_email_settings() {
    echo '<p>The server settings and credentials used to send out emails.</p>';
}

function copydiss_forms_name_field() {
    $value = esc_attr( get_option( 'cdf_name' ) );
    echo "<input name='cdf_name' type='text' value='$value' />";
    echo "<p class=\"description\">Mainly used to sign above templates.</p>";
}

function copydiss_forms_email_field() {
    $value = esc_attr( get_option( 'cdf_email' ) );
    echo "<input name='cdf_email' type='text' value='$value' />";
}

function copydiss_forms_password_field() {
    $value = esc_attr( get_option( 'cdf_password' ) );
    echo "<input name='cdf_password' type='password' value='$value' />";
}

function copydiss_forms_host_field() {
    $value = esc_attr( get_option( 'cdf_host' ) );
    echo "<input name='cdf_host' type='text' value='$value' />";
}

function copydiss_forms_protocol_field() {
    $value = esc_attr( get_option( 'cdf_protocol' ) );
    echo "<input name='cdf_protocol' type='text' value='$value' />";
}

function copydiss_forms_port_field() {
    $value = esc_attr( get_option( 'cdf_port' ) );
    echo "<input name='cdf_port' type='text' value='$value' />";
}




/////////////////////////////
// Admin Settings Template //
/////////////////////////////
function copydiss_forms_admin_template() {
    ?>
    <style>
        h2 {
            border-top: dotted black 2px;
            padding-top: 1rem;
        }
    </style>
    <h1>CopyDiss Forms Settings</h1>
    <?php settings_errors(); ?>
    <p>CopyDiss Forms provides three forms that can be added to a page using shortcode.</p>
    <p>Copy and paste the bold shortcodes below into the page you'd like to display the form.</p>
    <p>Printing: <strong>[copydiss_forms_printing]</strong></p>
    <p>Photos: <strong>[copydiss_forms_photo]</strong></p>
    <p>Contact: <strong>[copydiss_forms_contact]</strong></p>
    <p>WARNING: Do not add more than one form per page.</p>
    <form action="options.php" method="post">
        <?php settings_fields( 'copydiss-forms-settings' ); ?>
        <?php do_settings_sections( 'copydiss-forms-settings' ); ?>
        <?php submit_button(); ?>
    </form>
    <?php
}

?>
