<?php
session_start();
include "db.php";

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
include "email_rule.php";
$email = strtolower($email);
if (!is_company_email($email)) {
    header("Location: login.html?error=domain");
    exit;
}
$tables = ["technician" => "Technician", "manager" => "Manager", "employee" => "Employee"];

foreach ($tables as $role => $table) {
    $stmt = $conn->prepare("SELECT * FROM $table WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["email"] = $user["email"];
        $_SESSION["role"] = $role;
        $_SESSION["id"] = $user["id"];
        $_SESSION["name"] = $user["name"] ?? $user["email"];
          header("Location: dashboard.php");
        exit;
    }
}

header("Location: login.html?error=1");
exit;
?>