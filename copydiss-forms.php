<?php
/**
 * Plugin Name: CopyDiss Forms
 * Plugin URI: https://github.com/matthewdcooper/copydiss-forms
 * Version: 1.0.0
 * Requires at least: 5.3
 * Requires PHP: 7.4
 * Author: Matthew Cooper
 * License: GPLv3
 * 
 * @package CopyDissForms
 **/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require 'form-parts.php';
require 'submit-script.php';

function copydiss_shortcode_contact_form() {
    ?>
    <form id="contact_form" class="copydiss-form" enctype="multipart/form-data">
        <?php echo copydiss_form_part_timestamp(); ?>
        <?php echo copydiss_form_part_honey("the_password"); ?>
        <?php echo copydiss_form_part_target("contact"); ?>

        <textarea id="ta_message" rows="10" name="message" placeholder="How can we help?" required></textarea>

        <?php echo copydiss_form_part_contact_details(); ?>

        <?php copydiss_form_part_spinner("Sending your message. Please wait."); ?>

        <button id="btn_submit" type="submit">Send Message</button>

    </form>
    <?php echo copydiss_submit_script(
        "contact_form",
        plugin_dir_url( __FILE__ ) . "post.php",
        "Thank you for your message. We will be in touch soon.",
        "We're really sorry, but something has gone wrong.<br />Please ring us on 01379 644567."
    ); ?>

    <?php
}

add_shortcode( 'copydiss_contact_form', 'copydiss_shortcode_contact_form' );

flush_rewrite_rules();