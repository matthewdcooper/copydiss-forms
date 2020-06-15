<?php


function copydiss_form_part_contact_details() {
    ?>
    <fieldset>
        <legend>Contact Details</legend>
        <input type="text" name="contactname" placeholder="Name" required>
        <input type="text" name="contactphone" placeholder="Phone">
        <input type="text" name="contactemail" placeholder="Email" required pattern="[^@\s]+@[^@\s]+\.[^@\s]+">
    </fieldset>
    <?php
}


function copydiss_form_part_target($value) {
    ?> <input type="hidden" name="target" value="<?php echo $value; ?>" /> <?php
}


function copydiss_form_part_honey($name) {
    ?> <input
        type="text"
        name="<?php echo $name ?>" 
        style="display: none !important"
        tabindex="-1"
        autocomplete="no thanks"
       >
    <?php
}


function copydiss_form_part_spinner($message) {
    ?>
    <div id="message" class="message" style="display: none;">
        <p><?php echo $message ?></p>
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
    </div>
    <?php
}


function copydiss_form_part_timestamp() {
    ?> <input type="hidden" value="<?php echo time() ?>" name="timestamp"> <?php
}

