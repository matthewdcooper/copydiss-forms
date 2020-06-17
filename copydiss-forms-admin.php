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

    // Contact Form
    _add_settings_section('contact-settings', 'Contact Form');
    _register_setting('contact-destination', 'Destination', 'contact-settings');
    _register_setting('contact-destination-name', 'Destination Name', 'contact-settings');
    _register_setting('contact-subject', 'Subject', 'contact-settings');
    _register_setting('contact-body', 'Body', 'contact-settings');

    // Email Settings
    _add_settings_section('email-settings', 'Email Settings');
    _register_setting('name', 'Name', 'email-settings');
    _register_setting('email', 'Email', 'email-settings');
    _register_setting('password', 'Password', 'email-settings');
    _register_setting('host', 'Host', 'email-settings');
    _register_setting('protocol', 'Protocol', 'email-settings');
    _register_setting('port', 'Port', 'email-settings');


}

function copydiss_forms_contact_settings() {
    ?>
    <p>The email template used to forward messages from the contact form.</p>
    <p>Available variables: [customer-name] [customer-phone] [customer-email] [customer-message] [user-name]</p>
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





function copydiss_forms_email_settings() {
    echo '<p>The server settings and credentials used to send out emails.</p>';
}

function copydiss_forms_name_field() {
    $value = esc_attr( get_option( 'cdf_name' ) );
    echo "<input name='cdf_name' type='text' value='$value' />";
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





function copydiss_forms_admin_template() {
    ?>
    <h1>CopyDiss Forms Settings</h1>
    <?php settings_errors(); ?>
    <form action="options.php" method="post">
        <?php settings_fields( 'copydiss-forms-settings' ); ?>
        <?php do_settings_sections( 'copydiss-forms-settings' ); ?>
        <?php submit_button(); ?>
    </form>
    <?php
}

?>
