<?php
include "auth.php";
require_role("manager");
include "db.php";

$type = trim($_POST["type"] ?? "");
$brand = trim($_POST["brand"] ?? "");
$serial = trim($_POST["serialNumber"] ?? "");
$owner = trim($_POST["owner"] ?? "");
$department = trim($_POST["department"] ?? "");
$date = $_POST["acquisitionDate"] ?? "";

if ($type === "" || $brand === "" || $serial === "") {
    header("Location: equipment.php?error=missing");
    exit;
}

$stmt = $conn->prepare("SELECT 1 FROM Equipment WHERE serialNumber = ?");
$stmt->bind_param("s", $serial);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    header("Location: equipment.php?error=exists");
    exit;
}

$owner = ($owner === "") ? null : $owner;
$department = ($department === "") ? null : $department;
$date = ($date === "") ? null : $date;

$stmt = $conn->prepare(
    "INSERT INTO Equipment (type, brand, serialNumber, owner, department, acquisitionDate)
     VALUES (?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("ssssss", $type, $brand, $serial, $owner, $department, $date);
$stmt->execute();

header("Location: equipment.php?saved=1");
exit;
?>