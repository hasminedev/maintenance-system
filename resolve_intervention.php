<?php
include "auth.php";
require_role("technician");
include "db.php";

$id = (int)($_POST["id"] ?? 0);
$date = $_POST["resolutionDate"] ?? "";

$valid = DateTime::createFromFormat("Y-m-d", $date);
if (!$valid || $valid->format("Y-m-d") !== $date || $date > date("Y-m-d")) {
    header("Location: my_interventions.php?error=date");
    exit;
}

$stmt = $conn->prepare(
    "UPDATE Intervention
     SET status = 'Resolved', resolutionDate = ?
     WHERE id = ? AND technicianId = ? AND status <> 'Resolved' AND failureDate <= ?"
);
$stmt->bind_param("siis", $date, $id, $_SESSION["id"], $date);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    header("Location: my_interventions.php?error=date");
    exit;
}

header("Location: my_interventions.php?resolved=1");
exit;
?>