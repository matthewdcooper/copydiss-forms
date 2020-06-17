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
require 'form-scripts.php';
require 'copydiss-forms-admin.php';

$ajax_url = plugin_dir_url( __FILE__ ) . "post.php";
$error_message = "We're really sorry, but something has gone wrong. Please ring us on 01379 644567.";

add_shortcode( 'copydiss_forms_contact', 'copydiss_shortcode_contact_form' );
function copydiss_shortcode_contact_form() {
    global $ajax_url;
    global $error_message;
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
    <?php echo copydiss_forms_submit_script(
        "contact_form",
        $ajax_url,
        "Thank you for your message. We will be in touch soon.",
        $error_message
    ); ?>

    <?php
}


add_shortcode( 'copydiss_forms_printing', 'copydiss_shortcode_printing_form' );
function copydiss_shortcode_printing_form() {
    global $ajax_url;
    global $error_message;
    ?>
    <form id="printing_form" class="copydiss-form" enctype="multipart/form-data">
        <?php echo copydiss_form_part_timestamp(); ?>
        <?php echo copydiss_form_part_honey("the_password"); ?>
        <?php echo copydiss_form_part_target("printing"); ?>

        <h1>Upload for printing</h1>

        <p>File size limit: <?php echo get_option( 'cdf_printing-file-size' ); ?> MB</p>

        <div id="div_files">
            <fieldset id="fieldset_file_1">
            <legend>File 1</legend>
            <div id="close_1" class="close">&times</div>
                <input
                    type="file"
                    id="inp_file_1"
                    name="file_1"
                    required
                    accept="<?php echo '.' . str_replace( " ", ",.", get_option( 'cdf_printing-allowed-extensions' ) ); ?>"
                />

                <div id="radio_inkcol_1" class="div_radio">
                <label>Ink:</label>
                <span>
                    <input id="radio_inkblack_1" name="inkcolor_1" type="radio" value="black" checked="checked"/>
                    <label for="radio_inkblack_1">Black</label>
                </span>
                <span>
                    <input id="radio_inkcol_1" name="inkcolor_1" type="radio" value="color" />
                    <label for="radio_inkcol_1">Colour</label>
                </span>
                </div>

                <div id="radio_papercol_1" class="div_radio">
                    <label>Paper:</label>
                    <span>
                        <input id="radio_paperwhite_1" name="papercolor_1" type="radio" value="white" checked="checked"/>
                        <label for="radio_paperwhite_1">White</label>
                    </span>
                    <span>
                        <input class="check-for-message" id="radio_papercol_1" name="papercolor_1" type="radio" value="color" />
                        <label for="radio_papercol_1">Colour</label>
                        <label style="width: 12rem; opacity: 0;">(Great—we'll contact you to discuss colour.)</label>
                    </span>
                </div>

                <div id="radio_sided_1" class="div_radio">
                    <label>Sided:</label>
                    <span>
                        <input id="radio_single_1" name="sided_1" type="radio" value="single" checked="checked"/>
                        <label for="radio_single_1">Single</label>
                    </span>
                    <span>
                        <input id="radio_double_1" name="sided_1" type="radio" value="double" checked="checked"/>
                        <label for="radio_double_1">Double</label>
                    </span>
                </div>

                <div>
                    <label for="inp_num_pages_1">Pages:</label>
                    <input id="inp_num_pages_1" type="number" name="num_pages_1" value="1" min="1">
                </div>

                <div>
                    <label for="inp_num_copies_1">Copies :</label>
                    <input id="inp_num_copies_1" type="number" name="num_copies_1" value="1" min="1">
                </div>

                <div>
                    <label>Size:</label>
                    <select name="size_1">
                        <option value="A0">A0</option>
                        <option value="A1">A1</option>
                        <option value="A2">A2</option>
                        <option value="A3">A3</option>
                        <option value="A4" selected="selected">A4</option>
                        <option value="A5">A5</option>
                    </select>
                </div>

                <div>
                    <label>Quality:</label>
                    <select name="quality_1">
                        <option value="80gsm">80gsm</option>
                        <option value="100gsm">100gsm</option>
                        <option value="200gsm">200gsm</option>
                        <option value="300gsm">300gsm</option>
                    </select>
                </div>

                <div>
                    <label style="width:100%">Building Plans are printed on 90gsm paper</label>
                </div>

            </fieldset>

        </div>

        <button id="btn_add_file" type="button">Add another file</button>

        <fieldset>
            <legend>Comments</legend>
            <textarea id="ta_comments" rows="10" name="comments" placeholder="Anything else we should know?"></textarea>
        </fieldset>

        <?php echo copydiss_form_part_contact_details(); ?>
        <?php echo copydiss_form_part_spinner("Uploading. Please wait."); ?>
        <button id="btn_submit" type="submit">Submit</button>
    </form>
    <?php echo copydiss_forms_add_file_script(); ?>
    <?php echo copydiss_forms_submit_script(
        "printing_form", 
        $ajax_url, 
        "Upload successful. Thank you.", 
        $error_message
    ); ?>
    <?php
}

add_filter( "plugin_action_links_" . plugin_basename(__FILE__), "copydiss_forms_settings_link" );
function copydiss_forms_settings_link( $links ) {
    $url = admin_url() . 'admin.php?page=copydiss_forms_plugin';
    $settings_link = "<a href=\"$url\">Settings</a>";
    array_push( $links, $settings_link );
    return $links;
}

flush_rewrite_rules();