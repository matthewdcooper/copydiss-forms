<?php

function copydiss_forms_add_file_script() {
    ?>
    <script>
    const fileIds = [1];
    const addFileButton = document.getElementById('btn_add_file');
    addFileButton.onclick = () => {
        const modify = (s) => {
            let i = s.lastIndexOf("_");
            return s.substr(0,i+1) + fileIds[fileIds.length-1].toString();
        }
        const recurModify = (el) => {
            for (let attr of ['id', 'name', 'for']) {
                if (el.hasAttribute(attr))
                    el.setAttribute(attr,
                        modify(el.getAttribute(attr)));
            }
            for (let child of el.children) recurModify(child);
        }

        const origFieldset = document.getElementById('fieldset_file_1');
        const newFieldset = origFieldset.cloneNode(true);
        newFieldset.fileId = fileIds[fileIds.length-1]+1;
        newFieldset.children[0].innerText = "File " + newFieldset.fileId.toString();
        newFieldset.children[1].style["display"] = "block"; // show closing 'x'
        fileIds.push(newFieldset.fileId);

        // give 'x' file deletion functionality
        const n = fileIds[fileIds.length-1];
        newFieldset.children[1].onclick = () => {
        const child = document.getElementById('fieldset_file_' + n.toString());
        document.getElementById('div_files').removeChild(child);
        const i = fileIds.indexOf(newFieldset.fileId);
        fileIds.splice(i, 1);
        };

        newFieldset.children[2].value = "";
        recurModify(newFieldset);
        origFieldset.parentElement.appendChild(newFieldset);
    }   
    </script>
    <?php
}

function copydiss_forms_submit_script($form_id, $ajax_url, $success_message, $error_message) {
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
    const form = document.getElementById("<?php echo $form_id; ?>");
    form.onsubmit = (e) => {
        e.preventDefault();

        // hide submit button and show waiting message 
        const btn = document.getElementById('btn_submit');
        const msg = document.getElementById('message');
        btn.style.display = "none";
        msg.style.display = "block";

        // TODO: what if there is no response from the server?
        const formData = new FormData(form);
        if (typeof fileIds !== 'undefined') {
            formData.append('fileIds', fileIds);
        } 
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


function copydiss_forms_show_final_size_script() {
    ?>
    <script>
    const showFinalSize = (selectSize) => {
        const id = selectSize.name.substr(selectSize.name.lastIndexOf("_")+1);
        const finalSize = document.getElementById("div_finalsize_" + id);
        if (selectSize.value !== "original") {
            finalSize.style.display = "block";
        } else {
            finalSize.style.display = "none";
        }
    }
    </script>
    <?php
}