<?php
session_start();

if (!isset($_SESSION["role"])) {
    header("Location: login.html");
    exit;
}

$pages = [
    "technician" => "technician_dashboard.php",
    "manager"    => "manager_dashboard.php",
    "employee"   => "employee_dashboard.php"
];

header("Location: " . $pages[$_SESSION["role"]]);
exit;
?>