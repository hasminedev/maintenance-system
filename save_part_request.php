<?php
include "auth.php";
require_role("technician");
include "db.php";

$interventionId = (int)($_POST["interventionId"] ?? 0);
$part = trim($_POST["partDescription"] ?? "");

$stmt = $conn->prepare("SELECT 1 FROM Intervention WHERE id = ? AND technicianId = ?");
$stmt->bind_param("ii", $interventionId, $_SESSION["id"]);
$stmt->execute();

if ($part === "" || $stmt->get_result()->num_rows === 0) {
    header("Location: request_part.php?error=1");
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO SparePartRequest (partDescription, requestDate, status, interventionId)
     VALUES (?, CURDATE(), 'Pending', ?)"
);
$stmt->bind_param("si", $part, $interventionId);
$stmt->execute();

header("Location: request_part.php?saved=1");
exit;
?>