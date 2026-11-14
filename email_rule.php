<?php
function is_company_email($email) {
    $email = strtolower(trim($email));
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false
        && str_ends_with($email, "@camlight.cm");
}
?>