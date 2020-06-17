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

    // Email Settings
    add_settings_section( 
        'copydiss-forms-email-settings',    // section slug
        'Email Settings',                   // title
        'copydiss_forms_email_settings',    // callback
        'copydiss-forms-settings'           // settings page slug
    );

    register_setting( 'copydiss-forms-settings', 'cdf_name' );
    add_settings_field(
        'copydiss-forms-name-field',       // field slug
        'Name',                            // field label
        'copydiss_forms_name_field',       // callback that echos html field
        'copydiss-forms-settings',          // settings page slug
        'copydiss-forms-email-settings'     // section slug
    );

    register_setting( 'copydiss-forms-settings', 'cdf_email' );
    add_settings_field(
        'copydiss-forms-email-field',       // field slug
        'Email',                            // field label
        'copydiss_forms_email_field',       // callback that echos html field
        'copydiss-forms-settings',          // settings page slug
        'copydiss-forms-email-settings'     // section slug
    );

    register_setting( 'copydiss-forms-settings', 'cdf_password' );
    add_settings_field(
        'copydiss-forms-password-field',       // field slug
        'Password',                            // field label
        'copydiss_forms_password_field',       // callback that echos html field
        'copydiss-forms-settings',          // settings page slug
        'copydiss-forms-email-settings'     // section slug
    );

    register_setting( 'copydiss-forms-settings', 'cdf_host' );
    add_settings_field(
        'copydiss-forms-host-field',       // field slug
        'Host',                            // field label
        'copydiss_forms_host_field',       // callback that echos html field
        'copydiss-forms-settings',          // settings page slug
        'copydiss-forms-email-settings'     // section slug
    );

    register_setting( 'copydiss-forms-settings', 'cdf_protocol' );
    add_settings_field(
        'copydiss-forms-protocol-field',       // field slug
        'Protocol',                            // field label
        'copydiss_forms_protocol_field',       // callback that echos html field
        'copydiss-forms-settings',          // settings page slug
        'copydiss-forms-email-settings'     // section slug
    );

    register_setting( 'copydiss-forms-settings', 'cdf_port' );
    add_settings_field(
        'copydiss-forms-port-field',       // field slug
        'Port',                            // field label
        'copydiss_forms_port_field',       // callback that echos html field
        'copydiss-forms-settings',          // settings page slug
        'copydiss-forms-email-settings'     // section slug
    );








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
