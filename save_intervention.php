<?php
include "auth.php";
require_role("technician");
include "db.php";

$equipmentId = (int)($_POST["equipmentId"] ?? 0);
$ticket = trim($_POST["ticketReferenceId"] ?? "");
$failureType = $_POST["failureType"] ?? "";
if ($failureType === "Other") {
    $other = trim($_POST["otherFailure"] ?? "");
    $failureType = ($other !== "") ? mb_substr($other, 0, 50) : "Other";
}
$failureDate = $_POST["failureDate"] ?? "";
$description = trim($_POST["description"] ?? "");
$technicianId = $_SESSION["id"];
$status = "In progress";

if ($ticket === "") {
    $ticket = null;
}

$stmt = $conn->prepare(
    "INSERT INTO Intervention
     (ticketReferenceId, status, failureType, failureDate, description, technicianId, equipmentId)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("sssssii", $ticket, $status, $failureType, $failureDate, $description, $technicianId, $equipmentId);
$stmt->execute();

header("Location: log_intervention.php?saved=1");
exit;
?>