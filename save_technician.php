<?php
include "auth.php";
require_role("manager");
include "db.php";

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
include "email_rule.php";
$email = strtolower($email);
if (!is_company_email($email)) {
    header("Location: add_technician.php?error=domain");
    exit;
}
if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
    header("Location: add_technician.php?error=password");
    exit;
}

foreach (["Technician", "Manager", "Employee"] as $table) {
    $stmt = $conn->prepare("SELECT 1 FROM $table WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        header("Location: add_technician.php?error=exists");
        exit;
    }
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO Technician (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $hash);
$stmt->execute();

header("Location: add_technician.php?saved=1");
exit;
?>