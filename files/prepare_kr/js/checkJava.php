<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'confirmed') {
        echo 'You pressed OK!';
        // Perform actions for OK
    } else {
        echo 'You pressed Cancel!';
        // Perform actions for Cancel
    }
    exit; // Exit to prevent the rest of the script from running after form submission
}
?>

<script>
function showConfirmBox() {
    var result = confirm("Do you want to proceed?");
    if (result) {
        postToServer('confirmed');
    } else {
        postToServer('canceled');
    }
}

function postToServer(action) {
    var form = document.createElement("form");
    form.method = "POST";
    form.style.display = "none";

    var input = document.createElement("input");
    input.type = "hidden";
    input.name = "action";
    input.value = action;
    form.appendChild(input);

    document.body.appendChild(form);
    form.submit();
}

document.addEventListener("DOMContentLoaded", function() {
    showConfirmBox();
});
</script>
