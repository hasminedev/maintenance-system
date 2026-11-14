<?php
include "auth.php";
require_login();
include "db.php";

$tables = ["technician" => "Technician", "manager" => "Manager", "employee" => "Employee"];
$table = $tables[$_SESSION["role"]];
$id = $_SESSION["id"];

$current = $_POST["current"] ?? "";
$password = $_POST["password"] ?? "";
$confirm = $_POST["confirm"] ?? "";

$stmt = $conn->prepare("SELECT password FROM $table WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row || !password_verify($current, $row["password"])) {
    header("Location: change_password.php?error=current");
    exit;
}
if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
    header("Location: change_password.php?error=password");
    exit;
}
if ($password !== $confirm) {
    header("Location: change_password.php?error=mismatch");
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE $table SET password = ? WHERE id = ?");
$stmt->bind_param("si", $hash, $id);
$stmt->execute();

header("Location: account.php?saved=1");
exit;
?>