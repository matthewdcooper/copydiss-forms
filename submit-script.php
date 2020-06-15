<?php

function copydiss_submit_script($form_id, $ajax_url, $success_message, $error_message) {
    ?>
    <script>
    // POST a FormData object
    const postForm = (formData, url, onsuccess) => {
        const xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            onsuccess(this.responseText);
        }
        };
        xmlhttp.open('POST', url, true);
        xmlhttp.send(formData);
    }


    // Upload Form Submission
    const contactForm = document.getElementById("<?php echo $form_id; ?>");
    contactForm.onsubmit = (e) => {
        e.preventDefault();

        const formData = new FormData(contactForm);
        const btn = document.getElementById('btn_submit');
        const msg = document.getElementById('message');

        // hide submit button and show waiting message 
        btn.style.display = "none";
        msg.style.display = "block";

        postForm(formData, "<?php echo $ajax_url ?>", (r) => {
        if (r.trim() == "ok") {
            msg.innerHTML="<p><?php echo $success_message; ?></p>";
        } else {
            console.log(r); // TODO: write a log for reference
            msg.innerHTML="<p><?php echo $error_message; ?></p>";
        }
        })
    }
    </script>
    <?php
}