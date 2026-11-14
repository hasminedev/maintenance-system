<?php
include "auth.php";
require_role("technician");
include "db.php";

$requestId = (int)($_POST["sparePartRequestId"] ?? 0);
$part = trim($_POST["partDescription"] ?? "");
$date = $_POST["replacementDate"] ?? "";
$verified = isset($_POST["verified"]) ? 1 : 0;
$tid = $_SESSION["id"];

$valid = DateTime::createFromFormat("Y-m-d", $date);
if ($part === "" || !$valid || $valid->format("Y-m-d") !== $date || $date > date("Y-m-d")) {
    header("Location: record_replacement.php?error=1");
    exit;
}

$stmt = $conn->prepare(
    "SELECT r.approvalDate
     FROM SparePartRequest r
     JOIN Intervention i ON r.interventionId = i.id
     WHERE r.id = ? AND i.technicianId = ? AND r.status = 'Approved'
       AND NOT EXISTS (SELECT 1 FROM Replacement rp WHERE rp.sparePartRequestId = r.id)"
);
$stmt->bind_param("ii", $requestId, $tid);
$stmt->execute();
$req = $stmt->get_result()->fetch_assoc();

if (!$req || ($req["approvalDate"] && $date < $req["approvalDate"])) {
    header("Location: record_replacement.php?error=1");
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO Replacement (partDescription, replacementDate, verified, sparePartRequestId, technicianId)
     VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param("ssiii", $part, $date, $verified, $requestId, $tid);
$stmt->execute();

header("Location: record_replacement.php?saved=1");
exit;
?>