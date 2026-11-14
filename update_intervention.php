<?php
include "auth.php";
require_role("technician");
include "db.php";

$id = (int)($_POST["id"] ?? 0);
$equipmentId = (int)($_POST["equipmentId"] ?? 0);
$ticket = trim($_POST["ticketReferenceId"] ?? "");
$failureType = $_POST["failureType"] ?? "";
$failureDate = $_POST["failureDate"] ?? "";
$description = trim($_POST["description"] ?? "");

if ($failureType === "Other") {
    $other = trim($_POST["otherFailure"] ?? "");
    $failureType = ($other !== "") ? mb_substr($other, 0, 50) : "Other";
}
if ($ticket === "") {
    $ticket = null;
}

$valid = DateTime::createFromFormat("Y-m-d", $failureDate);
$stmt = $conn->prepare("SELECT 1 FROM Equipment WHERE id = ?");
$stmt->bind_param("i", $equipmentId);
$stmt->execute();
$equipmentOk = $stmt->get_result()->num_rows > 0;

if (!$equipmentOk || $failureType === "" || $description === ""
    || !$valid || $valid->format("Y-m-d") !== $failureDate || $failureDate > date("Y-m-d")) {
    header("Location: edit_intervention.php?id=$id&error=1");
    exit;
}

$stmt = $conn->prepare(
    "UPDATE Intervention
     SET equipmentId = ?, ticketReferenceId = ?, failureType = ?, failureDate = ?, description = ?
     WHERE id = ? AND technicianId = ? AND status <> 'Resolved'"
);
$stmt->bind_param("issssii", $equipmentId, $ticket, $failureType, $failureDate, $description, $id, $_SESSION["id"]);
$stmt->execute();

header("Location: my_interventions.php?updated=1");
exit;
?>