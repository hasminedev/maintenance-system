<?php
include "db.php";

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$confirm = $_POST["confirm"] ?? "";
include "email_rule.php";
$email = strtolower($email);
if (!is_company_email($email)) {
    header("location: register.html?error=domain");
    exit;
}

if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
    header("Location: register.html?error=password");
    exit;
}
if ($password !== $confirm) {
    header("Location: register.html?error=mismatch");
    exit;
}


foreach (["Technician", "Manager", "Employee"] as $table) {
    $stmt = $conn->prepare("SELECT 1 FROM $table WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        header("Location: register.html?error=exists");
        exit;
    }
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO Employee (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $hash);
$stmt->execute();

header("Location: login.html?registered=1");
exit;
?>