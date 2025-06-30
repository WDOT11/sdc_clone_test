/** Functions to show and hide the loader */
function show_ajax_loader(){
    jQuery('#public_ajax_loader').show();
}

function hide_ajax_loader(){
    jQuery('#public_ajax_loader').hide();
}

/** Toggle Password Visibility */
function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (input.type == "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}