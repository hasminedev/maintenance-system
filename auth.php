<?php
session_start();

function require_login() {
    if (!isset($_SESSION["role"])) {
        header("Location: login.html");
        exit;
    }
}

function require_role($role) {
    require_login();
    if ($_SESSION["role"] !== $role) {
        header("Location: dashboard.php");
        exit;
    }
}
?>